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
$sql = "INSERT INTO tbl_users (user_id, first_name, last_name, gender, email, contact, ay, class, login)
VALUES ('$student_id', '$fname', '$lname', '$gender', '$email', '$contact', '$ay', '$class','$password')";

if ($conn->query($sql) === TRUE) {
    $mail=new PHPMailer(true);

    $mail->IsSMTP();
            
    $mail->Mailer="smtp";

    $mail->SMTPDebug =0;
    $mail->SMTPAuth =TRUE;
    $mail->SMTPSecure ='tls';
    $mail->Port =587;
    $mail->Host ="smtp.gmail.com";
    $mail->Username ="mistrymadan699@gmail.com";
    $mail->Password ="qmskesryhgwkihzw";

    $mail->SetFrom('mistrymadan699@gmail.com','Online Examination System');
    $mail->addAddress($email);
                        // $mail->addAddress($email,$name);

    $mail->IsHTML(true);
    $mail->Subject ="Online Examination System Student Account";
    $mail->Body ="Your student ID  and default Password to login to the system is $student_id";
   // $mail->AltBody ="Your student ID  and default Password to login to the system is $student_id";
   // $mail->MsgHTML("<h1> Your student ID  and default Password to login to the system is $student_id </h1>");

    if(!$mail->Send()){
        echo "Error Sending Mail";
    }
    else{
        echo "<script>
        alert('Student added successfully. STUDENT ID: '+'$student_id');
        window.location.href='../vstudents.php?cn='+'$class';
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