<!--ASSESSMENT page > SUBMIT_ASSESSMENT page-->
<?php
error_reporting(0);
$total_questions = $_POST['tq']; // this is comes from assessment page
$qmarks= $_POST['qm'];
$starting_mark = 1;
$mytotal_marks = 0;
$exam_id = $_POST['eid'];
$record = $_POST['ri'];

// store user's answers for the exam
$answers = $_POST["user_answers"] ? json_decode($_POST["user_answers"], true) : "";
$answers = is_array($answers) ? json_encode($answers, JSON_UNESCAPED_SLASHES) : null;

while ($starting_mark <= $total_questions) { // lets say starting marks is 1 and total_question is 3
if (strtoupper(base64_decode($_POST['ran'.$starting_mark.''])) == strtoupper($_POST['an'.$starting_mark.''])) {
   // ran and an comes from the assessment page
   // 'ran' stands for right answer and 'an' stands for only answer. if this is matched then this execute
$mytotal_marks = $mytotal_marks + $qmarks;	// if matched, then total_marks incremented
}else{
	
}
$starting_mark++; // incremented
}

$passmark = $_POST['pm'];  // (Given by the admin)you have to put the value, when the examination will create. 

if ($mytotal_marks >= $passmark) {   
$status = "PASS";	
}else{
$status = "FAIL";	
}

session_start();
$_SESSION['record_id'] = $record;
include '../../database/config.php';

$stmt = $conn->prepare("UPDATE tbl_assessment_records SET score = ?, status = ?, user_answers = ? WHERE record_id = ?");
$stmt->bind_param("isss", $mytotal_marks, $status, $answers, $record);

if ($stmt->execute()) {

	
   header("location:../submit_su.php");
} else {
   header("location:../submit_su.php");
}

$conn->close();
?>
