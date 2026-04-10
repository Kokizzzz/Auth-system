<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function sendResetCodeEmail($toEmail, $code) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'mail.privateemail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your-email@example.com';
        $mail->Password   = 'your-email-password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('your-email@example.com', 'Auth System');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Your password reset code';
        $mail->Body = "<h2>Your reset code is: {$code}</h2>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}