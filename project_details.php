<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;
$project_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$project_id) {
    setFlash('error', 'Invalid project ID.');
    header("Location: index.php");
    exit;
}

// Fetch project details with client info — scoped to the logged-in user's company
$query = "
    SELECT 
        p.*, 
        c.client_name, 
        c.company_name, 
        c.email as client_email,
        u.full_name as created_by_name
    FROM projects p
    LEFT JOIN clients c ON p.client_id = c.id
    LEFT JOIN users u ON p.created_by = u.id
    WHERE p.id = :id
      AND p.company_id = :company_id
";

$stmt = $pdo->prepare($query);
$stmt->bindValue(':id', $project_id, PDO::PARAM_INT);
$stmt->bindValue(':company_id', $_SESSION['company_id'] ?? 0, PDO::PARAM_INT);
$stmt->execute();
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    setFlash('error', 'Project not found.');
    header("Location: index.php");
    exit;
}

// Access Control
if ($role === 'client') {
    // Check if the project belongs to this client
    $clientCheckQuery = "SELECT id FROM clients WHERE id = :client_id AND user_id = :user_id";
    $cStmt = $pdo->prepare($clientCheckQuery);
    $cStmt->bindValue(':client_id', $project['client_id']);
    $cStmt->bindValue(':user_id', $user_id);
    $cStmt->execute();
    
    if (!$cStmt->fetch()) {
        setFlash('error', 'Access denied.');
        header("Location: index.php");
        exit;
    }
}

// Fetch Time Entries
$timeQuery = "
    SELECT 
        te.*, 
        u.full_name as user_name,
        TIME_FORMAT(TIMEDIFF(IFNULL(te.end_time, NOW()), te.start_time), '%H:%i') as duration
    FROM time_entries te
    LEFT JOIN users u ON te.user_id = u.id
    WHERE te.project_id = :project_id
    ORDER BY te.start_time DESC
";
$tStmt = $pdo->prepare($timeQuery);
$tStmt->bindValue(':project_id', $project_id, PDO::PARAM_INT);
$tStmt->execute();
$time_entries = $tStmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate Totals (ONLY Approved or Completed entries)
$total_seconds = 0;
foreach ($time_entries as $entry) {
    // Skip rejected or pending entries for cost calculation
    if ($entry['status'] === 'rejected' || $entry['status'] === 'pending') {
        continue;
    }

    if ($entry['end_time']) {
        $total_seconds += strtotime($entry['end_time']) - strtotime($entry['start_time']);
    } elseif ($entry['status'] == 'running') {
        $total_seconds += time() - strtotime($entry['start_time']);
    }
}
$total_hours = $total_seconds / 3600;
$total_cost = $total_hours * ($project['hourly_rate'] ?? 0);
$budget = $project['budget'] ?? 0;
$budget_remaining = $budget - $total_cost;

