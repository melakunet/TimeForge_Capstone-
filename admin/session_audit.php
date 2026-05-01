<?php
/**
 * admin/session_audit.php
 * Idle time audit log — shows tracked time, idle detected, idle discarded,
 * net billable time, and average activity score per session.
 */

$page_title = 'Session Audit Log';

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/flash.php';

requireRole('admin');

// ── Filters ────────────────────────────────────────────────────────────────
$filter_user    = filter_input(INPUT_GET, 'user_id',    FILTER_VALIDATE_INT) ?: 0;
$filter_project = filter_input(INPUT_GET, 'project_id', FILTER_VALIDATE_INT) ?: 0;
$filter_status  = $_GET['status'] ?? 'all';
$valid_statuses = ['all', 'completed', 'approved', 'abandoned'];
if (!in_array($filter_status, $valid_statuses)) $filter_status = 'all';

// ── Build query ────────────────────────────────────────────────────────────
$where   = ['te.status IN ("completed","approved","abandoned")', 'te.company_id = :company_id'];
$params  = [':company_id' => $_SESSION['company_id']];

if ($filter_user > 0) {
    $where[]               = 'te.user_id = :uid';
    $params[':uid']        = $filter_user;
}
if ($filter_project > 0) {
    $where[]               = 'te.project_id = :pid';
    $params[':pid']        = $filter_project;
}
if ($filter_status !== 'all') {
    $where[]               = 'te.status = :status';
    $params[':status']     = $filter_status;
}

$whereSQL = implode(' AND ', $where);

$sql = "
    SELECT te.id,
           te.start_time,
           te.end_time,
           te.total_seconds,
           te.idle_seconds,
           te.discarded_idle_seconds,
           te.activity_score_avg,
           te.close_reason,
           te.status,
           te.description,
           u.full_name   AS freelancer,
           p.project_name        AS project_name
    FROM time_entries te
    INNER JOIN users    u ON u.id = te.user_id
    INNER JOIN projects p ON p.id = te.project_id
    WHERE $whereSQL
    ORDER BY te.start_time DESC
    LIMIT 200
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ── Dropdown data ──────────────────────────────────────────────────────────
$users_stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE role IN ('freelancer','admin') AND company_id = :company_id ORDER BY full_name");
$users_stmt->execute([':company_id' => $_SESSION['company_id']]);
$users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);

$projects_stmt = $pdo->prepare("SELECT id, project_name FROM projects WHERE company_id = :company_id ORDER BY project_name");
$projects_stmt->execute([':company_id' => $_SESSION['company_id']]);
$projects = $projects_stmt->fetchAll(PDO::FETCH_ASSOC);

