<?php
/**
 * Enhanced Student Exam Status System
 * Provides live countdown timers and automatic status updates
 */

/**
 * Categorize exams for a student based on current time and exam schedule
 */
function categorizeExamsForStudent($class, $conn) {
    $current_time = time();
    $current_date = date('Y-m-d H:i:s');
    
    $exams = array(
        'pending' => array(),
        'active' => array(),
        'past' => array()
    );
    
    // Get all exams for the student's class
    $sql = "SELECT * FROM tbl_examinations WHERE class = ? ORDER BY date ASC, start_time ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $class);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        // Convert date format from MM/DD/YYYY to YYYY-MM-DD for proper parsing
        $start_date = $row['date'];
        $end_date = $row['end_exam_date'];
        
        // Handle different date formats
        if (strpos($start_date, '/') !== false) {
            // Convert MM/DD/YYYY to YYYY-MM-DD
            $start_parts = explode('/', $start_date);
            if (count($start_parts) == 3) {
                $start_date = $start_parts[2] . '-' . str_pad($start_parts[0], 2, '0', STR_PAD_LEFT) . '-' . str_pad($start_parts[1], 2, '0', STR_PAD_LEFT);
            }
        }
        
        if (strpos($end_date, '/') !== false) {
            // Convert MM/DD/YYYY to YYYY-MM-DD
            $end_parts = explode('/', $end_date);
            if (count($end_parts) == 3) {
                $end_date = $end_parts[2] . '-' . str_pad($end_parts[0], 2, '0', STR_PAD_LEFT) . '-' . str_pad($end_parts[1], 2, '0', STR_PAD_LEFT);
            }
        }
        
        $exam_start_time = strtotime($start_date . ' ' . $row['start_time']);
        $exam_end_time = strtotime($end_date . ' ' . $row['end_time']);
        
        // Debug: Add some logging
        error_log("Exam: " . $row['exam_name'] . " - Start: " . $start_date . ' ' . $row['start_time'] . " (" . $exam_start_time . ") - End: " . $end_date . ' ' . $row['end_time'] . " (" . $exam_end_time . ") - Current: " . $current_time);
        
        if ($current_time < $exam_start_time) {
            // Exam hasn't started yet
            $exams['pending'][] = $row;
        } elseif ($current_time >= $exam_start_time && $current_time <= $exam_end_time) {
            // Exam is currently active
            $exams['active'][] = $row;
        } else {
            // Exam has ended
            $exams['past'][] = $row;
        }
    }
    
    return $exams;
}

/**
 * Generate countdown timer HTML for upcoming exams
 */
function generateCountdownTimer($exam_id, $start_date, $start_time) {
    $exam_start = strtotime($start_date . ' ' . $start_time);
    $countdown_id = 'countdown-' . $exam_id;
    
    return '<span id="' . $countdown_id . '" class="countdown-timer" data-start-time="' . $exam_start . '"></span>';
}

/**
 * Generate exam status badge
 */
function generateExamStatusBadge($status) {
    $badge_class = '';
    $badge_text = '';
    
    switch (strtolower($status)) {
        case 'active':
            $badge_class = 'label-success';
            $badge_text = 'ACTIVE';
            break;
        case 'inactive':
            $badge_class = 'label-default';
            $badge_text = 'INACTIVE';
            break;
        case 'pending':
            $badge_class = 'label-warning';
            $badge_text = 'PENDING';
            break;
        case 'past':
            $badge_class = 'label-info';
            $badge_text = 'COMPLETED';
            break;
        default:
            $badge_class = 'label-default';
            $badge_text = strtoupper($status);
    }
    
    return '<span class="label ' . $badge_class . '">' . $badge_text . '</span>';
}

/**
 * Generate action button based on exam status
 */
function generateExamActionButton($exam, $exam_status) {
    global $conn, $myid;
    
    $exam_id = $exam['exam_id'];
    $status = strtolower($exam_status);
    
    if ($status === 'active') {
        // Check if student has already taken this exam
        $taken = hasStudentTakenExam($myid, $exam_id, $conn);
        if ($taken) {
            return '<button class="btn btn-info btn-rounded btn-sm" disabled>
                        <i class="fa fa-check"></i> Already Taken
                    </button>';
        } else {
            return '<a class="btn btn-success btn-rounded btn-sm" href="take-assessment.php?id=' . $exam_id . '">
                        <i class="fa fa-play"></i> Start Exam
                    </a>';
        }
    } elseif ($status === 'pending') {
        // For pending exams, the button should be disabled until start time
        return '<button class="btn btn-warning btn-rounded btn-sm" disabled>
                    <i class="fa fa-clock-o"></i> Not Started
                </button>';
    } elseif ($status === 'past') {
        // For past exams, check if results are published
        $current_time = time();
        $exam_end_time = strtotime($exam['end_exam_date'] . ' ' . $exam['end_time']);
        
        // Check if student has taken the exam and has results
        $results = getExamResults($myid, $exam_id, $conn);
        $results_published = false;
        
        if ($results && !empty($results['score'])) {
            // Check the rstatus field in assessment records
            if (isset($results['rstatus']) && $results['rstatus'] === 'Result Published') {
                $results_published = true;
            } else {
                // Fallback: Results are considered published if exam has ended and student has a score
                if ($current_time > $exam_end_time) {
                    $results_published = true;
                }
            }
        }
        
        if ($results_published) {
            return '<a class="btn btn-info btn-rounded btn-sm" href="view-past-exam.php?id=' . $exam_id . '">
                        <i class="fa fa-eye"></i> View Questions
                    </a>';
        } else {
            return '<button class="btn btn-default btn-rounded btn-sm" disabled>
                        <i class="fa fa-clock-o"></i> Results Not Published
                    </button>';
        }
    } else {
        return '<button class="btn btn-default btn-rounded btn-sm" disabled>
                    <i class="fa fa-question"></i> Unknown Status
                </button>';
    }
}

