<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/redirect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /TimeForge_Capstone/login.php');
    exit;
}

$username = filter_input(INPUT_POST, 'username');
$password = filter_input(INPUT_POST, 'password');

if ($username == null || $password == null) {
    $_SESSION['login_error'] = 'Please enter both username and password';
    $_SESSION['login_username'] = $username;
    header('Location: /TimeForge_Capstone/login.php');
    exit;
}

$result = authenticateUser($username, $password);

if ($result['success']) {
    redirectBasedOnRole($result['user']['role']);
} else {
    $_SESSION['login_error'] = $result['message'];
    $_SESSION['login_username'] = $username;
    header('Location: /TimeForge_Capstone/login.php');
    exit;
}
