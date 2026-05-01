<?php
/**
 * task_detail.php — Phase 12
 * Full view of a single task: details + comment thread.
 * Freelancers: read task info, post notes/problems/solutions.
 * Admins: same + see who reported what.
 */
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

requireLogin();

$company_id = (int)$_SESSION['company_id'];
$user_id    = (int)$_SESSION['user_id'];
$role       = $_SESSION['role'];

$task_id    = filter_input(INPUT_GET, 'id',         FILTER_VALIDATE_INT);
$project_id = filter_input(INPUT_GET, 'project_id', FILTER_VALIDATE_INT);

if (!$task_id || !$project_id) { header('Location: index.php'); exit; }

// Load task + assignee name
$ts = $pdo->prepare("
    SELECT t.*, u.full_name AS assignee_name, p.project_name
    FROM tasks t
    LEFT JOIN users    u ON u.id = t.assigned_to
    LEFT JOIN projects p ON p.id = t.project_id
    WHERE t.id = :tid AND t.company_id = :cid
    LIMIT 1
");
$ts->execute([':tid' => $task_id, ':cid' => $company_id]);
$task = $ts->fetch(PDO::FETCH_ASSOC);
if (!$task) { header("Location: tasks.php?project_id={$project_id}"); exit; }

// Freelancer: only assigned or unassigned tasks
if ($role === 'freelancer' && $task['assigned_to'] && (int)$task['assigned_to'] !== $user_id) {
    header("Location: tasks.php?project_id={$project_id}"); exit;
}

// Load comments newest-first
$cs = $pdo->prepare("
    SELECT tc.*, u.full_name, u.role AS poster_role
    FROM task_comments tc
    INNER JOIN users u ON u.id = tc.user_id
    WHERE tc.task_id = :tid AND tc.company_id = :cid
    ORDER BY tc.created_at ASC
");
$cs->execute([':tid' => $task_id, ':cid' => $company_id]);
$comments = $cs->fetchAll(PDO::FETCH_ASSOC);

// Count by type for the summary badges
$count_problems  = count(array_filter($comments, fn($c) => $c['type'] === 'problem'));
$count_solutions = count(array_filter($comments, fn($c) => $c['type'] === 'solution'));

$page_title = 'Task: ' . htmlspecialchars($task['title']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $page_title ?> — TimeForge</title>
  <?php include __DIR__ . '/includes/header_partial.php'; ?>
  <style>
    /* ── Layout ── */
    .td-wrap   { max-width: 820px; margin: 0 auto; padding: 2rem 1rem 4rem; }
    .td-back   { color: var(--color-accent); font-size: .88rem; text-decoration: none; display:inline-block; margin-bottom:1rem; }
    .td-back:hover { text-decoration: underline; }

    /* ── Task header card ── */
    .td-header { background: var(--color-card); border-radius: 12px; padding: 1.5rem 1.75rem; margin-bottom: 1.5rem; border-left: 4px solid var(--color-accent); }
    .td-header h1 { margin: 0 0 .4rem; font-size: 1.4rem; }
    .td-meta   { display: flex; flex-wrap: wrap; gap: .75rem 1.5rem; font-size: .85rem; color: var(--color-text-secondary); margin-top: .6rem; }
    .td-meta span strong { color: var(--color-text); }
    .priority-badge { display:inline-block; padding:.2rem .55rem; border-radius:4px; font-size:.72rem; font-weight:700; text-transform:uppercase; }
    .priority-badge.high   { background:#dc2626; color:#fff; }
    .priority-badge.medium { background:#d97706; color:#fff; }
    .priority-badge.low    { background:#059669; color:#fff; }
    .status-badge { display:inline-block; padding:.2rem .6rem; border-radius:4px; font-size:.75rem; font-weight:600; }
    .status-open        { background:#334155; color:#94a3b8; }
    .status-in_progress { background:#1d4ed8; color:#fff; }
    .status-done        { background:#15803d; color:#fff; }

    /* ── Summary badges ── */
    .td-summary { display:flex; gap:.75rem; margin-bottom:1.25rem; }
    .td-sum-badge { padding:.35rem .85rem; border-radius:20px; font-size:.8rem; font-weight:600; }
    .sum-problems  { background:rgba(239,68,68,.15);  color:#f87171; border:1px solid rgba(239,68,68,.3); }
    .sum-solutions { background:rgba(16,185,129,.15); color:#34d399; border:1px solid rgba(16,185,129,.3); }
    .sum-notes     { background:rgba(99,102,241,.15); color:#a5b4fc; border:1px solid rgba(99,102,241,.3); }

    /* ── Comment thread ── */
    .td-thread { display: flex; flex-direction: column; gap: .85rem; margin-bottom: 2rem; }
    .td-comment { background: var(--color-card); border-radius: 10px; padding: 1rem 1.25rem; border-left: 3px solid #334155; }
    .td-comment.type-problem  { border-left-color: #ef4444; }
    .td-comment.type-solution { border-left-color: #10b981; }
    .td-comment.type-note     { border-left-color: #6366f1; }
    .td-c-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:.5rem; }
    .td-c-who  { font-weight: 600; font-size: .88rem; }
    .td-c-who .role-tag { font-size:.72rem; font-weight:400; color:#64748b; margin-left:4px; }
    .td-c-time { font-size: .75rem; color: #475569; }
    .td-type-tag { display:inline-block; padding:.15rem .5rem; border-radius:4px; font-size:.72rem; font-weight:700; text-transform:uppercase; margin-bottom:.4rem; }
    .tag-problem  { background:rgba(239,68,68,.2);  color:#f87171; }
    .tag-solution { background:rgba(16,185,129,.2); color:#34d399; }
    .tag-note     { background:rgba(99,102,241,.2); color:#a5b4fc; }
    .td-c-body { font-size: .9rem; line-height: 1.6; white-space: pre-wrap; word-break: break-word; }

    /* ── Post form ── */
    .td-post { background: var(--color-card); border-radius: 12px; padding: 1.5rem 1.75rem; }
    .td-post h3 { margin: 0 0 1rem; font-size: 1rem; color: var(--color-accent); }
    .type-selector { display:flex; gap:.6rem; margin-bottom:1rem; flex-wrap:wrap; }
    .type-selector label { cursor:pointer; }
    .type-selector input[type=radio] { display:none; }
    .type-btn { display:inline-block; padding:.4rem .9rem; border-radius:20px; border:1px solid #334155; font-size:.82rem; font-weight:600; transition:.15s; user-select:none; }
    .type-selector input:checked + .type-btn.note-btn     { background:rgba(99,102,241,.25); border-color:#6366f1; color:#a5b4fc; }
    .type-selector input:checked + .type-btn.problem-btn  { background:rgba(239,68,68,.25);  border-color:#ef4444; color:#f87171; }
    .type-selector input:checked + .type-btn.solution-btn { background:rgba(16,185,129,.25); border-color:#10b981; color:#34d399; }
    .td-post textarea { width:100%; min-height:110px; background:var(--color-bg); border:1px solid #334155; color:var(--color-text); border-radius:8px; padding:.75rem 1rem; font-size:.9rem; resize:vertical; box-sizing:border-box; line-height:1.5; }
    .td-post textarea:focus { outline:none; border-color:var(--color-accent); }
    .td-post-footer { display:flex; justify-content:space-between; align-items:center; margin-top:.75rem; flex-wrap:wrap; gap:.5rem; }
    .td-hint { font-size:.78rem; color:#475569; }
    .empty-thread { text-align:center; padding:2rem; color:#475569; font-size:.9rem; background:var(--color-card); border-radius:10px; }
  </style>
</head>
<body>

<?php include __DIR__ . '/includes/flash.php'; ?>

<div class="td-wrap">
  <a href="tasks.php?project_id=<?= $project_id ?>" class="td-back">← Back to task board</a>

  <!-- Task header -->
  <div class="td-header">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:.5rem;">
      <h1><?= htmlspecialchars($task['title']) ?></h1>
      <div style="display:flex; gap:.5rem; align-items:center;">
        <span class="priority-badge <?= $task['priority'] ?>"><?= $task['priority'] ?></span>
        <span class="status-badge status-<?= $task['status'] ?>"><?= str_replace('_',' ', $task['status']) ?></span>
      </div>
    </div>
    <?php if ($task['description']): ?>
      <p style="margin:.6rem 0 0; color:var(--color-text-secondary); font-size:.9rem; line-height:1.6;">
        <?= nl2br(htmlspecialchars($task['description'])) ?>
      </p>
    <?php endif; ?>
    <div class="td-meta">
      <span>📁 <strong><?= htmlspecialchars($task['project_name']) ?></strong></span>
      <?php if ($task['assignee_name']): ?>
        <span>👤 <strong><?= htmlspecialchars($task['assignee_name']) ?></strong></span>
      <?php else: ?>
        <span>👤 <strong>Unassigned</strong></span>
      <?php endif; ?>
      <?php if ($task['due_date']): ?>
        <span>📅 Due <strong><?= date('M j, Y', strtotime($task['due_date'])) ?></strong></span>
      <?php endif; ?>
      <?php if ($task['estimated_hours']): ?>
        <span>⏱ <strong><?= $task['estimated_hours'] ?>h</strong> estimated</span>
      <?php endif; ?>
    </div>
  </div>

  <!-- Summary badges -->
  <?php $total = count($comments); ?>
  <div class="td-summary">
    <span class="td-sum-badge sum-notes">💬 <?= $total ?> comment<?= $total !== 1 ? 's' : '' ?></span>
    <?php if ($count_problems): ?>
      <span class="td-sum-badge sum-problems">🐛 <?= $count_problems ?> problem<?= $count_problems !== 1 ? 's' : '' ?> reported</span>
    <?php endif; ?>
    <?php if ($count_solutions): ?>
      <span class="td-sum-badge sum-solutions">💡 <?= $count_solutions ?> solution<?= $count_solutions !== 1 ? 's' : '' ?></span>
    <?php endif; ?>
  </div>

  <!-- Comment thread -->
  <?php if (empty($comments)): ?>
    <div class="empty-thread">
      No comments yet. Use the form below to post a note, report a problem, or suggest a solution.
    </div>
  <?php else: ?>
    <div class="td-thread">
      <?php foreach ($comments as $c):
        $typeLabels = ['note' => '💬 Note', 'problem' => '🐛 Problem', 'solution' => '💡 Solution'];
        $tagClass   = ['note' => 'tag-note',  'problem' => 'tag-problem',  'solution' => 'tag-solution'];
        $cardClass  = ['note' => 'type-note', 'problem' => 'type-problem', 'solution' => 'type-solution'];
        $isMe       = ((int)$c['user_id'] === $user_id);
      ?>
      <div class="td-comment <?= $cardClass[$c['type']] ?>" <?= $isMe ? 'style="margin-left:2rem;"' : '' ?>>
        <div class="td-c-header">
          <span class="td-c-who">
            <?= htmlspecialchars($c['full_name']) ?>
            <span class="role-tag">[<?= $c['poster_role'] ?>]</span>
            <?php if ($isMe): ?><span style="font-size:.72rem; color:#6366f1; margin-left:4px;">(you)</span><?php endif; ?>
          </span>
          <span class="td-c-time"><?= date('M j, Y g:i a', strtotime($c['created_at'])) ?></span>
        </div>
        <span class="td-type-tag <?= $tagClass[$c['type']] ?>"><?= $typeLabels[$c['type']] ?></span>
        <div class="td-c-body"><?= htmlspecialchars($c['body']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- Post a comment (not available to clients) -->
  <?php if ($role !== 'client'): ?>
  <div class="td-post">
    <h3>✏️ Add a Comment</h3>
    <form method="POST" action="task_comment.php">
      <input type="hidden" name="task_id"    value="<?= $task_id ?>">
      <input type="hidden" name="project_id" value="<?= $project_id ?>">

      <!-- Type selector -->
      <div class="type-selector">
        <label>
          <input type="radio" name="type" value="note" checked>
          <span class="type-btn note-btn">💬 Note</span>
        </label>
        <label>
          <input type="radio" name="type" value="problem">
          <span class="type-btn problem-btn">🐛 Problem Found</span>
        </label>
        <label>
          <input type="radio" name="type" value="solution">
          <span class="type-btn solution-btn">💡 Solution / Suggestion</span>
        </label>
      </div>

      <textarea name="body" placeholder="<?= $role === 'freelancer'
        ? 'Describe the note, problem you found, or your suggested solution…'
        : 'Add a note or reply to a comment…' ?>" required maxlength="2000"></textarea>

      <div class="td-post-footer">
        <span class="td-hint">
          <?php if ($role === 'freelancer'): ?>
            🔒 Only you and the admin can see this.
          <?php else: ?>
            👁 Visible to you and the assigned freelancer.
          <?php endif; ?>
        </span>
        <button type="submit" class="btn btn-primary" style="padding:.5rem 1.4rem;">Post Comment</button>
      </div>
    </form>
  </div>
  <?php endif; ?>

</div>

<?php include __DIR__ . '/includes/footer_partial.php'; ?>
</body>
</html>
