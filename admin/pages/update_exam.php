<?php
// Set timezone from config or use default
$timezone = defined('DEFAULT_TIMEZONE') ? DEFAULT_TIMEZONE : 'UTC';
date_default_timezone_set($timezone);
include '../../database/config.php';
include '../../includes/uniques.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>
        alert('Invalid request method.');
        window.location.href='../examinations.php';
        </script>";
    exit();
}

// Validate required fields
$required_fields = ['examid', 'exam', 'duration', 'passmark', 'fmarks', 'date', 'end_exam_date', 'start_time', 'end_time', 'subject', 'class'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        echo "<script>
            alert('Please fill in all required fields.');
            window.location.href='../examinations.php';
            </script>";
        exit();
    }
}

$exam_id = mysqli_real_escape_string($conn, $_POST['examid']);    // this id comes from edit-exam.php page
$exam = ucwords(mysqli_real_escape_string($conn, $_POST['exam']));
$duration = mysqli_real_escape_string($conn, $_POST['duration']);
$passmark = mysqli_real_escape_string($conn, $_POST['passmark']);
$f_marks = mysqli_real_escape_string($conn, $_POST['fmarks']);
$date = mysqli_real_escape_string($conn, $_POST['date']);
$end_exam_date = mysqli_real_escape_string($conn, $_POST['end_exam_date']);
$start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
$end_time = mysqli_real_escape_string($conn, $_POST['end_time']);
$subject = mysqli_real_escape_string($conn, $_POST['subject']);
$class = mysqli_real_escape_string($conn, $_POST['class']);

// Result publication scheduling fields
$result_publish_start_date = isset($_POST['result_publish_start_date']) ? mysqli_real_escape_string($conn, $_POST['result_publish_start_date']) : null;
$result_publish_start_time = isset($_POST['result_publish_start_time']) ? mysqli_real_escape_string($conn, $_POST['result_publish_start_time']) : null;
$result_publish_end_date = isset($_POST['result_publish_end_date']) ? mysqli_real_escape_string($conn, $_POST['result_publish_end_date']) : null;
$result_publish_end_time = isset($_POST['result_publish_end_time']) ? mysqli_real_escape_string($conn, $_POST['result_publish_end_time']) : null;

// Determine result publication status
$result_publish_status = 'Not Published';
if ($result_publish_start_date && $result_publish_end_date) {
    $result_publish_status = 'Scheduled';
}

// Validate time format
if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $start_time) || 
    !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $end_time)) {
    echo "<script>
        alert('Invalid time format. Please use HH:MM:SS format.');
        window.location.href='../edit-exam.php?eid=$exam_id&error=time_format';
        </script>";
    exit();
}

// Validate that end time is after start time
if (strtotime($end_time) <= strtotime($start_time)) {
    echo "<script>
        alert('End time must be after start time.');
        window.location.href='../edit-exam.php?eid=$exam_id&error=time_logic';
        </script>";
    exit();
}

$sql = "SELECT * FROM tbl_examinations WHERE exam_name = '$exam' AND subject = '$subject' AND class = '$class' AND exam_id != '$exam_id'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<script>
        alert('$exam already exists for this subject and class.');
        window.location.href='../edit-exam.php?eid=$exam_id&error=duplicate';
        </script>";
    exit();
} else {

$sql = "UPDATE tbl_examinations SET class = '$class', subject = '$subject', exam_name = '$exam', date = '$date', end_exam_date = '$end_exam_date', start_time = '$start_time', end_time = '$end_time', duration = '$duration', passmark = '$passmark', full_marks = '$f_marks', result_publish_start_date = " . ($result_publish_start_date ? "'$result_publish_start_date'" : "NULL") . ", result_publish_start_time = " . ($result_publish_start_time ? "'$result_publish_start_time'" : "NULL") . ", result_publish_end_date = " . ($result_publish_end_date ? "'$result_publish_end_date'" : "NULL") . ", result_publish_end_time = " . ($result_publish_end_time ? "'$result_publish_end_time'" : "NULL") . ", result_publish_status = '$result_publish_status' WHERE exam_id = '$exam_id'";

if ($conn->query($sql) === TRUE) {
    echo "<script>
        alert('$exam has been successfully updated.');
        window.location.href='../examinations.php?cn=$class&success=updated';
        </script>";
} else {
    $error_message = $conn->error;
    echo "<script>
        alert('Error updating exam: $error_message');
        window.location.href='../edit-exam.php?eid=$exam_id&error=update';
        </script>";
}

}
$conn->close();
?>
