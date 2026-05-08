<?php
/**
 * includes/forgot_process.php
 * Handles "Forgot Password" form submission.
 * Generates a secure token, stores it hashed in the DB, and emails a reset link.
 */
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . APP_BASE . '/forgot_password.php');
    exit;
}

verifyCsrfToken();

$email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ?? '');

if (!$email) {
    $_SESSION['forgot_error'] = 'Please enter a valid email address.';
    header('Location: ' . APP_BASE . '/forgot_password.php');
    exit;
}

$_SESSION['forgot_email'] = $email;

// Look up the user — always show the same message regardless of whether email exists (anti-enumeration)
try {
    $stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE email = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('forgot_process DB error: ' . $e->getMessage());
    $user = null;
}

if ($user) {
    // Generate a secure random token
    $raw_token   = bin2hex(random_bytes(32)); // 64-char hex string
    $hashed_token = hash('sha256', $raw_token); // store hash, send raw
    $expires      = date('Y-m-d H:i:s', time() + 3600); // 1 hour

    try {
        $upd = $pdo->prepare("
            UPDATE users
               SET password_reset_token   = ?,
                   password_reset_expires = ?
             WHERE id = ?
        ");
        $upd->execute([$hashed_token, $expires, $user['id']]);
    } catch (PDOException $e) {
        error_log('forgot_process token save error: ' . $e->getMessage());
        $_SESSION['forgot_error'] = 'A server error occurred. Please try again.';
        header('Location: ' . APP_BASE . '/forgot_password.php');
        exit;
    }

    // Build reset URL
    $protocol  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $reset_url = "$protocol://$host" . APP_BASE . "/reset_password.php?token=" . urlencode($raw_token) . "&email=" . urlencode($email);

    $name     = htmlspecialchars($user['full_name'] ?: 'User');
    $html_body = "
        <div style='font-family:sans-serif; max-width:520px; margin:auto;'>
            <h2 style='color:#3b82f6;'>TimeForge — Password Reset</h2>
            <p>Hi <strong>{$name}</strong>,</p>
            <p>We received a request to reset your TimeForge password. Click the button below to choose a new password. This link expires in <strong>1 hour</strong>.</p>
            <p style='text-align:center; margin:2rem 0;'>
                <a href='{$reset_url}' style='background:#3b82f6; color:#fff; padding:.75rem 2rem; border-radius:6px; text-decoration:none; font-weight:700;'>Reset My Password</a>
            </p>
            <p>Or copy this link into your browser:</p>
            <p style='word-break:break-all; color:#64748b; font-size:.9rem;'>{$reset_url}</p>
            <hr style='border-color:#e2e8f0; margin:2rem 0;'>
            <p style='color:#94a3b8; font-size:.8rem;'>If you did not request a password reset, you can safely ignore this email.</p>
        </div>
    ";

    sendEmail($email, $user['full_name'] ?: 'User', 'TimeForge — Password Reset Request', $html_body);
}

// Always show success (anti-enumeration: don't reveal whether email exists)
unset($_SESSION['forgot_email']);
$_SESSION['forgot_success'] = 'If that email is registered, a reset link has been sent. Check your inbox.';
header('Location: ' . APP_BASE . '/forgot_password.php');
exit;
