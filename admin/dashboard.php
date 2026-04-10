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
        </ul>
    </div>
</div>

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
