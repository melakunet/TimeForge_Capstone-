<?php
/**
 * test_notifications.php — Notification system smoke tests
 * DELETE THIS FILE after running.
 * Access: http://localhost/TimeForge_Capstone/test_notifications.php
 */
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/notify.php';

$pass = 0;
$fail = 0;
$log  = [];

function ok(string $name, bool $result, string $detail = ''): void {
    global $pass, $fail, $log;
    if ($result) { $pass++; $log[] = ['PASS', $name, $detail]; }
    else         { $fail++; $log[] = ['FAIL', $name, $detail]; }
}

// ── Pick any real user_id from the DB ─────────────────────────────────────
$uid_row = $pdo->query("SELECT id FROM users LIMIT 1")->fetch();
$test_uid = $uid_row ? (int)$uid_row['id'] : 0;
ok('users table has at least one row', $test_uid > 0, "user_id = $test_uid");

if (!$test_uid) {
    echo "<p style='color:red'>No users found — cannot proceed.</p>";
    exit;
}

// ── TEST 1: notify() inserts a row ─────────────────────────────────────────
$before = (int)$pdo->query("SELECT COUNT(*) FROM notifications")->fetchColumn();
notify($pdo, $test_uid, 'test_type', 'Unit test notification', '/TimeForge_Capstone/');
$after  = (int)$pdo->query("SELECT COUNT(*) FROM notifications")->fetchColumn();
ok('notify() inserts a row', $after === $before + 1, "before=$before after=$after");

// ── TEST 2: Row has correct values ─────────────────────────────────────────
$row = $pdo->query("SELECT * FROM notifications ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
ok('user_id matches',  (int)$row['user_id'] === $test_uid,           "stored={$row['user_id']}");
ok('type matches',     $row['type']    === 'test_type',              "stored={$row['type']}");
ok('message matches',  $row['message'] === 'Unit test notification', "stored={$row['message']}");
ok('link matches',     $row['link']    === '/TimeForge_Capstone/',   "stored={$row['link']}");
ok('is_read = 0',      (int)$row['is_read'] === 0,                  "stored={$row['is_read']}");

// ── TEST 3: unreadNotificationCount ───────────────────────────────────────
$count = unreadNotificationCount($pdo, $test_uid);
ok('unreadNotificationCount() returns int >= 1', $count >= 1, "count=$count");

// ── TEST 4: Mark as read ───────────────────────────────────────────────────
$notif_id = (int)$row['id'];
$pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id")->execute([':id' => $notif_id]);
$after_mark = $pdo->query("SELECT is_read FROM notifications WHERE id = $notif_id")->fetchColumn();
ok('mark as read sets is_read = 1', (int)$after_mark === 1, "is_read=$after_mark");

// ── TEST 5: unreadCount does NOT count the now-read row ───────────────────
$count_after = unreadNotificationCount($pdo, $test_uid);
ok('unreadCount decreases after mark-read', $count_after === $count - 1, "before=$count after=$count_after");

// ── TEST 6: Truncated message (>500 chars) ─────────────────────────────────
$long_msg = str_repeat('A', 600);
notify($pdo, $test_uid, 'test_long', $long_msg, null);
$long_row = $pdo->query("SELECT message FROM notifications ORDER BY id DESC LIMIT 1")->fetch();
ok('long message truncated to 500 chars', mb_strlen($long_row['message']) === 500, "len=".mb_strlen($long_row['message']));

// ── TEST 7: notify() is silent on bad user_id (no crash) ──────────────────
$errored = false;
try { notify($pdo, 999999, 'x', 'ghost'); } catch (Throwable $e) { $errored = true; }
ok('notify() with FK-violating user_id does not throw (caught internally)', !$errored);

// ── CLEANUP: remove all test rows ─────────────────────────────────────────
$pdo->prepare("DELETE FROM notifications WHERE type LIKE 'test_%'")->execute();
$remaining = (int)$pdo->query("SELECT COUNT(*) FROM notifications WHERE type LIKE 'test_%'")->fetchColumn();
ok('cleanup removes test rows', $remaining === 0, "remaining=$remaining");

// ── RENDER ─────────────────────────────────────────────────────────────────
$total = $pass + $fail;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Notification Tests</title>
  <style>
    body { font-family: monospace; background: #0f172a; color: #e2e8f0; padding: 2rem; }
    h1   { color: <?= $fail > 0 ? '#f87171' : '#4ade80' ?>; }
    table { border-collapse: collapse; width: 100%; max-width: 800px; }
    th,td { padding: .5rem 1rem; text-align: left; border-bottom: 1px solid #1e293b; }
    .PASS { color: #4ade80; font-weight: 700; }
    .FAIL { color: #f87171; font-weight: 700; }
    .summary { margin-top: 1.5rem; font-size: 1.1rem; }
    .del { margin-top:2rem; padding:.75rem 1rem; background:#7c3aed22; border:1px solid #7c3aed55; border-radius:8px; color:#a78bfa; font-size:.85rem; }
  </style>
</head>
<body>
<h1>Notification System Tests — <?= $fail === 0 ? '✅ All Passed' : "❌ {$fail} Failed" ?></h1>
<table>
  <thead><tr><th>Result</th><th>Test</th><th>Detail</th></tr></thead>
  <tbody>
  <?php foreach ($log as [$status, $name, $detail]): ?>
    <tr>
      <td class="<?= $status ?>"><?= $status ?></td>
      <td><?= htmlspecialchars($name) ?></td>
      <td style="color:#94a3b8;font-size:.85rem;"><?= htmlspecialchars($detail) ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<div class="summary">
  Passed: <strong style="color:#4ade80"><?= $pass ?></strong> /
  Failed: <strong style="color:#f87171"><?= $fail ?></strong> /
  Total:  <strong><?= $total ?></strong>
</div>
<div class="del">⚠️ Delete this file when done: <code>rm /Applications/XAMPP/xamppfiles/htdocs/TimeForge_Capstone/test_notifications.php</code></div>
</body>
</html>
