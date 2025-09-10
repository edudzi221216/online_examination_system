<?php
/**
 * Enhanced Student Exam Status System
 * Provides live countdown timers and automatic status updates
 */

/**
 * Categorize exams for a student based on current time and exam schedule
 */
function categorizeExamsForStudent($class, $conn, $student_id) {
    $current_time = time();
    $current_date = date('Y-m-d H:i:s');
    
    $exams = array(
        'pending' => array(),
        'active' => array(),
        'past' => array()
    );

    // retrieve pending exams
    $sql = "SELECT * FROM tbl_examinations
        WHERE `class` = ?
          AND TIMESTAMP(`date`, `start_time`) > ?
        ORDER BY `date` ASC, `start_time` ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $class, $current_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $exams["pending"] = $result->fetch_all(MYSQLI_ASSOC);

    // retrieve active
    $sql = "SELECT e.*
            FROM tbl_examinations e
            LEFT JOIN tbl_assessment_records ar
            ON e.exam_id = ar.exam_id AND ar.student_id = ?
            WHERE e.class = ?
            AND e.status = 'Active'
            AND TIMESTAMP(e.date, e.start_time) <= NOW()
            AND (
                    (e.end_exam_date IS NULL AND TIMESTAMP(e.date, e.end_time) >= NOW())
                    OR (e.end_exam_date IS NOT NULL AND TIMESTAMP(e.end_exam_date, e.end_time) >= NOW())
                )
            AND ar.exam_id IS NULL
            ORDER BY e.date ASC, e.start_time ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $student_id, $class);
    $stmt->execute();
    $result = $stmt->get_result();
    $exams["active"] = $result->fetch_all(MYSQLI_ASSOC);

    $sql = "SELECT e.*, ar.record_id, ar.rstatus
        FROM tbl_examinations e
        LEFT JOIN tbl_assessment_records ar
          ON e.exam_id = ar.exam_id AND ar.student_id = ?
        WHERE e.class = ?
          AND (
                ar.record_id IS NOT NULL
                OR (
                    (e.end_exam_date IS NULL AND TIMESTAMP(e.date, e.end_time) < NOW())
                    OR (e.end_exam_date IS NOT NULL AND TIMESTAMP(e.end_exam_date, e.end_time) < NOW())
                  )
              )
        ORDER BY e.date DESC, e.start_time DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $student_id, $class);
    $stmt->execute();
    $result = $stmt->get_result();
    $exams["past"] = $result->fetch_all(MYSQLI_ASSOC);
    
    
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
        $results_published = $exam["rstatus"] == "Result Published" || $exam["result_publish_status"] == "Published";
        
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
