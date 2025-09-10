<?php
// Set timezone from config or use default
$timezone = defined('DEFAULT_TIMEZONE') ? DEFAULT_TIMEZONE : 'UTC';
date_default_timezone_set($timezone);
include '../../database/config.php';
include '../../includes/uniques.php';	
$teacher_id = 'ACC'.get_rand_numbers(4);	// ACCOUNTANT ID
$password = md5($teacher_id) ;
$fname = ucwords(mysqli_real_escape_string($conn, $_POST['fname']));
$lname = ucwords(mysqli_real_escape_string($conn, $_POST['lname']));
$email = mysqli_real_escape_string($conn, $_POST['email']);
$contact = mysqli_real_escape_string($conn, $_POST['contact']);
$gender = mysqli_real_escape_string($conn, $_POST['gender']);


$sql = "SELECT * FROM tbl_teacher WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

    while($row = $result->fetch_assoc()) {
    $tem = $row['email'];
	if ($tem == $email) {	// checks same or not
	 echo "<script>
	 alert('Accountant with Email ID ('+ '$tem' + ') already exist.');
	 window.location.href='../accountant.php?Duplicate Email ID Found';
	 </script>";	
	}
    }
} else {
//	insert the new value
$sql = "INSERT INTO tbl_teacher (teacher_id, first_name, last_name, gender, email,login, role, contact)
VALUES ('$teacher_id', '$fname', '$lname', '$gender',  '$email',  '$password', 'accountant', '$contact')";

if ($conn->query($sql) === TRUE) {
  
  
  if($email){
    $mail = setup_accountant_email($teacher_id, $email);
    $mail->send();
  }

  echo "<script>
    alert('Accountant added successfully. ACCOUNTANT ID: '+'$teacher_id');
    window.location.href='../accountant.php?Added Successfully';
    </script>";
} else {
  echo "<script>
    alert('Could not add teacher.');
    window.location.href='../accountant.php?Could Not Register Accountant';
    </script>";
}


}

$conn->close();
?>