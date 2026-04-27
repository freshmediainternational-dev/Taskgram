<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendVerificationEmail($to_email, $to_name, $code){
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'damilareisrael53@gmail.com@gmail.com';
        $mail->Password = 'rece ihal szhz pedb';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('damilareisrael53@gmail.com', 'Taskgram');
        $mail->addAddress($to_email, $to_name);

        $mail->isHTML(true);
        $mail->Subject = 'Your Taskgram Verification Code';
        $mail->Body = "
            <div style='font-family:Arial,sans-serif;max-width:500px;margin:0 auto;padding:30px;'>
                <h2 style='color:#6c63ff;'>Taskgram Verification</h2>
                <p>Hi $to_name,</p>
                <p>Your verification code is:</p>
                <div style='background:#f8f7ff;padding:20px;border-radius:10px;text-align:center;margin:20px 0;'>
                    <h1 style='color:#6c63ff;font-size:40px;letter-spacing:10px;'>$code</h1>
                </div>
                <p>This code expires in <strong>10 minutes</strong>.</p>
                <p>If you didn't request this, please ignore this email.</p>
                <br>
                <p>The Taskgram Team</p>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>