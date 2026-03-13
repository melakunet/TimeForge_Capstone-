<?php
// admin/audit_logs.php
// View System Audit Logs (Login/Logout/System Events)

$page_title = 'System Audit Logs';

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('admin');

// Fetch logs
// Only last 100 for performance
try {
    $sql = "
        SELECT al.*, u.username, u.full_name 
        FROM audit_logs al 
        LEFT JOIN users u ON al.user_id = u.id 
        ORDER BY al.created_at DESC 
        LIMIT 100
    ";
    $stmt = $pdo->query($sql);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching logs: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - TimeForge</title>
    <link rel="stylesheet" href="/TimeForge_Capstone/css/style.css">
    <link rel="icon" type="image/png" href="/TimeForge_Capstone/icons/logo.png">
</head>
<body>
    <?php include_once __DIR__ . '/../includes/header_partial.php'; ?>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1>System Security Logs</h1>
                <p style="color: #666;">Tracking logins, security events, and system errors.</p>
            </div>
            <a href="/TimeForge_Capstone/admin/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <div class="card">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #eee; text-align: left;">
                            <th style="padding: 1rem;">Time</th>
                            <th style="padding: 1rem;">User</th>
                            <th style="padding: 1rem;">Action</th>
                            <th style="padding: 1rem;">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): 
                            $actionClass = '';
                            if (strpos($log['action'], 'failed') !== false) $actionClass = 'color: #d32f2f; font-weight: bold;';
                            elseif (strpos($log['action'], 'success') !== false) $actionClass = 'color: #388e3c;';
                        ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 1rem; white-space: nowrap;">
                                <?= date('M j, H:i:s', strtotime($log['created_at'])) ?>
                            </td>
                            <td style="padding: 1rem;">
                                <?php if ($log['user_id'] && $log['username']): ?>
                                    <strong><?= htmlspecialchars($log['username']) ?></strong>
                                    <span style="color: #888; font-size: 0.9em;">(<?= htmlspecialchars($log['full_name']) ?>)</span>
                                <?php else: ?>
                                    <span style="color: #888;">System / Guest</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem; <?= $actionClass ?>">
                                <?= htmlspecialchars(str_replace('_', ' ', strtoupper($log['action']))) ?>
                            </td>
                            <td style="padding: 1rem; font-family: monospace;">
                                <?= htmlspecialchars($log['ip_address']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p style="text-align: center; margin-top: 1rem; color: #888;">Showing last 100 events</p>
        </div>
    </div>

    <?php include_once __DIR__ . '/../includes/footer_partial.php'; ?>
    <script src="/TimeForge_Capstone/js/theme.js"></script>
</body>
</html>
