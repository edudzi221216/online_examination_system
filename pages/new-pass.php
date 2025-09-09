<!--Password Update-->
<?php
include '../database/config.php';
include '../includes/uniques.php';




$email = $_POST['email'] ;
$student_id = $_POST['user'];
$password = 'STU'.get_rand_numbers(5);
$new_password =  md5($password);  // this password comes from the profile.php page

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../vendor/phpmailer/phpmailer/src/Exception.php';
require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';
require '../vendor/autoload.php';

$sql = "UPDATE tbl_users SET login='$new_password' WHERE email = '$email' AND user_id = '$student_id' LIMIT 1";
// store it into the database 
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
    $mail->Subject ="Password Reset";
    $mail->Body ="Your your new password is $password . Please don't share your new password";
   // $mail->AltBody ="Your student ID  and default Password to login to the system is $student_id";
   // $mail->MsgHTML("<h1> Your student ID  and default Password to login to the system is $student_id </h1>");

    if(!$mail->Send()){
        echo "Error Sending Mail";
    }
    else{
        
        echo "<script>
        alert('Password has been changed.Check your email for new password');
        window.location.href='../index.php;
        </script>";

    } 
    echo "<script>
    alert('Password has been changed.Check your email for new password');
    window.location.href='../index.php';
    </script>"; 
   // if the query execute successfully then this msg will occur in the url
} else {
   echo "<script>
    alert('Something went wrong.');
    window.location.href='../index.php?rp=SOMETHING WENT WRONG';
    </script>";
}

$conn->close();
?>
