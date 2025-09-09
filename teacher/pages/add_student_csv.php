<?php
// Set timezone from config or use default
$timezone = defined('DEFAULT_TIMEZONE') ? DEFAULT_TIMEZONE : 'UTC';
date_default_timezone_set($timezone);
include '../../database/config.php';
include '../../includes/uniques.php';	

//$student_id = 'OES'.get_rand_numbers(5);
//$password = md5($student_id);
$class = mysqli_real_escape_string($conn, $_POST['class']);
$ay = mysqli_real_escape_string($conn, $_POST['ay']);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../../vendor/phpmailer/phpmailer/src/Exception.php';
require '../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../../vendor/phpmailer/phpmailer/src/SMTP.php';
require '../../vendor/autoload.php';

if(isset($_FILES["file"]["type"])) {
    // Check if uploaded file is a CSV file
    $csvMimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
    if(!empty($_FILES["file"]["name"]) && in_array($_FILES["file"]["type"],$csvMimes)){
        // Open uploaded CSV file with read-only mode
        $csvFile = fopen($_FILES["file"]["tmp_name"], 'r');
        // Skip the first line as it contains column headings
        fgetcsv($csvFile);
        // Loop through the CSV file and insert records into the database
        while(($line = fgetcsv($csvFile)) !== FALSE){
            $fname = ucwords(mysqli_real_escape_string($conn, $line[0]));
            $lname = ucwords(mysqli_real_escape_string($conn, $line[1]));
            $gender = mysqli_real_escape_string($conn, $line[2]);
            $email = mysqli_real_escape_string($conn, $line[3]);
            $contact = mysqli_real_escape_string($conn, $line[4]);
            // Generate a unique student ID
            $student_id = 'OES'.get_rand_numbers(5);
            // Hash the student ID to create a password
            $password = md5($student_id);
            // Insert the student record into the database
            $sql = "INSERT INTO tbl_users (user_id, login, first_name, last_name, email, contact, gender, class, ay) 
                    VALUES ('$student_id', '$password', '$fname', '$lname', '$email', '$contact', '$gender', '$class', '$ay')";
            if ($conn->query($sql) === TRUE) {
                // Student record inserted successfully
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
 
               
   $mail->Send()  ;    

            } else {
                // Error inserting student record
            }
        }
        // Close the CSV file
        fclose($csvFile);
        
    } else {
        // Invalid file type uploaded
    }
    
}

echo "<script>
                alert('Student uploaded successfully.');
                window.location.href='../vstudents.php';
                </script>";
                header("Location: ../vstudents.php");

$conn->close();
?> 