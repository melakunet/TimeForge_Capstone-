<?php
/**
 * admin/screenshots.php — Phase 9
 * Admin gallery: browse all worker screenshots, filter by worker/project/date.
 */
$page_title = 'Activity Screenshots';

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';

requireRole('admin');

$company_id = (int)$_SESSION['company_id'];

// ── Filters ───────────────────────────────────────────────────────────────────
$filter_user    = filter_input(INPUT_GET, 'user_id',    FILTER_VALIDATE_INT) ?: 0;
$filter_project = filter_input(INPUT_GET, 'project_id', FILTER_VALIDATE_INT) ?: 0;
$filter_entry   = filter_input(INPUT_GET, 'entry_id',   FILTER_VALIDATE_INT) ?: 0;
$filter_from    = trim($_GET['date_from'] ?? '');
$filter_to      = trim($_GET['date_to']   ?? '');

if ($filter_from && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $filter_from)) $filter_from = '';
if ($filter_to   && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $filter_to))   $filter_to   = '';

// ── Workers dropdown ──────────────────────────────────────────────────────────
$workers = $pdo->prepare("SELECT id, full_name FROM users WHERE company_id = :cid AND role IN ('freelancer','admin') ORDER BY full_name");
$workers->execute([':cid' => $company_id]);
$workers = $workers->fetchAll(PDO::FETCH_ASSOC);

// ── Projects dropdown ─────────────────────────────────────────────────────────
$projects = $pdo->prepare("SELECT id, project_name FROM projects WHERE company_id = :cid AND deleted_at IS NULL ORDER BY project_name");
$projects->execute([':cid' => $company_id]);
$projects = $projects->fetchAll(PDO::FETCH_ASSOC);

// ── Disk usage ────────────────────────────────────────────────────────────────
$upload_dir   = __DIR__ . '/../uploads/screenshots/' . $company_id;
$disk_size_mb = 0;
if (is_dir($upload_dir)) {
    $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($upload_dir, FilesystemIterator::SKIP_DOTS));
    foreach ($iter as $f) $disk_size_mb += $f->getSize();
    $disk_size_mb = round($disk_size_mb / (1024 * 1024), 1);
}

// ── Build query ───────────────────────────────────────────────────────────────
$where  = "WHERE s.company_id = :cid";
$params = [':cid' => $company_id];

if ($filter_user)    { $where .= " AND s.user_id    = :uid"; $params[':uid'] = $filter_user; }
if ($filter_project) { $where .= " AND s.project_id = :pid"; $params[':pid'] = $filter_project; }
if ($filter_entry)   { $where .= " AND s.entry_id   = :eid"; $params[':eid'] = $filter_entry; }
if ($filter_from)    { $where .= " AND DATE(s.captured_at) >= :dfrom"; $params[':dfrom'] = $filter_from; }
if ($filter_to)      { $where .= " AND DATE(s.captured_at) <= :dto";   $params[':dto']   = $filter_to; }

