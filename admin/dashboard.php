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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - TimeForge</title>
    <link rel="stylesheet" href="/TimeForge_Capstone/css/style.css">
    <link rel="stylesheet" href="/TimeForge_Capstone/css/time_tracker.css">
    <link rel="icon" type="image/png" href="/TimeForge_Capstone/icons/logo.png">
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

    <!-- Live freelancer presence panel -->
    <div class="card" style="margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 style="color: var(--color-accent); margin: 0;">🟢 Live Freelancer Presence</h2>
            <span id="presence-updated" style="font-size: 0.8rem; color: var(--color-text-secondary);">Updating…</span>
        </div>
        <div id="presence-panel">
            <p style="color: var(--color-text-secondary);">Loading…</p>
        </div>
    </div>

    <div class="card">
        <h2 style="color: var(--color-accent); margin-bottom: 1.5rem;">Admin Functions</h2>
        <ul style="list-style: none; padding: 0;">
            <li style="margin-bottom: 1rem;">
                <a href="/TimeForge_Capstone/admin/users.php" class="btn btn-primary" style="display: inline-block;">Manage Users</a>
            </li>
            <li style="margin-bottom: 1rem;">
                <a href="/TimeForge_Capstone/admin/audit_logs.php" class="btn btn-primary" style="display: inline-block;">View Audit Logs</a>
            </li>
            <li style="margin-bottom: 1rem;">
                <button class="btn btn-primary" style="display: inline-block; opacity: 0.6; cursor: not-allowed;" title="Coming soon">System Settings (Soon)</button>
            </li>
            <li style="margin-bottom: 1rem;">
                <a href="/TimeForge_Capstone/admin/session_audit.php" class="btn btn-primary" style="display: inline-block;">Session Audit Log</a>
            </li>
            <!-- Reporting and invoicing -->
            <li style="margin-bottom: 1rem;">
                <a href="/TimeForge_Capstone/admin/reports.php" class="btn btn-primary" style="display: inline-block;">📊 Financial Reports</a>
            </li>
            <li style="margin-bottom: 1rem;">
                <a href="/TimeForge_Capstone/invoices/history.php" class="btn btn-primary" style="display: inline-block;">🧾 Invoice History</a>
            </li>
            <!-- Phase 9 -->
            <li style="margin-bottom: 1rem;">
                <a href="/TimeForge_Capstone/admin/screenshots.php" class="btn btn-primary" style="display: inline-block;">📷 Activity Screenshots</a>
            </li>
        </ul>
    </div>

    <!-- Phase 9: Recent Screenshots mini-panel -->
    <?php
    $recent_shots = $pdo->prepare("
        SELECT s.file_path, s.activity_score_at_capture, s.captured_at, u.full_name, p.project_name
        FROM screenshots s
        INNER JOIN users    u ON u.id = s.user_id
        INNER JOIN projects p ON p.id = s.project_id
        WHERE s.company_id = :cid
        ORDER BY s.captured_at DESC
        LIMIT 6
    ");
    $recent_shots->execute([':cid' => $company_id]);
    $recent_shots = $recent_shots->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <?php if (!empty($recent_shots)): ?>
    <div class="card" style="margin-top:2rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <h2 style="color:var(--color-accent); margin:0;">📷 Recent Screenshots</h2>
            <a href="/TimeForge_Capstone/admin/screenshots.php" style="font-size:0.85rem;">View All →</a>
        </div>
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:0.75rem;">
            <?php foreach ($recent_shots as $shot):
                $border = $shot['activity_score_at_capture'] == 0 ? '2px solid #e74c3c' : '2px solid transparent';
            ?>
            <a href="/TimeForge_Capstone/admin/screenshots.php" style="display:block; border-radius:6px; overflow:hidden; border:<?= $border ?>; text-decoration:none;">
                <img src="/TimeForge_Capstone/<?= htmlspecialchars($shot['file_path']) ?>" style="width:100%;height:80px;object-fit:cover;display:block;" alt="screenshot">
                <div style="font-size:0.7rem; padding:0.3rem 0.4rem; background:var(--color-card); color:var(--color-text-secondary);">
                    <?= htmlspecialchars($shot['full_name']) ?> &bull; <?= date('g:i a', strtotime($shot['captured_at'])) ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div><!-- /.container -->

    <footer>
        <p>&copy; <?php echo date('Y'); ?> TimeForge. All rights reserved.</p>
        <p>Professional Time Tracking & Project Management Solution</p>
        <p>Web Capstone Project by Etefworkie Melaku — triOS College, Mobile and Web App Development</p>
    </footer>
    
    <script src="/TimeForge_Capstone/js/theme.js"></script>
    <script src="/TimeForge_Capstone/js/animations.js"></script>
    <script src="/TimeForge_Capstone/js/time_tracker.js"></script>
    <script src="/TimeForge_Capstone/js/presence.js"></script>
</body>
</html>
