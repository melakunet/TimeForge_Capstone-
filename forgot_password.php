<?php
$page_title = 'Forgot Password';

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: /TimeForge_Capstone/index.php');
    exit;
}

$error   = '';
$success = '';

if (isset($_SESSION['forgot_error']))   { $error   = $_SESSION['forgot_error'];   unset($_SESSION['forgot_error']); }
if (isset($_SESSION['forgot_success'])) { $success = $_SESSION['forgot_success']; unset($_SESSION['forgot_success']); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password – TimeForge</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth_layout.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
</head>
<body>

<div class="auth-wrapper">
    <!-- Left Side -->
    <div class="auth-left">
        <div class="auth-brand">
            <a href="index.php" class="brand-link">
                <img src="icons/logo.png" alt="Logo">
                <span>TIMEFORGE</span>
            </a>
        </div>
        <div class="auth-hero-text">
            <h1>Reset your password.</h1>
            <p>Enter your email and we'll send you a secure reset link.</p>
        </div>
    </div>

    <!-- Right Side -->
    <div class="auth-right">
        <h2>Forgot Password</h2>

        <div class="auth-tabs">
            <a href="login.php">Login</a>
            <a href="register.php">Sign Up</a>
        </div>

        <?php if ($success): ?>
            <div class="alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="includes/forgot_process.php" method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            <div class="form-group">
                <label for="email">Your Email Address</label>
                <input type="email" name="email" id="email" required
                       placeholder="you@example.com"
                       value="<?= htmlspecialchars($_SESSION['forgot_email'] ?? ''); unset($_SESSION['forgot_email']); ?>">
            </div>

            <button type="submit" class="btn-auth-primary">Send Reset Link</button>

            <a href="login.php" class="forgot-password">Back to Login</a>
        </form>
    </div>
</div>

<script src="js/theme.js"></script>
</body>
</html>
