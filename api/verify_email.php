<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data['email']);
$code = trim($data['code']);

// Check if code is valid
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND verification_code = ? AND code_expires_at > NOW()");
$stmt->execute([$email, $code]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user){
    echo json_encode(["status" => "error", "message" => "Invalid or expired verification code"]);
    exit;
}

// Mark user as verified
$stmt = $pdo->prepare("UPDATE users SET is_verified = 1, verification_code = NULL, code_expires_at = NULL WHERE email = ?");
$stmt->execute([$email]);

echo json_encode(["status" => "success", "message" => "Email verified successfully! You can now login."]);
?>