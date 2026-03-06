<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

// Only logged in users can access this script
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Only Admin can restore projects
$role = $_SESSION['role'] ?? '';
if ($role !== 'admin') {
    setFlash('error', 'Unauthorized access.');
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);

    if ($project_id) {
        try {
            // Restore project by clearing deleted_at
            $query = "UPDATE projects SET deleted_at = NULL WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':id', $project_id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                setFlash('success', 'Project restored successfully.');
                
                // Log the action if audit_logs table exists
                try {
                    $auditQuery = "INSERT INTO audit_logs (user_id, action, details, ip_address, created_at) 
                                   VALUES (:user_id, 'restore_project', :details, :ip, NOW())";
                    $auditStmt = $pdo->prepare($auditQuery);
                    $auditStmt->bindValue(':user_id', $_SESSION['user_id']);
                    $auditStmt->bindValue(':details', "Restored project ID: $project_id");
                    $auditStmt->bindValue(':ip', $_SERVER['REMOTE_ADDR']);
                    $auditStmt->execute();
                } catch (Exception $e) {
                    // Silently fail audit log if table doesn't exist or other error, 
                    // as main action succeeded.
                }

                header("Location: project_details.php?id=" . $project_id);
                exit;
            } else {
                setFlash('error', 'Failed to restore project.');
            }
        } catch (PDOException $e) {
            error_log("Restore Project Error: " . $e->getMessage());
            setFlash('error', 'Database error occurred.');
        }
    } else {
        setFlash('error', 'Invalid project ID.');
    }
} else {
    setFlash('error', 'Invalid request method.');
}

header("Location: index.php");
exit;