$shots_stmt = $pdo->prepare("
    SELECT s.id, s.file_path, s.file_size_kb, s.activity_score_at_capture, s.captured_at,
           u.full_name, p.project_name, s.entry_id
    FROM screenshots s
    INNER JOIN users    u ON u.id = s.user_id
    INNER JOIN projects p ON p.id = s.project_id
    $where
    ORDER BY s.captured_at DESC
    LIMIT 200
");
$shots_stmt->execute($params);
$screenshots = $shots_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - TimeForge</title>
    <link rel="stylesheet" href="/TimeForge_Capstone/css/style.css">
    <link rel="icon" type="image/png" href="/TimeForge_Capstone/icons/logo.png">
    <style>
        .ss-filter-bar { display:flex; flex-wrap:wrap; gap:0.75rem; margin-bottom:1.5rem; align-items:flex-end; }
        .ss-filter-bar select,
        .ss-filter-bar input { padding:0.4rem 0.6rem; border-radius:6px; border:1px solid var(--color-border,#444); background:var(--color-card,#1e1e2d); color:var(--color-text,#fff); font-size:0.85rem; }
        .ss-filter-bar button { padding:0.4rem 1rem; }

        .ss-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; }
        @media(max-width:900px) { .ss-grid { grid-template-columns:repeat(2,1fr); } }
        @media(max-width:500px) { .ss-grid { grid-template-columns:1fr; } }

        .ss-card { border-radius:8px; overflow:hidden; border:2px solid transparent; cursor:pointer; transition:transform .15s; background:var(--color-card,#1e1e2d); }
        .ss-card:hover { transform:scale(1.03); }
        .ss-card.zero-activity { border-color:#e74c3c; }
        .ss-card img { width:100%; height:150px; object-fit:cover; display:block; }
        .ss-meta { padding:0.5rem 0.6rem; font-size:0.75rem; color:var(--color-text-secondary,#aaa); }
        .ss-meta strong { display:block; color:var(--color-text,#fff); font-size:0.8rem; margin-bottom:2px; }
        .ss-meta .zero-warn { color:#e74c3c; font-weight:bold; }

        /* Lightbox */
        .ss-lightbox { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.88); z-index:9999; align-items:center; justify-content:center; flex-direction:column; }
        .ss-lightbox.open { display:flex; }
        .ss-lightbox img { max-width:90vw; max-height:80vh; border-radius:8px; }
        .ss-lightbox-info { margin-top:0.8rem; color:#fff; font-size:0.85rem; text-align:center; }
        .ss-lightbox-close { position:absolute; top:1.2rem; right:1.5rem; font-size:2rem; color:#fff; cursor:pointer; line-height:1; }

        .disk-warn { background:#e74c3c22; border:1px solid #e74c3c; border-radius:6px; padding:0.5rem 1rem; margin-bottom:1rem; font-size:0.85rem; color:#e74c3c; }
        .empty-state { text-align:center; padding:3rem; color:var(--color-text-secondary,#aaa); }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../includes/header_partial.php'; ?>

    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <h1 style="color:var(--color-accent);">📷 Activity Screenshots</h1>
            <a href="/TimeForge_Capstone/admin/dashboard.php" class="btn btn-secondary">← Dashboard</a>
        </div>

        <?php if ($disk_size_mb >= 500): ?>
            <div class="disk-warn">⚠️ Storage warning: screenshots folder is using <strong><?= $disk_size_mb ?> MB</strong>. Consider clearing old screenshots.</div>
        <?php else: ?>
            <p style="font-size:0.8rem; color:var(--color-text-secondary); margin-bottom:1rem;">Storage used: <strong><?= $disk_size_mb ?> MB</strong></p>
        <?php endif; ?>

        <!-- Filter bar -->
        <form method="GET" class="ss-filter-bar">
            <div>
                <label style="display:block;font-size:0.75rem;margin-bottom:3px;">Worker</label>
                <select name="user_id">
                    <option value="">All Workers</option>
                    <?php foreach ($workers as $w): ?>
                        <option value="<?= $w['id'] ?>" <?= $filter_user == $w['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($w['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="display:block;font-size:0.75rem;margin-bottom:3px;">Project</label>
                <select name="project_id">
                    <option value="">All Projects</option>
                    <?php foreach ($projects as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $filter_project == $p['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['project_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="display:block;font-size:0.75rem;margin-bottom:3px;">From</label>
                <input type="date" name="date_from" value="<?= htmlspecialchars($filter_from) ?>">
            </div>
            <div>
                <label style="display:block;font-size:0.75rem;margin-bottom:3px;">To</label>
                <input type="date" name="date_to" value="<?= htmlspecialchars($filter_to) ?>">
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="screenshots.php" class="btn btn-secondary">Reset</a>
        </form>

        <p style="margin-bottom:1rem; font-size:0.85rem; color:var(--color-text-secondary);">
            Showing <strong><?= count($screenshots) ?></strong> screenshot(s).
            <span style="color:#e74c3c;">Red border = zero activity at capture time.</span>
        </p>

        <!-- Screenshot grid -->
        <?php if (empty($screenshots)): ?>
            <div class="empty-state">No screenshots found. Screenshots appear here once workers start timers with screenshots enabled.</div>
        <?php else: ?>
            <div class="ss-grid">
                <?php foreach ($screenshots as $s):
                    $img_url  = '/TimeForge_Capstone/api/screenshot_img.php?id=' . (int)$s['id'];
                    $is_zero  = ($s['activity_score_at_capture'] == 0);
                    $cap_time = date('M j, Y g:i a', strtotime($s['captured_at']));
                ?>
                    <div class="ss-card <?= $is_zero ? 'zero-activity' : '' ?>"
                         onclick="openLightbox('<?= $img_url ?>', '<?= htmlspecialchars($s['full_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($s['project_name'], ENT_QUOTES) ?>', '<?= $cap_time ?>', <?= $s['activity_score_at_capture'] ?>)">
                        <img src="<?= $img_url ?>" alt="Screenshot" loading="lazy">
                        <div class="ss-meta">
                            <strong><?= htmlspecialchars($s['full_name']) ?></strong>
                            <?= htmlspecialchars($s['project_name']) ?><br>
                            <?= $cap_time ?> &bull; <?= $s['file_size_kb'] ?> KB<br>
                            <?php if ($is_zero): ?>
                                <span class="zero-warn">⚠ No activity at capture</span>
                            <?php else: ?>
                                Activity: <?= $s['activity_score_at_capture'] ?> events
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Lightbox -->
    <div class="ss-lightbox" id="ss-lightbox" onclick="closeLightbox()">
        <span class="ss-lightbox-close" onclick="closeLightbox()">&#10005;</span>
        <img id="ss-lb-img" src="" alt="Screenshot">
        <div class="ss-lightbox-info" id="ss-lb-info"></div>
    </div>

    <?php include_once __DIR__ . '/../includes/footer_partial.php'; ?>

    <script>
        function openLightbox(src, worker, project, time, score) {
            document.getElementById('ss-lb-img').src  = src;
            document.getElementById('ss-lb-info').innerHTML =
                `<strong>${worker}</strong> &bull; ${project} &bull; ${time}<br>Activity score: ${score} events`;
            document.getElementById('ss-lightbox').classList.add('open');
        }
        function closeLightbox() {
            document.getElementById('ss-lightbox').classList.remove('open');
            document.getElementById('ss-lb-img').src = '';
        }
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
    </script>
</body>
</html>
