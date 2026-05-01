<?php
/**
 * task_comment.php — Phase 12: Task Comment / Problem Report handler
 * Freelancers and admins can post notes, flag problems, or suggest solutions.
 * Clients have no access.
 */
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

requireLogin();

$company_id = (int)$_SESSION['company_id'];
$user_id    = (int)$_SESSION['user_id'];
$role       = $_SESSION['role'];

// Clients cannot post comments
if ($role === 'client') {
    setFlash('error', 'Not authorised.');
    header('Location: index.php');
    exit;
}

$task_id    = filter_input(INPUT_POST, 'task_id',    FILTER_VALIDATE_INT);
$project_id = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);
$type       = in_array($_POST['type'] ?? '', ['note','problem','solution']) ? $_POST['type'] : 'note';
$body       = trim($_POST['body'] ?? '');

if (!$task_id || !$project_id || $body === '') {
    setFlash('error', 'Comment cannot be empty.');
    header("Location: task_detail.php?id={$task_id}&project_id={$project_id}");
    exit;
}

// Verify task belongs to this company
$chk = $pdo->prepare("SELECT id, assigned_to FROM tasks WHERE id = :tid AND company_id = :cid LIMIT 1");
$chk->execute([':tid' => $task_id, ':cid' => $company_id]);
$task = $chk->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    setFlash('error', 'Task not found.');
    header("Location: tasks.php?project_id={$project_id}");
    exit;
}

// Freelancer can only comment on tasks assigned to them or unassigned
if ($role === 'freelancer' && $task['assigned_to'] && (int)$task['assigned_to'] !== $user_id) {
    setFlash('error', 'You can only comment on tasks assigned to you.');
    header("Location: tasks.php?project_id={$project_id}");
    exit;
}

$ins = $pdo->prepare("
    INSERT INTO task_comments (task_id, company_id, user_id, type, body)
    VALUES (:tid, :cid, :uid, :type, :body)
");
$ins->execute([
    ':tid'  => $task_id,
    ':cid'  => $company_id,
    ':uid'  => $user_id,
    ':type' => $type,
    ':body' => $body,
]);

setFlash('success', 'Comment posted.');
header("Location: task_detail.php?id={$task_id}&project_id={$project_id}");
exit;
