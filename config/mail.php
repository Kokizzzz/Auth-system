<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function sendResetCodeEmail($toEmail, $code) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = '';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your-email@example.com';
        $mail->Password   = 'your-email-password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('your-email@example.com', 'Auth System');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Your password reset code';
        $mail->Body = "
            <div style='font-family:Arial,sans-serif;padding:24px;color:#111'>
                <h2 style='margin:0 0 10px;'>Password Reset Code</h2>
                <p style='font-size:15px;line-height:1.6;'>Use the code below to reset your password:</p>
                <div style='font-size:34px;font-weight:700;letter-spacing:8px;padding:16px 20px;background:#f3f4f6;border-radius:12px;display:inline-block;margin:12px 0;'>
                    {$code}
                </div>
                <p style='font-size:14px;color:#666;'>This code expires in 15 minutes.</p>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}