<?php
$page_title = 'My Projects';

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../db.php';

requireRole('client');

$current_user = getCurrentUser();
$user_id      = $_SESSION['user_id'];

// ── Filter parameter (active / completed / all) ───────────────────────────────
$filter = $_GET['filter'] ?? 'active';
$allowed_filters = ['active', 'completed', 'all'];
if (!in_array($filter, $allowed_filters, true)) {
    $filter = 'active';
}

// ── Build WHERE clause based on filter ────────────────────────────────────────
$status_clause = '';
if ($filter === 'active') {
    $status_clause = "AND p.status = 'active'";
} elseif ($filter === 'completed') {
    $status_clause = "AND p.status = 'completed'";
}
// 'all' — no additional status filter

// ── Fetch projects belonging to this client, with per-project totals ──────────
$query = "
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
        COALESCE(SUM(CASE WHEN te.status = 'approved' AND te.end_time IS NOT NULL
                         THEN te.total_seconds ELSE 0 END), 0) AS approved_seconds,
        COALESCE(SUM(CASE WHEN te.status = 'pending'
                         THEN 1 ELSE 0 END), 0)                AS pending_count,
        COALESCE(SUM(CASE WHEN te.end_time IS NOT NULL
                         THEN te.total_seconds ELSE 0 END), 0) AS all_seconds
    FROM clients c
    INNER JOIN projects p  ON p.client_id = c.id
    LEFT  JOIN time_entries te ON te.project_id = p.id
    WHERE c.user_id    = :user_id
      AND p.deleted_at IS NULL
      $status_clause
    GROUP BY p.id
    ORDER BY p.id DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute([':user_id' => $user_id]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ── Count per status for the filter tab badges ────────────────────────────────
$counts_query = "
    SELECT p.status, COUNT(p.id) AS cnt
    FROM clients c
    INNER JOIN projects p ON p.client_id = c.id
    WHERE c.user_id = :user_id AND p.deleted_at IS NULL
    GROUP BY p.status
";
$counts_stmt = $pdo->prepare($counts_query);
$counts_stmt->execute([':user_id' => $user_id]);
$counts_raw = $counts_stmt->fetchAll(PDO::FETCH_ASSOC);

$counts = ['active' => 0, 'completed' => 0, 'all' => 0];
foreach ($counts_raw as $row) {
    $counts[$row['status']] = (int)$row['cnt'];
    $counts['all'] += (int)$row['cnt'];
}

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

$flash = getFlash();
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
            <li class="active">My Projects</li>
        </ol>
    </nav>

    <!-- ── Page Header ── -->
    <div class="client-hero">
        <div>
            <h1 class="text-accent">My Projects</h1>
            <p>All projects assigned to you, <?php echo htmlspecialchars($current_user['full_name'] ?? 'Client'); ?></p>
        </div>
    </div>

    <!-- ── Filter Tabs ── -->
    <div class="filter-section">
        <div class="filter-tabs">
            <a href="?filter=active"
               class="filter-tab <?php echo $filter === 'active' ? 'active' : ''; ?>">
                Active
                <?php if ($counts['active'] > 0): ?>
                    (<?php echo $counts['active']; ?>)
                <?php endif; ?>
            </a>
            <a href="?filter=completed"
               class="filter-tab <?php echo $filter === 'completed' ? 'active' : ''; ?>">
                Completed
                <?php if ($counts['completed'] > 0): ?>
                    (<?php echo $counts['completed']; ?>)
                <?php endif; ?>
            </a>
            <a href="?filter=all"
               class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">
                All Projects
                (<?php echo $counts['all']; ?>)
            </a>
        </div>
    </div>

    <!-- ── Projects Table ── -->
    <div class="card">
        <?php if (empty($projects)): ?>
            <div class="empty-portal">
                <div class="empty-icon">📂</div>
                <h3>No <?php echo $filter !== 'all' ? $filter : ''; ?> projects found</h3>
                <p>
                    <?php if ($filter === 'active'): ?>
                        You have no active projects right now.
                    <?php elseif ($filter === 'completed'): ?>
                        No completed projects yet.
                    <?php else: ?>
                        No projects have been assigned to you yet.
                    <?php endif; ?>
                </p>
                <a href="/TimeForge_Capstone/client/dashboard.php" class="btn btn-secondary btn-sm">
                    &larr; Back to Dashboard
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="project-table">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Stage</th>
                            <th>Rate</th>
                            <th>Approved Hrs</th>
                            <th>Billed</th>
                            <th>Budget Left</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $p):
                            $approved_hrs  = (int)$p['approved_seconds'] / 3600;
                            $billed        = $approved_hrs * (float)$p['hourly_rate'];
                            $budget        = (float)($p['budget'] ?? 0);
                            $remaining     = $budget > 0 ? $budget - $billed : null;
                            $stage_label   = $stage_labels[$p['stage']] ?? ucfirst($p['stage']);
                            $progress      = min((int)($p['progress_percentage'] ?? 0), 100);
                        ?>
                        <tr>
                            <td class="project-name">
                                <?php echo htmlspecialchars($p['project_name']); ?>
                                <?php if ($p['pending_count'] > 0): ?>
                                    <span class="badge-pending">⏳ <?php echo (int)$p['pending_count']; ?> pending</span>
                                <?php endif; ?>
                                <?php if ($progress > 0): ?>
                                    <div class="progress-bar-wrap" title="<?php echo $progress; ?>% complete">
                                        <div class="progress-bar-fill" style="width: <?php echo $progress; ?>%"></div>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($stage_label); ?></td>
                            <td>$<?php echo number_format($p['hourly_rate'], 2); ?>/hr</td>
                            <td><?php echo number_format($approved_hrs, 2); ?> hrs</td>
                            <td>$<?php echo number_format($billed, 2); ?></td>
                            <td>
                                <?php if ($remaining !== null): ?>
                                    <span class="<?php echo $remaining < 0 ? 'text-danger' : 'text-success'; ?>">
                                        $<?php echo number_format($remaining, 2); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-secondary">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo !empty($p['deadline'])
                                    ? date('M d, Y', strtotime($p['deadline']))
                                    : '<span class="text-secondary">—</span>'; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo htmlspecialchars($p['status']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($p['status'])); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="/TimeForge_Capstone/project_details.php?id=<?php echo (int)$p['id']; ?>"
                                   class="action-link">Details</a>
                                <span class="action-sep">|</span>
                                <a href="/TimeForge_Capstone/client/project_report.php?id=<?php echo (int)$p['id']; ?>"
                                   class="action-link">Report</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- ── Back Link ── -->
    <div style="margin-top: 1.5rem;">
        <a href="/TimeForge_Capstone/client/dashboard.php" class="btn btn-secondary btn-sm">
            &larr; Back to Dashboard
        </a>
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
