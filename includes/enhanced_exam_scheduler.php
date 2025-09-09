<?php
/**
 * Enhanced Exam Scheduler - Manages exam availability and result publication
 * This file includes all the enhanced functionality for the exam management system
 */

// Set timezone
// Set timezone from config or use default
$timezone = defined('DEFAULT_TIMEZONE') ? DEFAULT_TIMEZONE : 'UTC';
date_default_timezone_set($timezone);

/**
 * Check if an exam is currently available for students
 * @param string $exam_id The exam ID to check
 * @param object $conn Database connection object
 * @return array Array with 'available' boolean and 'message' string
 */
function checkExamAvailability($exam_id, $conn) {
    $exam_id = mysqli_real_escape_string($conn, $exam_id);
    
    // Get exam details
    $sql = "SELECT * FROM tbl_examinations WHERE exam_id = '$exam_id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 0) {
        return array('available' => false, 'message' => 'Exam not found');
    }
    
    $exam = $result->fetch_assoc();
    
    // Check if exam is active
    if ($exam['status'] != 'Active') {
        return array('available' => false, 'message' => 'Exam is not active');
    }
    
    // Get current date and time in MM/DD/YYYY format to match database
    $current_date = date('m/d/Y');
    $current_time = date('H:i:s');
    $exam_date = $exam['date'];
    $end_exam_date = $exam['end_exam_date'];
    $start_time = $exam['start_time'];
    $end_time = $exam['end_time'];
    
    // Convert dates to timestamps for proper comparison
    $current_timestamp = strtotime($current_date);
    $exam_timestamp = strtotime($exam_date);
    $end_exam_timestamp = $end_exam_date ? strtotime($end_exam_date) : $exam_timestamp;
    
    // Check if current date is within exam date range
    if ($current_timestamp < $exam_timestamp) {
        return array('available' => false, 'message' => 'Exam starts on ' . formatDate($exam['date']));
    }
    
    if ($current_timestamp > $end_exam_timestamp) {
        return array('available' => false, 'message' => 'Exam ended on ' . formatDate($end_exam_date ?: $exam['date']));
    }
    
    // Check if current time is within exam time window (only for start date)
    if ($current_timestamp == $exam_timestamp) {
        if ($current_time < $start_time) {
            return array('available' => false, 'message' => 'Exam starts at ' . formatTime($start_time));
        }
        
        if ($current_time > $end_time) {
            return array('available' => false, 'message' => 'Exam ended at ' . formatTime($end_time));
        }
    }
    
    return array('available' => true, 'message' => 'Exam is available');
}

/**
 * Check if results are currently published for an exam
 * @param string $exam_id The exam ID to check
 * @param object $conn Database connection object
 * @return array Array with 'published' boolean and 'message' string
 */
function checkResultPublication($exam_id, $conn) {
    $exam_id = mysqli_real_escape_string($conn, $exam_id);
    
    // Get exam details
    $sql = "SELECT * FROM tbl_examinations WHERE exam_id = '$exam_id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 0) {
        return array('published' => false, 'message' => 'Exam not found');
    }
    
    $exam = $result->fetch_assoc();
    
    // Check if results are manually published
    if ($exam['result_publish_status'] == 'Published') {
        return array('published' => true, 'message' => 'Results are published');
    }
    
    // Check scheduled publication
    if ($exam['result_publish_status'] == 'Scheduled') {
        $current_date = date('Y-m-d');
        $current_time = date('H:i:s');
        
        $start_date = $exam['result_publish_start_date'];
        $start_time = $exam['result_publish_start_time'];
        $end_date = $exam['result_publish_end_date'];
        $end_time = $exam['result_publish_end_time'];
        
        // Convert date formats if needed
        if ($start_date && strpos($start_date, '/') !== false) {
            $date_parts = explode('/', $start_date);
            if (count($date_parts) == 3) {
                $start_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
            }
        }
        
        if ($end_date && strpos($end_date, '/') !== false) {
            $date_parts = explode('/', $end_date);
            if (count($date_parts) == 3) {
                $end_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
            }
        }
        
        // Check if current date/time is within publication window
        if ($start_date && $end_date) {
            if ($current_date < $start_date || $current_date > $end_date) {
                return array('published' => false, 'message' => 'Results will be published from ' . formatDate($start_date) . ' to ' . formatDate($end_date));
            }
            
            if ($current_date == $start_date && $start_time && $current_time < $start_time) {
                return array('published' => false, 'message' => 'Results will be published at ' . formatTime($start_time));
            }
            
            if ($current_date == $end_date && $end_time && $current_time > $end_time) {
                return array('published' => false, 'message' => 'Results publication ended at ' . formatTime($end_time));
            }
        }
        
        return array('published' => true, 'message' => 'Results are published');
    }
    
    return array('published' => false, 'message' => 'Results are not published');
}

