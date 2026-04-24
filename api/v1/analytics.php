<?php
/**
 * api/v1/analytics.php — Phase 10
 * Returns JSON data for the React analytics dashboard.
 * Admin only. All data scoped to company_id.
 */

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (!isLoggedIn() || !hasRole('admin')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

$company_id = (int)$_SESSION['company_id'];

// ── 1. Hours per day — last 30 days ──────────────────────────────────────────
$hours_stmt = $pdo->prepare("
    SELECT
        DATE(te.start_time)                        AS day,
        ROUND(SUM(te.total_seconds) / 3600, 2)    AS hours
    FROM time_entries te
    INNER JOIN projects p ON p.id = te.project_id
    WHERE p.company_id = :cid
      AND te.start_time >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
      AND te.total_seconds IS NOT NULL
    GROUP BY DATE(te.start_time)
    ORDER BY day ASC
");
$hours_stmt->execute([':cid' => $company_id]);
$hours_per_day = $hours_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fill missing days with 0 so chart has no gaps
$filled_days = [];
for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $filled_days[$date] = 0;
}
foreach ($hours_per_day as $row) {
    $filled_days[$row['day']] = (float)$row['hours'];
}
$hours_chart = [];
foreach ($filled_days as $date => $hours) {
    $hours_chart[] = ['day' => date('M j', strtotime($date)), 'hours' => $hours, 'date' => $date];
}

// ── 2. Hours per project ─────────────────────────────────────────────────────
$proj_stmt = $pdo->prepare("
    SELECT
        p.project_name                             AS project,
        ROUND(SUM(te.total_seconds) / 3600, 2)    AS hours
    FROM time_entries te
    INNER JOIN projects p ON p.id = te.project_id
    WHERE p.company_id = :cid
      AND te.total_seconds IS NOT NULL
      AND p.deleted_at IS NULL
    GROUP BY p.id, p.project_name
    ORDER BY hours DESC
    LIMIT 10
");
$proj_stmt->execute([':cid' => $company_id]);
$project_chart = $proj_stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($project_chart as &$r) $r['hours'] = (float)$r['hours'];
unset($r);

// ── 3. Hours per user ─────────────────────────────────────────────────────────
$user_stmt = $pdo->prepare("
    SELECT
        u.full_name                                AS name,
        ROUND(SUM(te.total_seconds) / 3600, 2)    AS hours
    FROM time_entries te
    INNER JOIN users u ON u.id = te.user_id
    INNER JOIN projects p ON p.id = te.project_id
    WHERE p.company_id = :cid
      AND te.total_seconds IS NOT NULL
    GROUP BY u.id, u.full_name
    ORDER BY hours DESC
    LIMIT 8
");
$user_stmt->execute([':cid' => $company_id]);
$user_totals = $user_stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($user_totals as &$r) $r['hours'] = (float)$r['hours'];
unset($r);

// ── 4. Activity heatmap — last 365 days ──────────────────────────────────────
$heat_stmt = $pdo->prepare("
    SELECT
        DATE(te.start_time)                        AS day,
        ROUND(SUM(te.total_seconds) / 3600, 2)    AS hours
    FROM time_entries te
    INNER JOIN projects p ON p.id = te.project_id
    WHERE p.company_id = :cid
      AND te.start_time >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)
      AND te.total_seconds IS NOT NULL
    GROUP BY DATE(te.start_time)
");
$heat_stmt->execute([':cid' => $company_id]);
$heatmap = [];
foreach ($heat_stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $heatmap[$row['day']] = (float)$row['hours'];
}

// Build heatmap as array for React (last 84 days = 12 weeks)
$heatmap_arr = [];
for ($i = 83; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $heatmap_arr[] = ['date' => $date, 'hours' => $heatmap[$date] ?? 0];
}

echo json_encode([
    'success'       => true,
    'hours_per_day' => $hours_chart,
    'project_chart' => $project_chart,
    'per_user'      => $user_totals,
    'heatmap'       => $heatmap_arr,
]);
