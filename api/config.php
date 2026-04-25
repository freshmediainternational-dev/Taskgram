<?php
$host = "sql300.infinityfree.com";
$dbname = "if0_41751135_taskgram";
$username = "if0_41751135";
$password = "3EFh7amU8eJw2F";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die(json_encode([
        "status" => "error",
        "message" => "Connection failed: " . $e->getMessage()
    ]));
}
?>