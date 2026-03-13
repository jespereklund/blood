<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "db.php";

$input = json_decode(file_get_contents("php://input"), true);

$token = $input["token"] ?? "";

if (!$token) {

    echo json_encode([
        "success" => false
    ]);

    exit();
}

/* verificer token */

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

/* hent log data */

$stmt = $pdo->prepare(
    "SELECT blodsukker, note, created_at
     FROM blood2_log
     WHERE email = ?
     ORDER BY created_at ASC"
);

$stmt->execute([$email]);

$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "logs" => $logs
]);