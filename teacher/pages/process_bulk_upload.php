<?php
// Set timezone from config or use default
$timezone = defined('DEFAULT_TIMEZONE') ? DEFAULT_TIMEZONE : 'UTC';
date_default_timezone_set($timezone);
include '../../database/config.php';
include '../../includes/bulk_question_upload.php';
include '../includes/check_user.php';

// Check if user is logged in
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>
        alert('Invalid request method.');
        window.location.href='../bulk_upload_questions.php';
        </script>";
    exit();
}

// Validate required fields
if (!isset($_POST['exam_id']) || empty($_POST['exam_id'])) {
    echo "<script>
        alert('Please select an exam.');
        window.location.href='../bulk_upload_questions.php';
        </script>";
    exit();
}

if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    echo "<script>
        alert('Please select a valid CSV file.');
        window.location.href='../bulk_upload_questions.php';
        </script>";
    exit();
}

$exam_id = mysqli_real_escape_string($conn, $_POST['exam_id']);
$uploaded_by = $_SESSION['myid'];
$user_type = 'teacher';

// Validate exam exists
$exam_sql = "SELECT * FROM tbl_examinations WHERE exam_id = '$exam_id'";
$exam_result = $conn->query($exam_sql);
if ($exam_result->num_rows == 0) {
    echo "<script>
        alert('Selected exam not found.');
        window.location.href='../bulk_upload_questions.php';
        </script>";
    exit();
}

// Validate file type
$file_info = pathinfo($_FILES['csv_file']['name']);
if (strtolower($file_info['extension']) !== 'csv') {
    echo "<script>
        alert('Only CSV files are allowed.');
        window.location.href='../bulk_upload_questions.php';
        </script>";
    exit();
}

// Validate file size (5MB limit)
if ($_FILES['csv_file']['size'] > 5 * 1024 * 1024) {
    echo "<script>
        alert('File size must be less than 5MB.');
        window.location.href='../bulk_upload_questions.php';
        </script>";
    exit();
}

// Create uploads directory if it doesn't exist
$upload_dir = '../../uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Generate unique filename
$timestamp = date('Y-m-d_H-i-s');
$filename = 'bulk_upload_' . $timestamp . '_' . $_FILES['csv_file']['name'];
$file_path = $upload_dir . $filename;

// Move uploaded file
if (!move_uploaded_file($_FILES['csv_file']['tmp_name'], $file_path)) {
    echo "<script>
        alert('Error uploading file. Please try again.');
        window.location.href='../bulk_upload_questions.php';
        </script>";
    exit();
}

// Validate CSV format
$validation_result = validateCSVFormat($file_path);
if (!$validation_result['valid']) {
    // Delete uploaded file
    unlink($file_path);
    
    $error_message = "CSV format validation failed:\n" . implode("\n", $validation_result['errors']);
    echo "<script>
        alert('$error_message');
        window.location.href='../bulk_upload_questions.php';
        </script>";
    exit();
}

// Process the bulk upload
$result = processBulkQuestionUpload($file_path, $exam_id, $uploaded_by, $user_type, $conn);

// Clean up uploaded file
unlink($file_path);

// Prepare response message
$message = $result['message'];
if ($result['successful_uploads'] > 0) {
    $message .= "\\n\\nUpload Summary:\\n";
    $message .= "- Total Questions: " . $result['total_questions'] . "\\n";
    $message .= "- Successfully Uploaded: " . $result['successful_uploads'] . "\\n";
    $message .= "- Failed Uploads: " . $result['failed_uploads'] . "\\n";
    $message .= "- Upload ID: " . $result['upload_id'];
}

if ($result['failed_uploads'] > 0) {
    $message .= "\\n\\nErrors occurred during upload. Check the upload history for details.";
}

// Redirect with result
if ($result['successful_uploads'] > 0) {
    // If questions were successfully uploaded, redirect to view questions
    echo "<script>
        alert('" . addslashes($message) . "');
        window.location.href='../view-questions.php?eid=" . urlencode($exam_id) . "';
        </script>";
} else {
    // If no questions were uploaded, stay on bulk upload page
    echo "<script>
        alert('" . addslashes($message) . "');
        window.location.href='../bulk_upload_questions.php';
        </script>";
}
?> 