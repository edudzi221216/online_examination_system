<?php
/**
 * Cron Job Script - Update Exam Status
 * This script should be run periodically (e.g., every 5 minutes) to automatically
 * update exam availability based on date and time.
 * 
 * Usage: php update_exam_status.php
 * 
 * You can set up a cron job to run this script every 5 minutes.
 * See README.md for cron setup instructions.
 */

// Include database configuration
require_once '../database/config.php';

// Include exam scheduler functions
require_once '../includes/exam_scheduler.php';

// Set timezone
// Set timezone from config or use default
$timezone = defined('DEFAULT_TIMEZONE') ? DEFAULT_TIMEZONE : 'UTC';
date_default_timezone_set($timezone);

// Log file for tracking updates
$log_file = '../logs/exam_status_updates.log';

// Create logs directory if it doesn't exist
if (!file_exists('../logs')) {
    mkdir('../logs', 0755, true);
}

// Function to log messages
function logMessage($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

// Start logging
logMessage("Starting exam status update process");

try {
    // Update exam statuses
    updateExamStatus($conn);
    
    // Get current date and time for logging
    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');
    
    // Log the update
    logMessage("Exam status update completed at $current_date $current_time");
    
    // Get count of active exams for reporting
    $sql = "SELECT COUNT(*) as active_count FROM tbl_examinations WHERE status = 'Active'";
    $result = $conn->query($sql);
    $active_count = 0;
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $active_count = $row['active_count'];
    }
    
    logMessage("Currently active exams: $active_count");
    
    // Get exams that were recently activated or deactivated
    $sql = "SELECT exam_id, exam_name, class, subject, date, start_time, end_time, status 
            FROM tbl_examinations 
            WHERE date = '$current_date' 
            AND ((start_time <= '$current_time' AND end_time >= '$current_time' AND status = 'Active')
                 OR (end_time < '$current_time' AND status = 'Inactive'))";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        logMessage("Recent exam status changes:");
        while ($row = $result->fetch_assoc()) {
            $status_text = $row['status'] == 'Active' ? 'ACTIVATED' : 'DEACTIVATED';
            logMessage("- {$row['exam_name']} ({$row['subject']}) for {$row['class']}: $status_text");
        }
    }
    
    echo "Exam status update completed successfully.\n";
    echo "Active exams: $active_count\n";
    echo "Check log file for details: $log_file\n";
    
} catch (Exception $e) {
    $error_message = "Error updating exam status: " . $e->getMessage();
    logMessage($error_message);
    echo $error_message . "\n";
    exit(1);
} finally {
    // Close database connection
    if (isset($conn)) {
        $conn->close();
    }
}

logMessage("Exam status update process finished");
?> 