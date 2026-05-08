<?php
/**
 * src/Controllers/TimeController.php
 * Handles delete and approve time entry POST processing.
 * Entry points: delete_time_entry.php, approve_time_entry.php
 */

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/flash.php';
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../includes/notify.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ── DELETE TIME ENTRY ─────────────────────────────────────────────────────
if ($action === 'delete') {

    if (!isLoggedIn()) { header('Location: ' . APP_BASE . '/login.php'); exit; }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ' . APP_BASE . '/index.php'); exit; }
    verifyCsrfToken();

    $entry_id   = filter_input(INPUT_POST, 'entry_id',   FILTER_VALIDATE_INT);
    $project_id = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);
    $user_id    = $_SESSION['user_id'];
    $role       = $_SESSION['role'];

    if (!$entry_id || !$project_id) {
        setFlash('error', 'Invalid request.');
        header('Location: ' . APP_BASE . '/index.php'); exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT user_id, status FROM time_entries WHERE id = :id");
        $stmt->execute([':id' => $entry_id]);
        $entry = $stmt->fetch();

        if (!$entry) {
            setFlash('error', 'Time entry not found.');
            header('Location: ' . APP_BASE . '/project_details.php?id=' . $project_id); exit;
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

    header('Location: ' . APP_BASE . '/project_details.php?id=' . $project_id); exit;
}

// ── APPROVE / REJECT TIME ENTRY ───────────────────────────────────────────
if ($action === 'approve') {

    if (!isLoggedIn()) { header('Location: ' . APP_BASE . '/login.php'); exit; }
    if (!hasRole('admin')) {
        setFlash('error', 'Unauthorized access.');
        header('Location: ' . APP_BASE . '/index.php'); exit;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ' . APP_BASE . '/index.php'); exit; }
    verifyCsrfToken();

    $entry_id   = filter_input(INPUT_POST, 'entry_id',   FILTER_VALIDATE_INT);
    $decision   = $_POST['action_decision'] ?? $_POST['action'] ?? '';
    $project_id = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);

    // Support both the old field name 'action' (approve/reject) and new 'action_decision'
    if ($decision === 'approve' || $decision === 'reject') {
        // valid
    } else {
        setFlash('error', 'Invalid request.');
        header($project_id ? 'Location: ' . APP_BASE . '/project_details.php?id=' . $project_id : 'Location: ' . APP_BASE . '/index.php'); exit;
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

                // ── Notify the entry owner ────────────────────────────────
                $ownerRow = $pdo->prepare("SELECT user_id FROM time_entries WHERE id = :id");
                $ownerRow->execute([':id' => $entry_id]);
                $owner_id = (int)$ownerRow->fetchColumn();
                if ($owner_id) {
                    $notif_type = ($new_status === 'approved') ? 'time_approved' : 'time_rejected';
                    $notif_msg  = ($new_status === 'approved')
                        ? 'Your time entry was approved.'
                        : 'Your time entry was rejected.';
                    $notif_link = $project_id ? APP_BASE . '/project_details.php?id=' . $project_id : null;
                    notify($pdo, $owner_id, $notif_type, $notif_msg, $notif_link);
                }
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

    header($project_id ? 'Location: ' . APP_BASE . '/project_details.php?id=' . $project_id : 'Location: ' . APP_BASE . '/index.php'); exit;
}
