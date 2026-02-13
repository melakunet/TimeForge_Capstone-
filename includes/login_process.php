<?php
// includes/login_process.php
// Login Processing Logic

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/redirect.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
     header('Location: /TimeForge_Capstone/login.php');
     exit();
}

// Get inputs using filter_input (Instructor Style)
$username = filter_input(INPUT_POST, 'username');
$password = filter_input(INPUT_POST, 'password');

// Validate input
if ($username == null || $password == null) {
    $_SESSION['login_error'] = 'Please enter both username and password';
    $_SESSION['login_username'] = $username; // Preserve username
    header('Location: /TimeForge_Capstone/login.php');
    exit();
}

// Attempt authentication
$result = authenticateUser($username, $password);

if ($result['success']) {
    // Authentication successful
    $user = $result['user'];
    
    // Redirect based on role
    redirectBasedOnRole($user['role']);
    
} else {
    // Authentication failed
    $_SESSION['login_error'] = $result['message'];
    $_SESSION['login_username'] = $username; // Preserve username
    header('Location: /TimeForge_Capstone/login.php');
    exit();
}
?>
