<?php
session_start();
include '../database/config.php';

echo "<h2>Exam Creation Test</h2>";

// Check if teacher is logged in
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['role'] !== 'teacher') {
    echo "<p style='color: red;'>Error: Teacher not logged in</p>";
    echo "<p>Session data:</p>";
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
    exit();
}

$teacher_id = $_SESSION['myid'];
echo "<p><strong>Teacher ID:</strong> $teacher_id</p>";

// Test exam creation
$test_exam_id = 'TEST' . time();
$test_exam_name = 'Test Exam ' . date('Y-m-d H:i:s');
$test_class = 'Test Class';
$test_subject = 'Test Subject';
$test_date = date('m/d/Y');
$test_start_time = '09:00:00';
$test_end_time = '10:00:00';
$test_duration = 60;
$test_passmark = 50;
$test_f_marks = 100;

echo "<p><strong>Test Exam Details:</strong></p>";
echo "<ul>";
echo "<li>Exam ID: $test_exam_id</li>";
echo "<li>Exam Name: $test_exam_name</li>";
echo "<li>Class: $test_class</li>";
echo "<li>Subject: $test_subject</li>";
echo "<li>Date: $test_date</li>";
echo "<li>Start Time: $test_start_time</li>";
echo "<li>End Time: $test_end_time</li>";
echo "<li>Duration: $test_duration minutes</li>";
echo "<li>Pass Mark: $test_passmark</li>";
echo "<li>Full Marks: $test_f_marks</li>";
echo "</ul>";

// Check if test exam already exists
$check_sql = "SELECT * FROM tbl_examinations WHERE exam_id = '$test_exam_id'";
$check_result = $conn->query($check_sql);

if ($check_result && $check_result->num_rows > 0) {
    echo "<p style='color: orange;'>Test exam already exists, skipping creation.</p>";
} else {
    // Create test exam
    $insert_sql = "INSERT INTO tbl_examinations (
        exam_id, class, subject, exam_name, date, start_time, end_time, 
        duration, passmark, full_marks, re_exam, end_exam_date, status, 
        result_publish_status, created_by, created_by_type
    ) VALUES (
        '$test_exam_id', '$test_class', '$test_subject', '$test_exam_name', 
        '$test_date', '$test_start_time', '$test_end_time', $test_duration, 
        $test_passmark, $test_f_marks, 0, '$test_date', 'Inactive', 
        'Not Published', '$teacher_id', 'teacher'
    )";
    
    if ($conn->query($insert_sql) === TRUE) {
        echo "<p style='color: green;'>Test exam created successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error creating test exam: " . $conn->error . "</p>";
        exit();
    }
}

// Verify the exam was created correctly
$verify_sql = "SELECT * FROM tbl_examinations WHERE exam_id = '$test_exam_id'";
$verify_result = $conn->query($verify_sql);

if ($verify_result && $verify_result->num_rows > 0) {
    $exam = $verify_result->fetch_assoc();
    echo "<p><strong>Verification - Exam Details:</strong></p>";
    echo "<ul>";
    echo "<li>Exam ID: " . $exam['exam_id'] . "</li>";
    echo "<li>Exam Name: " . $exam['exam_name'] . "</li>";
    echo "<li>Class: " . $exam['class'] . "</li>";
    echo "<li>Subject: " . $exam['subject'] . "</li>";
    echo "<li>Created By: " . $exam['created_by'] . "</li>";
    echo "<li>Created By Type: " . $exam['created_by_type'] . "</li>";
    echo "<li>Status: " . $exam['status'] . "</li>";
    echo "</ul>";
    
    // Check if we can find this exam with the teacher filter
    $teacher_filter_sql = "SELECT * FROM tbl_examinations WHERE created_by='$teacher_id' AND created_by_type='teacher'";
    $teacher_filter_result = $conn->query($teacher_filter_sql);
    
    if ($teacher_filter_result && $teacher_filter_result->num_rows > 0) {
        echo "<p style='color: green;'>✓ Exam found with teacher filter! Count: " . $teacher_filter_result->num_rows . "</p>";
        
        echo "<p><strong>All exams by this teacher:</strong></p>";
        echo "<ul>";
        while ($row = $teacher_filter_result->fetch_assoc()) {
            echo "<li>" . $row['exam_name'] . " (Class: " . $row['class'] . ", Subject: " . $row['subject'] . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>✗ Exam NOT found with teacher filter!</p>";
    }
    
    // Check if we can find this exam with class filter
    $class_filter_sql = "SELECT * FROM tbl_examinations WHERE class='$test_class' AND created_by='$teacher_id' AND created_by_type='teacher'";
    $class_filter_result = $conn->query($class_filter_sql);
    
    if ($class_filter_result && $class_filter_result->num_rows > 0) {
        echo "<p style='color: green;'>✓ Exam found with class filter! Count: " . $class_filter_result->num_rows . "</p>";
    } else {
        echo "<p style='color: red;'>✗ Exam NOT found with class filter!</p>";
    }
    
} else {
    echo "<p style='color: red;'>Error: Could not verify exam creation!</p>";
}

// Clean up test exam
$cleanup_sql = "DELETE FROM tbl_examinations WHERE exam_id = '$test_exam_id'";
if ($conn->query($cleanup_sql) === TRUE) {
    echo "<p style='color: blue;'>Test exam cleaned up successfully.</p>";
} else {
    echo "<p style='color: orange;'>Warning: Could not clean up test exam: " . $conn->error . "</p>";
}

$conn->close();
echo "<p><a href='examinations.php'>← Back to Examinations</a></p>";
?>
