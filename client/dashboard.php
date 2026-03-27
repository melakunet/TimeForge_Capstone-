<?php
$page_title = 'Client Portal';

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../db.php';

requireRole('client');

$current_user = getCurrentUser();
$user_id      = $_SESSION['user_id'];

// ── 1. Summary stats: active project count, total approved hours, total cost ──
$stats_query = "
    SELECT
        COUNT(DISTINCT p.id)                                          AS project_count,
        COALESCE(SUM(te.total_seconds), 0)                           AS total_seconds,
        COALESCE(SUM((te.total_seconds / 3600) * p.hourly_rate), 0) AS total_cost
    FROM clients c
    INNER JOIN projects p  ON p.client_id  = c.id
    LEFT  JOIN time_entries te
           ON te.project_id = p.id
          AND te.status      = 'approved'
          AND te.end_time   IS NOT NULL
    WHERE c.user_id    = :user_id
      AND p.deleted_at IS NULL
";
$stats_stmt = $pdo->prepare($stats_query);
$stats_stmt->execute([':user_id' => $user_id]);
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

$total_hours = ($stats['total_seconds'] ?? 0) / 3600;
$total_cost  = $stats['total_cost']  ?? 0;
$project_count = $stats['project_count'] ?? 0;

// ── 2. Project list with per-project approved hours + cost ────────────────────
$projects_query = "
    SELECT
        p.id,
        p.project_name,
        p.status,
        p.stage,
        p.hourly_rate,
        p.budget,
        p.deadline,
        p.progress_percentage,
        COALESCE(SUM(CASE WHEN te.status = 'approved' AND te.end_time IS NOT NULL
                         THEN te.total_seconds ELSE 0 END), 0)   AS approved_seconds,
        COALESCE(SUM(CASE WHEN te.status = 'pending'
                         THEN 1 ELSE 0 END), 0)                  AS pending_count
    FROM clients c
    INNER JOIN projects p  ON p.client_id = c.id
    LEFT  JOIN time_entries te ON te.project_id = p.id
    WHERE c.user_id    = :user_id
      AND p.deleted_at IS NULL
    GROUP BY p.id
    ORDER BY p.id DESC
";
$projects_stmt = $pdo->prepare($projects_query);
$projects_stmt->execute([':user_id' => $user_id]);
$projects = $projects_stmt->fetchAll(PDO::FETCH_ASSOC);

$flash = getFlash();

// Stage label map
$stage_labels = [
    'planning'    => 'Planning',
    'in_progress' => 'In Progress',
    'review'      => 'In Review',
    'testing'     => 'Testing',
    'on_hold'     => 'On Hold',
    'completed'   => 'Completed',
    'archived'    => 'Archived',
];
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

    <!-- Page header -->
    <div class="client-hero">
        <div>
            <h1 class="text-accent">Client Portal</h1>
            <p>Welcome back, <?php echo htmlspecialchars($current_user['full_name'] ?? 'Client'); ?></p>
        </div>
    </div>

    <!-- ── Summary Stats ── -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">Active Projects</div>
            <div class="stat-value"><?php echo (int)$project_count; ?></div>
            <div class="stat-sub">Projects assigned to you</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Approved Hours</div>
            <div class="stat-value"><?php echo number_format($total_hours, 1); ?></div>
            <div class="stat-sub">Total hours across all projects</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Billed</div>
            <div class="stat-value">$<?php echo number_format($total_cost, 2); ?></div>
            <div class="stat-sub">Based on approved time entries</div>
        </div>
        <!-- Phase 7: quick link to invoice history -->
        <div class="stat-card" style="cursor:pointer;" onclick="location.href='/TimeForge_Capstone/invoices/history.php'">
            <div class="stat-label">Invoices</div>
            <div class="stat-value">🧾</div>
            <div class="stat-sub"><a href="/TimeForge_Capstone/invoices/history.php">View your invoices</a></div>
        </div>
    </div>

    <!-- ── Project List ── -->
    <div class="card">
        <div class="section-header">
            <h2>Your Projects</h2>
            <?php if (!empty($projects)): ?>
                <a href="/TimeForge_Capstone/client/projects.php" class="btn btn-secondary btn-sm">
                    View All &rarr;
                </a>
            <?php endif; ?>
        </div>

        <?php if (empty($projects)): ?>
            <div class="empty-portal">
                <div class="empty-icon">📂</div>
                <h3>No projects yet</h3>
                <p>Your projects will appear here once they are assigned to you.</p>
            </div>
        <?php else: ?>
            <div class="project-cards">
                <?php foreach ($projects as $p):
                    $proj_hours = ($p['approved_seconds'] / 3600);
                    $proj_cost  = $proj_hours * ($p['hourly_rate'] ?? 0);
                    $budget     = $p['budget'] ?? 0;
                    $remaining  = $budget > 0 ? ($budget - $proj_cost) : null;
                    $progress   = min((int)($p['progress_percentage'] ?? 0), 100);
                    $stage_label = $stage_labels[$p['stage']] ?? ucfirst($p['stage']);
                ?>
                <div class="project-row">
                    <div>
                        <div class="proj-name"><?php echo htmlspecialchars($p['project_name']); ?></div>
                        <div class="proj-meta">
                            <span>
                                Stage: <strong><?php echo htmlspecialchars($stage_label); ?></strong>
                            </span>
                            <span>
                                Rate: <strong>$<?php echo number_format($p['hourly_rate'], 2); ?>/hr</strong>
                            </span>
                            <span>
                                Approved Hours: <strong><?php echo number_format($proj_hours, 2); ?> hrs</strong>
                            </span>
                            <span>
                                Billed: <strong>$<?php echo number_format($proj_cost, 2); ?></strong>
                            </span>
                            <?php if ($budget > 0): ?>
                            <span>
                                Budget Left:
                                <strong class="<?php echo $remaining < 0 ? 'text-danger' : 'text-success'; ?>">
                                    $<?php echo number_format($remaining, 2); ?>
                                </strong>
                            </span>
                            <?php endif; ?>
                            <?php if (!empty($p['deadline'])): ?>
                            <span>
                                Deadline: <strong><?php echo date('M d, Y', strtotime($p['deadline'])); ?></strong>
                            </span>
                            <?php endif; ?>
                            <?php if ($p['pending_count'] > 0): ?>
                            <span>
                                <span class="badge-pending">⏳ <?php echo (int)$p['pending_count']; ?> pending</span>
                            </span>
                            <?php endif; ?>
                        </div>
                        <?php if ($progress > 0): ?>
                        <div class="progress-bar-wrap" title="<?php echo $progress; ?>% complete">
                            <div class="progress-bar-fill" style="width: <?php echo $progress; ?>%"></div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="proj-actions">
                        <span class="status-badge status-<?php echo htmlspecialchars($p['status']); ?>">
                            <?php echo ucfirst(htmlspecialchars($p['status'])); ?>
                        </span>
                        <a href="/TimeForge_Capstone/project_details.php?id=<?php echo (int)$p['id']; ?>"
                           class="btn btn-secondary btn-sm">
                            View Details
                        </a>
                        <a href="/TimeForge_Capstone/client/project_report.php?id=<?php echo (int)$p['id']; ?>"
                           class="btn btn-primary btn-sm">
                            View Report
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
