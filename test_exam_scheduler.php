<?php
/**
 * Test Script for Exam Scheduler
 * This script tests the exam scheduler functionality
 */

// Include database configuration
require_once 'database/config.php';

// Include exam scheduler functions
require_once 'includes/exam_scheduler.php';

// Set timezone
// Set timezone from config or use default
$timezone = defined('DEFAULT_TIMEZONE') ? DEFAULT_TIMEZONE : 'UTC';
date_default_timezone_set($timezone);

echo "=== Exam Scheduler Test ===\n";
echo "Current Date: " . date('Y-m-d') . "\n";
echo "Current Time: " . date('H:i:s') . "\n\n";

try {
    // Test 1: Update exam statuses
    echo "1. Testing exam status update...\n";
    updateExamStatus($conn);
    echo "   ✓ Exam status update completed\n\n";
    
    // Test 2: Get active exams count
    echo "2. Testing active exams count...\n";
    $sql = "SELECT COUNT(*) as active_count FROM tbl_examinations WHERE status = 'Active'";
    $result = $conn->query($sql);
    $active_count = 0;
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $active_count = $row['active_count'];
    }
    echo "   ✓ Active exams: $active_count\n\n";
    
    // Test 3: Get all exams with their status
    echo "3. Testing exam status check...\n";
    $sql = "SELECT exam_id, exam_name, class, subject, date, start_time, end_time, status FROM tbl_examinations LIMIT 5";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo "   Sample exams:\n";
        while ($row = $result->fetch_assoc()) {
            $availability = checkExamAvailability($row['exam_id'], $conn);
            $status_icon = $availability['available'] ? '✓' : '✗';
            echo "   $status_icon {$row['exam_name']} ({$row['subject']}) - {$row['date']} {$row['start_time']}-{$row['end_time']} - {$availability['message']}\n";
        }
    } else {
        echo "   No exams found in database\n";
    }
    echo "\n";
    
    // Test 4: Test formatting functions
    echo "4. Testing formatting functions...\n";
    $test_time = "14:30:00";
    $test_date = "07/08/2023";
    echo "   Time formatting: $test_time → " . formatTime($test_time) . "\n";
    echo "   Date formatting: $test_date → " . formatDate($test_date) . "\n\n";
    
    // Test 5: Test upcoming exams (if any class exists)
    echo "5. Testing upcoming exams...\n";
    $sql = "SELECT DISTINCT class FROM tbl_examinations LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $class = $row['class'];
        $upcoming_exams = getUpcomingExams($class, $conn);
        echo "   Upcoming exams for $class: " . count($upcoming_exams) . "\n";
        
        foreach ($upcoming_exams as $exam) {
            echo "   - {$exam['exam_name']} ({$exam['subject']}) on {$exam['date']}\n";
        }
    } else {
        echo "   No classes found in database\n";
    }
    echo "\n";
    
    echo "=== Test Completed Successfully ===\n";
    
} catch (Exception $e) {
    echo "Error during testing: " . $e->getMessage() . "\n";
    exit(1);
} finally {
    // Close database connection
    if (isset($conn)) {
        $conn->close();
    }
}
?> 