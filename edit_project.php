<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

requireLogin();

$role = $_SESSION['role'] ?? null;
if (!in_array($role, ['admin', 'freelancer'], true)) {
    include __DIR__ . '/includes/403.php';
    exit();
}

$project_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$project_id) {
    header('Location: index.php');
    exit();
}

// Fetch active clients for dropdown — scoped to this company
$clients_stmt = $pdo->prepare("SELECT id, client_name, company_name FROM clients WHERE is_active = 1 AND company_id = :company_id ORDER BY client_name");
$clients_stmt->execute([':company_id' => $_SESSION['company_id']]);
$clients = $clients_stmt->fetchAll();

// Fetch project
$stmt = $pdo->prepare('SELECT * FROM projects WHERE id = :id AND deleted_at IS NULL LIMIT 1');
$stmt->bindValue(':id', $project_id, PDO::PARAM_INT);
$stmt->execute();
$project = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();

if (!$project) {
    http_response_code(404);
    die('Project not found');
}

$page_title = 'Edit Project';
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - TimeForge</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="icons/logo.png">
</head>
<body>
    <?php include_once __DIR__ . '/includes/header_partial.php'; ?>

    <main class="container">
        <div class="card form-card">
            <h2 class="page-title">Edit Project</h2>

            <?php if (!empty($flash) && !empty($flash['message'])): ?>
                <div class="alert alert-<?php echo htmlspecialchars($flash['type'] ?? 'info'); ?>"><?php echo htmlspecialchars($flash['message']); ?></div>
            <?php endif; ?>

            <form action="edit_project_process.php" method="post">
                <input type="hidden" name="project_id" value="<?php echo (int)$project['id']; ?>">

                <div class="form-group">
                    <label>Project Name:</label>
                    <input type="text" name="project_name" required value="<?php echo htmlspecialchars($project['project_name']); ?>">
                </div>

                <div class="form-group">
                    <label>Client:</label>
                    <select name="client_id" required>
                        <option value="">-- Select Client --</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?php echo (int)$client['id']; ?>" <?php echo ((int)$project['client_id'] === (int)$client['id']) ? 'selected' : ''; ?> >
                                <?php
                                    echo htmlspecialchars($client['client_name']);
                                    if (!empty($client['company_name'])) {
                                        echo ' (' . htmlspecialchars($client['company_name']) . ')';
                                    }
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Hourly Rate ($):</label>
                    <input type="number" name="hourly_rate" step="0.01" min="0" required value="<?php echo htmlspecialchars((string)$project['hourly_rate']); ?>">
                </div>

                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" rows="4"><?php echo htmlspecialchars($project['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Budget:</label>
                    <input type="number" name="budget" step="0.01" min="0" value="<?php echo htmlspecialchars((string)($project['budget'] ?? '')); ?>">
                </div>

                <div class="form-group">
                    <label>Deadline:</label>
                    <input type="date" name="deadline" value="<?php echo htmlspecialchars((string)($project['deadline'] ?? '')); ?>">
                </div>

                <div class="form-group">
                    <label>Status:</label>
                    <select name="status" required>
                        <?php
                            $statuses = ['active' => 'Active', 'completed' => 'Completed', 'archived' => 'Archived'];
                            foreach ($statuses as $value => $label) {
                                $selected = ($project['status'] === $value) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($value) . '" ' . $selected . '>' . htmlspecialchars($label) . '</option>';
                            }
                        ?>
                    </select>
                </div>

                <!-- Phase 9: Screenshots toggle + interval -->
                <div class="form-group">
                    <label>Activity Screenshots:</label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer; font-weight:normal;">
                        <input type="checkbox" name="screenshots_enabled" value="1" id="ss_enabled"
                            <?php echo !empty($project['screenshots_enabled']) ? 'checked' : ''; ?>
                            onchange="toggleSsInterval()">
                        Enable automatic screenshots while timer runs
                    </label>

                    <!-- Interval configurator (shown when screenshots are ON) -->
                    <div id="ss_interval_block" style="margin-top:.85rem; padding:1rem; background:var(--color-bg); border:1px solid #334155; border-radius:8px; <?php echo empty($project['screenshots_enabled']) ? 'display:none;' : ''; ?>">
                        <div style="font-size:.82rem; font-weight:600; color:var(--color-text-secondary); text-transform:uppercase; letter-spacing:.05em; margin-bottom:.75rem;">Screenshot Interval</div>

                        <div style="display:flex; gap:1.25rem; flex-wrap:wrap; align-items:flex-end;">
                            <div>
                                <label style="font-size:.8rem; color:var(--color-text-secondary);">Min (minutes)</label>
                                <input type="number" name="screenshot_min_interval" id="ss_min"
                                    min="1" max="120" step="1"
                                    value="<?= (int)($project['screenshot_min_interval'] ?? 5) ?>"
                                    oninput="updateSsPreview()"
                                    style="width:100px; background:var(--color-card); border:1px solid #334155; color:var(--color-text); border-radius:6px; padding:.45rem .6rem; margin-top:.25rem;">
                            </div>
                            <div>
                                <label style="font-size:.8rem; color:var(--color-text-secondary);">Max (minutes)</label>
                                <input type="number" name="screenshot_max_interval" id="ss_max"
                                    min="1" max="120" step="1"
                                    value="<?= (int)($project['screenshot_max_interval'] ?? 15) ?>"
                                    oninput="updateSsPreview()"
                                    style="width:100px; background:var(--color-card); border:1px solid #334155; color:var(--color-text); border-radius:6px; padding:.45rem .6rem; margin-top:.25rem;">
                            </div>
                            <div id="ss_preview" style="font-size:.85rem; color:#22c55e; font-weight:600; padding-bottom:.5rem;"></div>
                        </div>

                        <!-- Quick presets -->
                        <div style="margin-top:.85rem; display:flex; flex-wrap:wrap; gap:.45rem;">
                            <span style="font-size:.75rem; color:#64748b; align-self:center;">Quick presets:</span>
                            <?php
                            $presets = [
                                ['label' => 'Every 5 min',    'min' => 5,  'max' => 5],
                                ['label' => 'Every 10 min',   'min' => 10, 'max' => 10],
                                ['label' => 'Every 15 min',   'min' => 15, 'max' => 15],
                                ['label' => 'Every 30 min',   'min' => 30, 'max' => 30],
                                ['label' => 'Random 5–10',    'min' => 5,  'max' => 10],
                                ['label' => 'Random 5–15',    'min' => 5,  'max' => 15],
                                ['label' => 'Random 10–30',   'min' => 10, 'max' => 30],
                                ['label' => 'Random 15–60',   'min' => 15, 'max' => 60],
                            ];
                            foreach ($presets as $p):
                            ?>
                            <button type="button"
                                onclick="setPreset(<?= $p['min'] ?>, <?= $p['max'] ?>)"
                                style="background:#1e293b; border:1px solid #334155; color:#94a3b8; border-radius:5px; padding:.2rem .6rem; font-size:.75rem; cursor:pointer; transition:border-color .15s;"
                                onmouseover="this.style.borderColor='#3b82f6';this.style.color='#e2e8f0'"
                                onmouseout="this.style.borderColor='#334155';this.style.color='#94a3b8'">
                                <?= $p['label'] ?>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <script>
                function toggleSsInterval() {
                    const on  = document.getElementById('ss_enabled').checked;
                    document.getElementById('ss_interval_block').style.display = on ? 'block' : 'none';
                    if (on) updateSsPreview();
                }
                function updateSsPreview() {
                    let mn = parseInt(document.getElementById('ss_min').value) || 5;
                    let mx = parseInt(document.getElementById('ss_max').value) || 15;
                    if (mn < 1)  { mn = 1;  document.getElementById('ss_min').value = mn; }
                    if (mx < mn) { mx = mn; document.getElementById('ss_max').value = mx; }
                    const txt = (mn === mx)
                        ? `📸 Fixed: every ${mn} minute${mn !== 1 ? 's' : ''}`
                        : `📸 Random: every ${mn}–${mx} minutes`;
                    document.getElementById('ss_preview').textContent = txt;
                }
                function setPreset(mn, mx) {
                    document.getElementById('ss_min').value = mn;
                    document.getElementById('ss_max').value = mx;
                    updateSsPreview();
                }
                // Run on load
                updateSsPreview();
                </script>

                <div class="form-group buttons">
                    <input type="submit" value="Save Changes" class="btn btn-primary">
                    <a href="index.php" class="btn btn-secondary" style="margin-left:8px;">Cancel</a>
                </div>
            </form>
        </div>
    </main>

    <?php include_once __DIR__ . '/includes/footer_partial.php'; ?>
</body>
</html>
