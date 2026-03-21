<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require("./PHPMailer/PHPMailer.php");
require("./PHPMailer/SMTP.php");

require_once 'db.php';

$resetBaseUrl = 'https://flettedehvaler.dk/blodsukker/';
$fromEmail = 'no-reply@flettedehvaler.dk';
$fromName = 'Blodsukker Logger';

function sendMail($email, $subject, $message, $headers) {
    $myemail = "noreply@flettedehvaler.dk";

    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->IsSMTP();
    $mail->Host = 'mailout.one.com';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = $myemail;
    $mail->Password = 'Sus9Sus8Sus7'; 
    $mail->SMTPSecure = 'tls';
    $mail->From = $myemail;
    $mail->FromName = "Bloodsugar Reset Email";
    $mail->AddAddress($email, 'JE');
    $mail->WordWrap = 50;
    $mail->IsHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->SMTPDebug = 0;

    if(!$mail->Send()) {
        return "Mailer Error: " . $mail->ErrorInfo;
    } else {
        return "Message has been sent";
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false]);
        exit;
    }

    $email = trim($_POST['email'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Ugyldig email'
        ]);
        exit;
    }

    // Tjek om email findes i blood2_users
    $stmt = $pdo->prepare("
        SELECT email 
        FROM blood2_users 
        WHERE email = :email 
        LIMIT 1
    ");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    // Svar der altid returneres (sikkerhed)
    $response = [
        'success' => true,
        'message' => 'Hvis emailen findes, er der sendt et reset-link.'
    ];

    if (!$user) {
        echo json_encode($response);
        exit;
    }

    // Generér token
    $token = bin2hex(random_bytes(32));
    $tokenHash = password_hash($token, PASSWORD_DEFAULT);
    $expiresAt = date('Y-m-d H:i:s', strtotime('+90 minutes'));

    // Gem token (email som key)
    $stmt = $pdo->prepare("
        INSERT INTO blood2_password_reset_tokens (email, token_hash, expires_at, used_at, created_at)
        VALUES (:email, :token_hash, :expires_at, NULL, NOW())
        ON DUPLICATE KEY UPDATE
            token_hash = VALUES(token_hash),
            expires_at = VALUES(expires_at),
            used_at = NULL,
            created_at = NOW()
    ");

    $stmt->execute([
        ':email' => $email,
        ':token_hash' => $tokenHash,
        ':expires_at' => $expiresAt
    ]);

    // Reset link
    $resetLink = $resetBaseUrl
        . '?email=' . urlencode($email)
        . '&token=' . urlencode($token)
        . '#/reset-password';

    // Email indhold
    $subject = 'Nulstil dit password';

    $message = "Hej\n\n<br><br>"
        . "Klik på linket her for at nulstille dit password:\n\n<br><br>"
        . $resetLink . "\n\n<br><br>"
        . "Linket virker i 30 minutter.\n\n<br><br>"
        . "Hvis det ikke var dig, så ignorer denne mail.";

    $headers = "From: $fromName <$fromEmail>\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Send mail
    //@mail($email, $subject, $message, $headers);
    $res = sendMail($email, $subject, $message, $headers);

    echo json_encode($res);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Serverfejl: '.$e.' '.$res
    ]);
}