<?php
// admin/audit_logs.php
// View System Audit Logs (Login/Logout/System Events)

$page_title = 'System Audit Logs';

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('admin');

$company_id = (int)$_SESSION['company_id'];

// Filters
$filter_user   = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT) ?: 0;
$filter_action = trim($_GET['action_type'] ?? '');

// Fetch logs scoped to THIS company only
try {
    $where  = ['(al.company_id = :cid OR (al.company_id IS NULL AND u.company_id = :cid2))'];
    $params = [':cid' => $company_id, ':cid2' => $company_id];

    if ($filter_user > 0) {
        $where[]        = 'al.user_id = :uid';
        $params[':uid'] = $filter_user;
    }
    if ($filter_action !== '') {
        $where[]           = 'al.action LIKE :act';
        $params[':act']    = '%' . $filter_action . '%';
    }

    $whereSQL = implode(' AND ', $where);

    $sql = "
        SELECT al.*, u.username, u.full_name
        FROM audit_logs al
        LEFT JOIN users u ON al.user_id = u.id
        WHERE $whereSQL
        ORDER BY al.created_at DESC
        LIMIT 200
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Dropdown: users in this company for filter
    $user_opts = $pdo->prepare("SELECT id, username, full_name FROM users WHERE company_id = :cid ORDER BY full_name");
    $user_opts->execute([':cid' => $company_id]);
    $filter_users = $user_opts->fetchAll(PDO::FETCH_ASSOC);

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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap:wrap; gap:1rem;">
            <div>
                <h1>🔐 Security Audit Log</h1>
                <p style="color: var(--color-text-secondary); margin:.25rem 0 0; font-size:.9rem;">
                    Showing login, registration, and security events for <strong>your company only</strong>.
                </p>
            </div>
            <a href="/TimeForge_Capstone/admin/dashboard.php" class="btn btn-secondary">← Dashboard</a>
        </div>

        <!-- Filters -->
        <form method="GET" style="display:flex; gap:.75rem; flex-wrap:wrap; margin-bottom:1.25rem; align-items:flex-end;">
            <div>
                <label style="font-size:.8rem; color:var(--color-text-secondary); display:block; margin-bottom:.3rem;">User</label>
                <select name="user_id" style="background:var(--color-card); border:1px solid #334155; color:var(--color-text); border-radius:6px; padding:.45rem .75rem; font-size:.85rem;">
                    <option value="">All users</option>
                    <?php foreach ($filter_users as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= $filter_user == $u['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['username']) ?> (<?= htmlspecialchars($u['full_name']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="font-size:.8rem; color:var(--color-text-secondary); display:block; margin-bottom:.3rem;">Action contains</label>
                <input type="text" name="action_type" value="<?= htmlspecialchars($filter_action) ?>"
                       placeholder="e.g. login, failed, register"
                       style="background:var(--color-card); border:1px solid #334155; color:var(--color-text); border-radius:6px; padding:.45rem .75rem; font-size:.85rem; width:200px;">
            </div>
            <button type="submit" class="btn btn-primary" style="height:36px;">Filter</button>
            <?php if ($filter_user || $filter_action): ?>
                <a href="/TimeForge_Capstone/admin/audit_logs.php" class="btn btn-secondary" style="height:36px; line-height:1.2;">Clear</a>
            <?php endif; ?>
            <span style="margin-left:auto; font-size:.82rem; color:var(--color-text-secondary); align-self:center;">
                <?= count($logs) ?> event<?= count($logs) !== 1 ? 's' : '' ?> shown
            </span>
        </form>

        <div class="card">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #334155; text-align: left;">
                            <th style="padding: 1rem;">Time</th>
                            <th style="padding: 1rem;">User</th>
                            <th style="padding: 1rem;">Action</th>
                            <th style="padding: 1rem;">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                        <tr><td colspan="4" style="padding:2rem; text-align:center; color:var(--color-text-secondary);">No events found.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($logs as $log): 
                            $actionClass = '';
                            if (strpos($log['action'], 'failed') !== false || strpos($log['action'], 'error') !== false)
                                $actionClass = 'color: #ef4444; font-weight: bold;';
                            elseif (strpos($log['action'], 'success') !== false)
                                $actionClass = 'color: #22c55e;';
                            elseif (strpos($log['action'], 'register') !== false)
                                $actionClass = 'color: #3b82f6;';
                        ?>
                        <tr style="border-bottom: 1px solid #1e293b;">
                            <td style="padding: .85rem 1rem; white-space: nowrap; font-size:.85rem;">
                                <?= date('M j, H:i:s', strtotime($log['created_at'])) ?>
                            </td>
                            <td style="padding: .85rem 1rem;">
                                <?php if ($log['user_id'] && $log['username']): ?>
                                    <strong><?= htmlspecialchars($log['username']) ?></strong>
                                    <span style="color: var(--color-text-secondary); font-size: 0.82em;">(<?= htmlspecialchars($log['full_name'] ?? '') ?>)</span>
                                <?php else: ?>
                                    <span style="color: var(--color-text-secondary);">System / Guest</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: .85rem 1rem; <?= $actionClass ?>; font-size:.875rem;">
                                <?= htmlspecialchars(str_replace('_', ' ', strtoupper($log['action']))) ?>
                            </td>
                            <td style="padding: .85rem 1rem; font-family: monospace; font-size:.82rem; color:var(--color-text-secondary);">
                                <?= htmlspecialchars($log['ip_address'] ?? '—') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/../includes/footer_partial.php'; ?>
    <script src="/TimeForge_Capstone/js/theme.js"></script>
</body>
</html>