// Count approved billable entries — used to gate the invoice pre-flight modal
$approved_stmt = $pdo->prepare("
    SELECT COUNT(*) AS cnt
    FROM time_entries
    WHERE project_id  = :pid
      AND status      = 'approved'
      AND is_billable = 1
      AND end_time   IS NOT NULL
");
$approved_stmt->execute([':pid' => $project_id]);
$approved_billable_count = (int)$approved_stmt->fetchColumn();

$page_title = 'Project Details: ' . $project['project_name'];
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
    <div class="page-header">
        <div class="header-content">
            <h1><?php echo htmlspecialchars($project['project_name']); ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li><a href="index.php">Dashboard</a></li>
                    <li class="active"><?php echo htmlspecialchars($project['project_name']); ?></li>
                </ol>
            </nav>
        </div>
        <div class="header-actions">
            <?php if ($role === 'admin' || $role === 'freelancer'): ?>
                <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="btn btn-secondary">Edit Project</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($flash['message'])): ?>
        <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>

    <div class="grid-layout">
        <div class="main-column">
            <div class="card">
                <div class="card-header">
                    <h3>Project Overview</h3>
                    <span class="status-badge status-<?php echo htmlspecialchars($project['status']); ?>">
                        <?php echo ucfirst(htmlspecialchars($project['status'])); ?>
                    </span>
                </div>
                <div class="card-body">
                    <p class="text-label">Description</p>
                    <div class="description-box">
                        <?php echo nl2br(htmlspecialchars($project['description'] ?? 'No description provided.')); ?>
                    </div>
                    
                    <div class="info-grid user-info-grid">
                        <div class="info-item">
                            <span class="label">Total Budget</span>
                            <span class="value">$<?php echo number_format($project['budget'] ?? 0, 2); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Hourly Rate</span>
                            <span class="value">$<?php echo number_format($project['hourly_rate'] ?? 0, 2); ?>/hr</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Hours Logged</span>
                            <span class="value"><?php echo number_format($total_hours, 2); ?> hrs</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Total Cost</span>
                            <span class="value text-accent">$<?php echo number_format($total_cost, 2); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Budget Remaining</span>
                            <span class="value <?php echo ($budget_remaining < 0) ? 'text-danger' : 'text-success'; ?>">
                                $<?php echo number_format($budget_remaining, 2); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Time Entries -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3>Time Entries</h3>
                    <?php if ($role === 'admin' || $role === 'freelancer'): ?>
                        <div class="header-actions">
                            <button class="btn btn-secondary btn-sm" onclick="openManualEntryModal()">
                                + Manual Entry
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="startProjectTimer()">
                                ▶ Start Timer
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <!-- Timer Integration Script -->
                    <script>
                        async function startProjectTimer() {
                            const projectId   = <?php echo json_encode($project['id']); ?>;
                            const projectName = <?php echo json_encode($project['project_name']); ?>;

                            if (!window.timeTracker) {
                                alert('Timer module not loaded. Please refresh the page.');
                                return;
                            }

                            // Phase 11: fetch tasks for this project
                            let taskId = null;
                            try {
                                const res = await fetch(`/TimeForge_Capstone/api/get_tasks.php?project_id=${projectId}`);
                                const tasks = await res.json();
                                if (tasks.length > 0) {
                                    // Build simple prompt with select
                                    const opts = tasks.map(t => `<option value="${t.id}">[${t.status.replace('_',' ')}] ${t.title}</option>`).join('');
                                    const wrapper = document.createElement('div');
                                    wrapper.innerHTML = `
                                        <div id="_tf_task_picker" style="position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:99999;display:flex;align-items:center;justify-content:center;">
                                          <div style="background:#1e293b;border:1px solid #334155;border-radius:10px;padding:1.5rem;width:min(420px,92vw);">
                                            <h3 style="margin:0 0 1rem;color:#3b82f6;">Select a Task (optional)</h3>
                                            <select id="_tf_task_sel" style="width:100%;background:#0f172a;color:#e2e8f0;border:1px solid #334155;border-radius:6px;padding:.55rem .75rem;font-size:.9rem;margin-bottom:1rem;">
                                              <option value="">— No specific task —</option>
                                              ${opts}
                                            </select>
                                            <div style="display:flex;gap:.75rem;justify-content:flex-end;">
                                              <button id="_tf_task_cancel" style="background:#334155;color:#e2e8f0;border:none;border-radius:6px;padding:.5rem 1rem;cursor:pointer;">Cancel</button>
                                              <button id="_tf_task_ok" style="background:#3b82f6;color:#fff;border:none;border-radius:6px;padding:.5rem 1.2rem;cursor:pointer;font-weight:600;">Start Timer</button>
                                            </div>
                                          </div>
                                        </div>`;
                                    document.body.appendChild(wrapper);
                                    taskId = await new Promise(resolve => {
                                        document.getElementById('_tf_task_ok').onclick = () => {
                                            const v = document.getElementById('_tf_task_sel').value;
                                            wrapper.remove();
                                            resolve(v ? parseInt(v) : null);
                                        };
                                        document.getElementById('_tf_task_cancel').onclick = () => {
                                            wrapper.remove();
                                            resolve(false); // false = user cancelled
                                        };
                                    });
                                    if (taskId === false) return; // cancelled
                                }
                            } catch(e) { /* silently skip task picker on network error */ }

                            window.timeTracker.startTimer(projectId, projectName, null, taskId);
                        }
                    </script>
                    
                    <div class="table-responsive">
                        <table class="table project-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Description</th>
                                    <th>Start - End</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($time_entries) > 0): ?>
                                    <?php foreach ($time_entries as $entry): ?>
                                        <tr>
                                            <td><?php echo date('M d, Y', strtotime($entry['start_time'])); ?></td>
                                            <td>
                                                <div class="user-avatar-sm" title="<?php echo htmlspecialchars($entry['user_name']); ?>">
                                                    <?php echo strtoupper(substr($entry['user_name'] ?? 'U', 0, 2)); ?>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($entry['description']); ?></td>
                                            <td>
                                                <?php 
                                                    echo date('H:i', strtotime($entry['start_time']));
                                                    if ($entry['end_time']) {
                                                        echo ' - ' . date('H:i', strtotime($entry['end_time']));
                                                    } else {
                                                        echo ' - ...';
                                                    }
                                                ?>
                                            </td>
                                            <td><strong><?php echo $entry['duration']; ?></strong></td>
                                            <td>
                                                <?php if ($entry['status'] === 'running'): ?>
                                                    <span class="status-badge status-active">Running</span>
                                                <?php elseif (in_array($entry['status'], ['pending', 'completed', 'abandoned'])): ?>
                                                    <span class="status-badge status-warning">
                                                        <?php
                                                        $label_map = ['pending' => 'Pending', 'completed' => 'Completed', 'abandoned' => 'Abandoned'];
                                                        echo $label_map[$entry['status']];
                                                        ?>
                                                    </span>
                                                    <?php if ($role === 'admin'): ?>
                                                        <div class="approval-actions mt-1">
                                                            <form action="approve_time_entry.php" method="POST" class="d-inline">
                                                                <input type="hidden" name="entry_id" value="<?php echo $entry['id']; ?>">
                                                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                                                <button type="submit" name="action" value="approve" class="btn-approve" title="Approve">✓</button>
                                                            </form>
                                                            <form action="approve_time_entry.php" method="POST" class="d-inline">
                                                                <input type="hidden" name="entry_id" value="<?php echo $entry['id']; ?>">
                                                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                                                <button type="submit" name="action" value="reject" class="btn-reject" title="Reject">✕</button>
                                                            </form>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php elseif ($entry['status'] === 'rejected'): ?>
                                                    <span class="status-badge status-danger" title="<?php echo htmlspecialchars($entry['rejection_reason'] ?? ''); ?>">Rejected</span>
                                                <?php elseif ($entry['status'] === 'approved'): ?>
                                                    <span class="status-badge status-success">Approved</span>
                                                <?php else: ?>
                                                    <span class="status-badge status-completed">Completed</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (($entry['user_id'] == $user_id && $entry['status'] == 'pending') || $role === 'admin'): ?>
                                                    <a href="edit_time_entry.php?id=<?php echo $entry['id']; ?>" class="btn-icon" title="Edit">✎</a>
                                                    <form action="delete_time_entry.php" method="POST" class="d-inline" onsubmit="return confirm('Delete this time entry?');">
                                                        <input type="hidden" name="entry_id" value="<?php echo $entry['id']; ?>">
                                                        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                                        <button type="submit" class="btn-icon btn-icon-danger" title="Delete">🗑</button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No time logs yet. Start the timer to begin tracking!</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Detailed Time Entries Table -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3>Detailed Time Entries</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($time_entries)): ?>
                        <div class="empty-state">
                            <p>No time entries found for this project.</p>
                        </div>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Status</th>
                                    <th>Duration</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($time_entries as $entry): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($entry['user_name']); ?></td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($entry['start_time'])); ?></td>
                                        <td>
                                            <?php if ($entry['end_time']): ?>
                                                <?php echo date('Y-m-d H:i', strtotime($entry['end_time'])); ?>
                                            <?php else: ?>
                                                <span class="text-warning">In Progress</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo htmlspecialchars($entry['status']); ?>">
                                                <?php echo ucfirst(htmlspecialchars($entry['status'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($entry['duration']): ?>
                                                <?php echo htmlspecialchars($entry['duration']); ?>
                                            <?php else: ?>
                                                <span class="text-warning">Calculating...</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <!-- Action buttons for time entry -->
                                            <?php if ($role === 'admin' || $role === 'freelancer'): ?>
                                                <div class="action-buttons">
                                                    <?php if ($entry['status'] === 'running'): ?>
                                                        <form action="stop_timer.php" method="POST" class="d-inline">
                                                            <input type="hidden" name="entry_id" value="<?php echo $entry['id']; ?>">
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Stop the timer?');">
                                                                ■ Stop Timer
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form action="start_timer.php" method="POST" class="d-inline">
                                                            <input type="hidden" name="entry_id" value="<?php echo $entry['id']; ?>">
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                ▶ Resume Timer
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    
                                                    <a href="edit_time_entry.php?id=<?php echo $entry['id']; ?>" class="btn btn-secondary btn-sm">
                                                        ✎ Edit
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="sidebar-column">
            <div class="card">
                <div class="card-header">
                    <h3>Client Details</h3>
                </div>
                <div class="card-body">
                    <h4 class="client-name"><?php echo htmlspecialchars($project['client_name']); ?></h4>
                    <?php if (!empty($project['company_name'])): ?>
                        <p class="company-name"><?php echo htmlspecialchars($project['company_name']); ?></p>
                    <?php endif; ?>
                    
                    <hr class="divider">
                    
                    <div class="contact-info">
                        <?php if (!empty($project['client_email'])): ?>
                            <div class="contact-item">
                                <span class="icon">✉</span>
                                <a href="mailto:<?php echo htmlspecialchars($project['client_email']); ?>">
                                    <?php echo htmlspecialchars($project['client_email']); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($role === 'admin'): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h3>Admin Actions</h3>
                    </div>
                    <div class="card-body">
                         <?php if ($project['deleted_at']): ?>
                             <form action="restore_project.php" method="POST">
                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                <button type="submit" class="btn btn-success btn-full">Restore Project</button>
                             </form>
                         <?php else: ?>
                            <form action="delete_project.php" method="POST" onsubmit="return confirm('Archive this project?');">
                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                <button type="submit" class="btn btn-archive btn-full">Archive Project</button>
                            </form>
                        <?php endif; ?>

                        <!-- Invoice and CSV export — opens pre-flight checklist modal -->
                        <hr style="margin: 1rem 0; border-color: var(--color-border, #e5e7eb);">

                        <?php if ($approved_billable_count === 0): ?>
                            <!-- No approved entries yet — button is disabled with a clear reason -->
                            <button class="btn btn-primary btn-full"
                                    style="margin-bottom:0.5rem; display:block; width:100%; opacity:0.55; cursor:not-allowed;"
                                    title="Approve at least one billable time entry before generating an invoice."
                                    disabled>
                                🧾 Generate Invoice
                            </button>
                            <p style="font-size:0.78rem; color:#ef4444; margin:0 0 0.75rem; line-height:1.4;">
                                ⚠️ No approved billable entries yet. Approve time entries in the table below first.
                            </p>
                        <?php else: ?>
                            <!-- Entries ready — open the pre-flight checklist modal -->
                            <button class="btn btn-primary btn-full"
                                    style="margin-bottom:0.5rem; display:block; width:100%;"
                                    onclick="openInvoicePreflight()">
                                🧾 Generate Invoice
                            </button>
                        <?php endif; ?>

                        <button class="btn btn-secondary btn-full"
                                style="display:block; width:100%;"
                                onclick="openCsvPreflight()">
                            📥 Export Time Log CSV
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Manual Entry Modal -->
    <div id="manualEntryModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeManualEntryModal()">&times;</span>
            <h2>Log Time Manually</h2>
            <form action="add_time_manual.php" method="POST">
                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="start_date" required value="<?php echo date('Y-m-d'); ?>" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Start Time</label>
                    <input type="time" name="start_time" required value="09:00" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Duration (Hours)</label>
                    <input type="number" name="duration_hours" step="0.25" min="0.25" required placeholder="e.g. 1.5" class="form-control">
                    <small class="form-text">Enter decimal hours (1.5 = 1h 30m)</small>
                </div>
                
                <div class="form-group">
                    <label>Description of Work</label>
                    <textarea name="description" required placeholder="What did you work on?" class="form-control"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeManualEntryModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Time</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ── Tasks Panel ───────────────────────────────────────────── -->
    <div class="card" style="margin-top:2rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; flex-wrap:wrap; gap:.75rem;">
            <h2 style="margin:0; font-size:1.1rem;">📋 Tasks</h2>
            <a href="/TimeForge_Capstone/tasks.php?project_id=<?= $project_id ?>" class="btn btn-secondary" style="font-size:.85rem;">
                View Full Task Board →
            </a>
        </div>
        <?php
        // Quick task summary for project_details
        $cid_pd  = (int)($_SESSION['company_id'] ?? 0);
        $uid_pd  = (int)($_SESSION['user_id'] ?? 0);
        $role_pd = $_SESSION['role'] ?? '';
        $task_extra = ($role_pd === 'freelancer') ? "AND (t.assigned_to = {$uid_pd} OR t.assigned_to IS NULL)" : '';
        $tsum = $pdo->prepare("
            SELECT t.id, t.title, t.status, t.priority, t.due_date, t.estimated_hours,
                   u.full_name AS assignee_name,
                   COALESCE(SUM(CASE WHEN te.end_time IS NOT NULL THEN te.total_seconds ELSE 0 END),0) AS logged_sec
            FROM tasks t
            LEFT JOIN users u ON u.id = t.assigned_to
            LEFT JOIN time_entries te ON te.task_id = t.id
            WHERE t.project_id = :pid AND t.company_id = :cid {$task_extra}
            GROUP BY t.id
            ORDER BY FIELD(t.status,'in_progress','open','done'), FIELD(t.priority,'high','medium','low'), t.due_date ASC
            LIMIT 10
        ");
        $tsum->execute([':pid' => $project_id, ':cid' => $cid_pd]);
        $pd_tasks = $tsum->fetchAll(PDO::FETCH_ASSOC);

        // Count totals
        $tcnt = $pdo->prepare("SELECT status, COUNT(*) AS cnt FROM tasks WHERE project_id=:pid AND company_id=:cid GROUP BY status");
        $tcnt->execute([':pid' => $project_id, ':cid' => $cid_pd]);
        $tcounts = ['open'=>0,'in_progress'=>0,'done'=>0];
        foreach ($tcnt->fetchAll(PDO::FETCH_ASSOC) as $r) $tcounts[$r['status']] = (int)$r['cnt'];
        $total_t = array_sum($tcounts);
        $pct_t   = $total_t > 0 ? round(($tcounts['done'] / $total_t) * 100) : 0;
        ?>
        <?php if ($total_t > 0): ?>
        <div style="display:flex; gap:1.5rem; margin-bottom:1rem; flex-wrap:wrap; align-items:center;">
            <div style="display:flex; gap:1rem;">
                <span style="font-size:.82rem; background:#94a3b822; color:#94a3b8; border-radius:99px; padding:.2rem .7rem; border:1px solid #94a3b833;">⬜ <?= $tcounts['open'] ?> Open</span>
                <span style="font-size:.82rem; background:#f59e0b22; color:#f59e0b; border-radius:99px; padding:.2rem .7rem; border:1px solid #f59e0b33;">🔄 <?= $tcounts['in_progress'] ?> In Progress</span>
                <span style="font-size:.82rem; background:#22c55e22; color:#22c55e; border-radius:99px; padding:.2rem .7rem; border:1px solid #22c55e33;">✅ <?= $tcounts['done'] ?> Done</span>
            </div>
            <div style="flex:1; min-width:120px;">
                <div style="background:#1e293b; border-radius:4px; height:7px; overflow:hidden;">
                    <div style="width:<?= $pct_t ?>%; height:100%; background:linear-gradient(90deg,#3b82f6,#22c55e); border-radius:4px;"></div>
                </div>
                <div style="font-size:.72rem; color:#64748b; margin-top:.2rem;"><?= $pct_t ?>% complete</div>
            </div>
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:.85rem;">
            <thead>
                <tr style="color:#64748b; border-bottom:1px solid #334155; text-align:left;">
                    <th style="padding:.45rem .6rem; font-weight:600;">Task</th>
                    <th style="padding:.45rem .6rem; font-weight:600;">Assignee</th>
                    <th style="padding:.45rem .6rem; font-weight:600;">Priority</th>
                    <th style="padding:.45rem .6rem; font-weight:600;">Status</th>
                    <th style="padding:.45rem .6rem; font-weight:600;">Progress</th>
                    <th style="padding:.45rem .6rem; font-weight:600;">Due</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($pd_tasks as $pt):
                $lg_h = round($pt['logged_sec'] / 3600, 1);
                $et_h = (float)($pt['estimated_hours'] ?? 0);
                $pp   = ($et_h > 0) ? min(100, round(($lg_h / $et_h) * 100)) : null;
                $is_ov = $pt['due_date'] && $pt['status'] !== 'done' && strtotime($pt['due_date']) < strtotime('today');
                $status_map = ['open'=>['⬜','#94a3b8'],'in_progress'=>['🔄','#f59e0b'],'done'=>['✅','#22c55e']];
                [$sico, $scol] = $status_map[$pt['status']] ?? ['?','#64748b'];
                $pri_map = ['high'=>['🔴','#ef4444'],'medium'=>['🟡','#f59e0b'],'low'=>['🟢','#22c55e']];
                [$pico, $pcol] = $pri_map[$pt['priority']] ?? ['⚪','#64748b'];
            ?>
            <tr style="border-bottom:1px solid #1e293b; transition:background .15s;" onmouseover="this.style.background='#1e293b'" onmouseout="this.style.background=''">
                <td style="padding:.55rem .6rem; font-weight:500;"><?= htmlspecialchars($pt['title']) ?></td>
                <td style="padding:.55rem .6rem; color:#94a3b8;"><?= $pt['assignee_name'] ? htmlspecialchars($pt['assignee_name']) : '<span style="color:#475569;">—</span>' ?></td>
                <td style="padding:.55rem .6rem; color:<?= $pcol ?>;"><?= $pico ?> <?= ucfirst($pt['priority']) ?></td>
                <td style="padding:.55rem .6rem; color:<?= $scol ?>;"><?= $sico ?> <?= ucwords(str_replace('_',' ',$pt['status'])) ?></td>
                <td style="padding:.55rem .6rem; min-width:100px;">
                    <?php if ($pp !== null): ?>
                        <div style="background:#1e293b; border-radius:3px; height:6px; overflow:hidden; margin-bottom:.2rem;">
                            <div style="width:<?= $pp ?>%; height:100%; background:<?= $pp >= 100 ? '#22c55e' : '#3b82f6' ?>; border-radius:3px;"></div>
                        </div>
                        <span style="font-size:.7rem; color:#64748b;"><?= $lg_h ?>h / <?= $et_h ?>h</span>
                    <?php elseif ($lg_h > 0): ?>
                        <span style="font-size:.7rem; color:#64748b;"><?= $lg_h ?>h logged</span>
                    <?php else: ?>
                        <span style="color:#475569;">—</span>
                    <?php endif; ?>
                </td>
                <td style="padding:.55rem .6rem; color:<?= $is_ov ? '#ef4444' : '#94a3b8' ?>; font-size:.8rem;">
                    <?= $pt['due_date'] ? date('M j', strtotime($pt['due_date'])) . ($is_ov ? ' ⚠' : '') : '—' ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php if ($total_t > 10): ?>
            <p style="text-align:center; margin:.75rem 0 0; font-size:.8rem; color:#64748b;">
                Showing 10 of <?= $total_t ?> tasks —
                <a href="/TimeForge_Capstone/tasks.php?project_id=<?= $project_id ?>" style="color:var(--color-accent);">View all on task board</a>
            </p>
        <?php endif; ?>
        <?php else: ?>
            <p style="color:#475569; text-align:center; padding:1.5rem 0;">
                No tasks yet.
                <?php if ($role_pd === 'admin'): ?>
                    <a href="/TimeForge_Capstone/tasks.php?project_id=<?= $project_id ?>" style="color:var(--color-accent);">Create the first task →</a>
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </div>

    <!-- Modal Scripts -->
    <script>
        const modal = document.getElementById('manualEntryModal');
        function openManualEntryModal() {
            modal.style.display = "block";
        }
        function closeManualEntryModal() {
            modal.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target == modal) {
                closeManualEntryModal();
            }
        }
    </script>
</main>

<?php include_once __DIR__ . '/includes/footer_partial.php'; ?>
<script src="/TimeForge_Capstone/js/time_tracker.js"></script>

<!-- ── Invoice Pre-flight Checklist Modal ────────────────────────────────── -->
<!-- Shows the admin a summary of key settings before going to generate.php. -->
<!-- If anything looks wrong they can cancel and fix it first.               -->
<div id="invoicePreflightModal" style="display:none; position:fixed; inset:0; z-index:9000; background:rgba(0,0,0,0.55); align-items:center; justify-content:center;">
    <div style="background:var(--color-card-bg,#1e293b); border:1px solid var(--color-border,#334155); border-radius:12px; padding:2rem; width:min(520px,94vw); max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,0.4);">
        <h2 style="margin:0 0 0.25rem; font-size:1.25rem;">🧾 Invoice Pre-flight Check</h2>
        <p style="color:var(--color-text-secondary,#94a3b8); font-size:0.85rem; margin:0 0 1.5rem;">
            Review these settings before generating. You can change them on the next page too.
        </p>

        <!-- Step 1: Approved entries -->
        <div style="display:flex; align-items:flex-start; gap:0.75rem; padding:0.85rem; background:rgba(22,163,74,0.1); border:1px solid rgba(22,163,74,0.35); border-radius:8px; margin-bottom:1rem;">
            <span style="font-size:1.3rem; line-height:1;">✅</span>
            <div>
                <strong style="font-size:0.9rem;">Approved Billable Entries</strong>
                <div style="font-size:0.82rem; color:var(--color-text-secondary,#94a3b8); margin-top:2px;">
                    <?php echo $approved_billable_count; ?> entr<?php echo $approved_billable_count === 1 ? 'y' : 'ies'; ?> ready to bill
                </div>
            </div>
        </div>

        <!-- Step 2: Hourly rate -->
        <div style="display:flex; align-items:flex-start; gap:0.75rem; padding:0.85rem; background:var(--color-bg-secondary,#0f172a); border:1px solid var(--color-border,#334155); border-radius:8px; margin-bottom:1rem;">
            <span style="font-size:1.3rem; line-height:1;">💲</span>
            <div>
                <strong style="font-size:0.9rem;">Hourly Rate</strong>
                <div style="font-size:0.82rem; color:var(--color-text-secondary,#94a3b8); margin-top:2px;">
                    $<?php echo number_format((float)$project['hourly_rate'], 2); ?>/hr
                    <?php if ((float)$project['hourly_rate'] === 0.0): ?>
                        &nbsp;<span style="color:#f59e0b;">⚠️ Rate is $0 — <a href="/TimeForge_Capstone/edit_project.php?id=<?php echo $project['id']; ?>" style="color:#f59e0b;">edit project</a> to set it.</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Step 3: Tax rate -->
        <div style="display:flex; align-items:flex-start; gap:0.75rem; padding:0.85rem; background:var(--color-bg-secondary,#0f172a); border:1px solid var(--color-border,#334155); border-radius:8px; margin-bottom:1rem;">
            <span style="font-size:1.3rem; line-height:1;">🏷️</span>
            <div>
                <strong style="font-size:0.9rem;">Default Tax Rate</strong>
                <div style="font-size:0.82rem; color:var(--color-text-secondary,#94a3b8); margin-top:2px;">
                    <?php echo number_format((float)($project['tax_rate'] ?? 0), 2); ?>%
                    — you can adjust this on the generate page.
                    <?php if ((float)($project['tax_rate'] ?? 0) === 0.0): ?>
                        &nbsp;<span style="color:#94a3b8;">(0% = no tax)</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Step 4: Logo -->
        <div style="display:flex; align-items:flex-start; gap:0.75rem; padding:0.85rem; background:var(--color-bg-secondary,#0f172a); border:1px solid var(--color-border,#334155); border-radius:8px; margin-bottom:1rem;">
            <span style="font-size:1.3rem; line-height:1;">🖼️</span>
            <div style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">
                <div>
                    <strong style="font-size:0.9rem;">Company Logo</strong>
                    <div style="font-size:0.82rem; color:var(--color-text-secondary,#94a3b8); margin-top:2px;">Used in the invoice header on all templates.</div>
                </div>
                <img src="/TimeForge_Capstone/icons/logo.png" alt="Logo preview"
                     style="height:36px; border-radius:4px; border:1px solid var(--color-border,#334155);">
            </div>
        </div>

        <!-- Step 5: Template picker (inline, default classic) -->
        <div style="margin-bottom:1.5rem;">
            <strong style="font-size:0.9rem; display:block; margin-bottom:0.6rem;">🎨 Choose Invoice Template</strong>
            <p style="font-size:0.78rem; color:var(--color-text-secondary,#94a3b8); margin:0 0 0.75rem;">
                Pick a style below. You can change it again on the generate page.
            </p>
            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(88px,1fr)); gap:0.6rem;" id="preflightTplPicker">
                <?php
                $tpl_opts = [
                    'classic'   => ['label' => 'Classic',   'color' => '#14532d'],
                    'modern'    => ['label' => 'Modern',    'color' => '#1e3a5f'],
                    'bold'      => ['label' => 'Bold',      'color' => '#1f2937'],
                    'minimal'   => ['label' => 'Minimal',   'color' => '#6b7280'],
                    'corporate' => ['label' => 'Corporate', 'color' => '#4c1d95'],
                ];
                foreach ($tpl_opts as $key => $opt): ?>
                <label style="display:flex; flex-direction:column; align-items:center; gap:0.35rem; padding:0.6rem 0.4rem; border:2px solid var(--color-border,#334155); border-radius:8px; cursor:pointer; transition:border-color 0.15s;" class="pf-tpl-card" data-key="<?php echo $key; ?>">
                    <input type="radio" name="pf_template" value="<?php echo $key; ?>" <?php echo $key === 'classic' ? 'checked' : ''; ?>
                           style="position:absolute;opacity:0;width:0;height:0;"
                           onchange="pfSelectTemplate('<?php echo $key; ?>')">
                    <span style="display:block; width:100%; height:22px; border-radius:4px; background:<?php echo $opt['color']; ?>;"></span>
                    <span style="font-size:0.72rem; font-weight:700; color:var(--color-text,#f1f5f9);"><?php echo $opt['label']; ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Action buttons -->
        <div style="display:flex; gap:0.75rem; justify-content:flex-end;">
            <button onclick="closeInvoicePreflight()" class="btn btn-secondary">Cancel</button>
            <a id="preflightContinueBtn"
               href="/TimeForge_Capstone/invoices/generate.php?project_id=<?php echo $project['id']; ?>&tpl=classic"
               class="btn btn-primary">
                Continue to Generate →
            </a>
        </div>
    </div>
</div>

<!-- ── CSV Pre-flight Modal ───────────────────────────────────────────────── -->
<!-- Prevents an accidental silent download by showing a confirmation step.  -->
<div id="csvPreflightModal" style="display:none; position:fixed; inset:0; z-index:9000; background:rgba(0,0,0,0.55); align-items:center; justify-content:center;">
    <div style="background:var(--color-card-bg,#1e293b); border:1px solid var(--color-border,#334155); border-radius:12px; padding:2rem; width:min(440px,94vw); box-shadow:0 20px 60px rgba(0,0,0,0.4);">
        <h2 style="margin:0 0 0.25rem; font-size:1.25rem;">📥 Export Time Log CSV</h2>
        <p style="color:var(--color-text-secondary,#94a3b8); font-size:0.85rem; margin:0 0 1.5rem;">
            This will download a CSV of <strong>all approved time entries</strong> for this project.
        </p>
        <div style="padding:0.85rem; background:var(--color-bg-secondary,#0f172a); border:1px solid var(--color-border,#334155); border-radius:8px; margin-bottom:1.25rem; font-size:0.85rem; line-height:1.7;">
            <strong>Project:</strong> <?php echo htmlspecialchars($project['project_name']); ?><br>
            <strong>Client:</strong> <?php echo htmlspecialchars($project['client_name'] ?? '—'); ?><br>
            <strong>Approved entries:</strong>
            <?php echo $approved_billable_count > 0
                ? $approved_billable_count . ' billable entr' . ($approved_billable_count === 1 ? 'y' : 'ies')
                : '<span style="color:#f59e0b;">None yet — CSV will be empty</span>'; ?>
        </div>
        <div style="display:flex; gap:0.75rem; justify-content:flex-end;">
            <button onclick="closeCsvPreflight()" class="btn btn-secondary">Cancel</button>
            <a href="/TimeForge_Capstone/api/export_csv.php?project_id=<?php echo $project['id']; ?>"
               class="btn btn-primary">⬇ Download CSV</a>
        </div>
    </div>
</div>

<script>
// ── Invoice pre-flight modal ────────────────────────────────────────────────
function openInvoicePreflight() {
    document.getElementById('invoicePreflightModal').style.display = 'flex';
}
function closeInvoicePreflight() {
    document.getElementById('invoicePreflightModal').style.display = 'none';
}

// Highlight the selected template card and update the continue link
function pfSelectTemplate(key) {
    document.querySelectorAll('.pf-tpl-card').forEach(function(card) {
        card.style.borderColor = 'var(--color-border, #334155)';
    });
    var chosen = document.querySelector('.pf-tpl-card[data-key="' + key + '"]');
    if (chosen) chosen.style.borderColor = '#3b82f6';

    var btn = document.getElementById('preflightContinueBtn');
    var base = '/TimeForge_Capstone/invoices/generate.php?project_id=<?php echo $project['id']; ?>';
    btn.href = base + '&tpl=' + key;
}

// Highlight classic on first open
document.addEventListener('DOMContentLoaded', function() {
    pfSelectTemplate('classic');
});

// Close modals when clicking the backdrop
document.getElementById('invoicePreflightModal').addEventListener('click', function(e) {
    if (e.target === this) closeInvoicePreflight();
});

// ── CSV pre-flight modal ────────────────────────────────────────────────────
function openCsvPreflight() {
    document.getElementById('csvPreflightModal').style.display = 'flex';
}
function closeCsvPreflight() {
    document.getElementById('csvPreflightModal').style.display = 'none';
}
document.getElementById('csvPreflightModal').addEventListener('click', function(e) {
    if (e.target === this) closeCsvPreflight();
});
</script>
</body>
</html>
