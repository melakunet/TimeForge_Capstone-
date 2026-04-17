<?php
/**
 * api/upload_screenshot.php — Phase 9
 * Receives a Base64 JPEG screenshot from the JS timer widget,
 * saves it to disk, and records a row in the screenshots table.
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

// ── Auth check ────────────────────────────────────────────────────────────────
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id    = (int)$_SESSION['user_id'];
$company_id = (int)$_SESSION['company_id'];

// ── Input ─────────────────────────────────────────────────────────────────────
$entry_id         = filter_input(INPUT_POST, 'entry_id',         FILTER_VALIDATE_INT);
$project_id       = filter_input(INPUT_POST, 'project_id',       FILTER_VALIDATE_INT);
$activity_score   = filter_input(INPUT_POST, 'activity_score',   FILTER_VALIDATE_INT) ?? 0;
$image_data       = $_POST['image'] ?? '';

if (!$entry_id || !$project_id || empty($image_data)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// ── Verify the entry belongs to this user and is still running ────────────────
$check = $pdo->prepare("
    SELECT id FROM time_entries
    WHERE id = :eid AND user_id = :uid AND project_id = :pid
    LIMIT 1
");
$check->execute([':eid' => $entry_id, ':uid' => $user_id, ':pid' => $project_id]);
if (!$check->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Invalid session entry']);
    exit;
}

// ── Max screenshot cap: 50 per entry ──────────────────────────────────────────
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM screenshots WHERE entry_id = :eid");
$count_stmt->execute([':eid' => $entry_id]);
if ((int)$count_stmt->fetchColumn() >= 50) {
    echo json_encode(['success' => false, 'message' => 'Screenshot limit reached for this session']);
    exit;
}

// ── Decode Base64 image ───────────────────────────────────────────────────────
// Strip the data URI prefix if present: data:image/jpeg;base64,<data>
$image_data = preg_replace('/^data:image\/\w+;base64,/', '', $image_data);
$image_data = str_replace(' ', '+', $image_data);
$decoded    = base64_decode($image_data, true);

if ($decoded === false || strlen($decoded) < 100) {
    echo json_encode(['success' => false, 'message' => 'Invalid image data']);
    exit;
}

// ── Build save path ───────────────────────────────────────────────────────────
$save_dir = __DIR__ . "/../uploads/screenshots/{$company_id}/{$user_id}/{$entry_id}";

if (!is_dir($save_dir)) {
    mkdir($save_dir, 0775, true);
}

$filename  = date('YmdHis') . '_' . mt_rand(100, 999) . '.jpg';
$full_path = $save_dir . '/' . $filename;
$rel_path  = "uploads/screenshots/{$company_id}/{$user_id}/{$entry_id}/{$filename}";

// ── Save file ─────────────────────────────────────────────────────────────────
if (file_put_contents($full_path, $decoded) === false) {
    error_log("upload_screenshot.php: failed to write to {$full_path}");
    echo json_encode(['success' => false, 'message' => 'Failed to save image']);
    exit;
}

$file_size_kb = (int)ceil(strlen($decoded) / 1024);

// ── Insert DB record ──────────────────────────────────────────────────────────
try {
    $stmt = $pdo->prepare("
        INSERT INTO screenshots
            (entry_id, user_id, project_id, company_id, file_path, file_size_kb, activity_score_at_capture, captured_at)
        VALUES
            (:eid, :uid, :pid, :cid, :path, :size, :score, NOW())
    ");
    $stmt->execute([
        ':eid'   => $entry_id,
        ':uid'   => $user_id,
        ':pid'   => $project_id,
        ':cid'   => $company_id,
        ':path'  => $rel_path,
        ':size'  => $file_size_kb,
        ':score' => $activity_score,
    ]);

    echo json_encode([
        'success' => true,
        'file'    => $rel_path,
        'size_kb' => $file_size_kb,
    ]);

} catch (PDOException $e) {
    error_log('upload_screenshot.php DB error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
