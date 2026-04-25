<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

$full_name = trim($data['full_name']);
$email = trim($data['email']);
$phone = trim($data['phone']);
$password = $data['password'];
$confirm_password = $data['confirm_password'];

// Validate
if(empty($full_name) || empty($email) || empty($phone) || empty($password)){
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

if($password !== $confirm_password){
    echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
    exit;
}

// Check if email exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if($stmt->rowCount() > 0){
    echo json_encode(["status" => "error", "message" => "Email already registered"]);
    exit;
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password) VALUES (?, ?, ?, ?)");
$stmt->execute([$full_name, $email, $phone, $hashed_password]);

echo json_encode(["status" => "success", "message" => "Registration successful"]);
?>