<?php
require_once __DIR__ . '/includes/guest.php';
require_once __DIR__ . '/config/db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST["fullname"] ?? '');
    $username = trim($_POST["username"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';
    $confirmPassword = $_POST["confirm_password"] ?? '';

    if ($fullname === '' || $username === '' || $email === '' || $password === '' || $confirmPassword === '') {
        $message = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $message = "Username must be 3-20 characters and use only letters, numbers, or underscores.";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
    } else {
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $checkStmt->execute([$email, $username]);

        if ($checkStmt->fetch()) {
            $message = "Email or username already exists.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (fullname, username, email, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$fullname, $username, $email, $hashedPassword]);

            header("Location: login.php?registered=1");
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
    <title>Sign Up | Auth System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-body">

<div class="auth-wrapper">
    <section class="auth-visual auth-visual-signup">
        <div class="auth-visual-overlay"></div>
        <div class="auth-visual-content">
            <span class="auth-kicker">AUTH SYSTEM</span>
            <h1>Create your account.</h1>
            <p>Start with a polished authentication flow built for real portfolio projects.</p>
            <div class="visual-shapes">
                <span class="shape shape-4"></span>
                <span class="shape shape-5"></span>
                <span class="shape shape-6"></span>
            </div>
        </div>
    </section>

    <section class="auth-panel">
        <form class="auth-box" method="POST" novalidate>
            <div class="auth-header">
                <h2>Sign Up</h2>
                <p>Create your account in a few steps.</p>
            </div>

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
                    <span>Sign up with Google</span>
                </button>

                <button type="button" class="social-btn social-apple">
                    <span class="social-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="currentColor" d="M16.7 12.9c0-2.1 1.7-3.1 1.8-3.2-1-1.4-2.5-1.6-3-1.6-1.3-.1-2.5.8-3.1.8-.6 0-1.5-.8-2.6-.8-1.3 0-2.6.8-3.3 2-.7 1.2-1 3-.2 4.9.6 1.5 1.4 3.2 2.5 3.1 1-.1 1.4-.7 2.6-.7s1.5.7 2.6.7c1.1 0 1.8-1.5 2.4-3 .7-1.7.9-3.1.9-3.2-.1 0-1.6-.6-1.6-3zm-2-6.1c.5-.6.9-1.5.8-2.3-.8 0-1.7.5-2.2 1.1-.5.5-.9 1.4-.8 2.2.9.1 1.7-.4 2.2-1z"/>
                        </svg>
                    </span>
                    <span>Sign up with Apple</span>
                </button>
            </div>

            <div class="auth-divider"><span>or create with email</span></div>

            <div class="input-group">
                <label for="fullname">Full Name</label>
                <input
                    id="fullname"
                    type="text"
                    name="fullname"
                    placeholder="Enter your full name"
                    value="<?php echo htmlspecialchars($_POST['fullname'] ?? ''); ?>"
                    required
                >
            </div>

            <div class="input-group">
                <label for="username">Username</label>
                <input
                    id="username"
                    type="text"
                    name="username"
                    placeholder="Choose a username"
                    value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                    required
                >
            </div>

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

            <div class="input-group">
                <label for="password">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="Create a password"
                    required
                >
            </div>

            <div class="input-group">
                <label for="confirm_password">Confirm Password</label>
                <input
                    id="confirm_password"
                    type="password"
                    name="confirm_password"
                    placeholder="Repeat your password"
                    required
                >
            </div>

            <button type="submit" class="primary-btn">Create Account</button>

            <p class="auth-footer-text">
                Already have an account?
                <a href="login.php">Login</a>
            </p>
        </form>
    </section>
</div>

</body>
</html>