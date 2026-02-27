<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

requireLogin();

if (!hasRole('admin')) {
    setFlash('error', 'Only admins can archive projects.');
    header('Location: index.php');
    exit();
}

$project_id = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);
$deletion_reason = filter_input(INPUT_POST, 'deletion_reason') ?? 'No reason provided';

// SOFT DELETE - Mark as deleted instead of removing from database
if ($project_id === false || $project_id === null) {
    setFlash('error', 'Invalid project id.');
    header('Location: index.php');
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Soft delete: Update deleted_at and deleted_by instead of DELETE
$query = 'UPDATE projects 
          SET deleted_at = NOW(), 
              deleted_by = :user_id,
              deletion_reason = :reason,
              status = "archived"
          WHERE id = :project_id AND deleted_at IS NULL';

try {
    $statement = $pdo->prepare($query);
    $statement->bindValue(':project_id', $project_id, PDO::PARAM_INT);
    $statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindValue(':reason', $deletion_reason);
    $statement->execute();
    $rowCount = $statement->rowCount();
    $statement->closeCursor();

    if ($rowCount > 0) {
        logAuditAction($user_id, 'project_archived', $_SERVER['REMOTE_ADDR'] ?? null);
        setFlash('success', 'Project archived successfully.');
    } else {
        setFlash('error', 'Project not found or already archived.');
    }
} catch (PDOException $e) {
    setFlash('error', 'Database error while archiving project.');
}

// Redirect to index
header('Location: index.php');
exit();
?>
