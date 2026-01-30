<?php

require_once "../lib/auth.php";

$data = json_decode(file_get_contents("php://input"), true);

$token   = $data["token"] ?? null;
$game_id = $data["game_id"] ?? null;

if (!$game_id) {
    echo json_encode(["error" => "game_id is required"]);
    exit;
}

$player = authenticate($token);
$player_id = $player["id"];

// Φόρτωση παιχνιδιού
$stmt = $pdo->prepare("SELECT * FROM games WHERE id = ?");
$stmt->execute([$game_id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    echo json_encode(["error" => "Game not found"]);
    exit;
}

// Φόρτωση κατάστασης
$stmt = $pdo->prepare("SELECT * FROM game_state WHERE game_id = ?");
$stmt->execute([$game_id]);
$state = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$state) {
    echo json_encode(["error" => "Game state not found"]);
    exit;
}

// Ποιος παίκτης είναι
$is_p1 = ($player_id == $game["player1_id"]);
$is_p2 = ($player_id == $game["player2_id"]);

if (!$is_p1 && !$is_p2) {
    echo json_encode(["error" => "Player not in this game"]);
    exit;
}

// Χέρια
$p1_hand = json_decode($state["p1_hand"], true);
$p2_hand = json_decode($state["p2_hand"], true);

// Τραπέζι
$table_cards = json_decode($state["table_cards"], true);
$top_table_card = count($table_cards) > 0 ? end($table_cards) : null;

// Τράπουλα
$deck = json_decode($state["deck"], true);

// Απάντηση
echo json_encode([
    "status" => "ok",
    "game_status" => $game["status"],
    "your_turn" => ($game["current_player"] == $player_id),
    "your_hand" => $is_p1 ? $p1_hand : $p2_hand,
    "top_table_card" => $top_table_card,
    "deck_remaining" => count($deck)
]);