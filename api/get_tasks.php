<?php
/**
 * api/get_tasks.php — Phase 11: return tasks for a project (for timer task picker)
 * Returns JSON array of open/in_progress tasks for the logged-in user's company.
 */
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');
if (!isLoggedIn()) { echo json_encode([]); exit; }

$company_id = (int)$_SESSION['company_id'];
$user_id    = (int)$_SESSION['user_id'];
$role       = $_SESSION['role'];
$project_id = filter_input(INPUT_GET, 'project_id', FILTER_VALIDATE_INT);

if (!$project_id) { echo json_encode([]); exit; }

// Verify project belongs to this company
$ps = $pdo->prepare("SELECT id FROM projects WHERE id = :pid AND company_id = :cid LIMIT 1");
$ps->execute([':pid' => $project_id, ':cid' => $company_id]);
if (!$ps->fetch()) { echo json_encode([]); exit; }

$extra = ($role === 'freelancer')
    ? "AND (t.assigned_to = {$user_id} OR t.assigned_to IS NULL)"
    : '';

$stmt = $pdo->prepare("
    SELECT t.id, t.title, t.status, t.priority
    FROM tasks t
    WHERE t.project_id = :pid AND t.company_id = :cid
      AND t.status IN ('open','in_progress')
      {$extra}
    ORDER BY FIELD(t.status,'in_progress','open'), FIELD(t.priority,'high','medium','low'), t.title ASC
");
$stmt->execute([':pid' => $project_id, ':cid' => $company_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
