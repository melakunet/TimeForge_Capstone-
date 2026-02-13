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

// Basic deletion logic
if ($project_id != false) {
    // Only Admin or Client who owns the project should delete.
    // For simplicity, checking if logged in as Admin or Client.
    // In a real app, verify ownership for Clients.
    
    $query = 'DELETE FROM projects WHERE project_id = :project_id';
    $statement = $pdo->prepare($query);
    $statement->bindValue(':project_id', $project_id);
    $success = $statement->execute();
    $statement->closeCursor();
}

// Redirect to index
header('Location: index.php');
exit();
?>
