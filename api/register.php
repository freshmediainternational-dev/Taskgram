<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once 'config.php';
require_once 'send_email.php';

$data = json_decode(file_get_contents("php://input"), true);

$full_name = trim($data['full_name']);
$email = trim($data['email']);
$phone = trim($data['phone']);
$password = $data['password'];
$confirm_password = $data['confirm_password'];
$account_type = $data['account_type'];

if(empty($full_name) || empty($email) || empty($phone) || empty($password)){
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

if($password !== $confirm_password){
    echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if($stmt->rowCount() > 0){
    echo json_encode(["status" => "error", "message" => "Email already registered"]);
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Generate 6-digit verification code
$code = rand(100000, 999999);
$expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));

$stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password, account_type, verification_code, code_expires_at, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
$stmt->execute([$full_name, $email, $phone, $hashed_password, $account_type, $code, $expires_at]);

// Send verification email
$sent = sendVerificationEmail($email, $full_name, $code);

if($sent){
    echo json_encode(["status" => "success", "message" => "Registration successful! Check your email for verification code."]);
} else {
    echo json_encode(["status" => "success", "message" => "Registration successful! But email could not be sent. Contact support."]);
}
?>