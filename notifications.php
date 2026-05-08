<?php
$page_title = 'Notifications';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/notify.php';

if (!isLoggedIn()) {
    header('Location: /TimeForge_Capstone/login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Mark all as read if requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_all_read'])) {
    verifyCsrfToken();
    $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :uid")
        ->execute([':uid' => $user_id]);
    header('Location: /TimeForge_Capstone/notifications.php');
    exit;
}

// Mark single notification as read + redirect to link
if (isset($_GET['read'])) {
    $nid = filter_input(INPUT_GET, 'read', FILTER_VALIDATE_INT);
    if ($nid) {
        $s = $pdo->prepare("SELECT link FROM notifications WHERE id = :id AND user_id = :uid");
        $s->execute([':id' => $nid, ':uid' => $user_id]);
        $row = $s->fetch();
        $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :uid")
            ->execute([':id' => $nid, ':uid' => $user_id]);
        if ($row && $row['link']) {
            header('Location: ' . $row['link']);
            exit;
        }
    }
    header('Location: /TimeForge_Capstone/notifications.php');
    exit;
}

// Paginate
$per_page = 20;
$page     = max(1, (int)filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT));

$cstmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :uid");
$cstmt->execute([':uid' => $user_id]);
$total       = (int)$cstmt->fetchColumn();
$total_pages = max(1, (int)ceil($total / $per_page));
$page        = min($page, $total_pages);
$offset      = ($page - 1) * $per_page;

$stmt = $pdo->prepare("
    SELECT id, type, message, link, is_read, created_at
    FROM notifications
    WHERE user_id = :uid
    ORDER BY created_at DESC
    LIMIT :lim OFFSET :off
");
$stmt->bindValue(':uid', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':lim', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':off', $offset,   PDO::PARAM_INT);
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

$unread = unreadNotificationCount($pdo, $user_id);
$flash  = getFlash();

$type_icons = [
    'time_approved'  => '✅',
    'time_rejected'  => '❌',
    'task_assigned'  => '📋',
    'invoice_overdue'=> '⚠️',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> — TimeForge</title>
    <link rel="stylesheet" href="/TimeForge_Capstone/css/style.css">
    <link rel="icon" type="image/png" href="/TimeForge_Capstone/icons/logo.png">
</head>
<body>
<?php include __DIR__ . '/includes/header_partial.php'; ?>

<div class="container" style="max-width:740px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
        <h1 class="heading-serif" style="color:var(--color-accent);margin:0;">
            🔔 Notifications
            <?php if ($unread > 0): ?>
                <span style="font-size:.75rem;background:#dc2626;color:#fff;border-radius:999px;padding:.1rem .55rem;vertical-align:middle;"><?= $unread ?></span>
            <?php endif; ?>
        </h1>
        <?php if ($unread > 0): ?>
            <form method="POST" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="mark_all_read" value="1">
                <button type="submit" class="btn btn-secondary" style="font-size:.85rem;">Mark all read</button>
            </form>
        <?php endif; ?>
    </div>

    <?php if ($flash): ?>
        <div class="flash flash-<?= htmlspecialchars($flash['type']) ?>" style="margin-bottom:1rem;">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($notifications)): ?>
        <div class="card" style="padding:2rem;text-align:center;color:var(--color-text-secondary);">
            <p style="font-size:2rem;margin-bottom:.5rem;">🔕</p>
            <p>You're all caught up — no notifications yet.</p>
        </div>
    <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:.5rem;">
        <?php foreach ($notifications as $n): ?>
            <?php $icon = $type_icons[$n['type']] ?? '🔔'; ?>
            <div style="display:flex;gap:1rem;align-items:flex-start;padding:1rem 1.25rem;
                        background:<?= $n['is_read'] ? 'var(--color-bg-secondary,#f9fafb)' : 'var(--color-accent-light,#eff6ff)' ?>;
                        border:1px solid <?= $n['is_read'] ? 'var(--color-border)' : '#bfdbfe' ?>;
                        border-radius:10px;">
                <span style="font-size:1.4rem;line-height:1;"><?= $icon ?></span>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:.92rem;<?= $n['is_read'] ? '' : 'font-weight:600;' ?>">
                        <?= htmlspecialchars($n['message']) ?>
                    </div>
                    <div style="font-size:.78rem;color:var(--color-text-secondary);margin-top:.25rem;">
                        <?= date('M j, Y · g:i a', strtotime($n['created_at'])) ?>
                    </div>
                </div>
                <div style="display:flex;gap:.5rem;align-items:center;flex-shrink:0;">
                    <?php if ($n['link']): ?>
                        <a href="/TimeForge_Capstone/notifications.php?read=<?= $n['id'] ?>"
                           style="font-size:.8rem;" class="btn btn-secondary" title="View">View →</a>
                    <?php endif; ?>
                    <?php if (!$n['is_read']): ?>
                        <a href="/TimeForge_Capstone/notifications.php?read=<?= $n['id'] ?>"
                           style="font-size:.78rem;color:var(--color-accent);" title="Mark read">✓</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        </div>

        <?php if ($total_pages > 1): ?>
        <div style="display:flex;justify-content:center;gap:.4rem;margin-top:1.5rem;">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" class="btn btn-secondary">&laquo; Prev</a>
            <?php endif; ?>
            <?php for ($p = max(1,$page-2); $p <= min($total_pages,$page+2); $p++): ?>
                <a href="?page=<?= $p ?>" class="btn <?= $p === $page ? 'btn-primary' : 'btn-secondary' ?>"><?= $p ?></a>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>" class="btn btn-secondary">Next &raquo;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer_partial.php'; ?>
<script src="/TimeForge_Capstone/js/theme.js"></script>
</body>
</html>
