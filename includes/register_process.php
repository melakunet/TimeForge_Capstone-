<?php
// includes/register_process.php
// Registration Processing Logic

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/auth.php';

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /TimeForge_Capstone/register.php');
    exit();
}

// Get inputs using filter_input (Instructor Style)
$username = filter_input(INPUT_POST, 'username');
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = filter_input(INPUT_POST, 'password');
$confirm_password = filter_input(INPUT_POST, 'confirm_password');
$full_name = filter_input(INPUT_POST, 'full_name');
$role = filter_input(INPUT_POST, 'role');
$terms = filter_input(INPUT_POST, 'terms');

// Preserve form data in session for repopulation
$_SESSION['register_form_data'] = [
    'username' => $username,
    'email' => $email,
    'full_name' => $full_name,
    'role' => $role
];

// Basic Validation
$errors = [];

if ($terms === null) {
    $errors[] = "You must agree to the Terms & Conditions";
}

if ($username == null || $email == null || $password == null || $confirm_password == null || $full_name == null) {
    $errors[] = "Invalid registration data, Check all fields and try again.";
}

if (empty($errors)) {
    // Call auth.php registration function which handles duplicate checks and hashing
    $result = registerUser($username, $email, $password, $confirm_password, $full_name, $role);
    
    if ($result['success']) {
        // Registration Successful
        unset($_SESSION['register_form_data']); // Clear form data
        
        // Instructor uses email sending here (commented out for now as no mail server is set up, but structure is ready)
        /*
        $to_address = $email;
        $to_name = $full_name;
        $subject = 'TimeForge - Registration Complete';
        $body = '<p>Thanks for registering with our site.</p><p>TimeForge Team</p>';
        try {
            // send_mail($to_address, $to_name, ...); 
        } catch (Exception $ex) {
             // log error
        }
        */
        
        $_SESSION['register_success'] = "Thank you, " . htmlspecialchars($username) . " for registering. You may now log in.";
        header('Location: /TimeForge_Capstone/login.php');
        exit();
    } else {
        $errors = array_merge($errors, $result['errors']);
    }
}

// Check for errors
if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    header('Location: /TimeForge_Capstone/register.php');
    exit();
}
?>
