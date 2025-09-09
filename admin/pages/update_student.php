<?php
$student_id = $_POST['student_id'];	
include '../../database/config.php';

$fname = ucwords(mysqli_real_escape_string($conn, $_POST['fname']));
$lname = ucwords(mysqli_real_escape_string($conn, $_POST['lname']));
$email = mysqli_real_escape_string($conn, $_POST['email']);
//$password = mysqli_real_escape_string($conn, $_POST['password']);
$ay = mysqli_real_escape_string($conn, $_POST['ay']);
$password = md5($_POST['password']);
$gender = mysqli_real_escape_string($conn, $_POST['gender']);

//	basically this query checks whether the existing data is same or not.
	// this query update the new data from edit-student.php page
$sql = "UPDATE tbl_users SET first_name = '$fname', last_name = '$lname',login='$password', gender = '$gender', ay = '$ay' WHERE user_id='$student_id'";

if ($conn->query($sql) === TRUE) {
  /*header("location:../edit-student.php?STUDENT UPDATED!!!&sid=$student_id");*/
  echo "<script>
    alert('Student updated successfully. STUDENT ID: '+'$student_id');
    window.location.href='../vstudents.php?cn='+'$class';
    </script>";
} else {
  echo "<script>
    alert('Could not update student.');
    window.location.href='../edit-student.php?Could Not Apply Settings!!!&sid=$student_id';
    </script>";
}




$conn->close();
?>