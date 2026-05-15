<?php
$page_title = 'Admin Dashboard';

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';

requireRole('admin');

$company_id  = $_SESSION['company_id'];
$current_user = getCurrentUser();

// Live stats — scoped to this company
$user_count    = $pdo->prepare("SELECT COUNT(*) FROM users WHERE company_id = :cid");
$user_count->execute([':cid' => $company_id]);
$total_users   = (int)$user_count->fetchColumn();

$role_counts   = $pdo->prepare("SELECT role, COUNT(*) AS n FROM users WHERE company_id = :cid GROUP BY role");
$role_counts->execute([':cid' => $company_id]);
$roles_raw     = $role_counts->fetchAll(PDO::FETCH_KEY_PAIR);

$proj_count    = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE company_id = :cid AND deleted_at IS NULL");
$proj_count->execute([':cid' => $company_id]);
$total_projects = (int)$proj_count->fetchColumn();

$entry_count   = $pdo->prepare("SELECT COUNT(*) FROM time_entries WHERE company_id = :cid");
$entry_count->execute([':cid' => $company_id]);
$total_entries  = (int)$entry_count->fetchColumn();

// Projects list for dashboard panel
$projects_stmt = $pdo->prepare("
    SELECT p.id, p.project_name, p.status, p.hourly_rate, p.stage,
           c.client_name AS client_name,
           (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id) AS task_count,
           (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id AND t.status NOT IN ('done','cancelled')) AS open_tasks
    FROM projects p
    LEFT JOIN clients c ON c.id = p.client_id
    WHERE p.company_id = :cid AND p.deleted_at IS NULL
    ORDER BY p.status ASC, p.project_name ASC
");
$projects_stmt->execute([':cid' => $company_id]);
$all_projects = $projects_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - TimeForge</title>
    <link rel="stylesheet" href="<?= APP_BASE ?>/css/style.css">
    <link rel="stylesheet" href="<?= APP_BASE ?>/css/time_tracker.css">
    <link rel="icon" type="image/png" href="<?= APP_BASE ?>/icons/logo.png">
</head>
<body>
    <?php include_once __DIR__ . '/../includes/header_partial.php'; ?>

<div class="container">
    <h1 style="margin-bottom: 2rem; color: var(--color-accent);">Admin Dashboard</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        <div class="card">
            <h3 style="color: var(--color-accent); margin-bottom: 1rem;">Total Users</h3>
            <p style="font-size: 2rem; font-weight: bold;"><?php echo $total_users; ?></p>
            <p style="color: var(--color-text-secondary);">
                <?php echo $roles_raw['admin'] ?? 0; ?> Admin,
                <?php echo $roles_raw['freelancer'] ?? 0; ?> Freelancer,
                <?php echo $roles_raw['client'] ?? 0; ?> Client
            </p>
        </div>
        
        <div class="card">
            <h3 style="color: var(--color-accent); margin-bottom: 1rem;">Active Projects</h3>
            <p style="font-size: 2rem; font-weight: bold;"><?php echo $total_projects; ?></p>
            <p style="color: var(--color-text-secondary);">Across your company</p>
        </div>
        
        <div class="card">
            <h3 style="color: var(--color-accent); margin-bottom: 1rem;">Time Entries</h3>
            <p style="font-size: 2rem; font-weight: bold;"><?php echo $total_entries; ?></p>
            <p style="color: var(--color-text-secondary);">Total tracked sessions</p>
        </div>
        
        <!-- NEW: Quick Start Widget -->
        <?php include __DIR__ . '/dashboard_quick_start.php'; ?>
    </div>

    <!-- ── Projects Panel ─────────────────────────────────────────────────── -->
    <div class="card" style="margin-bottom:2rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; flex-wrap:wrap; gap:.75rem;">
            <h2 style="color:var(--color-accent); margin:0;">📁 Projects</h2>
            <a href="<?= APP_BASE ?>/add_project.php" class="btn btn-primary">+ New Project</a>
        </div>
        <?php if (empty($all_projects)): ?>
            <p style="color:var(--color-text-secondary);">No projects yet. <a href="<?= APP_BASE ?>/add_project.php">Create your first project →</a></p>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table" style="width:100%;">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Client</th>
                    <th>Rate</th>
                    <th>Status</th>
                    <th>Tasks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($all_projects as $proj):
                $status_color = match($proj['status']) {
                    'active'    => '#22c55e',
                    'completed' => '#3b82f6',
                    'on_hold'   => '#f59e0b',
                    default     => '#94a3b8',
                };
            ?>
                <tr>
                    <td style="font-weight:600;">
                        <a href="<?= APP_BASE ?>/project_details.php?id=<?= (int)$proj['id'] ?>" style="color:var(--color-text); text-decoration:none;">
                            <?= htmlspecialchars($proj['project_name']) ?>
                        </a>
                    </td>
                    <td style="color:var(--color-text-secondary); font-size:.88rem;"><?= htmlspecialchars($proj['client_name'] ?? '—') ?></td>
                    <td style="font-size:.88rem;">$<?= number_format((float)$proj['hourly_rate'], 2) ?>/hr</td>
                    <td>
                        <span style="background:<?= $status_color ?>22; color:<?= $status_color ?>; border:1px solid <?= $status_color ?>55; padding:.2rem .65rem; border-radius:999px; font-size:.78rem; font-weight:600; text-transform:capitalize;">
                            <?= ucfirst($proj['status']) ?>
                        </span>
                    </td>
                    <td style="font-size:.88rem;">
                        <?php if ($proj['open_tasks'] > 0): ?>
                            <span style="color:#f59e0b;"><?= (int)$proj['open_tasks'] ?> open</span>
                            <span style="color:var(--color-text-secondary);">/<?= (int)$proj['task_count'] ?></span>
                        <?php else: ?>
                            <span style="color:var(--color-text-secondary);"><?= (int)$proj['task_count'] ?> total</span>
                        <?php endif; ?>
                    </td>
                    <td style="white-space:nowrap; font-size:.88rem;">
                        <a href="<?= APP_BASE ?>/project_details.php?id=<?= (int)$proj['id'] ?>" style="color:#3b82f6;">View</a>
                        &nbsp;|&nbsp;
                        <a href="<?= APP_BASE ?>/tasks.php?project_id=<?= (int)$proj['id'] ?>" style="color:#3b82f6;">Tasks</a>
                        &nbsp;|&nbsp;
                        <a href="<?= APP_BASE ?>/edit_project.php?id=<?= (int)$proj['id'] ?>" style="color:#94a3b8;">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Phase 10: Live Freelancer Presence — vanilla JS panel (reliable, no React dependency) -->
    <div class="card" style="margin-bottom: 2rem;" id="presence-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <h2 style="color:var(--color-accent); margin:0;">🟢 Live Freelancer Presence</h2>
            <span id="presence-updated" style="font-size:0.78rem; color:var(--color-text-secondary);">Loading…</span>
        </div>
        <div id="presence-panel">
            <p style="color:var(--color-text-secondary);">Fetching presence data…</p>
        </div>
    </div>

    <div class="card">
        <h2 style="color: var(--color-accent); margin-bottom: 1.5rem;">Admin Functions</h2>
        <ul style="list-style: none; padding: 0;">
            <li style="margin-bottom: 1rem;">
                <a href="<?= APP_BASE ?>/admin/users.php" class="btn btn-primary" style="display: inline-block;">Manage Users</a>
            </li>
            <li style="margin-bottom: 1rem;">
                <a href="<?= APP_BASE ?>/admin/audit_logs.php" class="btn btn-primary" style="display: inline-block;">View Audit Logs</a>
            </li>
            <li style="margin-bottom: 1rem;">
                <a href="<?= APP_BASE ?>/admin/system_settings.php" class="btn btn-primary" style="display:inline-block;">⚙️ System Settings</a>
            </li>
            <li style="margin-bottom: 1rem;">
                <a href="<?= APP_BASE ?>/admin/session_audit.php" class="btn btn-primary" style="display: inline-block;">Session Audit Log</a>
            </li>
            <!-- Reporting and invoicing -->
            <li style="margin-bottom: 1rem;">
                <a href="<?= APP_BASE ?>/admin/reports.php" class="btn btn-primary" style="display: inline-block;">📊 Financial Reports</a>
            </li>
            <li style="margin-bottom: 1rem;">
                <a href="<?= APP_BASE ?>/invoices/history.php" class="btn btn-primary" style="display: inline-block;">🧾 Invoice History</a>
            </li>
            <!-- Phase 9 -->
            <li style="margin-bottom: 1rem;">
                <a href="<?= APP_BASE ?>/admin/screenshots.php" class="btn btn-primary" style="display: inline-block;">📷 Activity Screenshots</a>
            </li>
        </ul>
    </div>

    <!-- Phase 9: Recent Screenshots mini-panel -->
    <?php
    $recent_shots = $pdo->prepare("
        SELECT s.id, s.file_path, s.activity_score_at_capture, s.captured_at,
               u.full_name, p.project_name
        FROM screenshots s
        LEFT JOIN users    u ON u.id = s.user_id
        LEFT JOIN projects p ON p.id = s.project_id
        WHERE s.company_id = :cid
        ORDER BY s.captured_at DESC
        LIMIT 6
    ");
    $recent_shots->execute([':cid' => $company_id]);
    $recent_shots = $recent_shots->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="card" style="margin-top:2rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <h2 style="color:var(--color-accent); margin:0;">📷 Recent Screenshots</h2>
            <a href="<?= APP_BASE ?>/admin/screenshots.php" style="font-size:0.85rem;">View All →</a>
        </div>
        <?php if (empty($recent_shots)): ?>
            <p style="color:var(--color-text-secondary);">No screenshots yet. Screenshots appear here once a worker starts a timer on a project with screenshots enabled.</p>
        <?php else: ?>
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:0.75rem;">
            <?php foreach ($recent_shots as $shot):
                $border = $shot['activity_score_at_capture'] == 0 ? '2px solid #e74c3c' : '2px solid transparent';
            ?>
            <a href="<?= APP_BASE ?>/admin/screenshots.php" style="display:block; border-radius:6px; overflow:hidden; border:<?= $border ?>; text-decoration:none;">
                <img src="<?= APP_BASE ?>/api/screenshot_img.php?id=<?= (int)$shot['id'] ?>" style="width:100%;height:80px;object-fit:cover;display:block;" alt="screenshot">
                <div style="font-size:0.7rem; padding:0.3rem 0.4rem; background:var(--color-card); color:var(--color-text-secondary);">
                    <?= htmlspecialchars($shot['full_name'] ?? 'Unknown') ?> &bull; <?= date('M j, g:i a', strtotime($shot['captured_at'])) ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

</div><!-- /.container -->

    <footer>
        <p>&copy; <?php echo date('Y'); ?> TimeForge. All rights reserved.</p>
        <p>Professional Time Tracking & Project Management Solution</p>
        <p>Web Capstone Project by Etefworkie Melaku — triOS College, Mobile and Web App Development</p>
    </footer>
    
    <script src="<?= APP_BASE ?>/js/theme.js"></script>
    <script src="<?= APP_BASE ?>/js/animations.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="<?= APP_BASE ?>/js/time_tracker.js"></script>
    <script src="<?= APP_BASE ?>/js/presence.js"></script>
</body>
</html>
