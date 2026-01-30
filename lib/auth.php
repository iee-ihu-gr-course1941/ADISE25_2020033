<?php

require_once "db.php";

function authenticate($token) {
    global $pdo;

    if (!$token) {
        echo json_encode([
            "error" => "Token is missing"
        ]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, username FROM players WHERE token = ?");
    $stmt->execute([$token]);
    $player = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$player) {
        echo json_encode([
            "error" => "Invalid token"
        ]);
        exit;
    }

    return $player;
}