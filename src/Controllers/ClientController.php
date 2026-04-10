<?php
/**
 * src/Controllers/ClientController.php
 * Handles add and edit client POST processing.
 * Loaded by add_client_process.php and edit_client_process.php (backward-compat wrappers).
 */

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/flash.php';
require_once __DIR__ . '/../../db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'add';

// ── ADD CLIENT ────────────────────────────────────────────────────────────
if ($action === 'add') {

    if (!isLoggedIn()) { header('Location: /TimeForge_Capstone/login.php'); exit; }
    if (!hasRole('admin') && !hasRole('freelancer')) {
        setFlash('error', 'You do not have permission to add clients.');
        header('Location: /TimeForge_Capstone/index.php'); exit;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /TimeForge_Capstone/add_client.php'); exit; }

    $client_name  = trim($_POST['client_name']  ?? '');
    $company_name = trim($_POST['company_name'] ?? '');
    $email        = trim($_POST['email']        ?? '');
    $phone        = trim($_POST['phone']        ?? '');
    $address      = trim($_POST['address']      ?? '');
    $is_active    = isset($_POST['is_active']) ? 1 : 0;
    $created_by   = $_SESSION['user_id'];

    $errors = [];
    if (empty($client_name)) $errors[] = 'Client name is required.';
    if (empty($email))       $errors[] = 'Email is required.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';

    if (empty($errors)) {
        $chk = $pdo->prepare("SELECT id FROM clients WHERE email = :email");
        $chk->execute([':email' => $email]);
        if ($chk->fetch()) $errors[] = 'A client with this email address already exists.';
    }

    if (!empty($errors)) {
        setFlash('error', implode(' ', $errors));
        header('Location: /TimeForge_Capstone/clients.php'); exit;
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO clients (client_name, company_name, email, phone, address, created_by, company_id, is_active)
            VALUES (:client_name, :company_name, :email, :phone, :address, :created_by, :company_id, :is_active)
        ");
        $stmt->execute([
            ':client_name'  => $client_name,
            ':company_name' => $company_name ?: null,
            ':email'        => $email,
            ':phone'        => $phone    ?: null,
            ':address'      => $address  ?: null,
            ':created_by'   => $created_by,
            ':company_id'   => $_SESSION['company_id'],
            ':is_active'    => $is_active,
        ]);
        setFlash('success', 'Client added successfully!');
        header('Location: /TimeForge_Capstone/clients.php'); exit;
    } catch (PDOException $e) {
        error_log('ClientController add: ' . $e->getMessage());
        setFlash('error', 'An error occurred while adding the client. Please try again.');
        header('Location: /TimeForge_Capstone/clients.php'); exit;
    }
}

// ── EDIT CLIENT ───────────────────────────────────────────────────────────
if ($action === 'edit') {

    if (!isLoggedIn()) { header('Location: /TimeForge_Capstone/login.php'); exit; }
    if (!hasRole('admin') && !hasRole('freelancer')) {
        setFlash('error', 'You do not have permission to edit clients.');
        header('Location: /TimeForge_Capstone/clients.php'); exit;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /TimeForge_Capstone/clients.php'); exit; }

    $client_id    = $_POST['client_id'] ?? null;
    $client_name  = trim($_POST['client_name']  ?? '');
    $company_name = trim($_POST['company_name'] ?? '');
    $email        = trim($_POST['email']        ?? '');
    $phone        = trim($_POST['phone']        ?? '');
    $address      = trim($_POST['address']      ?? '');
    $is_active    = isset($_POST['is_active']) ? 1 : 0;

    $errors = [];
    if (!$client_id || !is_numeric($client_id)) $errors[] = 'Invalid client ID.';
    if (empty($client_name))  $errors[] = 'Client name is required.';
    if (empty($email))        $errors[] = 'Email is required.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';

    if (empty($errors)) {
        $chk = $pdo->prepare("SELECT id FROM clients WHERE email = :email AND id != :client_id");
        $chk->execute([':email' => $email, ':client_id' => $client_id]);
        if ($chk->fetch()) $errors[] = 'A different client with this email address already exists.';
    }

    if (!empty($errors)) {
        setFlash('error', implode(' ', $errors));
        header('Location: /TimeForge_Capstone/edit_client.php?id=' . $client_id); exit;
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE clients
            SET client_name = :client_name, company_name = :company_name,
                email = :email, phone = :phone, address = :address, is_active = :is_active
            WHERE id = :client_id
        ");
        $stmt->execute([
            ':client_name'  => $client_name,
            ':company_name' => $company_name ?: null,
            ':email'        => $email,
            ':phone'        => $phone   ?: null,
            ':address'      => $address ?: null,
            ':is_active'    => $is_active,
            ':client_id'    => $client_id,
        ]);
        setFlash('success', 'Client updated successfully!');
        header('Location: /TimeForge_Capstone/clients.php'); exit;
    } catch (PDOException $e) {
        error_log('ClientController edit: ' . $e->getMessage());
        setFlash('error', 'An error occurred while updating the client. Please try again.');
        header('Location: /TimeForge_Capstone/edit_client.php?id=' . $client_id); exit;
    }
}
