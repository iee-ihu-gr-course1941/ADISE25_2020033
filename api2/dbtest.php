<?php

require_once "../lib/db.php";

echo json_encode([
    "status" => "ok",
    "message" => "Database connected"
]);