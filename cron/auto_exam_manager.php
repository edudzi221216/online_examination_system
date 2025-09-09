<?php
/**
 * Auto Exam Manager - Cron Job Script
 * This script automatically starts and stops exams based on their scheduled times
 * Run this script every minute via cron job: * * * * * php /path/to/auto_exam_manager.php
 */

// Set timezone
// Set timezone from config or use default
$timezone = defined('DEFAULT_TIMEZONE') ? DEFAULT_TIMEZONE : 'UTC';
date_default_timezone_set($timezone);

// Get the directory where this script is located
$script_dir = dirname(__FILE__);
$project_root = dirname($script_dir);

// Include database configuration
include $project_root . '/database/config.php';

// Include exam scheduler functions
include $project_root . '/includes/enhanced_exam_scheduler.php';

// Log function
function logMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logFile = '../logs/exam_manager.log';
    
    // Create logs directory if it doesn't exist
    if (!is_dir('../logs')) {
        mkdir('../logs', 0755, true);
    }
    
    $logEntry = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    
    // Also output to console if running from command line
    if (php_sapi_name() === 'cli') {
        echo $logEntry;
    }
}

try {
    logMessage("Starting auto exam manager...");
    
    // Update exam status
    updateExamStatus($conn);
    
    logMessage("Exam status update completed successfully");
    
    // Get current exam counts for reporting
    $active_count = $conn->query("SELECT COUNT(*) as count FROM tbl_examinations WHERE status = 'Active'")->fetch_assoc()['count'];
    $inactive_count = $conn->query("SELECT COUNT(*) as count FROM tbl_examinations WHERE status = 'Inactive'")->fetch_assoc()['count'];
    
    logMessage("Current exam status - Active: $active_count, Inactive: $inactive_count");
    
} catch (Exception $e) {
    logMessage("Error in auto exam manager: " . $e->getMessage());
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

logMessage("Auto exam manager completed");
?> 