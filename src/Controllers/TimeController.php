<?php
/**
 * src/Controllers/TimeController.php
 * Handles delete and approve time entry POST processing.
 * Loaded by delete_time_entry.php and approve_time_entry.php (backward-compat wrappers).
 */

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/flash.php';
require_once __DIR__ . '/../../db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ── DELETE TIME ENTRY ─────────────────────────────────────────────────────
if ($action === 'delete') {

    if (!isLoggedIn()) { header('Location: /TimeForge_Capstone/login.php'); exit; }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /TimeForge_Capstone/index.php'); exit; }

    $entry_id   = filter_input(INPUT_POST, 'entry_id',   FILTER_VALIDATE_INT);
    $project_id = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);
    $user_id    = $_SESSION['user_id'];
    $role       = $_SESSION['role'];

    if (!$entry_id || !$project_id) {
        setFlash('error', 'Invalid request.');
        header('Location: /TimeForge_Capstone/index.php'); exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT user_id, status FROM time_entries WHERE id = :id");
        $stmt->execute([':id' => $entry_id]);
        $entry = $stmt->fetch();

        if (!$entry) {
            setFlash('error', 'Time entry not found.');
            header('Location: /TimeForge_Capstone/project_details.php?id=' . $project_id); exit;
        }

        $can_delete = ($role === 'admin') ||
                      ($role === 'freelancer' && $entry['user_id'] == $user_id && $entry['status'] === 'pending');

        if ($can_delete) {
            $pdo->prepare("DELETE FROM time_entries WHERE id = :id")->execute([':id' => $entry_id]);
            setFlash('success', 'Time entry deleted successfully.');
        } else {
            setFlash('error', 'Unauthorized to delete this entry.');
        }
    } catch (PDOException $e) {
        error_log('TimeController delete: ' . $e->getMessage());
        setFlash('error', 'Database error.');
    }

    header('Location: /TimeForge_Capstone/project_details.php?id=' . $project_id); exit;
}

// ── APPROVE / REJECT TIME ENTRY ───────────────────────────────────────────
if ($action === 'approve') {

    if (!isLoggedIn()) { header('Location: /TimeForge_Capstone/login.php'); exit; }
    if (!hasRole('admin')) {
        setFlash('error', 'Unauthorized access.');
        header('Location: /TimeForge_Capstone/index.php'); exit;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /TimeForge_Capstone/index.php'); exit; }

    $entry_id   = filter_input(INPUT_POST, 'entry_id',   FILTER_VALIDATE_INT);
    $decision   = $_POST['action_decision'] ?? $_POST['action'] ?? '';
    $project_id = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);

    // Support both the old field name 'action' (approve/reject) and new 'action_decision'
    if ($decision === 'approve' || $decision === 'reject') {
        // valid
    } else {
        setFlash('error', 'Invalid request.');
        header($project_id ? 'Location: /TimeForge_Capstone/project_details.php?id=' . $project_id : 'Location: /TimeForge_Capstone/index.php'); exit;
    }

    if ($entry_id) {
        try {
            $new_status = ($decision === 'approve') ? 'approved' : 'rejected';
            $stmt = $pdo->prepare("
                UPDATE time_entries
                SET status = :status, reviewed_by = :reviewer, reviewed_at = NOW()
                WHERE id = :id
            ");
            $stmt->execute([':status' => $new_status, ':reviewer' => $_SESSION['user_id'], ':id' => $entry_id]);

            if ($stmt->rowCount() > 0) {
                setFlash('success', 'Time entry marked as ' . $new_status . '.');
            } else {
                setFlash('error', 'Time entry not found or no change made.');
            }
        } catch (PDOException $e) {
            error_log('TimeController approve: ' . $e->getMessage());
            setFlash('error', 'Database error occurred.');
        }
    } else {
        setFlash('error', 'Invalid request.');
    }

    header($project_id ? 'Location: /TimeForge_Capstone/project_details.php?id=' . $project_id : 'Location: /TimeForge_Capstone/index.php'); exit;
}
