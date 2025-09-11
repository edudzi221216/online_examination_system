<?php
include 'includes/check_user.php';
include '../database/config.php';


/*$exid = mysqli_real_escape_string($conn, $_GET['id']);*/
$exam_id = $_GET['eid'];
$status = $_GET['status'];

$status_text = $status == 1 ? 'Result Published' : 'Result Not Published';

$sql = "UPDATE tbl_assessment_records SET rstatus='$status_text' WHERE exam_id='$exam_id'";
$back = $_SERVER["HTTP_REFERER"];


if ($conn->query($sql) === TRUE) {
    echo "<script>
    alert('Exam $exam_id results has been ".($status == 1 ? "Published" : "Unpublished").".');
    window.location.href='$back';
    </script>";

} else {
    echo "<script>
    alert('Something went wrong.');
    window.location.href='$back';
    </script>";
}

$conn->close();
?>