/**
 * Automatically start exams at scheduled time
 * @param object $conn Database connection object
 * @return void
 */
function autoStartExams($conn) {
    $current_date = date('m/d/Y');
    $current_time = date('H:i:s');
    
    // Find exams that should start now
    $sql = "UPDATE tbl_examinations 
            SET status = 'Active' 
            WHERE date = '$current_date' 
            AND start_time <= '$current_time' 
            AND end_time > '$current_time' 
            AND status = 'Inactive'";
    
    $conn->query($sql);
    
    // Find exams that should be completed
    $sql = "UPDATE tbl_examinations 
            SET status = 'Completed' 
            WHERE (date < '$current_date') 
            OR (date = '$current_date' AND end_time < '$current_time') 
            AND status = 'Active'";
    
    $conn->query($sql);
}

/**
 * Automatically stop exams when their scheduled time ends
 * @param object $conn Database connection object
 */
function autoStopExams($conn) {
    $current_date = date('m/d/Y'); // Use MM/DD/YYYY format to match database
    $current_time = date('H:i:s');
    
    // Get exams that should be stopped
    $sql = "SELECT * FROM tbl_examinations 
            WHERE status = 'Active' 
            AND ((end_exam_date < '$current_date') OR 
                 (end_exam_date = '$current_date' AND end_time < '$current_time'))";
    
    $result = $conn->query($sql);
    
    while ($exam = $result->fetch_assoc()) {
        $exam_id = $exam['exam_id'];
        $update_sql = "UPDATE tbl_examinations SET status = 'Inactive' WHERE exam_id = '$exam_id'";
        $conn->query($update_sql);
    }
}

/**
 * Update exam status automatically
 * @param object $conn Database connection object
 */
function updateExamStatus($conn) {
    autoStartExams($conn);
    autoStopExams($conn);
}

/**
 * Automatically update result publication status
 * @param object $conn Database connection object
 */
function updateResultPublicationStatus($conn) {
    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');
    
    // Get all exams with scheduled result publication
    $sql = "SELECT * FROM tbl_examinations WHERE result_publish_status = 'Scheduled'";
    $result = $conn->query($sql);
    
    while ($exam = $result->fetch_assoc()) {
        $start_date = $exam['result_publish_start_date'];
        $start_time = $exam['result_publish_start_time'];
        $end_date = $exam['result_publish_end_date'];
        $end_time = $exam['result_publish_end_time'];
        
        // Convert date formats if needed
        if ($start_date && strpos($start_date, '/') !== false) {
            $date_parts = explode('/', $start_date);
            if (count($date_parts) == 3) {
                $start_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
            }
        }
        
        if ($end_date && strpos($end_date, '/') !== false) {
            $date_parts = explode('/', $end_date);
            if (count($date_parts) == 3) {
                $end_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
            }
        }
        
        $exam_id = $exam['exam_id'];
        
        // Check if results should be published
        $should_publish = false;
        $should_unpublish = false;
        
        if ($start_date && $end_date) {
            if ($current_date >= $start_date && $current_date <= $end_date) {
                if ($current_date == $start_date && $start_time && $current_time < $start_time) {
                    $should_unpublish = true;
                } elseif ($current_date == $end_date && $end_time && $current_time > $end_time) {
                    $should_unpublish = true;
                } else {
                    $should_publish = true;
                }
            } else {
                $should_unpublish = true;
            }
        }
        
        // Update result publication status
        if ($should_publish) {
            $update_sql = "UPDATE tbl_examinations SET result_publish_status = 'Published' WHERE exam_id = '$exam_id'";
            $conn->query($update_sql);
            
            // Also update assessment records
            $update_records_sql = "UPDATE tbl_assessment_records SET rstatus = 'Result Published' WHERE exam_id = '$exam_id'";
            $conn->query($update_records_sql);
        } elseif ($should_unpublish) {
            $update_sql = "UPDATE tbl_examinations SET result_publish_status = 'Not Published' WHERE exam_id = '$exam_id'";
            $conn->query($update_sql);
            
            // Also update assessment records
            $update_records_sql = "UPDATE tbl_assessment_records SET rstatus = 'Result not published' WHERE exam_id = '$exam_id'";
            $conn->query($update_records_sql);
        }
    }
}

