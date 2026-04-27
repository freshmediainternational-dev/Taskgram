<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data['user_id'];
$task_id = $data['task_id'];
$telegram_username = $data['telegram_username'];

$bot_token = "8500414716:AAFt-iGNftOvc5Lop5LSBZ9V4LK5RpR0fqQ";
$group_id = "-1003916880939";

// Check if user is in the group
$url = "https://api.telegram.org/bot$bot_token/getChatMember?chat_id=$group_id&user_id=";

// First get telegram user ID from username
$searchUrl = "https://api.telegram.org/bot$bot_token/getChat?chat_id=@$telegram_username";
$response = file_get_contents($searchUrl);
$result = json_decode($response, true);

if(!$result['ok']){
    echo json_encode(["status" => "error", "message" => "Telegram username not found"]);
    exit;
}

$telegram_user_id = $result['result']['id'];

// Check membership
$memberUrl = "https://api.telegram.org/bot$bot_token/getChatMember?chat_id=$group_id&user_id=$telegram_user_id";
$memberResponse = file_get_contents($memberUrl);
$memberResult = json_decode($memberResponse, true);

if(!$memberResult['ok']){
    echo json_encode(["status" => "error", "message" => "Could not verify membership"]);
    exit;
}

$status = $memberResult['result']['status'];

if($status === 'member' || $status === 'administrator' || $status === 'creator'){
    // User is in the group - credit reward
    $stmt = $pdo->prepare("SELECT reward FROM tasks WHERE id = ?");
    $stmt->execute([$task_id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if already completed
    $stmt = $pdo->prepare("SELECT id FROM completed_tasks WHERE user_id = ? AND task_id = ?");
    $stmt->execute([$user_id, $task_id]);
    if($stmt->rowCount() > 0){
        echo json_encode(["status" => "error", "message" => "Task already completed"]);
        exit;
    }

    // Mark as completed
    $stmt = $pdo->prepare("INSERT INTO completed_tasks (user_id, task_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $task_id]);

    // Update balance
    $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
    $stmt->execute([$task['reward'], $user_id]);

    echo json_encode([
        "status" => "success",
        "message" => "Verified! Reward credited.",
        "reward" => $task['reward']
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "You have not joined the group yet!"]);
}
?>