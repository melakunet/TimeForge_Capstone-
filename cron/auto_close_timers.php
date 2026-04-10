<?php
/**
 * cron/auto_close_timers.php — Auto-close stale timers
 *
 * Behaviour:
 *  - Any time entry with status='running' whose owner has not sent
 *    a heartbeat in the last 30 minutes is considered abandoned.
 *  - Sets status='abandoned', close_reason='abandoned',
 *    end_time = users.last_active_at (last known heartbeat),
 *    total_seconds = TIMESTAMPDIFF using that last_active_at.
 *  - Finalises activity_score_avg from session_activity rows.
 *
 * Usage (run every 10 minutes via cron):
 *   Every 10 min: [slash]10 * * * * /path/to/php /path/to/cron/auto_close_timers.php
 */

require_once __DIR__ . '/../db.php';

$IDLE_GRACE_MINUTES = 30; // abandon after 30 min of no heartbeat

echo "[" . date('Y-m-d H:i:s') . "] Auto-Close Job starting...\n";

try {
    // Find all running entries where the user's last heartbeat
    // is older than the grace period
    $findSql = "
        SELECT te.id AS entry_id,
               te.project_id,
               te.user_id,
               te.start_time,
               u.last_active_at,
               u.full_name
        FROM time_entries te
        INNER JOIN users u ON u.id = te.user_id
        WHERE te.status = 'running'
          AND (
              u.last_active_at IS NULL
              OR u.last_active_at < DATE_SUB(NOW(), INTERVAL :grace MINUTE)
          )
    ";
    $findStmt = $pdo->prepare($findSql);
    $findStmt->execute([':grace' => $IDLE_GRACE_MINUTES]);
    $stale = $findStmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($stale)) {
        echo "No abandoned sessions found.\n";
        exit(0);
    }

    // Close each stale entry individually so we can set
    // end_time = last_active_at (not NOW())
    $closeSql = "
        UPDATE time_entries
        SET status        = 'abandoned',
            close_reason  = 'abandoned',
            end_time      = COALESCE(:last_active, NOW()),
            total_seconds = GREATEST(0,
                TIMESTAMPDIFF(SECOND, start_time, COALESCE(:last_active2, NOW()))
            )
        WHERE id = :eid AND status = 'running'
    ";
    $closeStmt = $pdo->prepare($closeSql);

    // Finalise activity_score_avg from session_activity
    $avgSql = "
        UPDATE time_entries
        SET activity_score_avg = (
            SELECT AVG(activity_score)
            FROM session_activity
            WHERE time_entry_id = :eid
        )
        WHERE id = :eid2
    ";
    $avgStmt = $pdo->prepare($avgSql);

    // Clear user presence
    $presenceSql = "UPDATE users SET current_project_id = NULL WHERE id = :uid";
    $presenceStmt = $pdo->prepare($presenceSql);

    $count = 0;
    foreach ($stale as $row) {
        $lastActive = $row['last_active_at']; // could be NULL for very old entries

        $closeStmt->execute([
            ':last_active'  => $lastActive,
            ':last_active2' => $lastActive,
            ':eid'          => $row['entry_id']
        ]);

        $avgStmt->execute([':eid' => $row['entry_id'], ':eid2' => $row['entry_id']]);
        $presenceStmt->execute([':uid' => $row['user_id']]);

        echo "  Abandoned entry #{$row['entry_id']} — user: {$row['full_name']}"
           . " — last heartbeat: " . ($lastActive ?? 'never') . "\n";
        $count++;
    }

    // Write audit log entry
    try {
        $auditSql = "INSERT INTO audit_logs (user_id, action, ip_address, created_at)
                     VALUES (0, :action, '127.0.0.1', NOW())";
        $auditStmt = $pdo->prepare($auditSql);
        $auditStmt->execute([':action' => "System auto-abandoned $count stale timers"]);
    } catch (Exception $e) {
        // audit_logs table optional — ignore silently
    }

    echo "[" . date('Y-m-d H:i:s') . "] Done. Abandoned $count session(s).\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Job Complete.\n";
