<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

if($method === 'GET'){
    $stmt = $pdo->prepare("SELECT * FROM tasks ORDER BY created_at DESC");
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["status" => "success", "tasks" => $tasks]);
}

if($method === 'POST'){
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("INSERT INTO tasks (title, description, reward, link) VALUES (?, ?, ?, ?)");
    $stmt->execute([$data['title'], $data['description'], $data['reward'], $data['link']]);
    echo json_encode(["status" => "success", "message" => "Task added"]);
}

if($method === 'DELETE'){
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$data['task_id']]);
    echo json_encode(["status" => "success", "message" => "Task deleted"]);
}
?>