<?php
/**
 * Exam Scheduler - Automatically manages exam availability based on date and time
 * This file should be included in pages where exam availability needs to be checked
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
    
    // Get current date and time
    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');
    $exam_date = $exam['date'];
    $start_time = $exam['start_time'];
    $end_time = $exam['end_time'];
    
    // Convert date format if needed (assuming date is in dd/mm/yyyy format)
    if (strpos($exam_date, '/') !== false) {
        $date_parts = explode('/', $exam_date);
        if (count($date_parts) == 3) {
            $exam_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
        }
    }
    
    // Check if today is the exam date
    if ($current_date != $exam_date) {
        if ($current_date < $exam_date) {
            return array('available' => false, 'message' => 'Exam is scheduled for ' . $exam['date']);
        } else {
            return array('available' => false, 'message' => 'Exam date has passed');
        }
    }
    
    // Check if current time is within exam time window
    if ($current_time < $start_time) {
        return array('available' => false, 'message' => 'Exam starts at ' . $start_time);
    }
    
    if ($current_time > $end_time) {
        return array('available' => false, 'message' => 'Exam ended at ' . $end_time);
    }
    
    return array('available' => true, 'message' => 'Exam is available');
}

/**
 * Automatically update exam status based on date and time
 * This function should be called periodically (e.g., via cron job)
 * @param object $conn Database connection object
 */
function updateExamStatus($conn) {
    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');
    
    // Get all exams
    $sql = "SELECT * FROM tbl_examinations WHERE status = 'Active'";
    $result = $conn->query($sql);
    
    while ($exam = $result->fetch_assoc()) {
        $exam_date = $exam['date'];
        $start_time = $exam['start_time'];
        $end_time = $exam['end_time'];
        
        // Convert date format if needed
        if (strpos($exam_date, '/') !== false) {
            $date_parts = explode('/', $exam_date);
            if (count($date_parts) == 3) {
                $exam_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
            }
        }
        
        $exam_id = $exam['exam_id'];
        
        // Check if exam date has passed
        if ($current_date > $exam_date) {
            // Exam date has passed, deactivate it
            $update_sql = "UPDATE tbl_examinations SET status = 'Inactive' WHERE exam_id = '$exam_id'";
            $conn->query($update_sql);
        } 
        // Check if exam time has passed today
        elseif ($current_date == $exam_date && $current_time > $end_time) {
            // Exam time has passed today, deactivate it
            $update_sql = "UPDATE tbl_examinations SET status = 'Inactive' WHERE exam_id = '$exam_id'";
            $conn->query($update_sql);
        }
        // Check if exam should be activated (current date and time is within window)
        elseif ($current_date == $exam_date && $current_time >= $start_time && $current_time <= $end_time) {
            // Exam is within time window, activate it
            $update_sql = "UPDATE tbl_examinations SET status = 'Active' WHERE exam_id = '$exam_id'";
            $conn->query($update_sql);
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
            AND date = '$current_date'
            AND start_time <= '$current_time'
            AND end_time >= '$current_time'";
    
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
    
    // Convert date format if needed
    if (strpos($exam_date, '/') !== false) {
        $date_parts = explode('/', $exam_date);
        if (count($date_parts) == 3) {
            $exam_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
        }
    }
    
    $status_info = array(
        'exam_id' => $exam['exam_id'],
        'exam_name' => $exam['exam_name'],
        'class' => $exam['class'],
        'subject' => $exam['subject'],
        'date' => $exam['date'],
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
?>
