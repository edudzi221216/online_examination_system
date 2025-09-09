<?php
session_start();
include '../database/config.php';

echo "<h2>Session Debug Information</h2>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "All Session Variables:\n";
print_r($_SESSION);

echo "\n\nDatabase Connection Test:\n";
if ($conn) {
    echo "Database connected successfully\n";
    
    // Check if teacher ID exists in session
    if (isset($_SESSION['myid'])) {
        $teacher_id = $_SESSION['myid'];
        echo "Teacher ID from session: $teacher_id\n";
        
        // Check if this teacher exists in database
        $sql = "SELECT * FROM tbl_teacher WHERE teacher_id = '$teacher_id'";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $teacher = $result->fetch_assoc();
            echo "Teacher found in database: " . $teacher['first_name'] . " " . $teacher['last_name'] . "\n";
        } else {
            echo "Teacher NOT found in database with ID: $teacher_id\n";
        }
        
        // Check exams created by this teacher
        $sql = "SELECT COUNT(*) as count FROM tbl_examinations WHERE created_by = '$teacher_id' AND created_by_type = 'teacher'";
        $result = $conn->query($sql);
        if ($result) {
            $row = $result->fetch_assoc();
            echo "Exams created by this teacher: " . $row['count'] . "\n";
        }
        
        // Show all exams for debugging
        $sql = "SELECT exam_id, exam_name, created_by, created_by_type FROM tbl_examinations WHERE class = 'Class 10' ORDER BY created_date DESC LIMIT 10";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            echo "\nRecent exams in Class 10:\n";
            while ($row = $result->fetch_assoc()) {
                echo "- " . $row['exam_name'] . " (Created by: " . $row['created_by'] . " - " . $row['created_by_type'] . ")\n";
            }
        }
        
    } else {
        echo "Teacher ID NOT found in session\n";
    }
} else {
    echo "Database connection failed\n";
}

echo "</pre>";
?>
