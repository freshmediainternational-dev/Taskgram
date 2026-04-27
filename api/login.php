<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once 'config.php';
require_once 'send_email.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data['email']);
$password = $data['password'];

if(empty($email) || empty($password)){
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

// Check if user exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user){
    echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
    exit;
}

// Verify password
if(!password_verify($password, $user['password'])){
    echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
    exit;
}

// Check if email is verified
if($user['is_verified'] == 0){
    echo json_encode(["status" => "unverified", "message" => "Please verify your email first", "email" => $email]);
    exit;
}

// Generate 2FA code
$code = rand(100000, 999999);
$expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));

$stmt = $pdo->prepare("UPDATE users SET verification_code = ?, code_expires_at = ? WHERE email = ?");
$stmt->execute([$code, $expires_at, $email]);

// Send 2FA code
$sent = sendVerificationEmail($email, $user['full_name'], $code);

if($sent){
    echo json_encode([
        "status" => "2fa_required",
        "message" => "Verification code sent to your email",
        "email" => $email
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Could not send verification code. Please try again."]);
}
?>