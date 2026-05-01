<?php
/**
 * tasks.php — Phase 11: Task Management
 * Admin/Freelancer task board for a specific project.
 * Kanban-style columns: Open → In Progress → Done
 */
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

requireLogin();

$company_id = (int)$_SESSION['company_id'];
$user_id    = (int)$_SESSION['user_id'];
$role       = $_SESSION['role'];

$project_id = filter_input(INPUT_GET, 'project_id', FILTER_VALIDATE_INT);
if (!$project_id) { header('Location: index.php'); exit; }

// Verify project belongs to this company (clients see their project too)
$proj_stmt = $pdo->prepare("SELECT id, project_name, status FROM projects WHERE id = :pid AND company_id = :cid LIMIT 1");
$proj_stmt->execute([':pid' => $project_id, ':cid' => $company_id]);
$project = $proj_stmt->fetch(PDO::FETCH_ASSOC);
if (!$project) { header('Location: index.php'); exit; }

// Freelancers only see tasks assigned to them or open tasks
$task_where = ($role === 'freelancer')
    ? "AND (t.assigned_to = :uid OR t.assigned_to IS NULL)"
    : "";
$task_params = [':pid' => $project_id, ':cid' => $company_id];
if ($role === 'freelancer') $task_params[':uid'] = $user_id;

