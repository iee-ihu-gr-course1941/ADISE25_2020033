<?php

require_once "../lib/db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["username"]) || trim($data["username"]) === "") {
    echo json_encode([
        "error" => "Username is required"
    ]);
    exit;
}

$username = trim($data["username"]);
$token = bin2hex(random_bytes(16));

$stmt = $pdo->prepare("INSERT INTO players (username, token) VALUES (?, ?)");
$stmt->execute([$username, $token]);

echo json_encode([
    "status" => "ok",
    "username" => $username,
    "token" => $token
]);