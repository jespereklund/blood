<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'db.php';

header('Content-Type: application/json; charset=utf-8');

// Hvis din kolonne hedder noget andet, ret denne:
$passwordColumn = 'password_hash';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Kun POST er tilladt'
        ]);
        exit;
    }

    $email = trim($_POST['email'] ?? '');
    $token = trim($_POST['token'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    // 🔍 Valider input
    if (
        $email === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        $token === '' ||
        $password === '' ||
        $passwordConfirm === ''
    ) {
        echo json_encode([
            'success' => false,
            'message' => 'Alle felter skal udfyldes'
        ]);
        exit;
    }

    if ($password !== $passwordConfirm) {
        echo json_encode([
            'success' => false,
            'message' => 'Passwords matcher ikke'
        ]);
        exit;
    }

    /*
    if (strlen($password) < 8) {
        echo json_encode([
            'success' => false,
            'message' => 'Password skal være mindst 8 tegn'
        ]);
        exit;
    }
    */

    // 🔎 Find token i database
    $stmt = $pdo->prepare("
        SELECT token_hash, expires_at, used_at
        FROM blood2_password_reset_tokens
        WHERE email = :email
        LIMIT 1
    ");
    $stmt->execute([':email' => $email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode([
            'success' => false,
            'message' => '1 Ugyldigt eller udløbet link'
        ]);
        exit;
    }

    // ❌ Allerede brugt
    if (!empty($row['used_at'])) {
        echo json_encode([
            'success' => false,
            'message' => '2 Linket er allerede brugt'
        ]);
        exit;
    }

    // ⏰ Udløbet
    if (strtotime($row['expires_at']) < time()) {
        echo json_encode([
            'success' => false,
            'message' => '3 Linket er udløbet'
        ]);
        exit;
    }

    // 🔐 Tjek token
    if (!password_verify($token, $row['token_hash'])) {
        echo json_encode([
            'success' => false,
            'message' => '4 Ugyldigt eller udløbet link '.$token." ".$row['token_hash']
        ]);
        exit;
    }

    // 🔑 Hash nyt password
    $salt = bin2hex(random_bytes(16));

    $newPasswordHash = password_hash($password . $salt, PASSWORD_DEFAULT);

    // 💾 Opdater bruger
    $stmt = $pdo->prepare("
        UPDATE blood2_users
        SET {$passwordColumn} = :password, salt = :salt
        WHERE email = :email
    ");
    $stmt->execute([
        ':password' => $newPasswordHash,
        ':salt' => $salt,
        ':email' => $email
    ]);

    // ✅ Markér token som brugt
    $stmt = $pdo->prepare("
        UPDATE blood2_password_reset_tokens
        SET used_at = NOW()
        WHERE email = :email
    ");
    $stmt->execute([':email' => $email]);

    echo json_encode([
        'success' => true,
        'message' => 'Password er ændret'
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Serverfejl '.$e
    ]);
}