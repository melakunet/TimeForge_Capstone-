<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

requireLogin();

$role = $_SESSION['role'] ?? null;
if (!in_array($role, ['admin', 'freelancer'], true)) {
    include __DIR__ . '/includes/403.php';
    exit();
}

$project_id = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);
$project_name = filter_input(INPUT_POST, 'project_name');
$description = filter_input(INPUT_POST, 'description');
$client_id = filter_input(INPUT_POST, 'client_id', FILTER_VALIDATE_INT);
$hourly_rate = filter_input(INPUT_POST, 'hourly_rate', FILTER_VALIDATE_FLOAT);
$budget = filter_input(INPUT_POST, 'budget', FILTER_VALIDATE_FLOAT);
$deadline = filter_input(INPUT_POST, 'deadline');
$status = filter_input(INPUT_POST, 'status');

$allowed_statuses = ['active', 'completed', 'archived'];

if (!$project_id || $project_name === null || $project_name === '' || $client_id === false || $hourly_rate === false || $status === null || !in_array($status, $allowed_statuses, true)) {
    setFlash('error', 'Invalid data. Check all required fields.');
    header('Location: edit_project.php?id=' . urlencode((string)$project_id));
    exit();
}

// Ensure project exists and not deleted
$stmt = $pdo->prepare('SELECT id FROM projects WHERE id = :id AND deleted_at IS NULL LIMIT 1');
$stmt->bindValue(':id', $project_id, PDO::PARAM_INT);
$stmt->execute();
$exists = $stmt->fetchColumn();
$stmt->closeCursor();

if (!$exists) {
    setFlash('error', 'Project not found.');
    header('Location: index.php');
    exit();
}

// Ensure client exists and active
$cstmt = $pdo->prepare('SELECT id FROM clients WHERE id = :id AND is_active = 1 LIMIT 1');
$cstmt->bindValue(':id', $client_id, PDO::PARAM_INT);
$cstmt->execute();
$clientExists = $cstmt->fetchColumn();
$cstmt->closeCursor();

if (!$clientExists) {
    setFlash('error', 'Selected client does not exist or is inactive.');
    header('Location: edit_project.php?id=' . urlencode((string)$project_id));
    exit();
}

$query = 'UPDATE projects
          SET project_name = :project_name,
              description = :description,
              client_id = :client_id,
              hourly_rate = :hourly_rate,
              budget = :budget,
              deadline = :deadline,
              status = :status
          WHERE id = :project_id';

$statement = $pdo->prepare($query);
$statement->bindValue(':project_id', $project_id, PDO::PARAM_INT);
$statement->bindValue(':project_name', $project_name);
$statement->bindValue(':description', $description);
$statement->bindValue(':client_id', $client_id, PDO::PARAM_INT);
$statement->bindValue(':hourly_rate', $hourly_rate);
$statement->bindValue(':budget', $budget);
$statement->bindValue(':deadline', $deadline);
$statement->bindValue(':status', $status);
$statement->execute();
$statement->closeCursor();

logAuditAction((int)($_SESSION['user_id'] ?? 0), 'project_updated', $_SERVER['REMOTE_ADDR'] ?? null);

setFlash('success', 'Project updated successfully.');
header('Location: index.php');
exit();
