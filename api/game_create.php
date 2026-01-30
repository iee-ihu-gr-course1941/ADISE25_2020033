<?php

require_once "../lib/auth.php";

$data = json_decode(file_get_contents("php://input"), true);
$token = $data["token"] ?? null;

$player = authenticate($token);
$player_id = $player["id"];

$stmt = $pdo->prepare(
    "INSERT INTO games (player1_id, current_player, status)
     VALUES (?, ?, ?)"
);

$stmt->execute([
    $player_id,
    $player_id,
    "waiting"
]);

$game_id = $pdo->lastInsertId();

$values = ["2","3","4","5","6","7","8","9","10","J","Q","K","A"];
$suits  = ["H","D","C","S"];

$deck = [];

foreach ($suits as $suit) {
    foreach ($values as $value) {
        $deck[] = $value . $suit;
    }
}

shuffle($deck);


$p1_hand = array_splice($deck, 0, 6);
$p2_hand = []; 

$table_cards = array_splice($deck, 0, 4);

$p1_collected = [];
$p2_collected = [];

$stmt = $pdo->prepare(
    "INSERT INTO game_state
     (game_id, deck, table_cards, p1_hand, p2_hand, p1_collected, p2_collected)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);

$stmt->execute([
    $game_id,
    json_encode(array_values($deck)),
    json_encode($table_cards),
    json_encode($p1_hand),
    json_encode($p2_hand),
    json_encode($p1_collected),
    json_encode($p2_collected)
]);

echo json_encode([
    "status" => "ok",
    "game_id" => $game_id,
    "player1_hand" => $p1_hand,
    "table_cards" => $table_cards,
    "deck_remaining" => count($deck)
]);