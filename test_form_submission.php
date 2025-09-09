<?php
/**
 * Test Form Submission
 * This script tests the exam form submission functionality
 */

// Include database configuration
require_once 'database/config.php';

echo "=== Form Submission Test ===\n";

// Test 1: Check if the add_exam.php file exists and is accessible
$add_exam_file = 'admin/pages/add_exam.php';
if (file_exists($add_exam_file)) {
    echo "✓ add_exam.php file exists\n";
} else {
    echo "✗ add_exam.php file not found\n";
    exit(1);
}

// Test 2: Check database connection
if ($conn) {
    echo "✓ Database connection successful\n";
} else {
    echo "✗ Database connection failed\n";
    exit(1);
}

// Test 3: Check if required tables exist
$tables = ['tbl_examinations', 'tbl_subjects', 'tbl_classes'];
foreach ($tables as $table) {
    $sql = "SHOW TABLES LIKE '$table'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        echo "✓ Table '$table' exists\n";
    } else {
        echo "✗ Table '$table' not found\n";
    }
}

// Test 4: Check if the new time fields exist in tbl_examinations
$sql = "SHOW COLUMNS FROM tbl_examinations LIKE 'start_time'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    echo "✓ start_time field exists\n";
} else {
    echo "✗ start_time field not found - run database/add_time_fields.sql\n";
}

$sql = "SHOW COLUMNS FROM tbl_examinations LIKE 'end_time'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    echo "✓ end_time field exists\n";
} else {
    echo "✗ end_time field not found - run database/add_time_fields.sql\n";
}

// Test 5: Check if there are subjects and classes available
$sql = "SELECT COUNT(*) as count FROM tbl_subjects";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✓ Subjects available: " . $row['count'] . "\n";
} else {
    echo "✗ No subjects found in database\n";
}

$sql = "SELECT COUNT(*) as count FROM tbl_classes";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✓ Classes available: " . $row['count'] . "\n";
} else {
    echo "✗ No classes found in database\n";
}

// Test 6: Check form action URL
$examinations_file = 'admin/examinations.php';
if (file_exists($examinations_file)) {
    echo "✓ examinations.php file exists\n";
    
    // Check if form action is correct
    $content = file_get_contents($examinations_file);
    if (strpos($content, 'action="pages/add_exam.php"') !== false) {
        echo "✓ Form action is correctly set\n";
    } else {
        echo "✗ Form action may be incorrect\n";
    }
} else {
    echo "✗ examinations.php file not found\n";
}

echo "\n=== Test Summary ===\n";
echo "If all tests passed, the form should work correctly.\n";
echo "If any tests failed, please fix the issues before using the form.\n";

$conn->close();
?> 