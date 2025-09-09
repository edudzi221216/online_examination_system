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
$attempts = mysqli_real_escape_string($conn, $_POST['attempts']);
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

$sql = "SELECT * FROM tbl_examinations WHERE exam_name = '$exam' AND subject = '$subject' AND class = '$class' AND exam_id != '$exam_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

    while($row = $result->fetch_assoc()) {
echo "<script>
    alert('$exam' + ' already exist.');
    window.location.href='../classexamination.php?DUPLICATE RECORD FOUND';
    </script>";
    }
} else {

$sql = "UPDATE tbl_examinations SET class = '$class', subject = '$subject', exam_name = '$exam', date = '$date', end_exam_date = '$end_exam_date', start_time = '$start_time', end_time = '$end_time', duration = '$duration', passmark = '$passmark', full_marks = '$f_marks', re_exam = '$attempts', result_publish_start_date = " . ($result_publish_start_date ? "'$result_publish_start_date'" : "NULL") . ", result_publish_start_time = " . ($result_publish_start_time ? "'$result_publish_start_time'" : "NULL") . ", result_publish_end_date = " . ($result_publish_end_date ? "'$result_publish_end_date'" : "NULL") . ", result_publish_end_time = " . ($result_publish_end_time ? "'$result_publish_end_time'" : "NULL") . ", result_publish_status = '$result_publish_status' WHERE exam_id = '$exam_id'";

if ($conn->query($sql) === TRUE) {
echo "<script>
    alert('$exam' + ' is Successfully Updated.');
    window.location.href='../examinations.php?cn='+'$class';
    </script>";
} else {
echo "<script>
    alert('Something went wrong.');
    window.location.href='../edit-exam.php?Could Not Apply Settings!!&eid=$exam_id';
    </script>";
}


}
$conn->close();
?>
