<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/db.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$project_name = filter_input(INPUT_POST, 'project_name');
$description = filter_input(INPUT_POST, 'description');
$budget = filter_input(INPUT_POST, 'budget', FILTER_VALIDATE_FLOAT);
$deadline = filter_input(INPUT_POST, 'deadline');
$status = filter_input(INPUT_POST, 'status');

if ($project_name == null || $budget === false || $deadline == null || $status == null) {
    echo "Invalid data. Check all fields.";
    include('add_project.php'); // Or header redirection
    exit();
} else {
    // Add the project
    $query = 'INSERT INTO projects (project_name, description, client_id, budget, deadline, status)
              VALUES (:project_name, :description, :client_id, :budget, :deadline, :status)';
    $statement = $pdo->prepare($query);
    $statement->bindValue(':project_name', $project_name);
    $statement->bindValue(':description', $description);
    $statement->bindValue(':client_id', $_SESSION['user_id']); // Assuming logged in user is client
    $statement->bindValue(':budget', $budget);
    $statement->bindValue(':deadline', $deadline);
    $statement->bindValue(':status', $status);
    $statement->execute();
    $statement->closeCursor();

    header('Location: index.php');
    exit();
}
?>
