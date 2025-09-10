<?php
/**
 * Enhanced Exam Scheduler Cron Job
 * This script automatically manages exam availability and result publication
 * based on date and time schedules.
 * 
 * Usage: php enhanced_exam_scheduler.php
 * 
 * You can set up a cron job to run this script every 5 minutes:
 * 0,5,10,15,20,25,30,35,40,45,50,55 * * * * php /path/to/your/project/cron/enhanced_exam_scheduler.php
 */

// Get the directory where this script is located
$script_dir = dirname(__FILE__);
$project_root = dirname($script_dir);

// Include database configuration
require_once $project_root.'/database/config.php';

// Include enhanced exam scheduler functions
require_once $project_root.'/includes/enhanced_exam_scheduler.php';

// Set timezone
// Set timezone from config or use default
$timezone = defined('DEFAULT_TIMEZONE') ? DEFAULT_TIMEZONE : 'UTC';
date_default_timezone_set($timezone);

// Log file for tracking updates
$log_file = $project_root.'/logs/enhanced_exam_scheduler.log';

// Create logs directory if it doesn't exist
if (!file_exists($project_root.'/logs')) {
    mkdir($project_root.'/logs', 0755, true);
}

// Function to log messages
function logMessage($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

// Start logging
logMessage("Starting enhanced exam scheduler process");

try {
    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');
    
    logMessage("Current date/time: $current_date $current_time");
    
    // Update exam statuses
    logMessage("Updating exam availability status...");
    updateExamStatus($conn);
    
    // Update result publication status
    logMessage("Updating result publication status...");
    updateResultPublicationStatus($conn);
    
    // Get statistics for reporting
    $active_exams_sql = "SELECT COUNT(*) as active_count FROM tbl_examinations WHERE status = 'Active'";
    $active_result = $conn->query($active_exams_sql);
    $active_count = 0;
    if ($active_result && $active_result->num_rows > 0) {
        $row = $active_result->fetch_assoc();
        $active_count = $row['active_count'];
    }
    
    $published_results_sql = "SELECT COUNT(*) as published_count FROM tbl_examinations WHERE result_publish_status = 'Published'";
    $published_result = $conn->query($published_results_sql);
    $published_count = 0;
    if ($published_result && $published_result->num_rows > 0) {
        $row = $published_result->fetch_assoc();
        $published_count = $row['published_count'];
    }
    
    $scheduled_results_sql = "SELECT COUNT(*) as scheduled_count FROM tbl_examinations WHERE result_publish_status = 'Scheduled'";
    $scheduled_result = $conn->query($scheduled_results_sql);
    $scheduled_count = 0;
    if ($scheduled_result && $scheduled_result->num_rows > 0) {
        $row = $scheduled_result->fetch_assoc();
        $scheduled_count = $row['scheduled_count'];
    }
    
    logMessage("Statistics:");
    logMessage("- Active exams: $active_count");
    logMessage("- Published results: $published_count");
    logMessage("- Scheduled results: $scheduled_count");
    
    // Get recent changes for detailed logging
    $recent_exam_changes_sql = "SELECT exam_id, exam_name, class, subject, date, start_time, end_time, status 
                                FROM tbl_examinations 
                                WHERE date = '$current_date' 
                                AND ((start_time <= '$current_time' AND end_time >= '$current_time' AND status = 'Active')
                                     OR (end_time < '$current_time' AND status = 'Inactive'))";
    
    $recent_exam_result = $conn->query($recent_exam_changes_sql);
    if ($recent_exam_result && $recent_exam_result->num_rows > 0) {
        logMessage("Recent exam status changes:");
        while ($row = $recent_exam_result->fetch_assoc()) {
            $status_text = $row['status'] == 'Active' ? 'ACTIVATED' : 'DEACTIVATED';
            logMessage("- {$row['exam_name']} ({$row['subject']}) for {$row['class']}: $status_text");
        }
    }
    
    // Get recent result publication changes
    $recent_result_changes_sql = "SELECT exam_id, exam_name, class, subject, result_publish_status, 
                                         result_publish_start_date, result_publish_end_date
                                  FROM tbl_examinations 
                                  WHERE result_publish_status IN ('Published', 'Not Published')
                                  AND (result_publish_start_date = '$current_date' OR result_publish_end_date = '$current_date')";
    
    $recent_result_result = $conn->query($recent_result_changes_sql);
    if ($recent_result_result && $recent_result_result->num_rows > 0) {
        logMessage("Recent result publication changes:");
        while ($row = $recent_result_result->fetch_assoc()) {
            $status_text = $row['result_publish_status'] == 'Published' ? 'PUBLISHED' : 'UNPUBLISHED';
            logMessage("- {$row['exam_name']} ({$row['subject']}) for {$row['class']}: $status_text");
        }
    }
    
    // Check for upcoming exams and results
    $upcoming_exams_sql = "SELECT exam_id, exam_name, class, subject, date, start_time 
                           FROM tbl_examinations 
                           WHERE date > '$current_date' 
                           OR (date = '$current_date' AND start_time > '$current_time')
                           ORDER BY date ASC, start_time ASC 
                           LIMIT 5";
    
    $upcoming_result = $conn->query($upcoming_exams_sql);
    if ($upcoming_result && $upcoming_result->num_rows > 0) {
        logMessage("Upcoming exams (next 5):");
        while ($row = $upcoming_result->fetch_assoc()) {
            logMessage("- {$row['exam_name']} ({$row['subject']}) for {$row['class']} on {$row['date']} at {$row['start_time']}");
        }
    }
    
    // Check for upcoming result publications
    $upcoming_results_sql = "SELECT exam_id, exam_name, class, subject, result_publish_start_date, result_publish_start_time 
                             FROM tbl_examinations 
                             WHERE result_publish_status = 'Scheduled'
                             AND (result_publish_start_date > '$current_date' 
                                  OR (result_publish_start_date = '$current_date' AND result_publish_start_time > '$current_time'))
                             ORDER BY result_publish_start_date ASC, result_publish_start_time ASC 
                             LIMIT 5";
    
    $upcoming_results_result = $conn->query($upcoming_results_sql);
    if ($upcoming_results_result && $upcoming_results_result->num_rows > 0) {
        logMessage("Upcoming result publications (next 5):");
        while ($row = $upcoming_results_result->fetch_assoc()) {
            logMessage("- {$row['exam_name']} ({$row['subject']}) for {$row['class']} on {$row['result_publish_start_date']} at {$row['result_publish_start_time']}");
        }
    }
    
    logMessage("Enhanced exam scheduler process completed successfully");
    
    echo "Enhanced exam scheduler completed successfully.\n";
    echo "Active exams: $active_count\n";
    echo "Published results: $published_count\n";
    echo "Scheduled results: $scheduled_count\n";
    echo "Check log file for details: $log_file\n";
    
} catch (Exception $e) {
    $error_message = "Error in enhanced exam scheduler: " . $e->getMessage();
    logMessage($error_message);
    echo $error_message . "\n";
    exit(1);
} finally {
    // Close database connection
    if (isset($conn)) {
        $conn->close();
    }
}
?> 