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
$phone = mysqli_real_escape_string($conn, $_POST['phone']);
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
$sql = "INSERT INTO tbl_teacher (teacher_id, first_name, last_name, gender, email,login, role)
VALUES ('$teacher_id', '$fname', '$lname', '$gender',  '$email',  '$password', 'accountant')";

if ($conn->query($sql) === TRUE) {
  $url = get_server_url()."/accountant/login.php";
  $subject = "Online Examination System Accountant Account";

  $mail_body = <<<HTML
  <!DOCTYPE html>
  <html>
    <body style="font-family: Arial, sans-serif; line-height:1.5; color:#333;">
      <p>Dear Accountant,</p>

      <p>
        Your account has been created successfully. Please find your login details below:
      </p>

      <p>
        <strong>Accountant ID:</strong> {$teacher_id}<br>
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

  $body_alt = "Your Accountant ID: $accountant_id\nDefault Password: $accountant_id\nLogin here: $url\nPlease change your password immediately after logging in.";
  
  if($email){
    $mail = send_mail($subject, $mail_body, $email, $body_alt);
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