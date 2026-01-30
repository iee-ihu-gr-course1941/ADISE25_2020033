<?php

require_once "../lib/auth.php";

$data = json_decode(file_get_contents("php://input"), true);
$token = $data["token"] ?? null;

$player = authenticate($token);

echo json_encode([
    "status" => "ok",
    "player" => $player
]);