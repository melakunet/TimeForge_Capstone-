<?php
/**
 * src/Controllers/ProjectController.php
 * Handles add, edit, delete (archive), and restore project POST processing.
 * Entry points: add_project_process.php, edit_project_process.php,
 * delete_project.php, restore_project.php
 */

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/flash.php';
require_once __DIR__ . '/../../db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'add';

// ── ADD PROJECT ───────────────────────────────────────────────────────────
if ($action === 'add') {

    if (!isLoggedIn()) { header('Location: /TimeForge_Capstone/login.php'); exit; }
    if (!hasRole('admin') && !hasRole('freelancer')) {
        setFlash('danger', 'You do not have permission to add projects.');
        header('Location: /TimeForge_Capstone/index.php'); exit;
    }

    $project_name = filter_input(INPUT_POST, 'project_name');
    $description  = filter_input(INPUT_POST, 'description');
    $client_id    = filter_input(INPUT_POST, 'client_id',   FILTER_VALIDATE_INT);
    $hourly_rate  = filter_input(INPUT_POST, 'hourly_rate', FILTER_VALIDATE_FLOAT);
    $budget       = filter_input(INPUT_POST, 'budget',      FILTER_VALIDATE_FLOAT);
    $deadline     = filter_input(INPUT_POST, 'deadline');
    $status       = filter_input(INPUT_POST, 'status');

    if ($project_name === null || $client_id === false || $hourly_rate === false || $status === null) {
        setFlash('danger', 'Invalid data. Check all required fields.');
        header('Location: /TimeForge_Capstone/add_project.php'); exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO projects (project_name, description, client_id, hourly_rate, budget, deadline, status, created_by, company_id)
        VALUES (:project_name, :description, :client_id, :hourly_rate, :budget, :deadline, :status, :created_by, :company_id)
    ");
    $stmt->bindValue(':project_name', $project_name);
    $stmt->bindValue(':description',  $description);
    $stmt->bindValue(':client_id',    $client_id);
    $stmt->bindValue(':hourly_rate',  $hourly_rate);
    $stmt->bindValue(':budget',       $budget);
    $stmt->bindValue(':deadline',     $deadline);
    $stmt->bindValue(':status',       $status);
    $stmt->bindValue(':created_by',   $_SESSION['user_id']);
    $stmt->bindValue(':company_id',   $_SESSION['company_id'], PDO::PARAM_INT);
    $stmt->execute();

    setFlash('success', 'Project added successfully!');
    header('Location: /TimeForge_Capstone/index.php'); exit;
}

// ── EDIT PROJECT ──────────────────────────────────────────────────────────
if ($action === 'edit') {

    requireLogin();
    if (!in_array($_SESSION['role'] ?? null, ['admin', 'freelancer'], true)) {
        include __DIR__ . '/../../includes/403.php'; exit;
    }

    $project_id   = filter_input(INPUT_POST, 'project_id',  FILTER_VALIDATE_INT);
    $project_name = filter_input(INPUT_POST, 'project_name');
    $description  = filter_input(INPUT_POST, 'description');
    $client_id    = filter_input(INPUT_POST, 'client_id',   FILTER_VALIDATE_INT);
    $hourly_rate  = filter_input(INPUT_POST, 'hourly_rate', FILTER_VALIDATE_FLOAT);
    $budget       = filter_input(INPUT_POST, 'budget',      FILTER_VALIDATE_FLOAT);
    $deadline     = filter_input(INPUT_POST, 'deadline');
    $status       = filter_input(INPUT_POST, 'status');
    $screenshots_enabled = isset($_POST['screenshots_enabled']) ? 1 : 0; // Phase 9
    $allowed      = ['active', 'completed', 'archived'];

    if (!$project_id || $project_name === null || $project_name === '' ||
        $client_id === false || $hourly_rate === false || $status === null || !in_array($status, $allowed, true)) {
        setFlash('error', 'Invalid data. Check all required fields.');
        header('Location: /TimeForge_Capstone/edit_project.php?id=' . urlencode((string)$project_id)); exit;
    }

    $chk = $pdo->prepare('SELECT id FROM projects WHERE id = :id AND company_id = :cid AND deleted_at IS NULL LIMIT 1');
    $chk->bindValue(':id',  $project_id, PDO::PARAM_INT);
    $chk->bindValue(':cid', $_SESSION['company_id'], PDO::PARAM_INT);
    $chk->execute();
    if (!$chk->fetchColumn()) {
        setFlash('error', 'Project not found.');
        header('Location: /TimeForge_Capstone/index.php'); exit;
    }

    $cchk = $pdo->prepare('SELECT id FROM clients WHERE id = :id AND company_id = :cid AND is_active = 1 LIMIT 1');
    $cchk->bindValue(':id',  $client_id, PDO::PARAM_INT);
    $cchk->bindValue(':cid', $_SESSION['company_id'], PDO::PARAM_INT);
    $cchk->execute();
    if (!$cchk->fetchColumn()) {
        setFlash('error', 'Selected client does not exist or is inactive.');
        header('Location: /TimeForge_Capstone/edit_project.php?id=' . urlencode((string)$project_id)); exit;
    }

    $stmt = $pdo->prepare("
        UPDATE projects
        SET project_name = :project_name, description = :description, client_id = :client_id,
            hourly_rate = :hourly_rate, budget = :budget, deadline = :deadline, status = :status,
            screenshots_enabled = :screenshots_enabled
        WHERE id = :project_id
    ");
    $stmt->bindValue(':project_id',          $project_id,         PDO::PARAM_INT);
    $stmt->bindValue(':project_name',        $project_name);
    $stmt->bindValue(':description',         $description);
    $stmt->bindValue(':client_id',           $client_id,          PDO::PARAM_INT);
    $stmt->bindValue(':hourly_rate',         $hourly_rate);
    $stmt->bindValue(':budget',              $budget);
    $stmt->bindValue(':deadline',            $deadline);
    $stmt->bindValue(':status',              $status);
    $stmt->bindValue(':screenshots_enabled', $screenshots_enabled, PDO::PARAM_INT);
    $stmt->execute();

    logAuditAction((int)($_SESSION['user_id'] ?? 0), 'project_updated');
    setFlash('success', 'Project updated successfully.');
    header('Location: /TimeForge_Capstone/index.php'); exit;
}

// ── DELETE (ARCHIVE) PROJECT ──────────────────────────────────────────────
if ($action === 'delete') {

    requireLogin();
    if (!hasRole('admin')) {
        setFlash('error', 'Only admins can archive projects.');
        header('Location: /TimeForge_Capstone/index.php'); exit;
    }

    $project_id      = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);
    $deletion_reason = filter_input(INPUT_POST, 'deletion_reason') ?? 'No reason provided';

    if ($project_id === false || $project_id === null) {
        setFlash('error', 'Invalid project id.');
        header('Location: /TimeForge_Capstone/index.php'); exit;
    }

    $user_id = (int)$_SESSION['user_id'];
    try {
        $stmt = $pdo->prepare("
            UPDATE projects
            SET deleted_at = NOW(), deleted_by = :user_id, deletion_reason = :reason, status = 'archived'
            WHERE id = :project_id AND deleted_at IS NULL
        ");
        $stmt->bindValue(':project_id', $project_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id',    $user_id,    PDO::PARAM_INT);
        $stmt->bindValue(':reason',     $deletion_reason);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            logAuditAction($user_id, 'project_archived');
            setFlash('success', 'Project archived successfully.');
        } else {
            setFlash('error', 'Project not found or already archived.');
        }
    } catch (PDOException $e) {
        setFlash('error', 'Database error while archiving project.');
    }

    header('Location: /TimeForge_Capstone/index.php'); exit;
}

// ── RESTORE PROJECT ───────────────────────────────────────────────────────
if ($action === 'restore') {

    if (!isLoggedIn()) { header('Location: /TimeForge_Capstone/login.php'); exit; }
    if (($_SESSION['role'] ?? '') !== 'admin') {
        setFlash('error', 'Unauthorized access.');
        header('Location: /TimeForge_Capstone/index.php'); exit;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        setFlash('error', 'Invalid request method.');
        header('Location: /TimeForge_Capstone/index.php'); exit;
    }

    $project_id = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);
    if (!$project_id) {
        setFlash('error', 'Invalid project ID.');
        header('Location: /TimeForge_Capstone/index.php'); exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE projects SET deleted_at = NULL WHERE id = :id");
        $stmt->bindValue(':id', $project_id, PDO::PARAM_INT);
        $stmt->execute();
        logAuditAction((int)$_SESSION['user_id'], 'restore_project');
        setFlash('success', 'Project restored successfully.');
        header('Location: /TimeForge_Capstone/project_details.php?id=' . $project_id); exit;
    } catch (PDOException $e) {
        error_log('ProjectController restore: ' . $e->getMessage());
        setFlash('error', 'Database error occurred.');
        header('Location: /TimeForge_Capstone/index.php'); exit;
    }
}
