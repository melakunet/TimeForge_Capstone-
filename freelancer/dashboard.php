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

    <!-- ── My Tasks ─────────────────────────────────────────────── -->
    <?php
    $my_tasks_stmt = $pdo->prepare("
        SELECT t.id, t.title, t.status, t.priority, t.due_date,
               p.project_name, p.id AS project_id
        FROM tasks t
        INNER JOIN projects p ON p.id = t.project_id
        WHERE t.assigned_to = :uid AND t.company_id = :cid AND t.status != 'done'
        ORDER BY FIELD(t.status,'in_progress','open'), FIELD(t.priority,'high','medium','low'), t.due_date ASC
        LIMIT 15
    ");
    $my_tasks_stmt->execute([':uid' => $user_id, ':cid' => $company_id]);
    $my_tasks = $my_tasks_stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="card" style="margin-top:2rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h2 style="margin:0; color:var(--color-accent);">📋 My Tasks</h2>
            <?php if (count($my_tasks) > 0): ?>
                <span style="font-size:.8rem; background:#334155; color:#94a3b8; border-radius:99px; padding:.2rem .7rem;"><?= count($my_tasks) ?> active</span>
            <?php endif; ?>
        </div>
        <?php if ($my_tasks): ?>
        <table style="width:100%; border-collapse:collapse; font-size:.85rem;">
            <thead>
                <tr style="color:#64748b; border-bottom:1px solid #334155; text-align:left;">
                    <th style="padding:.4rem .6rem; font-weight:600;">Task</th>
                    <th style="padding:.4rem .6rem; font-weight:600;">Project</th>
                    <th style="padding:.4rem .6rem; font-weight:600;">Priority</th>
                    <th style="padding:.4rem .6rem; font-weight:600;">Status</th>
                    <th style="padding:.4rem .6rem; font-weight:600;">Due</th>
                    <th style="padding:.4rem .6rem; font-weight:600;"></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($my_tasks as $mt):
                $is_ov = $mt['due_date'] && strtotime($mt['due_date']) < strtotime('today');
                $pri_col = ['high'=>'#ef4444','medium'=>'#f59e0b','low'=>'#22c55e'][$mt['priority']] ?? '#64748b';
                $sts_map = ['open'=>['⬜','#94a3b8'],'in_progress'=>['🔄','#f59e0b']];
                [$sico,$scol] = $sts_map[$mt['status']] ?? ['?','#64748b'];
            ?>
            <tr style="border-bottom:1px solid #1e293b;">
                <td style="padding:.5rem .6rem; font-weight:500;"><?= htmlspecialchars($mt['title']) ?></td>
                <td style="padding:.5rem .6rem; color:#94a3b8;"><?= htmlspecialchars($mt['project_name']) ?></td>
                <td style="padding:.5rem .6rem; color:<?= $pri_col ?>; font-size:.78rem; font-weight:700; text-transform:uppercase;"><?= $mt['priority'] ?></td>
                <td style="padding:.5rem .6rem; color:<?= $scol ?>;"><?= $sico ?> <?= ucwords(str_replace('_',' ',$mt['status'])) ?></td>
                <td style="padding:.5rem .6rem; color:<?= $is_ov ? '#ef4444' : '#94a3b8' ?>; font-size:.8rem;">
                    <?= $mt['due_date'] ? date('M j', strtotime($mt['due_date'])) . ($is_ov ? ' ⚠' : '') : '—' ?>
                </td>
                <td style="padding:.5rem .6rem;">
                    <form method="POST" action="/TimeForge_Capstone/task_action.php" style="display:inline;">
                        <input type="hidden" name="action" value="move">
                        <input type="hidden" name="task_id" value="<?= $mt['id'] ?>">
                        <input type="hidden" name="project_id" value="<?= $mt['project_id'] ?>">
                        <?php if ($mt['status'] === 'open'): ?>
                            <input type="hidden" name="status" value="in_progress">
                            <button type="submit" style="background:#f59e0b22; color:#f59e0b; border:1px solid #f59e0b44; border-radius:4px; padding:.2rem .6rem; font-size:.75rem; cursor:pointer; font-weight:600;">▶ Start</button>
                        <?php else: ?>
                            <input type="hidden" name="status" value="done">
                            <button type="submit" style="background:#22c55e22; color:#22c55e; border:1px solid #22c55e44; border-radius:4px; padding:.2rem .6rem; font-size:.75rem; cursor:pointer; font-weight:600;">✔ Done</button>
                        <?php endif; ?>
                    </form>
                    <a href="/TimeForge_Capstone/tasks.php?project_id=<?= $mt['project_id'] ?>" style="color:#3b82f6; font-size:.78rem; margin-left:.5rem;">Board</a>
                    <a href="/TimeForge_Capstone/task_detail.php?id=<?= $mt['id'] ?>&project_id=<?= $mt['project_id'] ?>"
                       style="color:#a5b4fc; font-size:.78rem; margin-left:.5rem;" title="Notes &amp; Problem Reports">💬 Notes</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="color:#475569; text-align:center; padding:1.5rem 0;">🎉 No open tasks assigned to you!</p>
        <?php endif; ?>
    </div>
</div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> TimeForge. All rights reserved.</p>
        <p>Professional Time Tracking & Project Management Solution</p>
        <p>Web Capstone Project by Etefworkie Melaku — triOS College, Mobile and Web App Development</p>
    </footer>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="/TimeForge_Capstone/js/time_tracker.js"></script>
    <script src="/TimeForge_Capstone/js/theme.js"></script>
    <script src="/TimeForge_Capstone/js/animations.js"></script>
</body>
</html>
