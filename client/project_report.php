<?php
$page_title = 'Project Report';

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../db.php';

requireRole('client');

$user_id    = $_SESSION['user_id'];
$project_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Validate project ID supplied
if (!$project_id) {
    setFlash('error', 'Invalid project ID.');
    header('Location: /TimeForge_Capstone/client/dashboard.php');
    exit;
}

// ── Fetch project — must belong to this client (security: INNER JOIN on user_id) ──
$project_query = "
    SELECT
        p.id,
        p.project_name,
        p.description,
        p.status,
        p.stage,
        p.hourly_rate,
        p.budget,
        p.deadline,
        p.progress_percentage,
        c.client_name,
        c.company_name
    FROM projects p
    INNER JOIN clients c ON c.id = p.client_id
    WHERE p.id         = :project_id
      AND c.user_id    = :user_id
      AND p.deleted_at IS NULL
    LIMIT 1
";
$proj_stmt = $pdo->prepare($project_query);
$proj_stmt->execute([':project_id' => $project_id, ':user_id' => $user_id]);
$project = $proj_stmt->fetch(PDO::FETCH_ASSOC);

// If not found or doesn't belong to this client → deny
if (!$project) {
    setFlash('error', 'Project not found or access denied.');
    header('Location: /TimeForge_Capstone/client/dashboard.php');
    exit;
}

// ── Fetch approved time entries for this project only ─────────────────────────
$entries_query = "
    SELECT
        te.id,
        te.start_time,
        te.end_time,
        te.total_seconds,
        te.description,
        te.entry_type,
        te.reviewed_at,
        u.full_name AS worker_name
    FROM time_entries te
    LEFT JOIN users u ON u.id = te.user_id
    WHERE te.project_id = :project_id
      AND te.status     = 'approved'
      AND te.end_time  IS NOT NULL
    ORDER BY te.start_time ASC
";
$entries_stmt = $pdo->prepare($entries_query);
$entries_stmt->execute([':project_id' => $project_id]);
$entries = $entries_stmt->fetchAll(PDO::FETCH_ASSOC);

// ── Calculate totals ──────────────────────────────────────────────────────────
$total_seconds = 0;
foreach ($entries as $entry) {
    $total_seconds += (int)$entry['total_seconds'];
}
$total_hours  = $total_seconds / 3600;
$hourly_rate  = (float)($project['hourly_rate'] ?? 0);
$total_cost   = $total_hours * $hourly_rate;
$budget       = (float)($project['budget'] ?? 0);
$remaining    = $budget > 0 ? ($budget - $total_cost) : null;

// ── Stage label map ───────────────────────────────────────────────────────────
$stage_labels = [
    'planning'    => 'Planning',
    'in_progress' => 'In Progress',
    'review'      => 'In Review',
    'testing'     => 'Testing',
    'on_hold'     => 'On Hold',
    'completed'   => 'Completed',
    'archived'    => 'Archived',
];
$stage_label = $stage_labels[$project['stage']] ?? ucfirst($project['stage']);

$page_title  = 'Report: ' . $project['project_name'];
$flash       = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - TimeForge</title>
    <link rel="stylesheet" href="/TimeForge_Capstone/css/style.css">
    <link rel="stylesheet" href="/TimeForge_Capstone/css/client-portal.css">
    <link rel="icon" type="image/png" href="/TimeForge_Capstone/icons/logo.png">
</head>
<body>
    <?php include_once __DIR__ . '/../includes/header_partial.php'; ?>

