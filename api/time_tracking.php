<?php
/**
 * Time Tracking API Endpoint
 * Phase 6 upgrade: activity score heartbeat, idle recording, abandoned close_reason
 * Handles Start, Stop, Pulse, and IdleResolved actions from the JS Widget
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

$action      = $_POST['action']      ?? '';
$project_id  = filter_input(INPUT_POST, 'project_id',  FILTER_VALIDATE_INT);
$entry_id    = filter_input(INPUT_POST, 'entry_id',    FILTER_VALIDATE_INT);
$description = $_POST['description'] ?? '';
$user_id     = $_SESSION['user_id'];

// Activity score fields from JS (Task 6.3)
$mouse_events   = filter_input(INPUT_POST, 'mouse_events',   FILTER_VALIDATE_INT) ?? 0;
$key_events     = filter_input(INPUT_POST, 'key_events',     FILTER_VALIDATE_INT) ?? 0;
$activity_score = filter_input(INPUT_POST, 'activity_score', FILTER_VALIDATE_INT) ?? 0;

// Idle fields from JS (Task 6.1)
$idle_seconds          = filter_input(INPUT_POST, 'idle_seconds',          FILTER_VALIDATE_INT) ?? 0;
$discarded_idle_seconds = filter_input(INPUT_POST, 'discarded_idle_seconds', FILTER_VALIDATE_INT) ?? 0;

if (!$project_id && $action !== 'idle_resolved') {
    echo json_encode(['success' => false, 'message' => 'Invalid Project ID']);
    exit;
}

try {
    switch ($action) {

        // ── START ─────────────────────────────────────────────────────────────
        case 'start':
            $sql = "INSERT INTO time_entries
                        (project_id, user_id, start_time, description, status, close_reason, company_id)
                    VALUES (:pid, :uid, NOW(), :desc, 'running', 'manual', :company_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':pid' => $project_id, ':uid' => $user_id, ':desc' => $description, ':company_id' => $_SESSION['company_id']]);
            $new_entry_id = $pdo->lastInsertId();

            updateUserPresence($pdo, $user_id, $project_id);

            // Return entry_id so JS can store it and include it in every pulse
            echo json_encode([
                'success'  => true,
                'message'  => 'Timer Started',
                'entry_id' => (int)$new_entry_id
            ]);
            break;

        // ── STOP ──────────────────────────────────────────────────────────────
        case 'stop':
            // Update the specific entry if entry_id provided, else fallback to user+project
            if ($entry_id) {
                $sql = "UPDATE time_entries
                        SET end_time                = NOW(),
                            status                  = 'completed',
                            total_seconds           = TIMESTAMPDIFF(SECOND, start_time, NOW()),
                            idle_seconds            = :idle,
                            discarded_idle_seconds  = :discarded,
                            close_reason            = 'manual'
                        WHERE id = :eid AND user_id = :uid AND status = 'running'";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':idle'      => $idle_seconds,
                    ':discarded' => $discarded_idle_seconds,
                    ':eid'       => $entry_id,
                    ':uid'       => $user_id
                ]);
            } else {
                $sql = "UPDATE time_entries
                        SET end_time                = NOW(),
                            status                  = 'completed',
                            total_seconds           = TIMESTAMPDIFF(SECOND, start_time, NOW()),
                            idle_seconds            = :idle,
                            discarded_idle_seconds  = :discarded,
                            close_reason            = 'manual'
                        WHERE user_id = :uid AND status = 'running' AND project_id = :pid";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':idle'      => $idle_seconds,
                    ':discarded' => $discarded_idle_seconds,
                    ':uid'       => $user_id,
                    ':pid'       => $project_id
                ]);
            }
            // Finalize activity_score_avg on the entry
            updateActivityAvg($pdo, $entry_id ?: getRunningEntryId($pdo, $user_id, $project_id));
            updateUserPresence($pdo, $user_id, null);
            echo json_encode(['success' => true, 'message' => 'Timer Stopped']);
            break;

        // ── PULSE (heartbeat + activity score) ────────────────────────────────
        case 'pulse':
            updateUserPresence($pdo, $user_id, $project_id);

            // Task 6.3: Store activity snapshot if entry_id known
            if ($entry_id) {
                $sql = "INSERT INTO session_activity
                            (time_entry_id, user_id, recorded_at, mouse_events, key_events, activity_score)
                        VALUES (:eid, :uid, NOW(), :mouse, :key, :score)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':eid'   => $entry_id,
                    ':uid'   => $user_id,
                    ':mouse' => $mouse_events,
                    ':key'   => $key_events,
                    ':score' => $activity_score
                ]);
            }
            echo json_encode(['success' => true, 'message' => 'Pulse Acknowledged']);
            break;

        // ── IDLE_RESOLVED: user responded to idle modal ────────────────────────
        case 'idle_resolved':
            // JS sends: entry_id, idle_seconds, discarded_idle_seconds
            if ($entry_id) {
                $sql = "UPDATE time_entries
                        SET idle_seconds           = idle_seconds + :idle,
                            discarded_idle_seconds = discarded_idle_seconds + :discarded
                        WHERE id = :eid AND user_id = :uid";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':idle'      => $idle_seconds,
                    ':discarded' => $discarded_idle_seconds,
                    ':eid'       => $entry_id,
                    ':uid'       => $user_id
                ]);
            }
            echo json_encode(['success' => true, 'message' => 'Idle recorded']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid Action']);
            break;
    }
} catch (PDOException $e) {
    error_log("TimeTracking API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database Error']);
}

// ── Helpers ──────────────────────────────────────────────────────────────────

function updateUserPresence($pdo, $uid, $pid) {
    $sql = "UPDATE users SET last_active_at = NOW(), current_project_id = :pid WHERE id = :uid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pid' => $pid, ':uid' => $uid]);
}

function getRunningEntryId($pdo, $uid, $pid) {
    $stmt = $pdo->prepare("SELECT id FROM time_entries WHERE user_id=:uid AND project_id=:pid AND status='running' LIMIT 1");
    $stmt->execute([':uid' => $uid, ':pid' => $pid]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int)$row['id'] : null;
}

function updateActivityAvg($pdo, $entry_id) {
    if (!$entry_id) return;
    $sql = "UPDATE time_entries
            SET activity_score_avg = (
                SELECT AVG(activity_score) FROM session_activity WHERE time_entry_id = :eid
            )
            WHERE id = :eid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':eid' => $entry_id]);
}
