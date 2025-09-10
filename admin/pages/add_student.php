<?php
// Set timezone from config or use default
$timezone = defined('DEFAULT_TIMEZONE') ? DEFAULT_TIMEZONE : 'UTC';
date_default_timezone_set($timezone);
include '../../database/config.php';
include '../../includes/uniques.php';	

$student_id = 'OES'.get_rand_numbers(5);
$password = md5($student_id) ;	
$fname = ucwords(mysqli_real_escape_string($conn, $_POST['fname']));
$lname = ucwords(mysqli_real_escape_string($conn, $_POST['lname']));
$email = mysqli_real_escape_string($conn, $_POST['email']);
$contact = mysqli_real_escape_string($conn, $_POST['contact']);
$ay = mysqli_real_escape_string($conn, $_POST['ay']);
$class = mysqli_real_escape_string($conn, $_POST['class']);
$gender = mysqli_real_escape_string($conn, $_POST['gender']);

$sql = "SELECT * FROM tbl_users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

    while($row = $result->fetch_assoc()) {
    $sem = $row['email'];
	if ($sem == $email) {	
	 echo "<script>
    alert('Student with Email ID ('+ '$sem' + ') already exist.');
    window.location.href='../students.php';
    </script>";
	
	}
	
    }
} else {
//	insert the new value
$sql = "INSERT INTO tbl_users (user_id, first_name, last_name, gender, email, contact, ay, class, login, last_login)
VALUES ('$student_id', '$fname', '$lname', '$gender', '$email', '$contact', '$ay', '$class','$password', null)";

    if ($conn->query($sql) === TRUE) {
        $mail = setup_student_email($student_id, $email);

        if($email && !$mail->send()){
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            echo "<script>
                alert('Student added successfully. STUDENT ID: $student_id');
                window.location.href='../vstudents.php?cn=$class';
            </script>";
        }
        

  
   
} else {
	echo "<script>
    alert('Could not add student.');
    window.location.href='../students.php';
    </script>";
}

}

$conn->close();
?>