<main class="container">

    <?php if (!empty($flash['message'])): ?>
        <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>

    <!-- ── Breadcrumb ── -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li><a href="/TimeForge_Capstone/client/dashboard.php">Client Portal</a></li>
            <li class="active"><?php echo htmlspecialchars($project['project_name']); ?> — Report</li>
        </ol>
    </nav>

    <!-- ── Page Header ── -->
    <div class="client-hero">
        <div>
            <h1 class="text-accent"><?php echo htmlspecialchars($project['project_name']); ?></h1>
            <p>
                <?php echo htmlspecialchars($project['client_name']); ?>
                <?php if (!empty($project['company_name'])): ?>
                    &mdash; <?php echo htmlspecialchars($project['company_name']); ?>
                <?php endif; ?>
            </p>
        </div>
        <div>
            <button class="btn-print" onclick="printReport()">🖨 Print Report</button>
        </div>
    </div>

    <!-- ── Project Overview Card ── -->
    <div class="card">
        <div class="section-header">
            <h2>Project Overview</h2>
            <span class="status-badge status-<?php echo htmlspecialchars($project['status']); ?>">
                <?php echo ucfirst(htmlspecialchars($project['status'])); ?>
            </span>
        </div>

        <?php if (!empty($project['description'])): ?>
            <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
        <?php endif; ?>

        <div class="project-info-grid">
            <div class="project-info-item">
                <div class="pi-label">Stage</div>
                <div class="pi-value"><?php echo htmlspecialchars($stage_label); ?></div>
            </div>
            <div class="project-info-item">
                <div class="pi-label">Hourly Rate</div>
                <div class="pi-value">$<?php echo number_format($hourly_rate, 2); ?>/hr</div>
            </div>
            <?php if ($budget > 0): ?>
            <div class="project-info-item">
                <div class="pi-label">Total Budget</div>
                <div class="pi-value">$<?php echo number_format($budget, 2); ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($project['deadline'])): ?>
            <div class="project-info-item">
                <div class="pi-label">Deadline</div>
                <div class="pi-value"><?php echo date('M d, Y', strtotime($project['deadline'])); ?></div>
            </div>
            <?php endif; ?>
            <?php if ((int)$project['progress_percentage'] > 0): ?>
            <div class="project-info-item">
                <div class="pi-label">Progress</div>
                <div class="pi-value"><?php echo (int)$project['progress_percentage']; ?>%</div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── Financial Summary ── -->
    <div class="card">
        <div class="section-header">
            <h2>Financial Summary</h2>
        </div>
        <div class="report-summary-grid">
            <div class="report-summary-item">
                <div class="rs-label">Approved Hours</div>
                <div class="rs-value"><?php echo number_format($total_hours, 2); ?> hrs</div>
            </div>
            <div class="report-summary-item highlight">
                <div class="rs-label">Total Billed</div>
                <div class="rs-value">$<?php echo number_format($total_cost, 2); ?></div>
            </div>
            <?php if ($budget > 0): ?>
            <div class="report-summary-item <?php echo $remaining < 0 ? 'danger' : 'success'; ?>">
                <div class="rs-label">Budget Remaining</div>
                <div class="rs-value">$<?php echo number_format($remaining, 2); ?></div>
            </div>
            <?php endif; ?>
            <div class="report-summary-item">
                <div class="rs-label">Approved Entries</div>
                <div class="rs-value"><?php echo count($entries); ?></div>
            </div>
        </div>
    </div>

    <!-- ── Approved Time Entries ── -->
    <div class="card">
        <div class="section-header">
            <h2>Approved Time Log</h2>
        </div>

        <?php if (empty($entries)): ?>
            <div class="empty-portal">
                <div class="empty-icon">⏱</div>
                <h3>No approved entries yet</h3>
                <p>Time entries will appear here once they have been reviewed and approved.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Worker</th>
                            <th>Description</th>
                            <th>Duration</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entries as $i => $entry):
                            $entry_hours = (int)$entry['total_seconds'] / 3600;
                            $entry_cost  = $entry_hours * $hourly_rate;
                            $duration_h  = floor($entry_hours);
                            $duration_m  = round(fmod($entry_hours, 1) * 60);
                        ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><?php echo date('M d, Y', strtotime($entry['start_time'])); ?></td>
                            <td><?php echo htmlspecialchars($entry['worker_name'] ?? '—'); ?></td>
                            <td class="td-desc"><?php echo htmlspecialchars($entry['description'] ?? '—'); ?></td>
                            <td>
                                <strong>
                                    <?php printf('%dh %02dm', $duration_h, $duration_m); ?>
                                </strong>
                            </td>
                            <td>$<?php echo number_format($entry_cost, 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4"><strong>Total</strong></td>
                            <td>
                                <strong>
                                    <?php
                                        $th = floor($total_hours);
                                        $tm = round(fmod($total_hours, 1) * 60);
                                        printf('%dh %02dm', $th, $tm);
                                    ?>
                                </strong>
                            </td>
                            <td><strong>$<?php echo number_format($total_cost, 2); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- ── Back Link ── -->
    <div style="margin-top: 1.5rem;">
        <a href="/TimeForge_Capstone/client/dashboard.php" class="btn btn-secondary btn-sm">&larr; Back to Dashboard</a>
    </div>

</main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> TimeForge. All rights reserved.</p>
        <p>Professional Time Tracking & Project Management Solution</p>
        <p>Web Capstone Project by Etefworkie Melaku — triOS College, Mobile and Web App Development</p>
    </footer>

    <script src="/TimeForge_Capstone/js/theme.js"></script>
    <script src="/TimeForge_Capstone/js/animations.js"></script>
    <script src="/TimeForge_Capstone/js/client-portal.js"></script>
</body>
</html>
