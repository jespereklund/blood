<?php
function sendMail($email, $subject, $message, $headers) {
    $myemail = "noreply@flettedehvaler.dk";

    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->IsSMTP();
    $mail->Host = 'mailout.one.com';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = $myemail;
    $mail->Password = ''; 
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
?>