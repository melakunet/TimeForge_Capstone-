<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/db.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$project_id = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);
$deletion_reason = filter_input(INPUT_POST, 'deletion_reason') ?? 'No reason provided';

// SOFT DELETE - Mark as deleted instead of removing from database
if ($project_id != false) {
    // Only Admin or Client who owns the project should delete.
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    
    // Soft delete: Update deleted_at and deleted_by instead of DELETE
    $query = 'UPDATE projects 
              SET deleted_at = NOW(), 
                  deleted_by = :user_id,
                  deletion_reason = :reason,
                  status = "archived"
              WHERE id = :project_id';
    
    $statement = $pdo->prepare($query);
    $statement->bindValue(':project_id', $project_id);
    $statement->bindValue(':user_id', $user_id);
    $statement->bindValue(':reason', $deletion_reason);
    $success = $statement->execute();
    $statement->closeCursor();
    
    // Log the deletion in audit logs
    if ($success) {
        logAuditAction($user_id, 'project_archived', $_SERVER['REMOTE_ADDR']);
    }
}

// Redirect to index
header('Location: index.php');
exit();
?>
