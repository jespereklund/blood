<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "db.php";

$input = json_decode(file_get_contents("php://input"), true);

$token = $input["token"] ?? "";
$blodsukker = $input["blodsukker"] ?? "";
$note = $input["note"] ?? "";

/* find email ud fra token */

$stmt = $pdo->prepare(
    "SELECT email
     FROM blood2_users
     WHERE token = ?"
);

$stmt->execute([$token]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {

    echo json_encode([
        "success" => false
    ]);

    exit();
}

$email = $user["email"];

/* indsæt log */

$stmt = $pdo->prepare(
    "INSERT INTO blood2_log (email, blodsukker, note)
     VALUES (?, ?, ?)"
);

$stmt->execute([
    $email,
    $blodsukker,
    $note
]);

echo json_encode([
    "success" => true
]);