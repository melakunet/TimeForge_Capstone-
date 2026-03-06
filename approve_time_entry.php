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

// Only Admin can approve/reject time
if (!hasRole('admin')) {
    setFlash('error', 'Unauthorized access.');
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entry_id = filter_input(INPUT_POST, 'entry_id', FILTER_VALIDATE_INT);
    $action = $_POST['action'] ?? '';
    $project_id = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);

    if ($entry_id && ($action === 'approve' || $action === 'reject')) {
        try {
            $new_status = ($action === 'approve') ? 'approved' : 'rejected';
            
            // Update the time entry
            $query = "UPDATE time_entries 
                      SET status = :status, 
                          reviewed_by = :reviewer, 
                          reviewed_at = NOW() 
                      WHERE id = :id";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':status' => $new_status,
                ':reviewer' => $_SESSION['user_id'],
                ':id' => $entry_id
            ]);

            if ($stmt->rowCount() > 0) {
                setFlash('success', "Time entry marked as $new_status.");
            } else {
                setFlash('error', 'Time entry not found or no change made.');
            }
        } catch (PDOException $e) {
            error_log("Approve Time Entry Error: " . $e->getMessage());
            setFlash('error', 'Database error occurred.');
        }
    } else {
        setFlash('error', 'Invalid request.');
    }
    
    // Redirect back to project details
    if ($project_id) {
        header("Location: project_details.php?id=" . $project_id);
    } else {
        header("Location: index.php");
    }
    exit;
} else {
    header("Location: index.php");
    exit;
}
