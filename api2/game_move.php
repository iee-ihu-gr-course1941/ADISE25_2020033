<?php

require_once "../lib/auth.php";

$data = json_decode(file_get_contents("php://input"), true);

$token   = $data["token"] ?? null;
$game_id = $data["game_id"] ?? null;
$card    = $data["card"] ?? null;

if (!$game_id || !$card) {
    echo json_encode(["error" => "game_id and card are required"]);
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

if ($game["status"] !== "playing") {
    echo json_encode(["error" => "Game is not active"]);
    exit;
}

// Έλεγχος σειράς
if ($game["current_player"] != $player_id) {
    echo json_encode(["error" => "Not your turn"]);
    exit;
}

// Φόρτωση κατάστασης
$stmt = $pdo->prepare("SELECT * FROM game_state WHERE game_id = ?");
$stmt->execute([$game_id]);
$state = $stmt->fetch(PDO::FETCH_ASSOC);

// Ποιος παίκτης είναι
$is_p1 = ($player_id == $game["player1_id"]);
$hand = json_decode($is_p1 ? $state["p1_hand"] : $state["p2_hand"], true);

// Έλεγχος ότι το χαρτί υπάρχει στο χέρι
if (!in_array($card, $hand)) {
    echo json_encode(["error" => "Card not in hand"]);
    exit;
}
// Αφαίρεση χαρτιού από το χέρι
$hand_index = array_search($card, $hand);
unset($hand[$hand_index]);
$hand = array_values($hand);

// Τραπέζι & μαζεμένα
$table_cards = json_decode($state["table_cards"], true);
$p1_collected = json_decode($state["p1_collected"], true);
$p2_collected = json_decode($state["p2_collected"], true);

$top_table_card = count($table_cards) > 0 ? end($table_cards) : null;

// Έλεγχος Βαλέ
$is_jack = (substr($card, 0, -1) === "J");

// Έλεγχος ίδιας αξίας
$same_value = $top_table_card && (substr($card, 0, -1) === substr($top_table_card, 0, -1));

// Μαζεύει;
$collect = false;
if ($is_jack) {
    $collect = true;
} elseif ($same_value) {
    $collect = true;
}

// Έλεγχος Ξερής
$is_xeri = false;
$is_xeri_jack = false;

if ($collect && count($table_cards) === 1) {
    $is_xeri = true;
    if ($is_jack) {
        $is_xeri_jack = true;
    }
}


if ($collect) {
    // Μαζεύει όλα τα χαρτιά του τραπεζιού + το δικό του
    $collected_cards = $table_cards;
    $collected_cards[] = $card;
    $table_cards = [];

    if ($is_p1) {
        $p1_collected = array_merge($p1_collected, $collected_cards);
    } else {
        $p2_collected = array_merge($p2_collected, $collected_cards);
    }

    if ($is_xeri) {
        if ($is_p1) {
            $state["p1_xeri"] += 1;
        } else {
            $state["p2_xeri"] += 1;
        }
    }

} else {
    // Ρίχνει χαρτί στο τραπέζι
    $table_cards[] = $card;
}

// Αλλαγή σειράς
$next_player = ($player_id == $game["player1_id"])
    ? $game["player2_id"]
    : $game["player1_id"];

// Ενημέρωση game_state
$stmt = $pdo->prepare(
    "UPDATE game_state
     SET table_cards = ?, 
         p1_hand = ?, 
         p2_hand = ?, 
         p1_collected = ?, 
         p2_collected = ?,
         p1_xeri = ?,
         p2_xeri = ?
     WHERE game_id = ?"
);

$stmt->execute([
    json_encode($table_cards),
    json_encode($is_p1 ? $hand : json_decode($state["p1_hand"], true)),
    json_encode($is_p1 ? json_decode($state["p2_hand"], true) : $hand),
    json_encode($p1_collected),
    json_encode($p2_collected),
    $state["p1_xeri"],
    $state["p2_xeri"],
    $game_id
]);




// Ενημέρωση σειράς στο games
$stmt = $pdo->prepare(
    "UPDATE games SET current_player = ? WHERE id = ?"
);
$stmt->execute([$next_player, $game_id]);

// Έλεγχος τέλους παιχνιδιού
$deck = json_decode($state["deck"], true);
$p1_hand_check = json_decode($state["p1_hand"], true);
$p2_hand_check = json_decode($state["p2_hand"], true);

$game_over = false;
if (count($deck) === 0 && count($p1_hand_check) === 0 && count($p2_hand_check) === 0) {
    $game_over = true;

    $stmt = $pdo->prepare(
        "UPDATE games SET status = 'finished' WHERE id = ?"
    );
    $stmt->execute([$game_id]);
}

echo json_encode([
    "status" => "ok",
    "action" => $collect ? "collect" : "throw",
    "card_played" => $card,
    "xeri" => $is_xeri,
    "xeri_type" => $is_xeri_jack ? "jack" : ($is_xeri ? "normal" : null),
    "next_player" => $next_player,
    "game_over" => $game_over
]);
