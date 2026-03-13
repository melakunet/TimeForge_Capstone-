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
    // Ensure this function is available if not already loaded
    if (!window.startProjectTimer) {
        window.startProjectTimer = function(id, name) {
            if (!window.timeTracker) {
                console.error('TimeTracker not initialized');
                return;
            }
            if (window.timeTracker.projectId) {
                alert('A timer is already running. Please stop it first.');
                return;
            }
            const description = prompt(`Start timer for "${name}"?\n\nEnter task description (optional):`, "Freelance work");
            if (description !== null) {
                window.timeTracker.startTimer(id, description);
            }
        };
    }
</script>
