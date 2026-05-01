<?php
/**
 * api/project_tasks.php
 * Returns open/in_progress tasks for a project that the current user can work on.
 * Used by the timer-start modal to populate the task dropdown.
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['tasks' => []]);
    exit;
}

$project_id = filter_input(INPUT_GET, 'project_id', FILTER_VALIDATE_INT);
$company_id = (int)$_SESSION['company_id'];
$user_id    = (int)$_SESSION['user_id'];
$role       = $_SESSION['role'] ?? '';

if (!$project_id) {
    echo json_encode(['tasks' => []]);
    exit;
}

// Verify project belongs to this company
$proj = $pdo->prepare("SELECT id FROM projects WHERE id = :pid AND company_id = :cid LIMIT 1");
$proj->execute([':pid' => $project_id, ':cid' => $company_id]);
if (!$proj->fetch()) {
    echo json_encode(['tasks' => []]);
    exit;
}

// Freelancers only see tasks assigned to them or unassigned open tasks
$extra = ($role === 'freelancer')
    ? "AND (t.assigned_to = :uid OR t.assigned_to IS NULL)"
    : "";
$params = [':pid' => $project_id, ':cid' => $company_id];
if ($role === 'freelancer') $params[':uid'] = $user_id;

$stmt = $pdo->prepare("
    SELECT t.id, t.title, t.priority, t.status, t.estimated_hours,
           u.full_name AS assignee_name
    FROM tasks t
    LEFT JOIN users u ON u.id = t.assigned_to
    WHERE t.project_id = :pid
      AND t.company_id = :cid
      AND t.status IN ('open', 'in_progress')
      $extra
    ORDER BY FIELD(t.priority,'high','medium','low'), t.due_date ASC, t.id ASC
");
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['tasks' => $tasks]);
