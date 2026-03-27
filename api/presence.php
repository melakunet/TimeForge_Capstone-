<?php
/**
 * api/presence.php — Phase 6.7
 * Returns live presence status for all freelancers.
 * Called by admin dashboard AJAX every 30 seconds.
 * Uses existing last_active_at + current_project_id columns — no new DB tables.
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !hasRole('admin')) {
    http_response_code(403);
    echo json_encode(['success' => false]);
    exit;
}

$sql = "
    SELECT
        u.id,
        u.full_name,
        u.role,
        u.last_active_at,
        u.current_project_id,
        p.project_name,
        te.start_time AS timer_start,
        te.id         AS entry_id
    FROM users u
    LEFT JOIN projects p    ON p.id = u.current_project_id
    LEFT JOIN time_entries te
           ON te.user_id = u.id AND te.status = 'running'
    WHERE u.role IN ('freelancer', 'admin')
    ORDER BY u.full_name ASC
";

$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$now  = new DateTime();

$result = [];
foreach ($rows as $r) {
    // Determine presence status
    $status = 'offline';
    $label  = 'Offline';
    $since  = null;
    $elapsed = null;

    if ($r['last_active_at']) {
        $last   = new DateTime($r['last_active_at']);
        $diffSec = ($now->getTimestamp() - $last->getTimestamp());

        if ($diffSec <= 90) {           // heartbeat within 90 sec = active
            $status = 'active';
            $label  = 'Active now';
        } elseif ($diffSec <= 600) {    // within 10 min = idle
            $status = 'idle';
            $m = floor($diffSec / 60);
            $label  = "Idle {$m} min ago";
        } else {
            $status = 'offline';
            // Show human-readable last seen
            $diff = $now->diff($last);
            if ($diff->days > 0)      $label = "Last seen {$diff->days}d ago";
            elseif ($diff->h > 0)     $label = "Last seen {$diff->h}h ago";
            else                       $label = "Last seen {$diff->i}m ago";
        }
        $since = $r['last_active_at'];
    }

    // Elapsed timer time
    if ($r['timer_start'] && $r['current_project_id']) {
        $start   = new DateTime($r['timer_start']);
        $secs    = $now->getTimestamp() - $start->getTimestamp();
        $h = floor($secs / 3600);
        $m = floor(($secs % 3600) / 60);
        $elapsed = sprintf('%dh %02dm', $h, $m);
    }

    $result[] = [
        'id'           => (int)$r['id'],
        'name'         => $r['full_name'],
        'role'         => $r['role'],
        'status'       => $status,        // active | idle | offline
        'label'        => $label,
        'project_name' => $r['project_name'] ?? null,
        'elapsed'      => $elapsed,
        'last_active'  => $since,
    ];
}

echo json_encode(['success' => true, 'users' => $result, 'ts' => $now->format('H:i:s')]);
