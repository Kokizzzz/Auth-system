<?php
require_once __DIR__ . '/includes/guest.php';
require_once __DIR__ . '/config/db.php';

$message = '';
$successMessage = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $emailOrUsername = trim($_POST["email_or_username"] ?? '');
    $password = $_POST["password"] ?? '';

    if ($emailOrUsername === '' || $password === '') {
        $message = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$emailOrUsername, $emailOrUsername]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["fullname"] = $user["fullname"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["email"] = $user["email"];

            header("Location: dashboard.php");
            exit;
        } else {
            $message = "Invalid login credentials.";
        }
    }
}

if (isset($_GET["registered"])) {
    $successMessage = "Account created successfully. Please login.";
}
if (isset($_GET["reset"])) {
    $successMessage = "Password reset successful. You can login now.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Auth System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-body">

<div class="auth-wrapper">
    <section class="auth-visual auth-visual-login">
        <div class="auth-visual-overlay"></div>
        <div class="auth-visual-content">
            <span class="auth-kicker">AUTH SYSTEM</span>
            <h1>Welcome back.</h1>
            <p>Secure sign in for your account with a clean modern SaaS style.</p>
            <div class="visual-shapes">
                <span class="shape shape-1"></span>
                <span class="shape shape-2"></span>
                <span class="shape shape-3"></span>
            </div>
        </div>
    </section>

    <section class="auth-panel">
        <form class="auth-box" method="POST" novalidate>
            <div class="auth-header">
                <h2>Login</h2>
                <p>Access your account and dashboard.</p>
            </div>

            <?php if ($successMessage !== ''): ?>
                <div class="form-message success"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php endif; ?>

            <?php if ($message !== ''): ?>
                <div class="form-message error"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div class="social-auth">
                <button type="button" class="social-btn social-google">
                    <span class="social-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#EA4335" d="M12 10.2v3.9h5.5c-.2 1.3-1.5 3.9-5.5 3.9-3.3 0-6-2.8-6-6.2s2.7-6.2 6-6.2c1.9 0 3.1.8 3.8 1.5l2.6-2.6C16.7 2.8 14.5 2 12 2 6.9 2 2.8 6.3 2.8 11.8S6.9 21.5 12 21.5c6.9 0 9.1-5 9.1-7.6 0-.5-.1-.9-.1-1.3H12z"/>
                        </svg>
                    </span>
                    <span>Continue with Google</span>
                </button>

                <button type="button" class="social-btn social-apple">
                    <span class="social-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="currentColor" d="M16.7 12.9c0-2.1 1.7-3.1 1.8-3.2-1-1.4-2.5-1.6-3-1.6-1.3-.1-2.5.8-3.1.8-.6 0-1.5-.8-2.6-.8-1.3 0-2.6.8-3.3 2-.7 1.2-1 3-.2 4.9.6 1.5 1.4 3.2 2.5 3.1 1-.1 1.4-.7 2.6-.7s1.5.7 2.6.7c1.1 0 1.8-1.5 2.4-3 .7-1.7.9-3.1.9-3.2-.1 0-1.6-.6-1.6-3zm-2-6.1c.5-.6.9-1.5.8-2.3-.8 0-1.7.5-2.2 1.1-.5.5-.9 1.4-.8 2.2.9.1 1.7-.4 2.2-1z"/>
                        </svg>
                    </span>
                    <span>Continue with Apple</span>
                </button>
            </div>

            <div class="auth-divider"><span>or continue with email</span></div>

            <div class="input-group">
                <label for="email_or_username">Email or Username</label>
                <input
                    id="email_or_username"
                    type="text"
                    name="email_or_username"
                    placeholder="Enter your email or username"
                    value="<?php echo htmlspecialchars($_POST['email_or_username'] ?? ''); ?>"
                    required
                >
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >
            </div>

            <div class="auth-row">
                <a href="forgot.php" class="text-link">Forgot password?</a>
            </div>

            <button type="submit" class="primary-btn">Login</button>

            <p class="auth-footer-text">
                Don’t have an account?
                <a href="signup.php">Create one</a>
            </p>
        </form>
    </section>
</div>

</body>
</html>