<?php
/**
 * task_comment.php — Phase 12: Task Comment / Problem Report handler
 * All roles (admin, freelancer, client) can post.
 * Clients can only post on tasks belonging to their own projects.
 * Client comment types are limited to: note, feedback (mapped from 'solution' picker).
 */
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

requireLogin();

$company_id = (int)$_SESSION['company_id'];
$user_id    = (int)$_SESSION['user_id'];
$role       = $_SESSION['role'];

$task_id    = filter_input(INPUT_POST, 'task_id',    FILTER_VALIDATE_INT);
$project_id = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);
$type       = in_array($_POST['type'] ?? '', ['note','problem','solution','feedback']) ? $_POST['type'] : 'note';
$body       = trim($_POST['body'] ?? '');

// Clients can only post 'note' or 'feedback'
if ($role === 'client' && !in_array($type, ['note','feedback'])) {
    $type = 'note';
}

$back_url = "task_detail.php?id={$task_id}&project_id={$project_id}";

if (!$task_id || !$project_id || $body === '') {
    setFlash('error', 'Comment cannot be empty.');
    header("Location: {$back_url}");
    exit;
}

// For clients: verify the task's project belongs to them via clients table
if ($role === 'client') {
    $chk = $pdo->prepare("
        SELECT t.id FROM tasks t
        INNER JOIN projects p ON p.id = t.project_id
        INNER JOIN clients  c ON c.id = p.client_id
        WHERE t.id = :tid AND c.user_id = :uid
        LIMIT 1
    ");
    $chk->execute([':tid' => $task_id, ':uid' => $user_id]);
    if (!$chk->fetch()) {
        setFlash('error', 'Access denied.');
        header('Location: /TimeForge_Capstone/client/dashboard.php');
        exit;
    }
    // Use the task's company_id for insertion
    $cid_row = $pdo->prepare("SELECT company_id FROM tasks WHERE id = :tid LIMIT 1");
    $cid_row->execute([':tid' => $task_id]);
    $company_id = (int)($cid_row->fetchColumn() ?: $company_id);
} else {
    // Admin / freelancer: task must belong to their company
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
header("Location: {$back_url}");
exit;

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
