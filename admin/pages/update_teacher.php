<?php
$teacher_id = $_POST['teacher_id'];	// this id is fetched from teacher.php page
include '../../database/config.php';
//	fetched from teacher.php page & stored into variable.
$fname = ucwords(mysqli_real_escape_string($conn, $_POST['fname']));
$lname = ucwords(mysqli_real_escape_string($conn, $_POST['lname']));
$email = mysqli_real_escape_string($conn, $_POST['email']);
$gender = mysqli_real_escape_string($conn, $_POST['gender']);
$password = md5($_POST['password']);
$contact = mysqli_real_escape_string($conn, $_POST['contact']);
$teacher_id = $_POST['teacher_id'] ;

//	basically this query checks whether the existing data is same or not.
$sql = "SELECT * FROM tbl_teacher WHERE email = '$email' ";
$result = $conn->query($sql);
/*
if ($result->num_rows > 0) {

    while($row = $result->fetch_assoc()) {
    $tem = $row['email'];	
	if ($tem == $email) {		// if the email is same
	 echo "<script>
	 alert('Teacher with Email ID ('+ '$tem' + ') already exist.');
	 window.location.href='../edit-teacher.php?Duplicate Email ID Found!!&tid=$teacher_id';
	 </script>";	
	}
	
    }
} else { */
	// this query update the new data from edit-teacher.php page
$sql = "UPDATE tbl_teacher SET first_name = '$fname', last_name = '$lname', gender = '$gender',  email = '$email', login ='$password', contact='$contact'  WHERE teacher_id='$teacher_id'";

$from = $_SERVER["HTTP_REFERER"];
if ($conn->query($sql) === TRUE) {
  echo "<script>
    alert('Teacher updated successfully. TEACHER ID: '+'$teacher_id');
    window.location.href='$from';
    </script>";
} else {
  echo "<script>
    alert('Could not add teacher.');
    window.location.href='$from';
    </script>";
}

$conn->close();
?>