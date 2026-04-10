<?php
require_once __DIR__ . '/includes/guest.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/mail.php';

$message = '';
$messageType = 'error';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? '');

    if ($email === '') {
        $message = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } else {
        $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $message = "No account found with that email.";
        } else {
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $hashedCode = password_hash($code, PASSWORD_DEFAULT);
            $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            $deleteOld = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
            $deleteOld->execute([$email]);

            $insert = $pdo->prepare("INSERT INTO password_resets (email, reset_code, expires_at) VALUES (?, ?, ?)");
            $insert->execute([$email, $hashedCode, $expiresAt]);

          $result = sendResetCodeEmail($email, $code);

if ($result === true) {
    $_SESSION['reset_email'] = $email;
    header("Location: verify-code.php");
    exit;
} else {
    $message = "Could not send email: " . $result;
}
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Auth System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-body">

<div class="auth-wrapper">
    <section class="auth-visual auth-visual-forgot">
        <div class="auth-visual-overlay"></div>
        <div class="auth-visual-content">
            <span class="auth-kicker">AUTH SYSTEM</span>
            <h1>Reset your password.</h1>
            <p>We’ll send a secure 6-digit code to your email.</p>
            <div class="visual-shapes">
                <span class="shape shape-7"></span>
                <span class="shape shape-8"></span>
                <span class="shape shape-9"></span>
            </div>
        </div>
    </section>

    <section class="auth-panel">
        <form class="auth-box" method="POST" novalidate>
            <div class="auth-header">
                <h2>Forgot Password</h2>
                <p>Enter your email and we’ll send you a reset code.</p>
            </div>

            <?php if ($message !== ''): ?>
                <div class="form-message <?php echo htmlspecialchars($messageType); ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="input-group">
                <label for="email">Email Address</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                    required
                >
            </div>

            <button type="submit" class="primary-btn">Send Reset Code</button>

            <p class="auth-footer-text">
                Back to
                <a href="login.php">Login</a>
            </p>
        </form>
    </section>
</div>

</body>
</html>