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

// Φόρτωση κατάστασης
$stmt = $pdo->prepare("SELECT * FROM game_state WHERE game_id = ?");
$stmt->execute([$game_id]);
$state = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$state) {
    echo json_encode(["error" => "Game state not found"]);
    exit;
}

$p1_cards = json_decode($state["p1_collected"], true);
$p2_cards = json_decode($state["p2_collected"], true);

$p1_xeri = $state["p1_xeri"];
$p2_xeri = $state["p2_xeri"];

function score_player($cards, $xeri, $is_p1_cards, $p1_count, $p2_count) {
    $score = 0;

    // 2♠
    if (in_array("2S", $cards)) $score += 1;

    // 10♦
    if (in_array("10D", $cards)) $score += 1;

    // K Q J 10 (εκτός 10♦)
    foreach ($cards as $c) {
        $v = substr($c, 0, -1);
        if (in_array($v, ["K","Q","J","10"]) && $c !== "10D") {
            $score += 1;
        }
    }

    // Ξερές
    $score += $xeri * 10;

    // Περισσότερα χαρτιά
    if ($p1_count !== $p2_count) {
        if ($is_p1_cards && $p1_count > $p2_count) $score += 3;
        if (!$is_p1_cards && $p2_count > $p1_count) $score += 3;
    }

    return $score;
}

$p1_score = score_player($p1_cards, $p1_xeri, true, count($p1_cards), count($p2_cards));
$p2_score = score_player($p2_cards, $p2_xeri, false, count($p1_cards), count($p2_cards));

echo json_encode([
    "status" => "ok",
    "player1" => [
        "cards" => count($p1_cards),
        "xeri" => $p1_xeri,
        "score" => $p1_score
    ],
    "player2" => [
        "cards" => count($p2_cards),
        "xeri" => $p2_xeri,
        "score" => $p2_score
    ]
]);