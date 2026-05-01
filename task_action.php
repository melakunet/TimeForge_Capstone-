<?php
/**
 * task_action.php — Phase 11: Task CRUD handler
 * Handles: create, move (status), delete
 */
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

requireLogin();

$company_id = (int)$_SESSION['company_id'];
$user_id    = (int)$_SESSION['user_id'];
$role       = $_SESSION['role'];

$action     = $_POST['action'] ?? '';
$project_id = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);
$task_id    = filter_input(INPUT_POST, 'task_id',    FILTER_VALIDATE_INT);

// Helper: verify task belongs to this company
function getTask(PDO $pdo, int $task_id, int $company_id): ?array {
    $s = $pdo->prepare("SELECT * FROM tasks WHERE id = :tid AND company_id = :cid LIMIT 1");
    $s->execute([':tid' => $task_id, ':cid' => $company_id]);
    return $s->fetch(PDO::FETCH_ASSOC) ?: null;
}

$redirect = $project_id ? "tasks.php?project_id={$project_id}" : "index.php";

switch ($action) {

    /* ─── CREATE ──────────────────────────────────────────────── */
    case 'create':
        if ($role !== 'admin') { setFlash('error', 'Not authorized.'); header("Location: $redirect"); exit; }
        if (!$project_id)      { setFlash('error', 'Invalid project.'); header("Location: $redirect"); exit; }

        // Verify project belongs to company
        $ps = $pdo->prepare("SELECT id FROM projects WHERE id = :pid AND company_id = :cid LIMIT 1");
        $ps->execute([':pid' => $project_id, ':cid' => $company_id]);
        if (!$ps->fetch()) { setFlash('error', 'Project not found.'); header("Location: $redirect"); exit; }

        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $assigned_to = filter_input(INPUT_POST, 'assigned_to', FILTER_VALIDATE_INT) ?: null;
        $priority    = in_array($_POST['priority'] ?? '', ['low','medium','high']) ? $_POST['priority'] : 'medium';
        $est_hours   = filter_input(INPUT_POST, 'estimated_hours', FILTER_VALIDATE_FLOAT) ?: null;
        $due_date    = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

        if (!$title) { setFlash('error', 'Task title is required.'); header("Location: $redirect"); exit; }

        $ins = $pdo->prepare("
            INSERT INTO tasks (project_id, company_id, assigned_to, title, description, status, priority, estimated_hours, due_date, created_by)
            VALUES (:pid, :cid, :uid, :title, :desc, 'open', :priority, :est, :due, :creator)
        ");
        $ins->execute([
            ':pid'     => $project_id,
            ':cid'     => $company_id,
            ':uid'     => $assigned_to,
            ':title'   => $title,
            ':desc'    => $description ?: null,
            ':priority'=> $priority,
            ':est'     => $est_hours,
            ':due'     => $due_date,
            ':creator' => $user_id,
        ]);
        setFlash('success', 'Task created successfully.');
        header("Location: $redirect");
        exit;

    /* ─── MOVE (status change) ────────────────────────────────── */
    case 'move':
        if ($role === 'client') { setFlash('error', 'Not authorized.'); header("Location: $redirect"); exit; }

        $new_status = in_array($_POST['status'] ?? '', ['open','in_progress','done']) ? $_POST['status'] : null;
        if (!$task_id || !$new_status) { setFlash('error', 'Invalid request.'); header("Location: $redirect"); exit; }

        $task = getTask($pdo, $task_id, $company_id);
        if (!$task) { setFlash('error', 'Task not found.'); header("Location: $redirect"); exit; }

        // Freelancer can only move tasks assigned to them or unassigned
        if ($role === 'freelancer' && $task['assigned_to'] && $task['assigned_to'] != $user_id) {
            setFlash('error', 'You can only update tasks assigned to you.');
            header("Location: $redirect"); exit;
        }

        $pdo->prepare("UPDATE tasks SET status = :s, updated_at = NOW() WHERE id = :id AND company_id = :cid")
            ->execute([':s' => $new_status, ':id' => $task_id, ':cid' => $company_id]);
        setFlash('success', 'Task status updated.');
        header("Location: $redirect");
        exit;

    /* ─── DELETE ──────────────────────────────────────────────── */
    case 'delete':
        if ($role !== 'admin') { setFlash('error', 'Not authorized.'); header("Location: $redirect"); exit; }
        if (!$task_id) { setFlash('error', 'Invalid task.'); header("Location: $redirect"); exit; }

        $task = getTask($pdo, $task_id, $company_id);
        if (!$task) { setFlash('error', 'Task not found.'); header("Location: $redirect"); exit; }

        // Nullify time entry references first (FK is ON DELETE SET NULL but let's be explicit)
        $pdo->prepare("UPDATE time_entries SET task_id = NULL WHERE task_id = :tid")->execute([':tid' => $task_id]);
        $pdo->prepare("DELETE FROM tasks WHERE id = :tid AND company_id = :cid")->execute([':tid' => $task_id, ':cid' => $company_id]);
        setFlash('success', 'Task deleted.');
        header("Location: $redirect");
        exit;

    default:
        header("Location: $redirect");
        exit;
}
