<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data['user_id'];
$task_id = $data['task_id'];

// Check if already completed
$stmt = $pdo->prepare("SELECT id FROM completed_tasks WHERE user_id = ? AND task_id = ?");
$stmt->execute([$user_id, $task_id]);
if($stmt->rowCount() > 0){
    echo json_encode(["status" => "error", "message" => "Task already completed"]);
    exit;
}

// Get task reward
$stmt = $pdo->prepare("SELECT reward FROM tasks WHERE id = ?");
$stmt->execute([$task_id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

// Mark task as completed
$stmt = $pdo->prepare("INSERT INTO completed_tasks (user_id, task_id) VALUES (?, ?)");
$stmt->execute([$user_id, $task_id]);

// Update user balance
$stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
$stmt->execute([$task['reward'], $user_id]);

echo json_encode([
    "status" => "success", 
    "message" => "Task completed successfully",
    "reward" => $task['reward']
]);
?>