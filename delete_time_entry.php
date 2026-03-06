<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entry_id = filter_input(INPUT_POST, 'entry_id', FILTER_VALIDATE_INT);
    $project_id = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    if (!$entry_id || !$project_id) {
        setFlash('error', 'Invalid request.');
        header("Location: index.php");
        exit;
    }

    try {
        // Permissions Check: 
        // 1. Admin can delete any entry.
        // 2. Freelancer can delete their own entry IF it is still 'pending'.
        
        $query = "SELECT user_id, status FROM time_entries WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id' => $entry_id]);
        $entry = $stmt->fetch();

        if (!$entry) {
            setFlash('error', 'Time entry not found.');
            header("Location: project_details.php?id=" . $project_id);
            exit;
        }

        $can_delete = false;
        if ($role === 'admin') {
            $can_delete = true;
        } elseif ($role === 'freelancer' && $entry['user_id'] == $user_id && $entry['status'] === 'pending') {
            $can_delete = true;
        }

        if ($can_delete) {
            $delSql = "DELETE FROM time_entries WHERE id = :id";
            $delStmt = $pdo->prepare($delSql);
            $delStmt->execute([':id' => $entry_id]);
            setFlash('success', 'Time entry deleted successfully.');
        } else {
            setFlash('error', 'Unauthorized to delete this entry.');
        }

    } catch (PDOException $e) {
        error_log("Delete Time Entry Error: " . $e->getMessage());
        setFlash('error', 'Database error.');
    }

    header("Location: project_details.php?id=" . $project_id);
    exit;
}
