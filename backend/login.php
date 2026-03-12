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

$email = $input["email"] ?? "";
$password = $input["password"] ?? "";

/* hent bruger */

$stmt = $pdo->prepare(
    "SELECT password_hash, salt
     FROM blood2_users
     WHERE email = ?"
);

$stmt->execute([$email]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {

    echo json_encode([
        "success" => false
    ]);

    exit();
}

/* verificer password */

$hash = $user["password_hash"];
$salt = $user["salt"];

if (password_verify($password . $salt, $hash)) {

    echo json_encode([
        "success" => true
    ]);

} else {

    echo json_encode([
        "success" => false
    ]);
}