<?php
/**
 * edit_task.php — Phase 11: Edit an existing task (admin only)
 */
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

requireLogin();
if ($_SESSION['role'] !== 'admin') { header('Location: index.php'); exit; }

$company_id = (int)$_SESSION['company_id'];
$user_id    = (int)$_SESSION['user_id'];
$task_id    = filter_input(INPUT_GET, 'id',         FILTER_VALIDATE_INT);
$project_id = filter_input(INPUT_GET, 'project_id', FILTER_VALIDATE_INT);

if (!$task_id || !$project_id) { header('Location: index.php'); exit; }

// Load task
$ts = $pdo->prepare("SELECT * FROM tasks WHERE id = :tid AND company_id = :cid LIMIT 1");
$ts->execute([':tid' => $task_id, ':cid' => $company_id]);
$task = $ts->fetch(PDO::FETCH_ASSOC);
if (!$task) { header("Location: tasks.php?project_id={$project_id}"); exit; }

// Load project
$ps = $pdo->prepare("SELECT project_name FROM projects WHERE id = :pid AND company_id = :cid LIMIT 1");
$ps->execute([':pid' => $project_id, ':cid' => $company_id]);
$project = $ps->fetch(PDO::FETCH_ASSOC);
if (!$project) { header('Location: index.php'); exit; }

// Load team members
$ms = $pdo->prepare("SELECT id, full_name FROM users WHERE company_id = :cid AND role IN ('freelancer','admin') AND is_active = 1 ORDER BY full_name");
$ms->execute([':cid' => $company_id]);
$members = $ms->fetchAll(PDO::FETCH_ASSOC);

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $assigned_to = filter_input(INPUT_POST, 'assigned_to', FILTER_VALIDATE_INT) ?: null;
    $priority    = in_array($_POST['priority'] ?? '', ['low','medium','high']) ? $_POST['priority'] : 'medium';
    $status      = in_array($_POST['status']   ?? '', ['open','in_progress','done']) ? $_POST['status'] : 'open';
    $est_hours   = filter_input(INPUT_POST, 'estimated_hours', FILTER_VALIDATE_FLOAT) ?: null;
    $due_date    = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

    if (!$title) {
        setFlash('error', 'Title is required.');
    } else {
        $upd = $pdo->prepare("
            UPDATE tasks SET title=:title, description=:desc, assigned_to=:uid,
                            priority=:priority, status=:status, estimated_hours=:est,
                            due_date=:due, updated_at=NOW()
            WHERE id=:tid AND company_id=:cid
        ");
        $upd->execute([
            ':title'    => $title,
            ':desc'     => $description ?: null,
            ':uid'      => $assigned_to,
            ':priority' => $priority,
            ':status'   => $status,
            ':est'      => $est_hours,
            ':due'      => $due_date,
            ':tid'      => $task_id,
            ':cid'      => $company_id,
        ]);
        setFlash('success', 'Task updated.');
        header("Location: tasks.php?project_id={$project_id}");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Task — TimeForge</title>
  <?php include __DIR__ . '/includes/header_partial.php'; ?>
</head>
<body>
<div class="container" style="max-width:700px; padding:2rem 1rem;">
  <?php include __DIR__ . '/includes/flash.php'; ?>
  <a href="tasks.php?project_id=<?= $project_id ?>" style="color:var(--color-accent); font-size:.9rem;">← Back to task board</a>
  <h1 style="margin:.75rem 0 1.5rem; color:var(--color-accent);">✏️ Edit Task</h1>
  <p style="color:var(--color-text-secondary); margin-top:-1rem; margin-bottom:1.5rem;">
    Project: <strong><?= htmlspecialchars($project['project_name']) ?></strong>
  </p>

  <div class="card">
    <form method="POST">
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div style="grid-column:1/-1;">
          <label class="form-label">Task Title *</label>
          <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($task['title']) ?>">
        </div>
        <div>
          <label class="form-label">Assign To</label>
          <select name="assigned_to" class="form-control">
            <option value="">— Anyone —</option>
            <?php foreach ($members as $m): ?>
              <option value="<?= $m['id'] ?>" <?= $task['assigned_to'] == $m['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['full_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="form-label">Status</label>
          <select name="status" class="form-control">
            <?php foreach (['open'=>'Open','in_progress'=>'In Progress','done'=>'Done'] as $v=>$l): ?>
              <option value="<?= $v ?>" <?= $task['status'] === $v ? 'selected' : '' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="form-label">Priority</label>
          <select name="priority" class="form-control">
            <?php foreach (['high'=>'High','medium'=>'Medium','low'=>'Low'] as $v=>$l): ?>
              <option value="<?= $v ?>" <?= $task['priority'] === $v ? 'selected' : '' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="form-label">Estimated Hours</label>
          <input type="number" name="estimated_hours" class="form-control" min="0.5" max="999" step="0.5" value="<?= htmlspecialchars($task['estimated_hours'] ?? '') ?>">
        </div>
        <div>
          <label class="form-label">Due Date</label>
          <input type="date" name="due_date" class="form-control" value="<?= htmlspecialchars($task['due_date'] ?? '') ?>">
        </div>
        <div style="grid-column:1/-1;">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
        </div>
      </div>
      <div style="margin-top:1.5rem; display:flex; gap:.75rem;">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="tasks.php?project_id=<?= $project_id ?>" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php include __DIR__ . '/includes/footer_partial.php'; ?>
</body>
</html>
