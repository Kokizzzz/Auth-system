<?php
require_once __DIR__ . '/includes/auth.php';

$initial = strtoupper(substr($_SESSION["fullname"], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Auth System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg: #070b14;
            --bg-2: #0b1020;
            --panel: #0f172a;
            --panel-2: #111827;
            --border: #1f2937;
            --text: #f8fafc;
            --muted: #94a3b8;
            --primary: #6366f1;
            --primary-2: #8b5cf6;
            --accent: #22c55e;
        }

        body {
            font-family: Inter, Arial, Helvetica, sans-serif;
            min-height: 100vh;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(99, 102, 241, 0.14), transparent 24%),
                radial-gradient(circle at bottom right, rgba(139, 92, 246, 0.12), transparent 26%),
                linear-gradient(135deg, var(--bg), var(--bg-2));
        }

        .page {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 28px 20px 40px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        .brand-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo-box {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 18px;
            color: #fff;
            box-shadow: 0 14px 32px rgba(99, 102, 241, 0.25);
        }

        .brand-text h1 {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .brand-text p {
            color: var(--muted);
            font-size: 13px;
        }

        .logout-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: white;
            background: #121a2d;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 18px;
            font-weight: 700;
            transition: 0.2s ease;
        }

        .logout-btn:hover {
            background: #182338;
        }

        .hero {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 22px;
            margin-bottom: 22px;
        }

        .hero-main,
        .hero-side,
        .card {
            background: linear-gradient(180deg, rgba(255,255,255,0.04), rgba(255,255,255,0.025));
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.22);
        }

        .hero-main {
            padding: 30px;
            position: relative;
            overflow: hidden;
        }

        .hero-main::after {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            right: -60px;
            top: -60px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99,102,241,0.28), transparent 70%);
            pointer-events: none;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border-radius: 999px;
            background: rgba(99, 102, 241, 0.12);
            border: 1px solid rgba(99, 102, 241, 0.24);
            color: #dbe4ff;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            margin-bottom: 16px;
            text-transform: uppercase;
        }

        .hero-main h2 {
            font-size: clamp(2rem, 4vw, 3.1rem);
            line-height: 1.03;
            margin-bottom: 14px;
        }

        .hero-main p {
            color: var(--muted);
            font-size: 16px;
            line-height: 1.7;
            max-width: 720px;
        }

        .hero-side {
            padding: 26px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 18px;
        }

        .avatar {
            width: 92px;
            height: 92px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            font-weight: 800;
            box-shadow: 0 18px 40px rgba(99, 102, 241, 0.28);
        }

        .side-title {
            font-size: 18px;
            font-weight: 800;
        }

        .side-text {
            color: var(--muted);
            line-height: 1.65;
            font-size: 14px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 22px;
        }

        .card {
            padding: 24px;
        }

        .card h3 {
            font-size: 21px;
            margin-bottom: 18px;
        }

        .info-list,
        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .info-item,
        .feature-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #0c1425;
            border: 1px solid #1a263a;
        }

        .info-item span,
        .feature-item span:first-child {
            color: var(--muted);
            font-size: 14px;
        }

        .info-item strong,
        .feature-item strong {
            color: white;
            text-align: right;
        }

        .feature-item {
            justify-content: flex-start;
            gap: 12px;
        }

        .dot {
            width: 10px;
            height: 10px;
            min-width: 10px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            box-shadow: 0 0 18px rgba(99, 102, 241, 0.5);
        }

        .full {
            grid-column: 1 / -1;
        }

        .project-box {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .mini-panel {
            background: #0c1425;
            border: 1px solid #1a263a;
            border-radius: 16px;
            padding: 18px;
        }

        .mini-panel h4 {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .mini-panel p,
        .mini-panel li {
            color: var(--muted);
            line-height: 1.65;
            font-size: 14px;
        }

        .mini-panel ul {
            padding-left: 18px;
        }

        .footer-note {
            margin-top: 24px;
            color: var(--muted);
            text-align: center;
            font-size: 14px;
        }

        .footer-note a {
            color: #a5b4fc;
            text-decoration: none;
            font-weight: 700;
        }

        .footer-note a:hover {
            text-decoration: underline;
        }

        @media (max-width: 980px) {
            .hero,
            .grid,
            .project-box {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .page {
                padding: 18px 14px 28px;
            }

            .topbar {
                flex-direction: column;
                align-items: stretch;
            }

            .logout-btn {
                width: 100%;
            }

            .hero-main,
            .hero-side,
            .card {
                border-radius: 20px;
            }

            .hero-main,
            .hero-side,
            .card {
                padding: 20px;
            }

            .info-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-item strong {
                text-align: left;
            }

            .brand-wrap {
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <header class="topbar">
            <div class="brand-wrap">
                <div class="logo-box">PW</div>
                <div class="brand-text">
                    <h1>PicartWeb Auth System</h1>
                    <p>Clean PHP authentication project dashboard</p>
                </div>
            </div>

            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <section class="hero">
            <div class="hero-main">
                <span class="chip">Protected area</span>
                <h2>Welcome back, <?php echo htmlspecialchars($_SESSION["fullname"]); ?></h2>
                <p>
                    This is your premium authentication system project built with PHP, MySQL, sessions, password hashing, email reset flow, and a modern frontend. It is structured to be portfolio-ready and easy to expand later.
                </p>
            </div>

            <aside class="hero-side">
                <div class="avatar"><?php echo htmlspecialchars($initial); ?></div>
                <div>
                    <div class="side-title">@CodewithKokizZ</div>
                    <p class="side-text">
                        Built by PicartWeb as a polished auth-system showcase for login, signup, forgot password, code verification, and reset password.
                    </p>
                </div>
            </aside>
        </section>

        <section class="grid">
            <div class="card">
                <h3>Account Details</h3>
                <div class="info-list">
                    <div class="info-item">
                        <span>Full Name</span>
                        <strong><?php echo htmlspecialchars($_SESSION["fullname"]); ?></strong>
                    </div>
                    <div class="info-item">
                        <span>Username</span>
                        <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong>
                    </div>
                    <div class="info-item">
                        <span>Email</span>
                        <strong><?php echo htmlspecialchars($_SESSION["email"]); ?></strong>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3>Project Features</h3>
                <div class="feature-list">
                    <div class="feature-item">
                        <span class="dot"></span>
                        <strong>Secure signup and login</strong>
                    </div>
                    <div class="feature-item">
                        <span class="dot"></span>
                        <strong>Session-protected routes</strong>
                    </div>
                    <div class="feature-item">
                        <span class="dot"></span>
                        <strong>Password hashing</strong>
                    </div>
                    <div class="feature-item">
                        <span class="dot"></span>
                        <strong>Email reset code flow</strong>
                    </div>
                </div>
            </div>

            <div class="card full">
                <h3>About This Project</h3>
                <div class="project-box">
                    <div class="mini-panel">
                        <h4>What it is</h4>
                        <p>
                            A portfolio auth system built locally with XAMPP using PHP and MySQL. It includes account creation, sign in, password recovery, email-based code verification, and a reset password flow.
                        </p>
                    </div>

                    <div class="mini-panel">
                        <h4>What can be added next</h4>
                        <ul>
                            <li>Profile picture upload</li>
                            <li>Remember me login</li>
                            <li>Role system for admin and users</li>
                            <li>Real Google OAuth login</li>
                            <li>Account settings page</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <p class="footer-note">
            Designed by <a href="https://www.picartweb.com">PicartWeb</a> · Handle: <a href="https://twitter.com/CodewithKokizZ">@CodewithKokizZ</a>
        </p>
    </main>
</body>
</html>