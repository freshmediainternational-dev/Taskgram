<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once 'config.php';
require_once 'send_email.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data['email']);

// Check if user exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user){
    echo json_encode(["status" => "error", "message" => "Email not found"]);
    exit;
}

if($user['is_verified'] == 1){
    echo json_encode(["status" => "error", "message" => "Email already verified"]);
    exit;
}

// Generate new code
$code = rand(100000, 999999);
$expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));

$stmt = $pdo->prepare("UPDATE users SET verification_code = ?, code_expires_at = ? WHERE email = ?");
$stmt->execute([$code, $expires_at, $email]);

$sent = sendVerificationEmail($email, $user['full_name'], $code);

if($sent){
    echo json_encode(["status" => "success", "message" => "New verification code sent to your email"]);
} else {
    echo json_encode(["status" => "error", "message" => "Could not send email. Please try again."]);
}
?>