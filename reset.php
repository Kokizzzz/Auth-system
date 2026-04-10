<?php
require_once __DIR__ . '/includes/guest.php';
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_verified']) || $_SESSION['reset_verified'] !== true) {
    header("Location: forgot.php");
    exit;
}

$message = '';
$email = $_SESSION['reset_email'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($password === '' || $confirmPassword === '') {
        $message = "Please fill in all fields.";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $update = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update->execute([$hashedPassword, $email]);

        $delete = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
        $delete->execute([$email]);

        unset($_SESSION['reset_email'], $_SESSION['reset_verified']);

        header("Location: login.php?reset=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Auth System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-body">

<div class="auth-wrapper">
    <section class="auth-visual auth-visual-signup">
        <div class="auth-visual-overlay"></div>
        <div class="auth-visual-content">
            <span class="auth-kicker">AUTH SYSTEM</span>
            <h1>Create a new password.</h1>
            <p>Choose a strong password for your account.</p>
        </div>
    </section>

    <section class="auth-panel">
        <form class="auth-box" method="POST" novalidate>
            <div class="auth-header">
                <h2>Reset Password</h2>
                <p>Enter your new password below.</p>
            </div>

            <?php if ($message !== ''): ?>
                <div class="form-message error"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div class="input-group">
                <label for="password">New Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="Enter new password"
                    required
                >
            </div>

            <div class="input-group">
                <label for="confirm_password">Confirm Password</label>
                <input
                    id="confirm_password"
                    type="password"
                    name="confirm_password"
                    placeholder="Repeat new password"
                    required
                >
            </div>

            <button type="submit" class="primary-btn">Update Password</button>
        </form>
    </section>
</div>

</body>
</html>