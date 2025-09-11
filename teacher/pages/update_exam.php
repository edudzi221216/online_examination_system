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
$attempts = mysqli_real_escape_string($conn, $_POST['attempts'] ?? 0);
$date = mysqli_real_escape_string($conn, $_POST['date']);
$end_exam_date = mysqli_real_escape_string($conn, $_POST['end_exam_date']);
$start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
$end_time = mysqli_real_escape_string($conn, $_POST['end_time']);
$subject = mysqli_real_escape_string($conn, $_POST['subject']);
$class = mysqli_real_escape_string($conn, $_POST['class']);

// convert dates
$date = date('Y-m-d', strtotime($date));
$end_exam_date = date('Y-m-d', strtotime($end_exam_date));

$start_date_time = "$date $start_time";
$end_date_time = "$end_exam_date $end_time";
$time_error = "";

// validate exam times
if(strtotime($end_date_time) <= strtotime($start_time)){
    $time_error = "End date cannot be lesser than the start date";
}else if(date_diff(new DateTime($end_date_time), new DateTime($start_date_time)) < $duration){
    $time_error = "Your duration is not within the time limit of the exam";
}

if(!empty($time_error)){
    echo "<script>
        alert('$time_error');
        window.location.href='$back';
        </script>";
    exit();
}

// validate mark values
$mark_error = "";
if(empty($f_marks) || $f_marks < 0){
    $mark_error = "Your full mark should be greater than zero";
}elseif(empty($passmark) || $passmark < 0){
    $mark_error = "Your passmark mark should be greater than zero";
}elseif($f_marks < $passmark){
    $mark_error = "Your full mark cannot be less than the pass mark";
}

if(!empty($mark_error)){
    echo "<script>
        alert('$mark_error');
        window.location.href='$back';
        </script>";
    exit();
}

// Result publication scheduling fields
$result_publish_start_date = isset($_POST['result_publish_start_date']) ? mysqli_real_escape_string($conn, $_POST['result_publish_start_date']) : null;
$result_publish_start_time = isset($_POST['result_publish_start_time']) ? mysqli_real_escape_string($conn, $_POST['result_publish_start_time']) : null;
$result_publish_end_date = isset($_POST['result_publish_end_date']) ? mysqli_real_escape_string($conn, $_POST['result_publish_end_date']) : null;
$result_publish_end_time = isset($_POST['result_publish_end_time']) ? mysqli_real_escape_string($conn, $_POST['result_publish_end_time']) : null;

// convert dates
if ($result_publish_start_date) {
    $result_publish_start_date = date('Y-m-d', strtotime($result_publish_start_date));
} 

if ($result_publish_end_date) {
    $result_publish_end_date = date('Y-m-d', strtotime($result_publish_end_date));
} 

// validate publish dates
if($result_publish_start_date && empty($result_publish_end_date)){
    $time_error = "Provide a publishing end date";
}elseif($result_publish_start_date && empty($result_publish_start_time)){
    $time_error = "Provide the publishing start time";
}elseif($result_publish_end_date && empty($result_publish_end_time)){
    $time_error = "Provide the publishing end time";
}

if(empty($time_error) && $result_publish_start_date){
    $publishing_date_time = "$result_publish_start_date $result_publish_start_time";
    $unpublishing_date_time = "$result_publish_end_date $result_publish_end_time";

    if(strtotime($unpublishing_date_time) < strtotime($publishing_date_time)){
        $time_error = "Your publishing end date cannot be less than the start date";
    }
}

if(!empty($time_error)){
    echo "<script>
        alert('$time_error');
        window.location.href='$back';
        </script>";
    exit();
}

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

$sql = "UPDATE tbl_examinations 
        SET class = '$class', subject = '$subject', exam_name = '$exam', date = '$date', 
            end_exam_date = $end_exam_date, start_time = '$start_time', end_time = '$end_time', duration = '$duration', passmark = 
            '$passmark', full_marks = '$f_marks', re_exam = '$attempts', result_publish_start_date = " . ($result_publish_start_date ? "'$result_publish_start_date'" : "NULL") . ", result_publish_start_time = " . ($result_publish_start_time ? "'$result_publish_start_time'" : "NULL") . ", result_publish_end_date = " . ($result_publish_end_date ? "'$result_publish_end_date'" : "NULL") . ", result_publish_end_time = " . ($result_publish_end_time ? "'$result_publish_end_time'" : "NULL") . ", result_publish_status = '$result_publish_status' WHERE exam_id = '$exam_id'";
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
