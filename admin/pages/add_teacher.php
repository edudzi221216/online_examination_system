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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../../vendor/phpmailer/phpmailer/src/Exception.php';
require '../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../../vendor/phpmailer/phpmailer/src/SMTP.php';
require '../../vendor/autoload.php';

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
    $mail->Subject ="Online Examination System Teacher Account";
    $mail->Body ="Your Teacher ID  and default Password to login to the system is $teacher_id. Please not to change the password";
   // $mail->AltBody ="Your student ID  and default Password to login to the system is $student_id";
   // $mail->MsgHTML("<h1> Your student ID  and default Password to login to the system is $student_id </h1>");

    if(!$mail->Send()){
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