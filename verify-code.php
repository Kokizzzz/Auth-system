<?php
require_once __DIR__ . '/includes/guest.php';
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot.php");
    exit;
}

$message = '';
$email = $_SESSION['reset_email'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = trim($_POST['code'] ?? '');

    if ($code === '' || strlen($code) !== 6) {
        $message = "Please enter the 6-digit code.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE email = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$email]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reset) {
            $message = "No reset request found.";
        } elseif (strtotime($reset['expires_at']) < time()) {
            $message = "This code has expired.";
        } elseif (!password_verify($code, $reset['reset_code'])) {
            $message = "Invalid code.";
        } else {
            $update = $pdo->prepare("UPDATE password_resets SET verified = 1 WHERE id = ?");
            $update->execute([$reset['id']]);

            $_SESSION['reset_verified'] = true;
            header("Location: reset.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code | Auth System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-body">

<div class="auth-wrapper">
    <section class="auth-visual auth-visual-login">
        <div class="auth-visual-overlay"></div>
        <div class="auth-visual-content">
            <span class="auth-kicker">AUTH SYSTEM</span>
            <h1>Verify your code.</h1>
            <p>Enter the 6-digit code we sent to your email.</p>
        </div>
    </section>

    <section class="auth-panel">
        <form class="auth-box" method="POST" novalidate>
            <div class="auth-header">
                <h2>Verification Code</h2>
                <p>We sent a code to <strong><?php echo htmlspecialchars($email); ?></strong></p>
            </div>

            <?php if ($message !== ''): ?>
                <div class="form-message error"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div class="otp-group" id="otpGroup">
                <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">
                <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">
                <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">
                <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">
                <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">
                <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">
            </div>

            <input type="hidden" name="code" id="fullCode">

            <button type="submit" class="primary-btn">Verify Code</button>

            <p class="auth-footer-text">
                <a href="forgot.php">Send a new code</a>
            </p>
        </form>
    </section>
</div>

<script>
const otpInputs = document.querySelectorAll('.otp-input');
const fullCode = document.getElementById('fullCode');
const form = document.querySelector('form');

otpInputs.forEach((input, index) => {
    input.addEventListener('input', () => {
        input.value = input.value.replace(/[^0-9]/g, '').slice(0, 1);

        if (input.value && index < otpInputs.length - 1) {
            otpInputs[index + 1].focus();
        }

        updateFullCode();
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !input.value && index > 0) {
            otpInputs[index - 1].focus();
        }
    });

    input.addEventListener('paste', (e) => {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);

        pasted.split('').forEach((char, i) => {
            if (otpInputs[i]) otpInputs[i].value = char;
        });

        updateFullCode();

        const nextIndex = Math.min(pasted.length, otpInputs.length - 1);
        otpInputs[nextIndex].focus();
    });
});

function updateFullCode() {
    fullCode.value = Array.from(otpInputs).map(input => input.value).join('');
}

form.addEventListener('submit', () => {
    updateFullCode();
});
</script>

</body>
</html>