<?php
// Set timezone from config or use default
$timezone = defined('DEFAULT_TIMEZONE') ? DEFAULT_TIMEZONE : 'UTC';
date_default_timezone_set($timezone);
include '../../database/config.php';
include '../../includes/bulk_question_upload.php';
include '../includes/check_user.php';

// Check if user is logged in
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit();
}

// Get exam ID if provided
$exam_id = isset($_GET['exam_id']) ? mysqli_real_escape_string($conn, $_GET['exam_id']) : null;

// Generate CSV template
$csv_content = generateCSVTemplate($exam_id, $conn);

if (!$csv_content) {
    // Generate default template if no exam specified
    $csv_content = "Question Type,Question,Marks,Correct Answer,Option A,Option B,Option C,Option D\n";
    $csv_content .= "MC,What is the capital of France?,20,A,Paris,London,Berlin,Madrid\n";
    $csv_content .= "MC,Which programming language is this system built with?,15,B,PHP,Java,Python,C++\n";
    $csv_content .= "MC,What is the largest planet in our solar system?,10,C,Earth,Mars,Jupiter,Saturn\n";
    $csv_content .= "FB,Complete the sentence: The sun _____ in the east.,10,rises\n";
    $csv_content .= "FB,The chemical symbol for gold is _____.,10,Au\n";
    $csv_content .= "TF,The Earth is round.,10,TRUE\n";
    $csv_content .= "TF,Water boils at 100 degrees Celsius at sea level.,10,TRUE\n";
    $csv_content .= "TF,The capital of Japan is Beijing.,10,FALSE\n";
}

// Set headers for file download
$filename = 'question_upload_template.csv';
if ($exam_id) {
    $exam_sql = "SELECT exam_name FROM tbl_examinations WHERE exam_id = '$exam_id'";
    $exam_result = $conn->query($exam_sql);
    if ($exam_result->num_rows > 0) {
        $exam = $exam_result->fetch_assoc();
        $filename = $exam['exam_name'] . '_template.csv';
    }
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

// Output CSV content
echo $csv_content;
exit();
?> 