// ── Helpers ────────────────────────────────────────────────────────────────
function fmtSeconds(int $s): string {
    $h = floor($s / 3600);
    $m = floor(($s % 3600) / 60);
    return sprintf('%dh %02dm', $h, $m);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> — TimeForge</title>
    <link rel="stylesheet" href="/TimeForge_Capstone/css/style.css">
    <link rel="stylesheet" href="/TimeForge_Capstone/css/session-audit.css">
    <link rel="icon" type="image/png" href="/TimeForge_Capstone/icons/logo.png">
</head>
<body>
<?php include_once __DIR__ . '/../includes/header_partial.php'; ?>

<div class="container">

    <!-- ── Page Header ─────────────────────────────────────────────── -->
    <div class="audit-page-header">
        <div>
            <h1 class="audit-title">Session Audit Log</h1>
            <p class="audit-subtitle">Tracked time vs idle time vs net billable time — per session.</p>
        </div>
        <a href="/TimeForge_Capstone/admin/dashboard.php" class="btn btn-secondary">← Back to Admin</a>
    </div>

    <!-- ── Filter Bar ──────────────────────────────────────────────── -->
    <form method="GET" class="audit-filter-bar">
        <select name="user_id" class="form-control audit-select">
            <option value="0">All Freelancers</option>
            <?php foreach ($users as $u): ?>
                <option value="<?= $u['id'] ?>" <?= $filter_user == $u['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($u['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="project_id" class="form-control audit-select">
            <option value="0">All Projects</option>
            <?php foreach ($projects as $pr): ?>
                <option value="<?= $pr['id'] ?>" <?= $filter_project == $pr['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($pr['project_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="status" class="form-control audit-select">
            <option value="all"      <?= $filter_status === 'all'       ? 'selected' : '' ?>>All Statuses</option>
            <option value="completed"<?= $filter_status === 'completed' ? 'selected' : '' ?>>Completed</option>
            <option value="approved" <?= $filter_status === 'approved'  ? 'selected' : '' ?>>Approved</option>
            <option value="abandoned"<?= $filter_status === 'abandoned' ? 'selected' : '' ?>>Abandoned (Auto-Closed)</option>
        </select>

        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="/TimeForge_Capstone/admin/session_audit.php" class="btn btn-secondary">Reset</a>
    </form>

    <!-- ── Legend ──────────────────────────────────────────────────── -->
    <div class="audit-legend">
        <span class="legend-dot legend-tracked"></span> Total Tracked
        <span class="legend-dot legend-idle"></span> Idle Detected (orange)
        <span class="legend-dot legend-discarded"></span> Idle Discarded (red)
        <span class="legend-dot legend-net"></span> Net Billable (green)
    </div>

    <!-- ── Table ───────────────────────────────────────────────────── -->
    <?php if (empty($entries)): ?>
        <div class="card audit-empty">
            <p>No sessions found matching the selected filters.</p>
        </div>
    <?php else: ?>
    <div class="audit-table-wrap">
        <table class="audit-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Freelancer</th>
                    <th>Project</th>
                    <th>Date</th>
                    <th>Total Tracked</th>
                    <th>Idle Detected</th>
                    <th>Idle Discarded</th>
                    <th>Net Billable</th>
                    <th>Avg Activity</th>
                    <th>Close</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($entries as $e):
                $total      = (int)$e['total_seconds'];
                $idle       = (int)$e['idle_seconds'];
                $discarded  = (int)$e['discarded_idle_seconds'];
                $net        = max(0, $total - $discarded);
                $score      = $e['activity_score_avg'] !== null ? round((float)$e['activity_score_avg']) : '—';
                $date       = date('M j, Y g:ia', strtotime($e['start_time']));
                $closeReason = $e['close_reason'];
            ?>
                <tr class="<?= $closeReason === 'abandoned' ? 'row-abandoned' : '' ?>">
                    <td class="audit-id"><?= $e['id'] ?></td>
                    <td><?= htmlspecialchars($e['freelancer']) ?></td>
                    <td><?= htmlspecialchars($e['project_name']) ?></td>
                    <td class="audit-date"><?= $date ?></td>
                    <td class="col-tracked"><?= fmtSeconds($total) ?></td>
                    <td class="col-idle"><?= $idle > 0 ? '<span class="val-idle">' . fmtSeconds($idle) . '</span>' : '<span class="text-muted">—</span>' ?></td>
                    <td class="col-discarded"><?= $discarded > 0 ? '<span class="val-discarded">' . fmtSeconds($discarded) . '</span>' : '<span class="text-muted">—</span>' ?></td>
                    <td class="col-net">
                        <strong><?= fmtSeconds($net) ?></strong>
                        <?php if ($discarded > 0): ?>
                            <span class="audit-savings">(-<?= fmtSeconds($discarded) ?> idle)</span>
                        <?php endif; ?>
                    </td>
                    <td class="col-score">
                        <?php if (is_numeric($score)):
                            $level = $score >= 50 ? 'score-high' : ($score >= 20 ? 'score-mid' : 'score-low');
                        ?>
                            <span class="activity-score <?= $level ?>"><?= $score ?></span>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($closeReason === 'abandoned'): ?>
                            <span class="close-badge close-abandoned" title="Auto-closed by server after 30min no heartbeat">⚠ Auto-Closed</span>
                        <?php elseif ($closeReason === 'auto'): ?>
                            <span class="close-badge close-auto">⚙ Auto</span>
                        <?php else: ?>
                            <span class="close-badge close-manual">✓ Manual</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="status-badge status-<?= htmlspecialchars($e['status']) ?>">
                            <?= ucfirst($e['status']) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="audit-count">Showing <?= count($entries) ?> session<?= count($entries) !== 1 ? 's' : '' ?></p>
    <?php endif; ?>

</div>

<?php include_once __DIR__ . '/../includes/footer_partial.php'; ?>
<script src="/TimeForge_Capstone/js/theme.js"></script>
</body>
</html>
