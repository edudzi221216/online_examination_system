<?php
// Set timezone from config or use default
$timezone = defined('DEFAULT_TIMEZONE') ? DEFAULT_TIMEZONE : 'UTC';
date_default_timezone_set($timezone);
include '../../database/config.php';
include '../../includes/uniques.php';	
$teacher_id = 'TCHR'.get_rand_numbers(4);	// TEACHER ID
$password = md5($teacher_id) ;
$fname = ucwords(mysqli_real_escape_string($conn, $_POST['fname']));
$lname = ucwords(mysqli_real_escape_string($conn, $_POST['lname']));
$email = mysqli_real_escape_string($conn, $_POST['email']);
$gender = mysqli_real_escape_string($conn, $_POST['gender']);

$sql = "SELECT * FROM tbl_teacher WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

    while($row = $result->fetch_assoc()) {
    $tem = $row['email'];

	if ($tem == $email) {	// checks same or not
	 echo "<script>
	 alert('Teacher with Email ID ('+ '$tem' + ') already exist.');
	 window.location.href='../teacher.php?Duplicate Email ID Found';
	 </script>";	
	}
	
    }
} else {
//	insert the new value
$sql = "INSERT INTO tbl_teacher (teacher_id, first_name, last_name, gender, email, login)
VALUES ('$teacher_id', '$fname', '$lname', '$gender', '$email', '$password')";

if ($conn->query($sql) === TRUE) {
    $url = get_server_url()."/login.php";
    $subject = "Online Examination System Teacher Account";

    $mail_body = <<<HTML
    <!DOCTYPE html>
    <html>
    <body style="font-family: Arial, sans-serif; line-height:1.5; color:#333;">
        <p>Dear Teacher,</p>

        <p>
        Your account has been created successfully. Please find your login details below:
        </p>

        <p>
        <strong>Teacher ID:</strong> {$teacher_id}<br>
        <strong>Default Password:</strong> {$teacher_id}
        </p>

        <p>
        You can log in to the system using the link below:<br>
        <a href="{$url}" target="_blank">{$url}</a>
        </p>

        <p>
        For security reasons, please change your password immediately after logging in.
        </p>

        <p>Best regards,<br>
        Online Examination System Team</p>
    </body>
    </html>
    HTML;

    $body_alt = "Your Teacher ID: $teacher_id\nDefault Password: (provided separately)\nLogin here: $url\nPlease change your password immediately after logging in.";
    $mail = send_mail($subject, $mail_body, $email, $body_alt);

    if($email && !$mail->send()){
        echo "Error Sending Mail";
    }
    else{
      echo "<script>
      alert('Teacher added successfully. TEACHER ID: '+'$teacher_id');
      window.location.href='../teacher.php?Added Successfully';
      </script>";
    }


  
} else {
  echo "<script>
    alert('Could not add teacher.');
    window.location.href='../teacher.php?Could Not Register Teacher';
    </script>";
}


}

$conn->close();
?>