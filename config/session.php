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

// Initialize theme in session if not set
if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'light';
}
?>
