<?php
/**
 * task_detail.php — Phase 12 (updated: clients can view + post feedback)
 * admin + freelancer + client can all read and post.
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

if (!$task_id || !$project_id) {
    header($role === 'client' ? 'Location: /TimeForge_Capstone/client/dashboard.php' : 'Location: index.php');
    exit;
}

if ($role === 'client') {
    $ts = $pdo->prepare("
        SELECT t.*, u.full_name AS assignee_name, p.project_name, p.company_id AS proj_company_id
        FROM tasks t
        LEFT JOIN users    u ON u.id = t.assigned_to
        LEFT JOIN projects p ON p.id = t.project_id
        INNER JOIN clients c ON c.id = p.client_id
        WHERE t.id = :tid AND t.project_id = :pid AND c.user_id = :uid
        LIMIT 1
    ");
    $ts->execute([':tid' => $task_id, ':pid' => $project_id, ':uid' => $user_id]);
    $task = $ts->fetch(PDO::FETCH_ASSOC);
    if (!$task) {
        setFlash('error', 'Task not found or access denied.');
        header('Location: /TimeForge_Capstone/client/dashboard.php'); exit;
    }
    $company_id = (int)$task['proj_company_id'];
} else {
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
    if ($role === 'freelancer' && $task['assigned_to'] && (int)$task['assigned_to'] !== $user_id) {
        header("Location: tasks.php?project_id={$project_id}"); exit;
    }
}

$cs = $pdo->prepare("
    SELECT tc.*, u.full_name, u.role AS poster_role
    FROM task_comments tc
    INNER JOIN users u ON u.id = tc.user_id
    WHERE tc.task_id = :tid AND tc.company_id = :cid
    ORDER BY tc.created_at ASC
");
$cs->execute([':tid' => $task_id, ':cid' => $company_id]);
$comments = $cs->fetchAll(PDO::FETCH_ASSOC);

$count_problems  = count(array_filter($comments, fn($c) => $c['type'] === 'problem'));
$count_solutions = count(array_filter($comments, fn($c) => $c['type'] === 'solution'));
$count_feedback  = count(array_filter($comments, fn($c) => $c['type'] === 'feedback'));

$back_url = $role === 'client'
    ? "/TimeForge_Capstone/client/project_report.php?id={$project_id}"
    : "tasks.php?project_id={$project_id}";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Task: <?= htmlspecialchars($task['title']) ?> — TimeForge</title>
  <?php include __DIR__ . '/includes/header_partial.php'; ?>
  <style>
    .td-wrap  { max-width:860px; margin:0 auto; padding:2rem 1rem 5rem; }
    .td-back  { color:var(--color-accent); font-size:.88rem; text-decoration:none; display:inline-block; margin-bottom:1rem; }
    .td-back:hover { text-decoration:underline; }
    .td-header { background:var(--color-card); border-radius:12px; padding:1.5rem 1.75rem; margin-bottom:1.25rem; border-left:4px solid var(--color-accent); }
    .td-header h1 { margin:0 0 .4rem; font-size:1.35rem; }
    .td-meta  { display:flex; flex-wrap:wrap; gap:.6rem 1.4rem; font-size:.84rem; color:var(--color-text-secondary); margin-top:.55rem; }
    .td-meta span strong { color:var(--color-text); }
    .priority-badge { display:inline-block; padding:.2rem .55rem; border-radius:4px; font-size:.72rem; font-weight:700; text-transform:uppercase; }
    .priority-badge.high   { background:#dc2626; color:#fff; }
    .priority-badge.medium { background:#d97706; color:#fff; }
    .priority-badge.low    { background:#059669; color:#fff; }
    .status-badge { display:inline-block; padding:.2rem .65rem; border-radius:4px; font-size:.75rem; font-weight:600; }
    .status-open        { background:#334155; color:#94a3b8; }
    .status-in_progress { background:#1d4ed8; color:#fff; }
    .status-done        { background:#15803d; color:#fff; }
    .td-summary { display:flex; gap:.6rem; margin-bottom:1.25rem; flex-wrap:wrap; }
    .sum-badge  { padding:.3rem .8rem; border-radius:20px; font-size:.78rem; font-weight:600; }
    .sum-total    { background:rgba(99,102,241,.15); color:#a5b4fc; border:1px solid rgba(99,102,241,.3); }
    .sum-problems { background:rgba(239,68,68,.15);  color:#f87171; border:1px solid rgba(239,68,68,.3); }
    .sum-solutions{ background:rgba(16,185,129,.15); color:#34d399; border:1px solid rgba(16,185,129,.3); }
    .sum-feedback { background:rgba(251,191,36,.15); color:#fcd34d; border:1px solid rgba(251,191,36,.3); }
    .td-thread { display:flex; flex-direction:column; gap:.8rem; margin-bottom:1.75rem; }
    .td-comment { border-radius:10px; padding:.9rem 1.2rem; }
    .bubble-admin      { background:rgba(99,102,241,.1);  border-left:3px solid #6366f1; }
    .bubble-freelancer { background:rgba(30,41,59,1);     border-left:3px solid #334155; }
    .bubble-client     { background:rgba(251,191,36,.07); border-left:3px solid #f59e0b; }
    .td-comment.type-problem  { border-left-color:#ef4444 !important; }
    .td-comment.type-solution { border-left-color:#10b981 !important; }
    .td-comment.type-feedback { border-left-color:#f59e0b !important; }
    .td-c-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:.35rem; }
    .td-c-who  { font-weight:600; font-size:.87rem; display:flex; align-items:center; gap:.4rem; }
    .role-pill  { font-size:.68rem; font-weight:700; padding:.1rem .45rem; border-radius:99px; text-transform:uppercase; }
    .pill-admin      { background:#6366f1; color:#fff; }
    .pill-freelancer { background:#334155; color:#94a3b8; }
    .pill-client     { background:#b45309; color:#fff; }
    .you-tag { font-size:.7rem; color:#6366f1; }
    .td-c-time { font-size:.73rem; color:#475569; }
    .td-type-tag { display:inline-block; padding:.13rem .48rem; border-radius:4px; font-size:.7rem; font-weight:700; text-transform:uppercase; margin-bottom:.3rem; }
    .tag-note     { background:rgba(99,102,241,.2); color:#a5b4fc; }
    .tag-problem  { background:rgba(239,68,68,.2);  color:#f87171; }
    .tag-solution { background:rgba(16,185,129,.2); color:#34d399; }
    .tag-feedback { background:rgba(251,191,36,.2); color:#fcd34d; }
    .td-c-body { font-size:.9rem; line-height:1.65; white-space:pre-wrap; word-break:break-word; }
    .td-post  { background:var(--color-card); border-radius:12px; padding:1.5rem 1.75rem; }
    .td-post h3 { margin:0 0 .9rem; font-size:1rem; }
    .type-selector { display:flex; gap:.55rem; margin-bottom:.9rem; flex-wrap:wrap; }
    .type-selector input[type=radio] { display:none; }
    .type-btn { display:inline-block; padding:.38rem .85rem; border-radius:20px; border:1px solid #334155; font-size:.8rem; font-weight:600; cursor:pointer; transition:.15s; }
    .type-selector input:checked + .type-btn.note-btn     { background:rgba(99,102,241,.25); border-color:#6366f1; color:#a5b4fc; }
    .type-selector input:checked + .type-btn.problem-btn  { background:rgba(239,68,68,.25);  border-color:#ef4444; color:#f87171; }
    .type-selector input:checked + .type-btn.solution-btn { background:rgba(16,185,129,.25); border-color:#10b981; color:#34d399; }
    .type-selector input:checked + .type-btn.feedback-btn { background:rgba(251,191,36,.25); border-color:#f59e0b; color:#fcd34d; }
    .td-post textarea { width:100%; min-height:100px; background:var(--color-bg); border:1px solid #334155; color:var(--color-text); border-radius:8px; padding:.7rem 1rem; font-size:.9rem; resize:vertical; box-sizing:border-box; line-height:1.55; font-family:inherit; }
    .td-post textarea:focus { outline:none; border-color:var(--color-accent); }
    .td-post-footer { display:flex; justify-content:space-between; align-items:center; margin-top:.65rem; flex-wrap:wrap; gap:.5rem; }
    .td-hint { font-size:.77rem; color:#475569; }
    .empty-thread { text-align:center; padding:2rem 1rem; color:#475569; font-size:.88rem; background:var(--color-card); border-radius:10px; margin-bottom:1.5rem; }
    .date-divider { text-align:center; font-size:.72rem; color:#475569; margin:.4rem 0; display:flex; align-items:center; gap:.5rem; }
    .date-divider::before,.date-divider::after { content:''; flex:1; height:1px; background:#1e293b; }
  </style>
</head>
<body>

<?php include __DIR__ . '/includes/flash.php'; ?>

<div class="td-wrap">
  <a href="<?= $back_url ?>" class="td-back">← Back<?= $role === 'client' ? ' to project' : ' to task board' ?></a>

  <div class="td-header">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:.5rem;">
      <h1><?= htmlspecialchars($task['title']) ?></h1>
      <div style="display:flex; gap:.5rem; align-items:center;">
        <span class="priority-badge <?= $task['priority'] ?>"><?= $task['priority'] ?></span>
        <span class="status-badge status-<?= $task['status'] ?>"><?= str_replace('_',' ', $task['status']) ?></span>
      </div>
    </div>
    <?php if ($task['description']): ?>
      <p style="margin:.6rem 0 0; color:var(--color-text-secondary); font-size:.88rem; line-height:1.6;">
        <?= nl2br(htmlspecialchars($task['description'])) ?>
      </p>
    <?php endif; ?>
    <div class="td-meta">
      <span>📁 <strong><?= htmlspecialchars($task['project_name']) ?></strong></span>
      <span>👤 <strong><?= $task['assignee_name'] ? htmlspecialchars($task['assignee_name']) : 'Unassigned' ?></strong></span>
      <?php if ($task['due_date']): ?><span>📅 Due <strong><?= date('M j, Y', strtotime($task['due_date'])) ?></strong></span><?php endif; ?>
      <?php if ($task['estimated_hours']): ?><span>⏱ <strong><?= $task['estimated_hours'] ?>h</strong> estimated</span><?php endif; ?>
    </div>
  </div>

  <div class="td-summary">
    <span class="sum-badge sum-total">💬 <?= count($comments) ?> comment<?= count($comments) !== 1 ? 's' : '' ?></span>
    <?php if ($count_problems):  ?><span class="sum-badge sum-problems">🐛 <?= $count_problems ?> problem<?= $count_problems  !== 1 ? 's' : '' ?></span><?php endif; ?>
    <?php if ($count_solutions): ?><span class="sum-badge sum-solutions">💡 <?= $count_solutions ?> solution<?= $count_solutions !== 1 ? 's' : '' ?></span><?php endif; ?>
    <?php if ($count_feedback):  ?><span class="sum-badge sum-feedback">⭐ <?= $count_feedback ?> client feedback</span><?php endif; ?>
  </div>

  <?php if (empty($comments)): ?>
    <div class="empty-thread">No comments yet. Be the first to post a note below.</div>
  <?php else: ?>
    <div class="td-thread">
      <?php
        $lastDate   = null;
        $typeLabels = ['note'=>'💬 Note','problem'=>'🐛 Problem','solution'=>'💡 Solution','feedback'=>'⭐ Feedback'];
        $tagClass   = ['note'=>'tag-note','problem'=>'tag-problem','solution'=>'tag-solution','feedback'=>'tag-feedback'];
        foreach ($comments as $c):
          $thisDate  = date('M j, Y', strtotime($c['created_at']));
          $isMe      = ((int)$c['user_id'] === $user_id);
          $bubbleCls = 'bubble-' . $c['poster_role'];
          $pillCls   = 'pill-'   . $c['poster_role'];
          $cardCls   = 'type-'   . $c['type'];
      ?>
        <?php if ($thisDate !== $lastDate): $lastDate = $thisDate; ?>
          <div class="date-divider"><?= $thisDate ?></div>
        <?php endif; ?>
        <div class="td-comment <?= $bubbleCls ?> <?= $cardCls ?>"
             style="<?= $isMe ? 'margin-left:2.5rem;' : 'margin-right:2.5rem;' ?>">
          <div class="td-c-header">
            <span class="td-c-who">
              <span class="role-pill <?= $pillCls ?>"><?= $c['poster_role'] ?></span>
              <?= htmlspecialchars($c['full_name']) ?>
              <?php if ($isMe): ?><span class="you-tag">(you)</span><?php endif; ?>
            </span>
            <span class="td-c-time"><?= date('g:i a', strtotime($c['created_at'])) ?></span>
          </div>
          <div><span class="td-type-tag <?= $tagClass[$c['type']] ?>"><?= $typeLabels[$c['type']] ?></span></div>
          <div class="td-c-body"><?= htmlspecialchars($c['body']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="td-post">
    <h3>
      <?php if ($role === 'client') echo '⭐ Post Feedback or a Note';
            elseif ($role === 'admin') echo '✏️ Reply or Add Note';
            else echo '✏️ Add a Comment'; ?>
    </h3>
    <form method="POST" action="/TimeForge_Capstone/task_comment.php">
      <input type="hidden" name="task_id"    value="<?= $task_id ?>">
      <input type="hidden" name="project_id" value="<?= $project_id ?>">
      <div class="type-selector">
        <?php if ($role === 'client'): ?>
          <label><input type="radio" name="type" value="note" checked><span class="type-btn note-btn">💬 Note</span></label>
          <label><input type="radio" name="type" value="feedback"><span class="type-btn feedback-btn">⭐ Feedback / Objection</span></label>
        <?php else: ?>
          <label><input type="radio" name="type" value="note" checked><span class="type-btn note-btn">💬 Note</span></label>
          <label><input type="radio" name="type" value="problem"><span class="type-btn problem-btn">🐛 Problem Found</span></label>
          <label><input type="radio" name="type" value="solution"><span class="type-btn solution-btn">💡 Solution / Suggestion</span></label>
        <?php endif; ?>
      </div>
      <textarea name="body" required maxlength="2000"
        placeholder="<?php
          if ($role === 'client')         echo 'Share your feedback, ask a question, or flag a concern…';
          elseif ($role === 'freelancer') echo 'Describe the note, problem you found, or your suggested fix…';
          else                            echo 'Add a reply, clarification, or instruction…';
        ?>"></textarea>
      <div class="td-post-footer">
        <span class="td-hint">
          <?php if ($role === 'client') echo '👁 Visible to you, the admin, and the assigned worker.';
                elseif ($role === 'freelancer') echo '👁 Visible to you, the admin, and the client.';
                else echo '👁 Visible to all participants on this task.'; ?>
        </span>
        <button type="submit" class="btn btn-primary" style="padding:.5rem 1.4rem;">Post</button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/includes/footer_partial.php'; ?>
</body>
</html>
