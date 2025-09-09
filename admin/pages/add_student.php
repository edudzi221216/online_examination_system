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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../../vendor/phpmailer/phpmailer/src/Exception.php';
require '../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../../vendor/phpmailer/phpmailer/src/SMTP.php';
require '../../vendor/autoload.php';

 


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
        $mail = new PHPMailer(true);

        $url = get_server_url();
        $url .= "/login.php";
        
        $mail->isSMTP();
        $mail->Mailer     = "smtp";
        $mail->SMTPDebug  = 0;
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = $mail_port;
        $mail->Host       = $mail_host;
        $mail->Username   = $mail_account;
        $mail->Password   = $mail_password;
        
        $mail->setFrom('mistrymadan699@gmail.com', 'Online Examination System');
        $mail->addAddress($email);
        
        $mail->isHTML(true);
        $mail->Subject = "Online Examination System Student Account";
        
        $mail->Body = <<<HTML
        <!DOCTYPE html>
        <html>
          <body style="font-family: Arial, sans-serif; line-height:1.5; color:#333;">
            <p>Dear Student,</p>
            <p>Your account has been created successfully. Please find your login details below:</p>
            <p>
              <strong>Student ID:</strong> {$student_id}<br>
              <strong>Default Password:</strong> {$student_id}
            </p>
            <p>
              You can log in to the system using the link below:<br>
              <a href="{$url}" target="_blank">{$url}</a>
            </p>
            <p>For security reasons, please change your password immediately after logging in.</p>
            <p>Best regards,<br>Student Services Team</p>
          </body>
        </html>
        HTML;
        
        $mail->AltBody = "Your Student ID: $student_id\nDefault Password: $default_password\nLogin here: $url";
        
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