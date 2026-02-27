<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Only admin and freelancers can add projects
if (!hasRole('admin') && !hasRole('freelancer')) {
    setFlash('danger', 'You do not have permission to add projects.');
    header('Location: index.php');
    exit();
}

$project_name = filter_input(INPUT_POST, 'project_name');
$description = filter_input(INPUT_POST, 'description');
$client_id = filter_input(INPUT_POST, 'client_id', FILTER_VALIDATE_INT);
$hourly_rate = filter_input(INPUT_POST, 'hourly_rate', FILTER_VALIDATE_FLOAT);
$budget = filter_input(INPUT_POST, 'budget', FILTER_VALIDATE_FLOAT);
$deadline = filter_input(INPUT_POST, 'deadline');
$status = filter_input(INPUT_POST, 'status');

if ($project_name == null || $client_id === false || $hourly_rate === false || $status == null) {
    setFlash('danger', 'Invalid data. Check all required fields.');
    header('Location: add_project.php');
    exit();
} else {
    // Add the project
    $query = 'INSERT INTO projects (project_name, description, client_id, hourly_rate, budget, deadline, status, created_by)
              VALUES (:project_name, :description, :client_id, :hourly_rate, :budget, :deadline, :status, :created_by)';
    $statement = $pdo->prepare($query);
    $statement->bindValue(':project_name', $project_name);
    $statement->bindValue(':description', $description);
    $statement->bindValue(':client_id', $client_id);
    $statement->bindValue(':hourly_rate', $hourly_rate);
    $statement->bindValue(':budget', $budget);
    $statement->bindValue(':deadline', $deadline);
    $statement->bindValue(':status', $status);
    $statement->bindValue(':created_by', $_SESSION['user_id']); // Track who created the project
    $statement->execute();
    $statement->closeCursor();

    setFlash('success', 'Project added successfully!');
    header('Location: index.php');
    exit();
}
?>
