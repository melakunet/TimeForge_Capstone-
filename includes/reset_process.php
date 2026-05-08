<?php
/**
 * includes/reset_process.php
 * Validates the reset token and updates the user's password.
 */
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /TimeForge_Capstone/login.php');
    exit;
}

verifyCsrfToken();

$raw_token       = trim($_POST['token']            ?? '');
$email           = trim($_POST['email']            ?? '');
$password        = $_POST['password']              ?? '';
$confirm         = $_POST['confirm_password']      ?? '';

// ── Validate inputs ───────────────────────────────────────────────────────────
$errors = [];

if (!$raw_token || !$email) {
    $errors[] = 'Invalid reset request. Please request a new link.';
}

if (strlen($password) < 8)               $errors[] = 'Password must be at least 8 characters.';
if (!preg_match('/[A-Z]/', $password))   $errors[] = 'Password must contain at least one uppercase letter.';
if (!preg_match('/[a-z]/', $password))   $errors[] = 'Password must contain at least one lowercase letter.';
if (!preg_match('/[0-9]/', $password))   $errors[] = 'Password must contain at least one number.';
if ($password !== $confirm)              $errors[] = 'Passwords do not match.';

if (!empty($errors)) {
    $_SESSION['reset_error'] = implode(' ', $errors);
    header('Location: /TimeForge_Capstone/reset_password.php?token=' . urlencode($raw_token) . '&email=' . urlencode($email));
    exit;
}

// ── Verify token in DB ────────────────────────────────────────────────────────
$hashed = hash('sha256', $raw_token);

try {
    $stmt = $pdo->prepare("
        SELECT id FROM users
         WHERE email = ?
           AND password_reset_token   = ?
           AND password_reset_expires > NOW()
           AND is_active = 1
         LIMIT 1
    ");
    $stmt->execute([$email, $hashed]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('reset_process DB error: ' . $e->getMessage());
    $user = null;
}

if (!$user) {
    $_SESSION['reset_error'] = 'This reset link is invalid or has expired. Please request a new one.';
    header('Location: /TimeForge_Capstone/reset_password.php?token=' . urlencode($raw_token) . '&email=' . urlencode($email));
    exit;
}

// ── Update password and clear token ───────────────────────────────────────────
$new_hash = hashPassword($password);

try {
    $upd = $pdo->prepare("
        UPDATE users
           SET password               = ?,
               password_reset_token   = NULL,
               password_reset_expires = NULL,
               updated_at             = NOW()
         WHERE id = ?
    ");
    $upd->execute([$new_hash, $user['id']]);
} catch (PDOException $e) {
    error_log('reset_process update error: ' . $e->getMessage());
    $_SESSION['reset_error'] = 'A server error occurred. Please try again.';
    header('Location: /TimeForge_Capstone/reset_password.php?token=' . urlencode($raw_token) . '&email=' . urlencode($email));
    exit;
}

$_SESSION['register_success'] = 'Your password has been reset. You can now log in with your new password.';
header('Location: /TimeForge_Capstone/login.php');
exit;
