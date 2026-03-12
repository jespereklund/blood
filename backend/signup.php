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

function generate_guid() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

$input = json_decode(file_get_contents("php://input"), true);

$email = $input["email"] ?? "";
$password = $input["password"] ?? "";

/* -------- Tjek om email findes -------- */

$stmt = $pdo->prepare(
    "SELECT id FROM blood2_users WHERE email = ?"
);

$stmt->execute([$email]);

if ($stmt->fetch()) {

    echo json_encode([
        "success" => false,
        "error" => "email findes allerede"
    ]);

    exit;
}

/* -------- Opret bruger -------- */

$salt = bin2hex(random_bytes(16));

$hash = password_hash($password . $salt, PASSWORD_DEFAULT);

$token = generate_guid();

$stmt = $pdo->prepare(
    "INSERT INTO blood2_users (email, password_hash, salt, token)
     VALUES (?, ?, ?, ?)"
);

$stmt->execute([$email, $hash, $salt, $token]);

echo json_encode([
    "success" => true
]);