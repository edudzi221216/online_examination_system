<?php 
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    $root_path = $_SERVER["DOCUMENT_ROOT"];
    require $root_path.'/vendor/autoload.php';

    function get_server_url(){
        //grabbing protocol
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off" || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

        //adding the domain name
        $domain_name = $_SERVER['HTTP_HOST'];

        //$url = $protocol.$domain_name;
        $url = $protocol.$domain_name;

        return $url;
    }

    /**
     * Used to send an email to the user. It returns the phpmailer object
     * @param string $subject The message subject
     * @param string $message The message
     * @param string $recepient The email to receive the message
     * @param ?string $alt_message Any alternative message to send if the normal message doesnt go through
     * @return PHPMailer
     */
    function send_mail($subject, $message, $recepient, $alt_message = null){
        // mailing account details
        $mail_account = "successinnovativehub@gmail.com";
        $mail_password = "wzap xjim dvpv bhfe";
        $mail_host = "smtp.gmail.com";
        $mail_port = 587;

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = $mail_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $mail_account;
        $mail->Password   = $mail_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $mail_port;

        $mail->setFrom($mail_account, 'Online Examination System');
        $mail->addAddress($recepient);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        if($alt_message){
            $mail->AltBody = $alt_message;
        }

        return $mail;
    }