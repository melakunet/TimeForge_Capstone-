<?php
/**
 * Time Tracking API Endpoint
 * Handles Start, Stop, and Pulse (Heartbeat) actions from the JS Widget
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

// Security Check
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';
$project_id = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);
$description = $_POST['description'] ?? '';
$user_id = $_SESSION['user_id'];

if (!$project_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid Project ID']);
    exit;
}

try {
    switch ($action) {
        case 'start':
            // 1. Create a new time entry
            $sql = "INSERT INTO time_entries (project_id, user_id, start_time, description, status) 
                    VALUES (:pid, :uid, NOW(), :desc, 'running')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':pid' => $project_id, 
                ':uid' => $user_id,
                ':desc' => $description
            ]);
            
            // 2. Update User Presence
            updateUserPresence($pdo, $user_id, $project_id);
            
            echo json_encode(['success' => true, 'message' => 'Timer Started']);
            break;

        case 'stop':
            // 1. Find the running entry for this user
            $sql = "UPDATE time_entries 
                    SET end_time = NOW(), 
                        status = 'completed',
                        total_seconds = TIMESTAMPDIFF(SECOND, start_time, NOW())
                    WHERE user_id = :uid AND status = 'running' AND project_id = :pid";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':uid' => $user_id, ':pid' => $project_id]);
            
            // 2. Clear User Presence (set current_project_id to NULL)
            updateUserPresence($pdo, $user_id, null);

            echo json_encode(['success' => true, 'message' => 'Timer Stopped']);
            break;

        case 'pulse':
            // 1. Just update user's last_active_time
            updateUserPresence($pdo, $user_id, $project_id);
            echo json_encode(['success' => true, 'message' => 'Pulse Acknowledged']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid Action']);
            break;
    }
} catch (PDOException $e) {
    error_log("TimeTracking API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database Error']);
}

/**
 * Helper to update user's "Online Status" table columns
 */
function updateUserPresence($pdo, $uid, $pid) {
    $sql = "UPDATE users 
            SET last_active_at = NOW(), 
                current_project_id = :pid 
            WHERE id = :uid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pid' => $pid, ':uid' => $uid]);
}
