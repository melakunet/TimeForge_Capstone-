<?php
$page_title = 'Freelancer Portal';

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';

requireRole('freelancer');

$current_user = getCurrentUser();
$user_id      = $_SESSION['user_id'];
$company_id   = $_SESSION['company_id'];

// Live stats for this freelancer
$proj_stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT p.id) AS project_count
    FROM projects p
    WHERE p.company_id = :cid AND p.deleted_at IS NULL
");
$proj_stmt->execute([':cid' => $company_id]);
$active_projects = (int)$proj_stmt->fetchColumn();

$hours_stmt = $pdo->prepare("
    SELECT
        COALESCE(SUM(te.total_seconds), 0) AS seconds,
        COALESCE(SUM((te.total_seconds / 3600) * p.hourly_rate), 0) AS earned
    FROM time_entries te
    INNER JOIN projects p ON p.id = te.project_id
    WHERE te.user_id = :uid
      AND te.status IN ('completed','approved')
      AND te.end_time IS NOT NULL
      AND YEAR(te.start_time) = YEAR(NOW())
      AND MONTH(te.start_time) = MONTH(NOW())
");
$hours_stmt->execute([':uid' => $user_id]);
$fl_stats = $hours_stmt->fetch();
$hours_this_month = round(($fl_stats['seconds'] ?? 0) / 3600, 1);
$earnings         = round($fl_stats['earned'] ?? 0, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - TimeForge</title>
    <link rel="stylesheet" href="/TimeForge_Capstone/css/time_tracker.css">
    <link rel="stylesheet" href="/TimeForge_Capstone/css/style.css">
    <link rel="icon" type="image/png" href="/TimeForge_Capstone/icons/logo.png">
</head>
<body>
    <?php include_once __DIR__ . '/../includes/header_partial.php'; ?>

<div class="container">
    <h1 style="margin-bottom: 2rem; color: var(--color-accent);">Freelancer Portal</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        <div class="card">
            <h3 style="color: var(--color-accent); margin-bottom: 1rem;">Active Projects</h3>
            <p style="font-size: 2rem; font-weight: bold;"><?php echo $active_projects; ?></p>
            <p style="color: var(--color-text-secondary);">Projects in your company</p>
        </div>
        
        <div class="card">
            <h3 style="color: var(--color-accent); margin-bottom: 1rem;">Hours This Month</h3>
            <p style="font-size: 2rem; font-weight: bold;"><?php echo $hours_this_month; ?></p>
            <p style="color: var(--color-text-secondary);">Your tracked hours this month</p>
        </div>
        
        <div class="card">
            <h3 style="color: var(--color-accent); margin-bottom: 1rem;">Earnings This Month</h3>
            <p style="font-size: 2rem; font-weight: bold;">$<?php echo number_format($earnings, 2); ?></p>
            <p style="color: var(--color-text-secondary);">Based on approved time entries</p>
        </div>
    </div>
    
    <div class="card">
        <h2 style="color: var(--color-accent); margin-bottom: 1.5rem;">Freelancer Functions</h2>
        <ul style="list-style: none; padding: 0;">
            <li style="margin-bottom: 1rem;">
                <a href="/TimeForge_Capstone/index.php" class="btn btn-primary" style="display: inline-block;">View Projects</a>
            </li>
            <li style="margin-bottom: 1rem;">
                <a href="/TimeForge_Capstone/index.php" class="btn btn-primary" style="display: inline-block;">Track Time</a>
            </li>
            <li style="margin-bottom: 1rem;">
                <a href="#" class="btn btn-primary" style="display: inline-block;">View Invoices</a>
            </li>
        </ul>
    </div>

    <!-- NEW: Quick Start Widget -->
    <?php include __DIR__ . '/quick_start.php'; ?>
</div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> TimeForge. All rights reserved.</p>
        <p>Professional Time Tracking & Project Management Solution</p>
        <p>Web Capstone Project by Etefworkie Melaku — triOS College, Mobile and Web App Development</p>
    </footer>
    
    <script src="/TimeForge_Capstone/js/time_tracker.js"></script>
    <script src="/TimeForge_Capstone/js/theme.js"></script>
    <script src="/TimeForge_Capstone/js/animations.js"></script>
</body>
</html>
