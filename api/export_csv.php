<?php
/*
 * Task 7.3 — CSV Export for Time Logs
 * Outputs a downloadable CSV of all approved billable time entries
 * for a given project. Accessible by admin; clients can export their
 * own projects via the client portal.
 * No HTML — this is a raw file download response.
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';

if (!isLoggedIn()) {
    http_response_code(401);
    exit('Not authenticated.');
}

$project_id = filter_input(INPUT_GET, 'project_id', FILTER_VALIDATE_INT);

if (!$project_id) {
    http_response_code(400);
    exit('Missing project_id.');
}

$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];

// Admin can export any project; clients only their own
if ($role === 'client') {
    $access_stmt = $pdo->prepare("
        SELECT p.id FROM projects p
        INNER JOIN clients c ON c.id = p.client_id
        WHERE p.id = :project_id AND c.user_id = :user_id AND p.deleted_at IS NULL
        LIMIT 1
    ");
    $access_stmt->execute([':project_id' => $project_id, ':user_id' => $user_id]);
    if (!$access_stmt->fetch()) {
        http_response_code(403);
        exit('Access denied.');
    }
} elseif ($role !== 'admin') {
    http_response_code(403);
    exit('Access denied.');
}

// Fetch project + client info for the filename and metadata header rows
$proj_stmt = $pdo->prepare("
    SELECT p.project_name, p.hourly_rate, p.description AS project_desc,
           c.client_name, c.company_name
    FROM projects p
    LEFT JOIN clients c ON c.id = p.client_id
    WHERE p.id = :id
    LIMIT 1
");
$proj_stmt->execute([':id' => $project_id]);
$project = $proj_stmt->fetch();

if (!$project) {
    http_response_code(404);
    exit('Project not found.');
}

$hourly_rate = (float)$project['hourly_rate'];

// Approved billable entries only — same scope the invoice uses
$entries_stmt = $pdo->prepare("
    SELECT
        te.id,
        te.start_time,
        te.end_time,
        te.total_seconds,
        te.description,
        te.entry_type,
        u.full_name AS freelancer_name
    FROM time_entries te
    LEFT JOIN users u ON u.id = te.user_id
    WHERE te.project_id = :project_id
      AND te.status      = 'approved'
      AND te.is_billable = 1
      AND te.end_time   IS NOT NULL
    ORDER BY te.start_time ASC
");
$entries_stmt->execute([':project_id' => $project_id]);
$entries = $entries_stmt->fetchAll();

// Build the safe filename from the project name
$safe_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $project['project_name']);
$filename  = 'TimeLog_' . $safe_name . '_' . date('Ymd') . '.csv';

// Send headers that force a file download
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// UTF-8 BOM so Excel opens the file with the correct encoding
echo "\xEF\xBB\xBF";

$out = fopen('php://output', 'w');

// ── Metadata header rows — visible context even with 0 data rows ──────────
fputcsv($out, ['TimeForge Time Log Export']);
fputcsv($out, ['Project:', $project['project_name']]);
fputcsv($out, ['Client:', $project['client_name'] ?? '']);
fputcsv($out, ['Company:', $project['company_name'] ?? '(none)']);
fputcsv($out, ['Hourly Rate:', '$' . number_format($hourly_rate, 2) . '/hr']);
fputcsv($out, ['Export Date:', date('F j, Y \a\t g:i A')]);
fputcsv($out, ['Approved Entries:', count($entries)]);
$total_hours = array_sum(array_map(fn($e) => round(($e['total_seconds'] ?? 0) / 3600, 2), $entries));
$total_cost  = round($total_hours * $hourly_rate, 2);
fputcsv($out, ['Total Hours:', number_format($total_hours, 2)]);
fputcsv($out, ['Total Cost:', '$' . number_format($total_cost, 2)]);
fputcsv($out, []); // blank spacer row before column headers

// Column headers
fputcsv($out, [
    'Entry ID',
    'Date',
    'Start Time',
    'End Time',
    'Hours',
    'Freelancer',
    'Description',
    'Type',
    'Rate ($/hr)',
    'Cost ($)',
]);

foreach ($entries as $e) {
    $hours = round(($e['total_seconds'] ?? 0) / 3600, 2);
    $cost  = round($hours * $hourly_rate, 2);

    fputcsv($out, [
        $e['id'],
        date('Y-m-d', strtotime($e['start_time'])),
        date('H:i:s',  strtotime($e['start_time'])),
        date('H:i:s',  strtotime($e['end_time'])),
        $hours,
        $e['freelancer_name'] ?? '',
        $e['description']     ?? '',
        $e['entry_type'],
        number_format($hourly_rate, 2),
        number_format($cost, 2),
    ]);
}

fclose($out);
exit;
