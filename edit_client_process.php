<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/db.php';

// Ensure user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Only admin and freelancers can edit clients
if (!hasRole('admin') && !hasRole('freelancer')) {
    $_SESSION['error_message'] = 'You do not have permission to edit clients.';
    header('Location: clients.php');
    exit();
}

// Validate form submission
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: clients.php');
    exit();
}

// Get form data
$client_id = $_POST['client_id'] ?? null;
$client_name = trim($_POST['client_name'] ?? '');
$company_name = trim($_POST['company_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$is_active = isset($_POST['is_active']) ? 1 : 0;

// Validation
$errors = [];

if (!$client_id || !is_numeric($client_id)) {
    $errors[] = 'Invalid client ID.';
}

if (empty($client_name)) {
    $errors[] = 'Client name is required.';
}

if (empty($email)) {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

// Check if email already exists for a different client
if (empty($errors)) {
    $check_email_query = $pdo->prepare("SELECT id FROM clients WHERE email = :email AND id != :client_id");
    $check_email_query->execute([
        ':email' => $email,
        ':client_id' => $client_id
    ]);
    
    if ($check_email_query->fetch()) {
        $errors[] = 'A different client with this email address already exists.';
    }
}

// If there are validation errors, redirect back
if (!empty($errors)) {
    $_SESSION['error_message'] = implode(' ', $errors);
    header('Location: edit_client.php?id=' . $client_id);
    exit();
}

try {
    // Update client in database
    $update_query = "UPDATE clients 
                     SET client_name = :client_name,
                         company_name = :company_name,
                         email = :email,
                         phone = :phone,
                         address = :address,
                         is_active = :is_active
                     WHERE id = :client_id";
    
    $stmt = $pdo->prepare($update_query);
    $result = $stmt->execute([
        ':client_name' => $client_name,
        ':company_name' => $company_name ?: null,
        ':email' => $email,
        ':phone' => $phone ?: null,
        ':address' => $address ?: null,
        ':is_active' => $is_active,
        ':client_id' => $client_id
    ]);

    if ($result) {
        $_SESSION['success_message'] = 'Client updated successfully!';
        header('Location: clients.php');
        exit();
    } else {
        throw new Exception('Failed to update client in database.');
    }
    
} catch (PDOException $e) {
    error_log("Database error in edit_client_process.php: " . $e->getMessage());
    $_SESSION['error_message'] = 'An error occurred while updating the client. Please try again.';
    header('Location: edit_client.php?id=' . $client_id);
    exit();
} catch (Exception $e) {
    error_log("Error in edit_client_process.php: " . $e->getMessage());
    $_SESSION['error_message'] = $e->getMessage();
    header('Location: edit_client.php?id=' . $client_id);
    exit();
}
