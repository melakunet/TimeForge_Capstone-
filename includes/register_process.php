<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /TimeForge_Capstone/register.php');
    exit;
}

$username         = filter_input(INPUT_POST, 'username');
$email            = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password         = filter_input(INPUT_POST, 'password');
$confirm_password = filter_input(INPUT_POST, 'confirm_password');
$full_name        = filter_input(INPUT_POST, 'full_name');
$role             = filter_input(INPUT_POST, 'role');
$terms            = filter_input(INPUT_POST, 'terms');

$_SESSION['register_form_data'] = [
    'username'  => $username,
    'email'     => $email,
    'full_name' => $full_name,
    'role'      => $role,
];

$errors = [];

if ($terms === null) $errors[] = 'You must agree to the Terms & Conditions';
if ($username == null || $email == null || $password == null || $confirm_password == null || $full_name == null)
    $errors[] = 'Invalid registration data. Check all fields and try again.';
if (strlen($password) < 8)              $errors[] = 'Password must be at least 8 characters long.';
if (!preg_match('/[A-Z]/', $password))  $errors[] = 'Password must contain at least one uppercase letter.';
if (!preg_match('/[a-z]/', $password))  $errors[] = 'Password must contain at least one lowercase letter.';
if (!preg_match('/[0-9]/', $password))  $errors[] = 'Password must contain at least one number.';

if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    header('Location: /TimeForge_Capstone/register.php');
    exit;
}

$result = registerUser($username, $email, $password, $confirm_password, $full_name, $role);

if ($result['success']) {
    unset($_SESSION['register_form_data']);
    $_SESSION['register_success'] = 'Thank you, ' . htmlspecialchars($username) . '. You may now log in.';
    header('Location: /TimeForge_Capstone/login.php');
    exit;
}

$_SESSION['register_errors'] = array_merge($errors, $result['errors'] ?? []);
header('Location: /TimeForge_Capstone/register.php');
exit;