$tasks_stmt = $pdo->prepare("
    SELECT t.*,
           u.full_name AS assignee_name,
           COALESCE(SUM(te.total_seconds),0) AS logged_seconds
    FROM tasks t
    LEFT JOIN users u        ON u.id = t.assigned_to
    LEFT JOIN time_entries te ON te.task_id = t.id AND te.end_time IS NOT NULL
    WHERE t.project_id = :pid AND t.company_id = :cid
    $task_where
    GROUP BY t.id
    ORDER BY FIELD(t.priority,'high','medium','low'), t.due_date ASC, t.id ASC
");
$tasks_stmt->execute($task_params);
$all_tasks = $tasks_stmt->fetchAll(PDO::FETCH_ASSOC);

// Group by status
$cols = ['open' => [], 'in_progress' => [], 'done' => []];
foreach ($all_tasks as $t) $cols[$t['status']][] = $t;

// Fetch team members for assign dropdown (admin only)
$members = [];
if ($role === 'admin') {
    $m_stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE company_id = :cid AND role IN ('freelancer','admin') AND is_active = 1 ORDER BY full_name");
    $m_stmt->execute([':cid' => $company_id]);
    $members = $m_stmt->fetchAll(PDO::FETCH_ASSOC);
}

$page_title = 'Tasks — ' . htmlspecialchars($project['project_name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $page_title ?> — TimeForge</title>
  <?php include __DIR__ . '/includes/header_partial.php'; ?>
  <style>
    .task-board { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-top: 1.5rem; }
    @media(max-width:768px){ .task-board { grid-template-columns: 1fr; } }
    .task-col { background: var(--color-card); border-radius: 10px; padding: 1rem; min-height: 300px; }
    .task-col-header { font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center; }
    .col-open .task-col-header        { color: #94a3b8; border-bottom: 2px solid #94a3b8; padding-bottom: .4rem; }
    .col-in_progress .task-col-header { color: #f59e0b; border-bottom: 2px solid #f59e0b; padding-bottom: .4rem; }
    .col-done .task-col-header        { color: #22c55e; border-bottom: 2px solid #22c55e; padding-bottom: .4rem; }
    .task-card { background: var(--color-bg); border-radius: 8px; padding: .85rem 1rem; margin-bottom: .75rem; border-left: 3px solid #334155; position: relative; }
    .task-card.priority-high   { border-left-color: #ef4444; }
    .task-card.priority-medium { border-left-color: #f59e0b; }
    .task-card.priority-low    { border-left-color: #22c55e; }
    .task-title { font-weight: 600; font-size: .9rem; margin-bottom: .35rem; }
    .task-meta  { font-size: .75rem; color: var(--color-text-secondary); }
    .task-meta span { margin-right: .6rem; }
    .task-progress { background: #1e293b; border-radius: 4px; height: 5px; margin-top: .5rem; overflow: hidden; }
    .task-progress-bar { height: 100%; background: #3b82f6; border-radius: 4px; transition: width .3s; }
    .task-actions { display: flex; gap: .4rem; margin-top: .6rem; flex-wrap: wrap; }
    .btn-xs { padding: .2rem .6rem; font-size: .72rem; border-radius: 4px; border: none; cursor: pointer; font-weight: 600; }
    .btn-move-prog { background: #f59e0b22; color: #f59e0b; border: 1px solid #f59e0b55; }
    .btn-move-done { background: #22c55e22; color: #22c55e; border: 1px solid #22c55e55; }
    .btn-move-open { background: #94a3b822; color: #94a3b8; border: 1px solid #94a3b855; }
    .btn-edit-task { background: #3b82f622; color: #3b82f6; border: 1px solid #3b82f655; }
    .btn-del-task  { background: #ef444422; color: #ef4444; border: 1px solid #ef444455; }
    .add-task-form { background: var(--color-bg); border-radius: 8px; padding: 1rem; margin-top: .75rem; border: 1px dashed #334155; }
    .add-task-form input, .add-task-form select, .add-task-form textarea { width: 100%; background: var(--color-card); border: 1px solid #334155; color: var(--color-text); border-radius: 5px; padding: .45rem .6rem; margin-bottom: .5rem; font-size: .85rem; }
    .priority-badge { display: inline-block; font-size: .65rem; font-weight: 700; padding: .1rem .4rem; border-radius: 3px; text-transform: uppercase; letter-spacing: .04em; }
    .priority-badge.high   { background: #ef444422; color: #ef4444; }
    .priority-badge.medium { background: #f59e0b22; color: #f59e0b; }
    .priority-badge.low    { background: #22c55e22; color: #22c55e; }
    .overdue { color: #ef4444 !important; }
    .badge-count { background: #334155; color: #94a3b8; border-radius: 99px; padding: .1rem .55rem; font-size: .75rem; }
  </style>
</head>
<body>
<?php include __DIR__ . '/includes/header_partial.php'; ?>
<div class="container" style="max-width:1200px; padding: 2rem 1rem;">

  <?php include __DIR__ . '/includes/flash.php'; ?>

  <!-- Header -->
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom:1.5rem;">
    <div>
      <h1 style="margin:0; color:var(--color-accent);">📋 Task Board</h1>
      <p style="margin:.3rem 0 0; color:var(--color-text-secondary);">
        Project: <strong><?= htmlspecialchars($project['project_name']) ?></strong>
      </p>
    </div>
    <div style="display:flex; gap:.75rem;">
      <a href="project_details.php?id=<?= $project_id ?>" class="btn btn-secondary">← Project Details</a>
      <?php if ($role === 'admin'): ?>
        <button class="btn btn-primary" onclick="toggleAddForm()">＋ Add Task</button>
      <?php endif; ?>
    </div>
  </div>

  <!-- Quick add form (admin only) -->
  <?php if ($role === 'admin'): ?>
  <div id="add-task-form" style="display:none;" class="card" style="margin-bottom:1.5rem;">
    <h3 style="margin-top:0; color:var(--color-accent);">New Task</h3>
    <form method="POST" action="task_action.php">
      <input type="hidden" name="action" value="create">
      <input type="hidden" name="project_id" value="<?= $project_id ?>">
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div>
          <label style="font-size:.82rem; color:var(--color-text-secondary);">Task Title *</label>
          <input type="text" name="title" required placeholder="e.g. Design homepage wireframes" style="width:100%; background:var(--color-card); border:1px solid #334155; color:var(--color-text); border-radius:6px; padding:.55rem .75rem; margin-top:.25rem;">
        </div>
        <div>
          <label style="font-size:.82rem; color:var(--color-text-secondary);">Assign To</label>
          <select name="assigned_to" style="width:100%; background:var(--color-card); border:1px solid #334155; color:var(--color-text); border-radius:6px; padding:.55rem .75rem; margin-top:.25rem;">
            <option value="">— Anyone —</option>
            <?php foreach ($members as $m): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['full_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label style="font-size:.82rem; color:var(--color-text-secondary);">Priority</label>
          <select name="priority" style="width:100%; background:var(--color-card); border:1px solid #334155; color:var(--color-text); border-radius:6px; padding:.55rem .75rem; margin-top:.25rem;">
            <option value="medium">Medium</option>
            <option value="high">High</option>
            <option value="low">Low</option>
          </select>
        </div>
        <div>
          <label style="font-size:.82rem; color:var(--color-text-secondary);">Estimated Hours</label>
          <input type="number" name="estimated_hours" min="0.5" max="999" step="0.5" placeholder="e.g. 8" style="width:100%; background:var(--color-card); border:1px solid #334155; color:var(--color-text); border-radius:6px; padding:.55rem .75rem; margin-top:.25rem;">
        </div>
        <div>
          <label style="font-size:.82rem; color:var(--color-text-secondary);">Due Date</label>
          <input type="date" name="due_date" style="width:100%; background:var(--color-card); border:1px solid #334155; color:var(--color-text); border-radius:6px; padding:.55rem .75rem; margin-top:.25rem;">
        </div>
        <div>
          <label style="font-size:.82rem; color:var(--color-text-secondary);">Description</label>
          <textarea name="description" rows="2" placeholder="Optional details…" style="width:100%; background:var(--color-card); border:1px solid #334155; color:var(--color-text); border-radius:6px; padding:.55rem .75rem; margin-top:.25rem; resize:vertical;"></textarea>
        </div>
      </div>
      <div style="margin-top:1rem; display:flex; gap:.75rem;">
        <button type="submit" class="btn btn-primary">Create Task</button>
        <button type="button" class="btn btn-secondary" onclick="toggleAddForm()">Cancel</button>
      </div>
    </form>
  </div>
  <?php endif; ?>

  <!-- Kanban Board -->
  <div class="task-board">
    <?php
    $col_labels = [
      'open'        => ['label' => '⬜ Open',       'css' => 'col-open'],
      'in_progress' => ['label' => '🔄 In Progress', 'css' => 'col-in_progress'],
      'done'        => ['label' => '✅ Done',        'css' => 'col-done'],
    ];
    foreach ($col_labels as $col_key => $col_info):
      $tasks = $cols[$col_key];
    ?>
    <div class="task-col <?= $col_info['css'] ?>">
      <div class="task-col-header">
        <span><?= $col_info['label'] ?></span>
        <span class="badge-count"><?= count($tasks) ?></span>
      </div>

      <?php foreach ($tasks as $t):
        $logged_h    = round($t['logged_seconds'] / 3600, 1);
        $est_h       = $t['estimated_hours'] ? (float)$t['estimated_hours'] : 0;
        $pct         = ($est_h > 0) ? min(100, round(($logged_h / $est_h) * 100)) : 0;
        $is_overdue  = $t['due_date'] && $t['status'] !== 'done' && strtotime($t['due_date']) < strtotime('today');
        $due_class   = $is_overdue ? 'overdue' : '';
      ?>
      <div class="task-card priority-<?= $t['priority'] ?>">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
          <div class="task-title"><?= htmlspecialchars($t['title']) ?></div>
          <span class="priority-badge <?= $t['priority'] ?>"><?= $t['priority'] ?></span>
        </div>
        <?php if ($t['description']): ?>
          <div class="task-meta" style="margin-bottom:.3rem;"><?= htmlspecialchars(mb_strimwidth($t['description'], 0, 80, '…')) ?></div>
        <?php endif; ?>
        <div class="task-meta">
          <?php if ($t['assignee_name']): ?>
            <span>👤 <?= htmlspecialchars($t['assignee_name']) ?></span>
          <?php else: ?>
            <span style="color:#64748b;">👤 Unassigned</span>
          <?php endif; ?>
          <?php if ($t['due_date']): ?>
            <span class="<?= $due_class ?>">📅 <?= date('M j', strtotime($t['due_date'])) ?><?= $is_overdue ? ' ⚠' : '' ?></span>
          <?php endif; ?>
          <?php if ($est_h > 0): ?>
            <span>⏱ <?= $logged_h ?>h / <?= $est_h ?>h</span>
          <?php elseif ($logged_h > 0): ?>
            <span>⏱ <?= $logged_h ?>h logged</span>
          <?php endif; ?>
        </div>
        <?php if ($est_h > 0): ?>
        <div class="task-progress">
          <div class="task-progress-bar" style="width:<?= $pct ?>%; background:<?= $pct >= 100 ? '#22c55e' : '#3b82f6' ?>;"></div>
        </div>
        <div class="task-meta" style="text-align:right; margin-top:.2rem;"><?= $pct ?>%</div>
        <?php endif; ?>

        <!-- Action buttons -->
        <div class="task-actions">
          <?php if ($col_key === 'open' && $role !== 'client'): ?>
            <form method="POST" action="task_action.php" style="display:inline;">
              <input type="hidden" name="action" value="move"><input type="hidden" name="task_id" value="<?= $t['id'] ?>">
              <input type="hidden" name="status" value="in_progress"><input type="hidden" name="project_id" value="<?= $project_id ?>">
              <button class="btn-xs btn-move-prog" type="submit">▶ Start</button>
            </form>
          <?php elseif ($col_key === 'in_progress' && $role !== 'client'): ?>
            <form method="POST" action="task_action.php" style="display:inline;">
              <input type="hidden" name="action" value="move"><input type="hidden" name="task_id" value="<?= $t['id'] ?>">
              <input type="hidden" name="status" value="done"><input type="hidden" name="project_id" value="<?= $project_id ?>">
              <button class="btn-xs btn-move-done" type="submit">✔ Done</button>
            </form>
            <form method="POST" action="task_action.php" style="display:inline;">
              <input type="hidden" name="action" value="move"><input type="hidden" name="task_id" value="<?= $t['id'] ?>">
              <input type="hidden" name="status" value="open"><input type="hidden" name="project_id" value="<?= $project_id ?>">
              <button class="btn-xs btn-move-open" type="submit">↩ Reopen</button>
            </form>
          <?php elseif ($col_key === 'done' && $role !== 'client'): ?>
            <form method="POST" action="task_action.php" style="display:inline;">
              <input type="hidden" name="action" value="move"><input type="hidden" name="task_id" value="<?= $t['id'] ?>">
              <input type="hidden" name="status" value="open"><input type="hidden" name="project_id" value="<?= $project_id ?>">
              <button class="btn-xs btn-move-open" type="submit">↩ Reopen</button>
            </form>
          <?php endif; ?>
          <?php if ($role === 'admin'): ?>
            <a href="edit_task.php?id=<?= $t['id'] ?>&project_id=<?= $project_id ?>" class="btn-xs btn-edit-task">✏ Edit</a>
            <form method="POST" action="task_action.php" style="display:inline;" onsubmit="return confirm('Delete this task?')">
              <input type="hidden" name="action" value="delete"><input type="hidden" name="task_id" value="<?= $t['id'] ?>">
              <input type="hidden" name="project_id" value="<?= $project_id ?>">
              <button class="btn-xs btn-del-task" type="submit">🗑</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>

      <?php if (empty($tasks)): ?>
        <p style="color:#475569; font-size:.82rem; text-align:center; padding:1rem 0;">No tasks here</p>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Summary bar -->
  <?php
    $total    = count($all_tasks);
    $done_cnt = count($cols['done']);
    $pct_done = $total > 0 ? round(($done_cnt / $total) * 100) : 0;
    $total_est = array_sum(array_column($all_tasks, 'estimated_hours'));
    $total_log = round(array_sum(array_column($all_tasks, 'logged_seconds')) / 3600, 1);
  ?>
  <?php if ($total > 0): ?>
  <div class="card" style="margin-top:2rem; display:flex; gap:2rem; flex-wrap:wrap; align-items:center;">
    <div>
      <div style="font-size:.75rem; color:var(--color-text-secondary); text-transform:uppercase; letter-spacing:.05em;">Overall Progress</div>
      <div style="font-size:1.6rem; font-weight:700; color:var(--color-accent);"><?= $pct_done ?>%</div>
      <div style="font-size:.8rem; color:var(--color-text-secondary);"><?= $done_cnt ?> / <?= $total ?> tasks done</div>
    </div>
    <div style="flex:1; min-width:200px;">
      <div style="background:#1e293b; border-radius:6px; height:10px; overflow:hidden;">
        <div style="width:<?= $pct_done ?>%; height:100%; background: linear-gradient(90deg,#3b82f6,#22c55e); border-radius:6px; transition:width .5s;"></div>
      </div>
    </div>
    <?php if ($total_est > 0): ?>
    <div style="text-align:right;">
      <div style="font-size:.75rem; color:var(--color-text-secondary); text-transform:uppercase; letter-spacing:.05em;">Time</div>
      <div style="font-size:1.2rem; font-weight:700;"><?= $total_log ?>h <span style="font-size:.8rem; color:var(--color-text-secondary);">/ <?= $total_est ?>h estimated</span></div>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

</div>

<?php include __DIR__ . '/includes/footer_partial.php'; ?>
<script src="/TimeForge_Capstone/js/time_tracker.js"></script>
<script>
function toggleAddForm() {
  const f = document.getElementById('add-task-form');
  f.style.display = f.style.display === 'none' ? 'block' : 'none';
}
</script>
</body>
</html>
