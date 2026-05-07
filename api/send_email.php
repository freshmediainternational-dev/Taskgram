<?php
function sendVerificationEmail($to_email, $to_name, $code){
    $subject = "Your Taskgram Verification Code";

    $message ="
    <html>
    <body style='font-family:Arial,sans-serif;max-width:500px;margin:0 auth;padding:30px;'>
    <h2 style='color:#6c63ff;'>Taskgram Verification</h2>
    <p>Hi $to_name.</p>
    <p>Your verification code is:</p>
    <div style='background:#f8f7ff;padding:20px;border-radius:10px;text-align:center;margin20px 0;'>
    <h1 style='color:#6c63ff;font-size:400px;letter-spacing:10px;'>$code</h1>
    </div>
    <p>This code expires in <strong>10 minutes</strong>.</p>
    <p>If you didn't request this, please ignore this email.</p>
    <br>
    <p>The Taskgram Team</p>
</body>
</html>
";

$headers = "MIME-VERSION: 1.0" . "\r\n";
$headers = "Content-type:text/html;chartset=UTF-8" . "\r\n";
$headers = "From: Taskgram <noreply@taskgram.infinityfreeapp.com>" . "\r\n";

$sent =mail($to_email, $subject, $message, $headers);
return $sent;
}
?>