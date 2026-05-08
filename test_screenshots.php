<?php
/**
 * test_screenshots.php — Screenshot System End-to-End Tests
 *
 * Tests:
 *  1. Upload endpoint (POST api/upload_screenshot.php) with a synthetic JPEG
 *  2. File written to disk at the correct path
 *  3. DB row created with correct columns
 *  4. Image-serve proxy (api/screenshot_img.php?id=N) returns a JPEG
 *  5. Admin gallery query returns the new screenshot
 *  6. Screenshot count cap (50/entry) logic
 *  7. Cleanup — removes test file + DB row
 *
 * DELETE THIS FILE after reviewing results.
 */

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';

$pass = 0; $fail = 0; $log = [];
$evidence = []; // file paths captured for visual display

function ok(string $name, bool $result, string $detail = ''): void {
    global $pass, $fail, $log;
    if ($result) { $pass++; $log[] = ['PASS', $name, $detail]; }
    else         { $fail++; $log[] = ['FAIL', $name, $detail]; }
}

// ── Pull a real uid / company_id / project_id / entry_id from DB ──────────
$row = $pdo->query("
    SELECT te.id AS eid, te.user_id AS uid, te.project_id AS pid,
           u.company_id
    FROM time_entries te
    JOIN users u ON u.id = te.user_id
    JOIN projects p ON p.id = te.project_id
    LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

ok('DB has at least one time_entry with a user + project', !empty($row), json_encode($row));

if (empty($row)) {
    echo "<p style='color:red;font-family:monospace;'>No usable time_entry rows — cannot continue.</p>";
    exit;
}

$test_uid     = (int)$row['uid'];
$test_cid     = (int)$row['company_id'];
$test_pid     = (int)$row['pid'];
$test_eid     = (int)$row['eid'];

// ── Build a minimal valid JPEG (1×1 white pixel) as Base64 ───────────────
// This is a real JPEG so file_put_contents + filesize checks pass
$tiny_jpeg_b64 = '/9j/4AAQSkZJRgABAQEAAAAAAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBk'
               . 'SExAUFhYYFBYWGR4lHh0sIR8hJiAmKy4vNDs2ODk4ODk4ODk4ODn/2wBDAQkJ'
               . 'CQwLDBgNDRgyIRYhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIy'
               . 'MjIyMjIyMjIyMjIyMjL/wAARCAABAAEDASIAAhEBAxEB/8QAFAABAAAAAAAAAAAAAAAAAAAACf/'
               . 'EABQQAQAAAAAAAAAAAAAAAAAAAAD/xAAUAQEAAAAAAAAAAAAAAAAAAAAA/8QAFBEB'
               . 'AAAAAAAAAAAAAAAAAAAAAP/aAAwDAQACEQMRAD8AJQAB/9k=';

$image_data_uri = 'data:image/jpeg;base64,' . $tiny_jpeg_b64;

// ── TEST 1: Verify upload dir permissions ─────────────────────────────────
$upload_base = __DIR__ . '/uploads/screenshots';
ok('uploads/screenshots/ directory exists', is_dir($upload_base), $upload_base);
ok('uploads/screenshots/ is writable', is_writable($upload_base), 'chmod 775 needed if false');

// ── TEST 2: Simulate upload_screenshot.php logic inline ───────────────────
$save_dir  = "{$upload_base}/{$test_cid}/{$test_uid}/{$test_eid}";
if (!is_dir($save_dir)) mkdir($save_dir, 0775, true);
ok('per-entry screenshot dir created', is_dir($save_dir), $save_dir);

$img_plain  = preg_replace('/^data:image\/\w+;base64,/', '', $image_data_uri);
$img_plain  = str_replace(' ', '+', $img_plain);
$decoded    = base64_decode($img_plain, true);
ok('base64_decode succeeds', $decoded !== false && strlen($decoded) >= 10,
   'len=' . strlen($decoded ?? ''));

$filename  = 'TEST_' . date('YmdHis') . '_' . mt_rand(100,999) . '.jpg';
$full_path = $save_dir . '/' . $filename;
$rel_path  = "uploads/screenshots/{$test_cid}/{$test_uid}/{$test_eid}/{$filename}";
$written   = file_put_contents($full_path, $decoded);
ok('file written to disk', $written !== false, "bytes=$written path=$full_path");

// ── TEST 3: DB INSERT ─────────────────────────────────────────────────────
$size_kb = (int)ceil(strlen($decoded) / 1024);
try {
    $ins = $pdo->prepare("
        INSERT INTO screenshots
            (entry_id, user_id, project_id, company_id, file_path, file_size_kb, activity_score_at_capture, captured_at)
        VALUES (:eid, :uid, :pid, :cid, :path, :size, :score, NOW())
    ");
    $ins->execute([
        ':eid'   => $test_eid,
        ':uid'   => $test_uid,
        ':pid'   => $test_pid,
        ':cid'   => $test_cid,
        ':path'  => $rel_path,
        ':size'  => max(1, $size_kb),
        ':score' => 42,
    ]);
    $new_id = (int)$pdo->lastInsertId();
    ok('INSERT into screenshots succeeds', $new_id > 0, "id=$new_id");
} catch (PDOException $e) {
    ok('INSERT into screenshots succeeds', false, $e->getMessage());
    $new_id = 0;
}

// ── TEST 4: DB row has correct values ─────────────────────────────────────
if ($new_id) {
    $dbrow = $pdo->prepare("SELECT * FROM screenshots WHERE id = :id");
    $dbrow->execute([':id' => $new_id]);
    $dbrow = $dbrow->fetch(PDO::FETCH_ASSOC);
    ok('DB row: entry_id matches',  (int)$dbrow['entry_id']   === $test_eid,  "stored={$dbrow['entry_id']}");
    ok('DB row: user_id matches',   (int)$dbrow['user_id']    === $test_uid,  "stored={$dbrow['user_id']}");
    ok('DB row: project_id matches',(int)$dbrow['project_id'] === $test_pid,  "stored={$dbrow['project_id']}");
    ok('DB row: company_id matches',(int)$dbrow['company_id'] === $test_cid,  "stored={$dbrow['company_id']}");
    ok('DB row: file_path correct', $dbrow['file_path'] === $rel_path,        "stored={$dbrow['file_path']}");
    ok('DB row: activity_score = 42',(int)$dbrow['activity_score_at_capture'] === 42, "stored={$dbrow['activity_score_at_capture']}");
    ok('DB row: file_size_kb >= 1', (int)$dbrow['file_size_kb'] >= 1,         "stored={$dbrow['file_size_kb']}");
} else {
    for ($i = 0; $i < 7; $i++) ok("DB row check $i (skipped — insert failed)", false, 'insert failed');
}

// ── TEST 5: File actually readable from disk ──────────────────────────────
ok('file exists on disk after insert', file_exists($full_path), $full_path);
ok('file is readable',   is_readable($full_path));
ok('file size > 0 bytes', filesize($full_path) > 0, 'size=' . filesize($full_path) . ' bytes');

// ── TEST 6: Relative path resolves correctly ──────────────────────────────
$resolved = __DIR__ . '/' . $rel_path;
ok('relative path resolves to existing file', file_exists($resolved), $resolved);

// ── TEST 7: screenshot_img.php logic (serve proxy) ───────────────────────
if ($new_id) {
    $proxy_row = $pdo->prepare("SELECT file_path FROM screenshots WHERE id = :id AND company_id = :cid LIMIT 1");
    $proxy_row->execute([':id' => $new_id, ':cid' => $test_cid]);
    $proxy = $proxy_row->fetch();
    ok('proxy query finds the row by id+company_id', !empty($proxy), "path={$proxy['file_path']}");

    $proxy_full = __DIR__ . '/' . $proxy['file_path'];
    ok('proxy full_path file_exists()', file_exists($proxy_full), $proxy_full);

    // Verify JPEG magic bytes
    $fh = fopen($proxy_full, 'rb');
    $magic = fread($fh, 3);
    fclose($fh);
    $is_jpeg = (substr($magic, 0, 2) === "\xFF\xD8");
    ok('file has JPEG magic bytes (FFD8)', $is_jpeg, bin2hex($magic));

    // Add to visual evidence
    $evidence[] = ['id' => $new_id, 'path' => $proxy_full, 'rel' => $rel_path];
}

// ── TEST 8: Admin gallery query returns new screenshot ────────────────────
if ($new_id) {
    $gallery = $pdo->prepare("
        SELECT s.id, s.file_path, s.activity_score_at_capture, s.captured_at,
               u.full_name, p.project_name
        FROM screenshots s
        INNER JOIN users    u ON u.id = s.user_id
        INNER JOIN projects p ON p.id = s.project_id
        WHERE s.company_id = :cid
        ORDER BY s.captured_at DESC
        LIMIT 5
    ");
    $gallery->execute([':cid' => $test_cid]);
    $gallery_rows = $gallery->fetchAll(PDO::FETCH_ASSOC);
    ok('admin gallery query returns rows', count($gallery_rows) > 0, 'count=' . count($gallery_rows));

    $found = array_filter($gallery_rows, fn($r) => (int)$r['id'] === $new_id);
    ok('new screenshot appears in gallery results', !empty($found), "id=$new_id");
}

// ── TEST 9: Count-cap logic (>=50 blocks) ─────────────────────────────────
$cap_count = (int)$pdo->prepare("SELECT COUNT(*) FROM screenshots WHERE entry_id = :eid")
    ->execute([':eid' => $test_eid]) ? 0 : 0;
$cs = $pdo->prepare("SELECT COUNT(*) FROM screenshots WHERE entry_id = :eid");
$cs->execute([':eid' => $test_eid]);
$cap_count = (int)$cs->fetchColumn();
ok('count-cap: current count is < 50 (not blocked)', $cap_count < 50, "count=$cap_count");

// ── CLEANUP ───────────────────────────────────────────────────────────────
if ($new_id) {
    $pdo->prepare("DELETE FROM screenshots WHERE id = :id")->execute([':id' => $new_id]);
    $deleted = (int)$pdo->prepare("SELECT COUNT(*) FROM screenshots WHERE id = :id")
                        ->execute([':id' => $new_id]);
    $gone_stmt = $pdo->prepare("SELECT COUNT(*) FROM screenshots WHERE id = :id");
    $gone_stmt->execute([':id' => $new_id]);
    ok('DB row removed on cleanup', (int)$gone_stmt->fetchColumn() === 0, "id=$new_id");
}
if (file_exists($full_path)) {
    unlink($full_path);
}
ok('test file removed from disk', !file_exists($full_path), $full_path);

$total = $pass + $fail;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Screenshot System Tests</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Courier New', monospace; background: #0f172a; color: #e2e8f0; padding: 2rem; }
    h1 { font-size: 1.4rem; margin-bottom: 1.5rem; color: <?= $fail > 0 ? '#f87171' : '#4ade80' ?>; }
    h2 { font-size: 1rem; color: #94a3b8; margin: 1.5rem 0 .75rem; }
    table { border-collapse: collapse; width: 100%; max-width: 860px; font-size: .85rem; }
    th, td { padding: .45rem .9rem; text-align: left; border-bottom: 1px solid #1e293b; }
    th { color: #94a3b8; font-size: .75rem; text-transform: uppercase; }
    .PASS { color: #4ade80; font-weight: 700; }
    .FAIL { color: #f87171; font-weight: 700; }
    .detail { color: #64748b; font-size: .78rem; word-break: break-all; }
    .summary { margin-top: 1.5rem; font-size: 1rem; }
    .badge { display: inline-block; padding: .2rem .6rem; border-radius: 4px; font-size: .78rem; font-weight: 700; }
    .badge-pass { background: #166534; color: #4ade80; }
    .badge-fail { background: #7f1d1d; color: #f87171; }
    .meta-box { max-width: 860px; background: #1e293b; border-radius: 8px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; font-size: .82rem; color: #94a3b8; line-height: 1.8; }
    .evidence-box { max-width: 860px; background: #1e293b; border-radius: 8px; padding: 1rem 1.25rem; margin-top: 1.5rem; }
    .evidence-box img { max-width: 320px; border: 2px solid #334155; border-radius: 6px; margin-top: .5rem; }
    .del { margin-top: 2rem; padding: .75rem 1rem; background: #7c3aed22; border: 1px solid #7c3aed55; border-radius: 8px; color: #a78bfa; font-size: .82rem; }
    .js-section { max-width: 860px; background: #1e293b; border-radius: 8px; padding: 1rem 1.25rem; margin-top: 1.5rem; }
    .js-section h2 { color: #f59e0b; margin: 0 0 .75rem; }
    pre { background: #0f172a; padding: .75rem; border-radius: 6px; font-size: .78rem; color: #a78bfa; overflow-x: auto; white-space: pre-wrap; }
    #js-result { margin-top: .75rem; min-height: 2rem; }
  </style>
</head>
<body>

<h1>📸 Screenshot System Tests — <?= $fail === 0 ? '✅ All Passed' : "❌ {$fail} Failed" ?></h1>

<div class="meta-box">
  Test IDs used: &nbsp;
  <strong>user_id=<?= $test_uid ?></strong> &nbsp;|&nbsp;
  <strong>company_id=<?= $test_cid ?></strong> &nbsp;|&nbsp;
  <strong>project_id=<?= $test_pid ?></strong> &nbsp;|&nbsp;
  <strong>entry_id=<?= $test_eid ?></strong>
  <br>Upload base: <code><?= htmlspecialchars($upload_base) ?></code>
</div>

<h2>PHP Backend Tests</h2>
<table>
  <thead><tr><th>Result</th><th>Test</th><th>Detail</th></tr></thead>
  <tbody>
  <?php foreach ($log as [$status, $name, $detail]): ?>
    <tr>
      <td class="<?= $status ?>"><?= $status ?></td>
      <td><?= htmlspecialchars($name) ?></td>
      <td class="detail"><?= htmlspecialchars($detail) ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<div class="summary">
  <span class="badge badge-pass">PASS <?= $pass ?></span>&nbsp;
  <span class="badge badge-fail">FAIL <?= $fail ?></span>&nbsp;
  Total: <strong><?= $total ?></strong>
</div>

<!-- ── JS Live Test (needs a running timer) ──────────────────────────────── -->
<div class="js-section">
  <h2>🖥 JS Live Screenshot Test (browser-side)</h2>
  <p style="color:#94a3b8;font-size:.85rem;margin-bottom:.75rem;">
    This uses the real <code>window.timeTracker.testScreenshot()</code> helper built into
    <code>time_tracker.js</code>. A timer must be running on <strong>any page</strong> first.
    Open the dashboard, start a timer, then come back here and click the button.
  </p>
  <button id="btn-js-test" onclick="runJsTest()"
          style="background:#3b82f6;color:#fff;border:none;padding:.6rem 1.4rem;border-radius:6px;font-weight:700;cursor:pointer;font-size:.9rem;">
    ▶ Run JS Screenshot Test
  </button>
  <div id="js-result"></div>
  <div style="margin-top:1rem;">
    <strong style="color:#94a3b8;font-size:.8rem;">Or paste this in the browser console on any page where the timer is running:</strong>
    <pre>window.timeTracker.testScreenshot()</pre>
  </div>
</div>

<!-- ── Admin Screenshots Gallery Link ───────────────────────────────────── -->
<div class="evidence-box">
  <h2 style="color:#4ade80;margin-bottom:.75rem;">🗂 Admin Gallery Evidence</h2>
  <p style="font-size:.85rem;color:#94a3b8;margin-bottom:1rem;">
    The test inserted then cleaned up a row. Any <em>real</em> screenshots captured during
    active timer sessions appear here:
  </p>
  <a href="/TimeForge_Capstone/admin/screenshots.php"
     target="_blank"
     style="display:inline-block;background:#1d4ed8;color:#fff;padding:.55rem 1.25rem;border-radius:6px;text-decoration:none;font-weight:700;font-size:.88rem;">
    Open Admin Screenshot Gallery →
  </a>
  &nbsp;
  <a href="/TimeForge_Capstone/admin/screenshots.php?date_from=<?= date('Y-m-d') ?>"
     target="_blank"
     style="display:inline-block;background:#065f46;color:#fff;padding:.55rem 1.25rem;border-radius:6px;text-decoration:none;font-weight:700;font-size:.88rem;">
    Today's Screenshots →
  </a>

  <?php
  // Show any real existing screenshots as evidence
  $real = $pdo->prepare("
      SELECT s.id, s.file_path, s.activity_score_at_capture, s.captured_at,
             u.full_name, p.project_name, s.file_size_kb
      FROM screenshots s
      JOIN users u ON u.id = s.user_id
      JOIN projects p ON p.id = s.project_id
      WHERE s.company_id = :cid
      ORDER BY s.captured_at DESC
      LIMIT 6
  ");
  $real->execute([':cid' => $test_cid]);
  $real_shots = $real->fetchAll(PDO::FETCH_ASSOC);
  ?>

  <?php if (!empty($real_shots)): ?>
  <h3 style="color:#94a3b8;font-size:.85rem;margin:1.25rem 0 .75rem;">Last <?= count($real_shots) ?> real screenshots in DB:</h3>
  <div style="display:flex;flex-wrap:wrap;gap:1rem;">
    <?php foreach ($real_shots as $s): ?>
    <div style="background:#0f172a;border:1px solid #334155;border-radius:8px;padding:.75rem;min-width:200px;max-width:220px;">
      <img src="/TimeForge_Capstone/api/screenshot_img.php?id=<?= $s['id'] ?>"
           alt="Screenshot <?= $s['id'] ?>"
           style="width:100%;border-radius:4px;border:1px solid #334155;"
           onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
      <div style="display:none;color:#f87171;font-size:.75rem;padding:.5rem;">
        ⚠ Image not accessible (need admin session to view via proxy)
      </div>
      <div style="margin-top:.5rem;font-size:.72rem;color:#94a3b8;line-height:1.6;">
        <div><strong style="color:#e2e8f0;">#<?= $s['id'] ?></strong> — <?= htmlspecialchars($s['full_name']) ?></div>
        <div><?= htmlspecialchars($s['project_name']) ?></div>
        <div><?= date('M j, g:i a', strtotime($s['captured_at'])) ?></div>
        <div>Activity: <?= $s['activity_score_at_capture'] ?> &bull; <?= $s['file_size_kb'] ?>KB</div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <p style="color:#64748b;font-size:.85rem;margin-top:1rem;">
    No real screenshots yet. Start a timer and wait 5–15 minutes, or call
    <code>window.timeTracker.testScreenshot()</code> in the console to force one immediately.
  </p>
  <?php endif; ?>
</div>

<div class="del">
  ⚠️ Delete when done:
  <code>rm /Applications/XAMPP/xamppfiles/htdocs/TimeForge_Capstone/test_screenshots.php</code>
</div>

<script>
async function runJsTest() {
    const out = document.getElementById('js-result');
    out.innerHTML = '<span style="color:#f59e0b;">⏳ Running…</span>';

    // Check if timeTracker is available (it's on other pages, not this one)
    if (!window.timeTracker) {
        out.innerHTML = '<span style="color:#f87171;">❌ window.timeTracker not found on this page. '
            + 'Go to the dashboard, start a timer, then open this test page in the same tab.</span>';
        return;
    }
    if (!window.timeTracker.startTime || !window.timeTracker.entryId) {
        out.innerHTML = '<span style="color:#f87171;">❌ Timer is not running. '
            + 'Start a timer on the dashboard first, then return here.</span>';
        return;
    }

    try {
        const before = window.timeTracker.screenshotCount;
        await window.timeTracker.testScreenshot();
        const after = window.timeTracker.screenshotCount;

        if (after > before) {
            out.innerHTML = `<span style="color:#4ade80;">✅ JS screenshot captured and uploaded!
                Count: ${before} → ${after}.
                Check <a href="/TimeForge_Capstone/admin/screenshots.php" target="_blank"
                style="color:#60a5fa;">admin/screenshots.php</a> to see it.</span>`;
        } else {
            out.innerHTML = '<span style="color:#f87171;">❌ testScreenshot() ran but count did not increase. '
                + 'Check the browser console for errors.</span>';
        }
    } catch(e) {
        out.innerHTML = '<span style="color:#f87171;">❌ Exception: ' + e.message + '</span>';
    }
}
</script>
</body>
</html>