/**
 * Get upcoming exams for a specific class
 * @param string $class The class name
 * @param object $conn Database connection object
 * @return array Array of upcoming exams
 */
function getUpcomingExams($class, $conn) {
    $class = mysqli_real_escape_string($conn, $class);
    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');
    
    $sql = "SELECT * FROM tbl_examinations 
            WHERE class = '$class' 
            AND ((date > '$current_date') OR (date = '$current_date' AND start_time > '$current_time'))
            ORDER BY date ASC, start_time ASC";
    
    $result = $conn->query($sql);
    $upcoming_exams = array();
    
    while ($row = $result->fetch_assoc()) {
        $upcoming_exams[] = $row;
    }
    
    return $upcoming_exams;
}

/**
 * Get all exams for a specific class
 * @param string $class The class name
 * @param object $conn Database connection object
 * @return array Array of all exams for the class
 */
function getAllExamsForClass($class, $conn) {
    $class = mysqli_real_escape_string($conn, $class);
    
    $sql = "SELECT * FROM tbl_examinations 
            WHERE class = '$class' 
            ORDER BY date ASC, start_time ASC";
    
    $result = $conn->query($sql);
    $exams = array();
    
    while ($row = $result->fetch_assoc()) {
        $exams[] = $row;
    }
    
    return $exams;
}

/**
 * Categorize exams for a specific class
 * @param string $class The class name
 * @param object $conn Database connection object
 * @return array Array with categorized exams
 */
function categorizeExamsForClass($class, $conn) {
    $class = mysqli_real_escape_string($conn, $class);
    $current_date = date('m/d/Y'); // Use MM/DD/YYYY format to match database
    $current_time = date('H:i:s');
    
    $sql = "SELECT * FROM tbl_examinations 
            WHERE class = '$class' 
            ORDER BY date ASC, start_time ASC";
    
    $result = $conn->query($sql);
    $active_exams = array();
    $upcoming_exams = array();
    $past_exams = array();
    
    while ($row = $result->fetch_assoc()) {
        $exam_date = $row['date'];
        $end_exam_date = $row['end_exam_date'] ?: $row['date'];
        $start_time = $row['start_time'];
        $end_time = $row['end_time'];
        $status = $row['status'];
        
        // Convert database date format to timestamp for comparison
        $exam_timestamp = strtotime($exam_date);
        $end_exam_timestamp = strtotime($end_exam_date);
        $current_timestamp = strtotime($current_date);
        
        // Check if exam is currently active, upcoming, or past
        $is_active = false;
        $is_upcoming = false;
        $is_past = false;
        
        // Check exam status first
        if ($status === 'Active') {
            // If exam is marked as Active, check if it's within time window
            if ($current_timestamp >= $exam_timestamp && $current_timestamp <= $end_exam_timestamp) {
                if ($current_time >= $start_time && $current_time <= $end_time) {
                    $is_active = true;
                } elseif ($current_time < $start_time) {
                    $is_upcoming = true;
                } else {
                    $is_past = true;
                }
            } elseif ($current_timestamp < $exam_timestamp) {
                $is_upcoming = true;
            } else {
                $is_past = true;
            }
        } elseif ($status === 'Inactive') {
            // If exam is Inactive, check if it's scheduled for the future
            if ($current_timestamp < $exam_timestamp) {
                $is_upcoming = true;
            } else {
                $is_past = true;
            }
        } elseif ($status === 'Completed') {
            $is_past = true;
        } else {
            // Default case - categorize by date/time
            if ($current_timestamp >= $exam_timestamp && $current_timestamp <= $end_exam_timestamp) {
                if ($current_time >= $start_time && $current_time <= $end_time) {
                    $is_active = true;
                } elseif ($current_time < $start_time) {
                    $is_upcoming = true;
                } else {
                    $is_past = true;
                }
            } elseif ($current_timestamp < $exam_timestamp) {
                $is_upcoming = true;
            } else {
                $is_past = true;
            }
        }
        
        // Categorize the exam
        if ($is_active) {
            $active_exams[] = $row;
        } elseif ($is_upcoming) {
            $upcoming_exams[] = $row;
        } elseif ($is_past) {
            $past_exams[] = $row;
        }
    }
    
    return array(
        'active' => $active_exams,
        'upcoming' => $upcoming_exams,
        'past' => $past_exams
    );
}

