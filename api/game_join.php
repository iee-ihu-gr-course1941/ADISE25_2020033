<?php

require_once "../lib/auth.php";

$data = json_decode(file_get_contents("php://input"), true);

$token   = $data["token"] ?? null;
$game_id = $data["game_id"] ?? null;

if (!$game_id) {
    echo json_encode([
        "error" => "game_id is required"
    ]);
    exit;
}

$player = authenticate($token);
$player_id = $player["id"];

// Έλεγχος παιχνιδιού
$stmt = $pdo->prepare("SELECT * FROM games WHERE id = ?");
$stmt->execute([$game_id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    echo json_encode([
        "error" => "Game not found"
    ]);
    exit;
}

if ($game["status"] !== "waiting") {
    echo json_encode([
        "error" => "Game is not available"
    ]);
    exit;
}

if ($game["player1_id"] == $player_id) {
    echo json_encode([
        "error" => "Player already in game"
    ]);
    exit;
}

// Δήλωση player2
$stmt = $pdo->prepare(
    "UPDATE games
     SET player2_id = ?, status = 'playing'
     WHERE id = ?"
);
$stmt->execute([$player_id, $game_id]);

// Φόρτωση game_state
$stmt = $pdo->prepare("SELECT * FROM game_state WHERE game_id = ?");
$stmt->execute([$game_id]);
$state = $stmt->fetch(PDO::FETCH_ASSOC);

// Μοίρασμα 6 χαρτιών στον παίκτη 2
$deck = json_decode($state["deck"], true);
$p2_hand = array_splice($deck, 0, 6);

// Ενημέρωση game_state
$stmt = $pdo->prepare(
    "UPDATE game_state
     SET deck = ?, p2_hand = ?
     WHERE game_id = ?"
);

$stmt->execute([
    json_encode(array_values($deck)),
    json_encode($p2_hand),
    $game_id
]);

echo json_encode([
    "status" => "ok",
    "game_id" => $game_id,
    "player2_hand" => $p2_hand,
    "deck_remaining" => count($deck)
]);
