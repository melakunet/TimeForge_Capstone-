<?php
/**
 * reset_password.php
 * Shows the "choose new password" form after clicking the email link.
 * Validates: token exists in URL, matches hashed value in DB, not expired.
 */
$page_title = 'Reset Password';

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/db.php';

if (isLoggedIn()) {
    header('Location: ' . APP_BASE . '/index.php');
    exit;
}

$raw_token = trim($_GET['token'] ?? '');
$email     = trim($_GET['email'] ?? '');
$error     = '';

if (isset($_SESSION['reset_error']))   { $error = $_SESSION['reset_error']; unset($_SESSION['reset_error']); }

$valid = false;
$user  = null;

if ($raw_token && $email) {
    $hashed = hash('sha256', $raw_token);
    try {
        $stmt = $pdo->prepare("
            SELECT id, full_name
              FROM users
             WHERE email = ?
               AND password_reset_token   = ?
               AND password_reset_expires > NOW()
               AND is_active = 1
             LIMIT 1
        ");
        $stmt->execute([$email, $hashed]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $valid = (bool)$user;
    } catch (PDOException $e) {
        error_log('reset_password validate error: ' . $e->getMessage());
    }
}

if (!$valid && !$error) {
    $error = 'This password reset link is invalid or has expired. Please request a new one.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password – TimeForge</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth_layout.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-left">
        <div class="auth-brand">
            <a href="index.php" class="brand-link">
                <img src="icons/logo.png" alt="Logo">
                <span>TIMEFORGE</span>
            </a>
        </div>
        <div class="auth-hero-text">
            <h1>Choose a new password.</h1>
            <p>Make it strong — at least 8 characters with upper, lower, and a number.</p>
        </div>
    </div>

    <div class="auth-right">
        <h2>Reset Password</h2>

        <?php if ($error): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <p style="margin-top:1.5rem; text-align:center;">
                <a href="forgot_password.php" class="btn-auth-primary" style="display:inline-block;">Request a New Link</a>
            </p>
        <?php else: ?>
            <form action="includes/reset_process.php" method="POST" class="auth-form" id="reset-form">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="token" value="<?= htmlspecialchars($raw_token) ?>">
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

                <div class="form-group">
                    <label for="password">New Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="password" required
                               placeholder="At least 8 characters" autocomplete="new-password">
                    </div>
                    <ul id="pw-rules" style="font-size:.8rem; color:#94a3b8; margin:.4rem 0 0 1rem; padding:0; list-style:disc;">
                        <li id="r-len">At least 8 characters</li>
                        <li id="r-upper">One uppercase letter</li>
                        <li id="r-lower">One lowercase letter</li>
                        <li id="r-digit">One number</li>
                    </ul>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required
                           placeholder="Repeat password" autocomplete="new-password">
                    <span id="match-msg" style="font-size:.8rem; display:none;"></span>
                </div>

                <button type="submit" class="btn-auth-primary" id="submit-btn">Set New Password</button>
                <a href="login.php" class="forgot-password">Back to Login</a>
            </form>
        <?php endif; ?>
    </div>
</div>

<script src="js/theme.js"></script>
<script>
(function () {
    const pw  = document.getElementById('password');
    const cpw = document.getElementById('confirm_password');
    const msg = document.getElementById('match-msg');
    const btn = document.getElementById('submit-btn');

    const rules = {
        'r-len':   v => v.length >= 8,
        'r-upper': v => /[A-Z]/.test(v),
        'r-lower': v => /[a-z]/.test(v),
        'r-digit': v => /[0-9]/.test(v),
    };

    function checkPw() {
        if (!pw) return;
        const val = pw.value;
        let allOk = true;
        Object.entries(rules).forEach(([id, fn]) => {
            const el = document.getElementById(id);
            const ok = fn(val);
            el.style.color = ok ? '#22c55e' : '#94a3b8';
            if (!ok) allOk = false;
        });
        return allOk;
    }

    function checkMatch() {
        if (!pw || !cpw) return false;
        const match = pw.value === cpw.value && cpw.value.length > 0;
        msg.style.display = cpw.value.length > 0 ? 'block' : 'none';
        msg.textContent   = match ? '✓ Passwords match' : '✗ Passwords do not match';
        msg.style.color   = match ? '#22c55e' : '#ef4444';
        return match;
    }

    if (pw)  pw.addEventListener('input',  () => { checkPw(); checkMatch(); });
    if (cpw) cpw.addEventListener('input', () => checkMatch());

    const form = document.getElementById('reset-form');
    if (form) {
        form.addEventListener('submit', e => {
            if (!checkPw() || !checkMatch()) {
                e.preventDefault();
                alert('Please satisfy all password requirements before submitting.');
            }
        });
    }
})();
</script>
</body>
</html>