/**
 * Get active exams for a specific class
 * @param string $class The class name
 * @param object $conn Database connection object
 * @return array Array of active exams
 */
function getActiveExams($class, $conn) {
    $class = mysqli_real_escape_string($conn, $class);
    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');
    
    $sql = "SELECT * FROM tbl_examinations 
            WHERE class = '$class' 
            AND status = 'Active'
            AND date <= '$current_date'
            AND (end_exam_date IS NULL OR end_exam_date >= '$current_date')
            AND ((date < '$current_date') OR (date = '$current_date' AND start_time <= '$current_time'))
            AND ((end_exam_date IS NULL OR end_exam_date > '$current_date') OR (end_exam_date = '$current_date' AND end_time >= '$current_time'))";
    
    $result = $conn->query($sql);
    $active_exams = array();
    
    while ($row = $result->fetch_assoc()) {
        $active_exams[] = $row;
    }
    
    return $active_exams;
}

/**
 * Get exam status with detailed information
 * @param string $exam_id The exam ID
 * @param object $conn Database connection object
 * @return array Array with status information
 */
function getExamStatus($exam_id, $conn) {
    $exam_id = mysqli_real_escape_string($conn, $exam_id);
    
    $sql = "SELECT * FROM tbl_examinations WHERE exam_id = '$exam_id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 0) {
        return array('error' => 'Exam not found');
    }
    
    $exam = $result->fetch_assoc();
    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');
    $exam_date = $exam['date'];
    $end_exam_date = $exam['end_exam_date'];
    
    // Convert date format if needed
    if (strpos($exam_date, '/') !== false) {
        $date_parts = explode('/', $exam_date);
        if (count($date_parts) == 3) {
            $exam_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
        }
    }
    
    if ($end_exam_date && strpos($end_exam_date, '/') !== false) {
        $date_parts = explode('/', $end_exam_date);
        if (count($date_parts) == 3) {
            $end_exam_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
        }
    }
    
    $status_info = array(
        'exam_id' => $exam['exam_id'],
        'exam_name' => $exam['exam_name'],
        'class' => $exam['class'],
        'subject' => $exam['subject'],
        'date' => $exam['date'],
        'end_exam_date' => $exam['end_exam_date'],
        'start_time' => $exam['start_time'],
        'end_time' => $exam['end_time'],
        'duration' => $exam['duration'],
        'status' => $exam['status'],
        'current_date' => $current_date,
        'current_time' => $current_time,
        'is_available' => false,
        'message' => ''
    );
    
    // Check availability
    $availability = checkExamAvailability($exam_id, $conn);
    $status_info['is_available'] = $availability['available'];
    $status_info['message'] = $availability['message'];
    
    return $status_info;
}

/**
 * Format time for display
 * @param string $time Time in HH:MM:SS format
 * @return string Formatted time
 */
function formatTime($time) {
    return date('h:i A', strtotime($time));
}

/**
 * Format date for display
 * @param string $date Date in various formats
 * @return string Formatted date
 */
function formatDate($date) {
    // Convert from dd/mm/yyyy to yyyy-mm-dd for processing
    if (strpos($date, '/') !== false) {
        $date_parts = explode('/', $date);
        if (count($date_parts) == 3) {
            $date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
        }
    }
    
    return date('F j, Y', strtotime($date));
}

/**
 * Get exam duration in minutes
 * @param string $start_time Start time
 * @param string $end_time End time
 * @return int Duration in minutes
 */
function getExamDuration($start_time, $end_time) {
    $start = strtotime($start_time);
    $end = strtotime($end_time);
    return round(($end - $start) / 60);
}

/**
 * Validate exam date range
 * @param string $start_date Start date
 * @param string $end_date End date
 * @return bool True if valid
 */
function validateExamDateRange($start_date, $end_date) {
    if (!$start_date || !$end_date) {
        return false;
    }
    
    $start = strtotime($start_date);
    $end = strtotime($end_date);
    
    return $start <= $end;
}

/**
 * Validate time range
 * @param string $start_time Start time
 * @param string $end_time End time
 * @return bool True if valid
 */
function validateTimeRange($start_time, $end_time) {
    if (!$start_time || !$end_time) {
        return false;
    }
    
    $start = strtotime($start_time);
    $end = strtotime($end_time);
    
    return $start < $end;
}
?> 