/**
 * Check if student has already taken this exam
 */
function hasStudentTakenExam($student_id, $exam_id, $conn) {
    $sql = "SELECT COUNT(*) as count FROM tbl_assessment_records WHERE student_id = ? AND exam_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $student_id, $exam_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    
    return $count > 0;
}

/**
 * Get exam results for a student
 */
function getExamResults($student_id, $exam_id, $conn) {
    $sql = "SELECT * FROM tbl_assessment_records WHERE student_id = ? AND exam_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $student_id, $exam_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Format time for display
 */
function formatTime($time) {
    return date('H:i', strtotime($time));
}

/**
 * Format date for display
 */
function formatDate($date) {
    if (strpos($date, '/') !== false) {
        // Convert MM/DD/YYYY to YYYY-MM-DD
        $parts = explode('/', $date);
        if (count($parts) === 3) {
            return $parts[2] . '-' . $parts[0] . '-' . $parts[1];
        }
    }
    return $date;
}

/**
 * Generate JavaScript for countdown timers
 */
function generateCountdownJavaScript($upcoming_exams) {
    $js = "
    <script>
        // Countdown timer system
        function updateCountdowns() {
            const now = new Date().getTime();
            
            const pendingExams = " . json_encode($upcoming_exams) . ";
            
            pendingExams.forEach(function(exam) {
                const countdownId = 'countdown-' + exam.exam_id;
                const countdownElement = document.getElementById(countdownId);
                
                if (countdownElement) {
                    const examDate = exam.date;
                    const examTime = exam.start_time;
                    
                    // Convert date format if needed
                    let examDateTime;
                    if (examDate.includes('/')) {
                        const dateParts = examDate.split('/');
                        examDateTime = dateParts[2] + '-' + dateParts[0] + '-' + dateParts[1] + ' ' + examTime;
                    } else {
                        examDateTime = examDate + ' ' + examTime;
                    }
                    
                    const examStart = new Date(examDateTime).getTime();
                    const timeLeft = examStart - now;
                    
                    if (timeLeft > 0) {
                        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                        
                        let countdownText = '';
                        if (days > 0) countdownText += days + 'd ';
                        if (hours > 0) countdownText += hours + 'h ';
                        if (minutes > 0) countdownText += minutes + 'm ';
                        countdownText += seconds + 's';
                        
                        countdownElement.innerHTML = countdownText;
                        countdownElement.className = 'countdown-timer text-warning';
                    } else {
                        // Exam should start now - enable start button and refresh
                        countdownElement.innerHTML = 'Starting...';
                        countdownElement.className = 'countdown-timer text-success';
                        
                        // Find and enable the start exam button for this exam
                        const startButton = document.querySelector('a[href*=\"take-assessment.php?id=' + exam.exam_id + '\"]');
                        if (startButton) {
                            startButton.className = 'btn btn-success btn-rounded btn-sm';
                            startButton.disabled = false;
                        }
                        
                        // Auto-refresh the page after 5 seconds to update status
                        setTimeout(function() {
                            location.reload();
                        }, 5000);
                    }
                }
            });
        }
        
        // Update countdown every second
        setInterval(updateCountdowns, 1000);
        
        // Initial call
        updateCountdowns();
        
        // Auto-refresh page every 30 seconds to keep exam status updated
        setInterval(function() {
            location.reload();
        }, 30000);
    </script>";
    
    return $js;
}

/**
 * Generate exam table HTML
 */
function generateExamTable($exams, $exam_status, $show_actions = true) {
    if (empty($exams)) {
        return '<div class="alert alert-info" role="alert">
                    <em>No ' . $exam_status . ' exams available at this time.</em>
                  </div>';
    }
    
    $table_html = '<div class="panel panel-white">
                        <div class="panel-heading">
                            <h4>' . ($exam_status === 'pending' ? 'Pending' : ucfirst($exam_status)) . ' Exams</h4>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Subject</th>
                                            <th>Exam Name</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Duration</th>';
    
    if ($exam_status === 'pending') {
        $table_html .= '<th>Countdown</th>';
    }
    
    if ($show_actions) {
        $table_html .= '<th>Action</th>';
    }
    
    $table_html .= '</tr></thead><tbody>';
    
    foreach ($exams as $exam) {
        $table_html .= '<tr>
                            <td>' . htmlspecialchars($exam['subject']) . '</td>
                            <td>' . htmlspecialchars($exam['exam_name']) . '</td>
                            <td>' . formatDate($exam['date']) . '</td>
                            <td>' . formatDate($exam['end_exam_date']) . '</td>
                            <td>' . formatTime($exam['start_time']) . '</td>
                            <td>' . formatTime($exam['end_time']) . '</td>
                            <td>' . $exam['duration'] . ' minutes</td>';
        
        if ($exam_status === 'pending') {
            $table_html .= '<td>' . generateCountdownTimer($exam['exam_id'], $exam['date'], $exam['start_time']) . '</td>';
        }
        
        if ($show_actions) {
            $table_html .= '<td>' . generateExamActionButton($exam, $exam_status) . '</td>';
        }
        
        $table_html .= '</tr>';
    }
    
    $table_html .= '</tbody></table></div></div></div>';
    
    return $table_html;
}
?>
