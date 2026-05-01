<?php
// Session Configuration

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Set session cookie parameters
    session_set_cookie_params([
        'lifetime' => 86400,  // 24 hours
        'path' => '/',
        'domain' => '',
        'secure' => false,    // Set to true in production with HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    session_start();
}

// Development auth bypass (set to false when you want to enforce login)
// Allows reviewers/advisors to navigate without credentials during development.
if (!defined('DEV_AUTH_BYPASS')) {
    define('DEV_AUTH_BYPASS', false);
}

// Session timeout (in seconds)
define('SESSION_TIMEOUT', 3600);  // 1 hour

// Check session timeout
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        // Session expired
        session_destroy();
        header('Location: /TimeForge_Capstone/login.php?timeout=1');
        exit();
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();

// ── Live presence: update last_active_at on every page load (throttled to 1/min) ──
// This ensures "Last seen" is always accurate regardless of which page the user is on.
if (isset($_SESSION['user_id']) && isset($pdo)) {
    $now_ts = time();
    if (!isset($_SESSION['_presence_ping']) || ($now_ts - $_SESSION['_presence_ping']) >= 60) {
        try {
            $pdo->prepare("UPDATE users SET last_active_at = NOW() WHERE id = ? LIMIT 1")
                ->execute([$_SESSION['user_id']]);
            $_SESSION['_presence_ping'] = $now_ts;
        } catch (Exception $e) { /* non-fatal */ }
    }
}

// Initialize theme in session if not set
if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'light';
}
