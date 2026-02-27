<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

// Ensure user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Only admin and freelancers can add clients
if (!hasRole('admin') && !hasRole('freelancer')) {
    setFlash('error', 'You do not have permission to add clients.');
    header('Location: index.php');
    exit();
}

// Validate form submission
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: add_client.php');
    exit();
}

// Get form data and sanitize
$client_name = trim($_POST['client_name'] ?? '');
$company_name = trim($_POST['company_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$is_active = isset($_POST['is_active']) ? 1 : 0;
$created_by = $_SESSION['user_id'];

// Validation
$errors = [];

if (empty($client_name)) {
    $errors[] = 'Client name is required.';
}

if (empty($email)) {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

// Check if email already exists
if (empty($errors)) {
    $check_email_query = $pdo->prepare("SELECT id FROM clients WHERE email = :email");
    $check_email_query->execute([':email' => $email]);
    
    if ($check_email_query->fetch()) {
        $errors[] = 'A client with this email address already exists.';
    }
}

// If there are validation errors, redirect back
if (!empty($errors)) {
    setFlash('error', implode(' ', $errors));
    header('Location: clients.php');
    exit();
}

try {
    // Insert client into database
    $insert_query = "INSERT INTO clients 
                     (client_name, company_name, email, phone, address, created_by, is_active) 
                     VALUES 
                     (:client_name, :company_name, :email, :phone, :address, :created_by, :is_active)";
    
    $stmt = $pdo->prepare($insert_query);
    $result = $stmt->execute([
        ':client_name' => $client_name,
        ':company_name' => $company_name ?: null,
        ':email' => $email,
        ':phone' => $phone ?: null,
        ':address' => $address ?: null,
        ':created_by' => $created_by,
        ':is_active' => $is_active
    ]);

    if ($result) {
        setFlash('success', 'Client added successfully!');
        header('Location: clients.php');
        exit();
    } else {
        throw new Exception('Failed to insert client into database.');
    }
    
} catch (PDOException $e) {
    error_log("Database error in add_client_process.php: " . $e->getMessage());
    setFlash('error', 'An error occurred while adding the client. Please try again.');
    header('Location: clients.php');
    exit();
} catch (Exception $e) {
    error_log("Error in add_client_process.php: " . $e->getMessage());
    setFlash('error', $e->getMessage());
    header('Location: clients.php');
    exit();
}
