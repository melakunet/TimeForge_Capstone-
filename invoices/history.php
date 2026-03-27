<?php
$page_title = 'Invoice History';

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../db.php';

if (!isLoggedIn()) {
    header('Location: /TimeForge_Capstone/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];

// Fetch invoice list — wrapped so a DB outage shows a flash instead of a 500
try {
    if ($role === 'admin') {
        $list_stmt = $pdo->prepare("
            SELECT
                inv.id,
                inv.invoice_number,
                inv.issue_date,
                inv.due_date,
                inv.total_amount,
                inv.partial_amount,
                inv.status,
                inv.created_at,
                p.project_name,
                c.client_name,
                c.company_name
            FROM invoices inv
            INNER JOIN projects p ON p.id = inv.project_id
            INNER JOIN clients  c ON c.id = inv.client_id
            ORDER BY inv.created_at DESC
        ");
        $list_stmt->execute();
    } elseif ($role === 'client') {
        // Client sees invoices for projects they own
        $list_stmt = $pdo->prepare("
            SELECT
                inv.id,
                inv.invoice_number,
                inv.issue_date,
                inv.due_date,
                inv.total_amount,
                inv.partial_amount,
                inv.status,
                inv.created_at,
                p.project_name,
                c.client_name,
                c.company_name
            FROM invoices inv
            INNER JOIN projects p ON p.id  = inv.project_id
            INNER JOIN clients  c ON c.id  = inv.client_id
            WHERE c.user_id = :user_id
            ORDER BY inv.created_at DESC
        ");
        $list_stmt->execute([':user_id' => $user_id]);
    } else {
        setFlash('error', 'Access denied.');
        header('Location: /TimeForge_Capstone/index.php');
        exit;
    }
    $invoices = $list_stmt->fetchAll();
} catch (PDOException $e) {
    error_log('history.php fetch error: ' . $e->getMessage());
    setFlash('error', 'Could not load invoices. Please try again.');
    $invoices = [];
}
$flash    = getFlash();

$status_labels = [
    'draft'     => 'Draft',
    'sent'      => 'Sent',
    'viewed'    => 'Viewed',
    'overdue'   => 'Overdue',
    'partial'   => 'Partial',
    'paid'      => 'Paid',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled',
];
$status_colors = [
    'draft'     => '#6b7280',
    'sent'      => '#2563eb',
    'viewed'    => '#7c3aed',
    'overdue'   => '#dc2626',
    'partial'   => '#d97706',
    'paid'      => '#16a34a',
    'completed' => '#0f766e',
    'cancelled' => '#374151',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> — TimeForge</title>
    <link rel="stylesheet" href="/TimeForge_Capstone/css/style.css">
    <link rel="stylesheet" href="/TimeForge_Capstone/css/invoice.css">
    <link rel="icon" type="image/png" href="/TimeForge_Capstone/icons/logo.png">
</head>
<body>
<?php include __DIR__ . '/../includes/header_partial.php'; ?>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
        <h1 class="heading-serif" style="color:var(--color-accent); margin:0;">Invoice History</h1>
        <?php if (hasRole('admin')): ?>
            <span style="font-size:0.85rem; color:var(--color-text-secondary);">
                <?php echo count($invoices); ?> invoice<?php echo count($invoices) !== 1 ? 's' : ''; ?> total
            </span>
        <?php endif; ?>
    </div>

    <?php if ($flash): ?>
        <div class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>" style="margin-bottom:1rem;">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($invoices)): ?>
        <div class="card" style="text-align:center; padding:3rem;">
            <p style="color:var(--color-text-secondary); margin-bottom:1rem;">No invoices have been generated yet.</p>
            <?php if (hasRole('admin')): ?>
                <a href="/TimeForge_Capstone/index.php" class="btn btn-primary">Go to Projects</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="card" style="padding:0; overflow:hidden;">
            <table class="project-table" style="margin:0;">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Project</th>
                        <th>Client</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th style="text-align:right;">Total</th>
                        <th style="text-align:center;">Status</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $inv):
                        $is_overdue = ($inv['status'] === 'overdue');
                        $row_style  = $is_overdue ? 'background:rgba(220,38,38,0.05);' : '';
                    ?>
                    <tr style="<?php echo $row_style; ?>">
                        <td>
                            <a href="/TimeForge_Capstone/invoices/view.php?id=<?php echo $inv['id']; ?>" style="font-weight:600;">
                                <?php echo htmlspecialchars($inv['invoice_number']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($inv['project_name']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($inv['client_name']); ?>
                            <?php if ($inv['company_name']): ?>
                                <br><small style="color:var(--color-text-secondary);"><?php echo htmlspecialchars($inv['company_name']); ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($inv['issue_date'])); ?></td>
                        <td>
                            <?php echo date('M j, Y', strtotime($inv['due_date'])); ?>
                            <?php if ($is_overdue): ?>
                                <br><small style="color:#dc2626; font-weight:600;">Overdue</small>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:right; font-weight:600;">
                            $<?php echo number_format($inv['total_amount'], 2); ?>
                            <?php if (!empty($inv['partial_amount'])): ?>
                                <br><small style="color:#d97706; font-weight:400;">Partial: $<?php echo number_format($inv['partial_amount'], 2); ?></small>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center;">
                            <span class="inv-status-badge"
                                  style="background:<?php echo $status_colors[$inv['status']] ?? '#6b7280'; ?>; font-size:0.75rem; padding:3px 10px; border-radius:12px; color:#fff; display:inline-block; white-space:nowrap;">
                                <?php echo $status_labels[$inv['status']] ?? ucfirst($inv['status']); ?>
                            </span>
                        </td>
                        <td style="text-align:center; white-space:nowrap;">
                            <a href="/TimeForge_Capstone/invoices/view.php?id=<?php echo $inv['id']; ?>" class="btn btn-secondary" style="padding:4px 10px; font-size:0.8rem;">View</a>
                            <a href="/TimeForge_Capstone/invoices/download.php?id=<?php echo $inv['id']; ?>" class="btn btn-primary" style="padding:4px 10px; font-size:0.8rem;" target="_blank" rel="noopener">PDF</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer_partial.php'; ?>
<script src="/TimeForge_Capstone/js/theme.js"></script>
</body>
</html>
