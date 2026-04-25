<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data['email']);
$password = $data['password'];

// Validate
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

// Return user data
echo json_encode([
    "status" => "success",
    "message" => "Login successful",
    "user" => [
        "id" => $user['id'],
        "full_name" => $user['full_name'],
        "email" => $user['email'],
        "balance" => $user['balance']
    ]
]);
?>