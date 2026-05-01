<?php
/**
 * api/presence.php — Live freelancer presence
 * Returns active status for all freelancers in the company.
 * Called by the admin dashboard via AJAX every 30 seconds.
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
        -- Use whichever is more recent: active ping OR login time
        GREATEST(
            COALESCE(u.last_active_at, '2000-01-01'),
            COALESCE(u.last_login,     '2000-01-01')
        ) AS last_seen_at,
        u.last_active_at,
        u.last_login,
        u.current_project_id,
        p.project_name,
        te.start_time AS timer_start,
        te.id         AS entry_id
    FROM users u
    LEFT JOIN projects p    ON p.id = u.current_project_id
    LEFT JOIN time_entries te
           ON te.user_id = u.id AND te.status = 'running'
    WHERE u.role IN ('freelancer', 'admin')
      AND u.company_id = :company_id
    ORDER BY last_seen_at DESC, u.full_name ASC
";

$rows = $pdo->prepare($sql);
$rows->execute([':company_id' => $_SESSION['company_id']]);
$rows = $rows->fetchAll(PDO::FETCH_ASSOC);
$now  = new DateTime();

$result = [];
foreach ($rows as $r) {
    // Determine presence status
    // Determine presence status using the most-recent timestamp
    $status  = 'offline';
    $label   = 'Never seen';
    $since   = null;
    $elapsed = null;

    $lastTimestamp = $r['last_seen_at'] ?? null;

    if ($lastTimestamp && $lastTimestamp !== '2000-01-01 00:00:00') {
        $last    = new DateTime($lastTimestamp);
        $diffSec = ($now->getTimestamp() - $last->getTimestamp());

        // A running timer is absolute proof of activity — always show active
        if ($r['entry_id']) {
            $status = 'active';
            $label  = 'Active now';
        } elseif ($diffSec <= 180) {
            $status = 'active';
            $label  = 'Online now';
        } elseif ($diffSec <= 600) {
            $status = 'idle';
            // Show exact clock time, not a relative duration
            $label  = 'Last seen at ' . $last->format('H:i');
        } else {
            $status = 'offline';
            // Show exact clock time: today → "11:03", older → "Apr 30, 11:03"
            $isToday = ($last->format('Y-m-d') === $now->format('Y-m-d'));
            $label   = $isToday
                ? 'Last seen at ' . $last->format('H:i')
                : 'Last seen ' . $last->format('M j, H:i');
        }
        $since = $lastTimestamp;
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
        'status'       => $status,
        'label'        => $label,
        'project_name' => $r['project_name'] ?? null,
        'timer_start'  => $r['timer_start'] ?? null,
        'elapsed'      => $elapsed,
        'last_active'  => $since,
        // Exact datetime for tooltip (e.g. "May 1, 15:23")
        'last_seen_exact' => $since ? (new DateTime($since))->format('M j, H:i') : null,
    ];
}

echo json_encode(['success' => true, 'users' => $result, 'ts' => $now->format('H:i:s')]);
