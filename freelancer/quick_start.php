<?php
// freelancer/quick_start.php
// Partial for Freelancer Dashboard to show active projects and start buttons

require_once __DIR__ . '/../db.php';

// Fetch active projects
// Note: In Phase 1-5 logic, freelancers see ALL active projects.
// In later phases, we might filter by assignment.
$qs_stmt = $pdo->query("SELECT id, project_name FROM projects WHERE status = 'active' ORDER BY id DESC LIMIT 5");
$qs_projects = $qs_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="card">
    <h3 style="color: var(--color-accent); margin-bottom: 1rem;">Quick Timer Start</h3>
    <?php if (empty($qs_projects)): ?>
        <p style="color: #666;">No active projects.</p>
    <?php else: ?>
        <ul style="list-style: none; padding: 0;">
            <?php foreach ($qs_projects as $p): ?>
                <li style="margin-bottom: 0.8rem; display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.5rem; border-bottom: 1px solid #eee;">
                    <span><?= htmlspecialchars($p['project_name']) ?></span>
                    <button class="btn btn-sm btn-success" 
                            onclick="window.startProjectTimer(<?= $p['id'] ?>, '<?= htmlspecialchars($p['project_name'], ENT_QUOTES) ?>')">
                        ▶ Start
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<script>
    // Phase 6.8: Per-Task Declaration
    if (!window.startProjectTimer) {
        window.startProjectTimer = function(id, name) {
            if (!window.timeTracker) { console.error('TimeTracker not initialized'); return; }
            if (window.timeTracker.projectId) {
                alert('A timer is already running. Please stop it first.');
                return;
            }
            window._tfPendingProject = { id, name };
            document.getElementById('tf-task-modal-title').textContent = name;
            document.getElementById('tf-task-modal').style.display = 'flex';
        };
    }
</script>

<!-- Phase 6.8 Task Type Modal -->
<div id="tf-task-modal" style="
    display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6);
    z-index:10000; align-items:center; justify-content:center;">
    <div style="background:#1e1e2d; color:#fff; border-radius:12px; padding:2rem; width:360px; box-shadow:0 8px 32px rgba(0,0,0,0.5);">
        <h3 style="color:#00d2b5; margin-bottom:1rem;">▶ Start Timer</h3>
        <p style="margin-bottom:1.2rem; color:#aaa;">Project: <strong id="tf-task-modal-title"></strong></p>
        <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem;">Task Type</label>
        <select id="tf-task-type" style="width:100%; padding:0.5rem; border-radius:6px; border:1px solid #444; background:#2a2a3d; color:#fff; margin-bottom:1rem;">
            <option value="Coding">💻 Coding</option>
            <option value="Design">🎨 Design</option>
            <option value="Research">🔍 Research</option>
            <option value="Meeting">📞 Meeting / Client Call</option>
            <option value="Admin">📋 Admin / Paperwork</option>
            <option value="Testing">🧪 Testing / QA</option>
            <option value="Other">⚙ Other</option>
        </select>
        <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem;">Note (optional)</label>
        <input id="tf-task-note" type="text" placeholder="e.g. Fixing login bug…"
            style="width:100%; padding:0.5rem; border-radius:6px; border:1px solid #444; background:#2a2a3d; color:#fff; margin-bottom:1.2rem; box-sizing:border-box;">
        <div style="display:flex; gap:0.8rem;">
            <button onclick="window._tfConfirmTask()" style="flex:1; padding:0.6rem; background:#00d2b5; color:#000; border:none; border-radius:6px; font-weight:bold; cursor:pointer;">Start</button>
            <button onclick="document.getElementById('tf-task-modal').style.display='none'" style="flex:1; padding:0.6rem; background:#444; color:#fff; border:none; border-radius:6px; cursor:pointer;">Cancel</button>
        </div>
    </div>
</div>

<script>
    window._tfConfirmTask = function() {
        const p    = window._tfPendingProject;
        const type = document.getElementById('tf-task-type').value;
        const note = document.getElementById('tf-task-note').value.trim();
        const desc = note ? `${type}: ${note}` : type;
        document.getElementById('tf-task-modal').style.display = 'none';
        document.getElementById('tf-task-note').value = '';
        if (p) window.timeTracker.startTimer(p.id, desc);
    };
</script>
