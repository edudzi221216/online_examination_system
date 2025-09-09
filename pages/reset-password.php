<!--this page is responsible for login access, both student and admin section-->

<?php
include '../database/config.php';
include '../includes/uniques.php';
$email = mysqli_real_escape_string($conn, $_POST['email']);
$pass = 'STU'.get_rand_numbers(5);
$mypassword = md5($pass);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require 'vendor/autoload.php';



$sql = "SELECT * FROM tbl_users WHERE email = '$email'" ;

$result = $conn->query($sql);


    $update = "UPDATE tbl_users SET login='$pass' WHERE email = '$email'";
	$result1 = $conn->query($update);
    if($result1){
    /*$mail=new PHPMailer(true);

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
    $mail->Subject ="Reset Password";
    $mail->Body ="Your your new password is $pass. Please don't share your new password";
   // $mail->AltBody ="Your student ID  and default Password to login to the system is $student_id";
   // $mail->MsgHTML("<h1> Your student ID  and default Password to login to the system is $student_id </h1>");

    if(!$mail->Send()){
        echo "Error Sending Mail";
    }
    else{
        echo "<script>
        alert('Check your email for new password');
        window.location.href='../index.php;
        </script>";
    } */
    echo "<script>
    alert('Check your email for new password $pass');
    window.location.href='../index.php;
    </script>";

    }
	
 else {	
	echo "<script>
    alert('Email not found.');
    window.location.href='../index.php';
    </script>";
}



$conn->close();
echo $email;
?>