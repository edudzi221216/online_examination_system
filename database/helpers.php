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

    /**
     * Used to create a teacher ready email
     * @param string $teacher_id
     * @param string $email
     * @return PHPMailer
     */
    function setup_teacher_email($teacher_id, $email){
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

        return $mail;
    }

    /**
     * Used to create a student ready email
     * @param string $student_id
     * @param string $email
     * @return PHPMailer
     */
    function setup_student_email($student_id, $email){
        $url = get_server_url();
        $url .= "/login.php";
        
        $mail_body = <<<HTML
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
        
        $mail_alt = "Your Student ID: $student_id\nDefault Password: $student_id\nLogin here: $url";
        $mail = send_mail("Online Examination System Student Account", $mail_body, $email, $mail_alt);

        return $mail;
    }

    /**
     * Used to setup an email for accountant
     * @param string $accountant_id
     * @param string $email
     * @return PHPMailer
     */
    function setup_accountant_email($accountant_id, $email){
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
                <strong>Accountant ID:</strong> {$accountant_id}<br>
                <strong>Default Password:</strong> {$accountant_id}
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
        $mail = send_mail($subject, $mail_body, $email, $body_alt);

        return $mail;
    }