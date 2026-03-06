<?php
/**
 * Auto-Close Stale Timers Cron Job
 * 
 * Purpose: 
 * - Finds time entries that have been 'running' for more than 12 hours (likely abandoned).
 * - Automatically closes them to prevent infinite sessions.
 * - Sets the end time to 12 hours after start time.
 * 
 * Usage:
 * - Run this script via system cron every hour.
 * - Example: 0 * * * * /usr/bin/php /path/to/TimeForge_Capstone/cron/auto_close_timers.php
 */

// Adjust path to db.php based on your server structure
require_once __DIR__ . '/../db.php';

// Set maximum session duration (12 hours in seconds)
$MAX_DURATION_SECONDS = 12 * 60 * 60; 

echo "Starting Auto-Close Job...\n";

try {
    // 1. Find stale entries
    $sql = "UPDATE time_entries 
            SET end_time = DATE_ADD(start_time, INTERVAL 12 HOUR),
                status = 'completed',
                total_seconds = :max_seconds,
                description = CONCAT(description, ' [Auto-closed by system]')
            WHERE status = 'running' 
            AND start_time < DATE_SUB(NOW(), INTERVAL 12 HOUR)";
            
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':max_seconds', $MAX_DURATION_SECONDS, PDO::PARAM_INT);
    $stmt->execute();
    
    $count = $stmt->rowCount();
    
    if ($count > 0) {
        echo "Success: Closed $count stale time entries.\n";
        
        // Log to audit_logs if available
        // We use a dummy user_id 0 or NULL for system actions
        try {
            $auditSql = "INSERT INTO audit_logs (user_id, action, ip_address, created_at) VALUES (0, :action, '127.0.0.1', NOW())";
            $auditStmt = $pdo->prepare($auditSql);
            $actionMsg = "System auto-closed $count stale timers";
            $auditStmt->execute([':action' => $actionMsg]);
        } catch (Exception $e) {
            // Ignore audit log errors
        }
    } else {
        echo "No stale entries found.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Job Complete.\n";
