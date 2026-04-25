<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once 'config.php';

$user_id = $_GET['user_id'];

// Get all tasks
$stmt = $pdo->prepare("SELECT t.*, 
    CASE WHEN ct.id IS NOT NULL THEN 1 ELSE 0 END as completed
    FROM tasks t
    LEFT JOIN completed_tasks ct ON t.id = ct.task_id 
    AND ct.user_id = ?");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "status" => "success",
    "tasks" => $tasks
]);
?>