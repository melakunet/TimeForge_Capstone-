<?php
/**
 * api/screenshot_img.php — Secure image proxy for Phase 9 screenshots
 *
 * Streams a screenshot JPEG to the browser after verifying:
 *  1. User is logged in
 *  2. User is admin
 *  3. The requested screenshot belongs to their company
 *
 * Usage: /TimeForge_Capstone/api/screenshot_img.php?id=9
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn() || !hasRole('admin')) {
    http_response_code(403);
    exit;
}

$id         = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$company_id = (int)$_SESSION['company_id'];

if (!$id) {
    http_response_code(400);
    exit;
}

// Verify the screenshot belongs to this admin's company
$stmt = $pdo->prepare("SELECT file_path FROM screenshots WHERE id = :id AND company_id = :cid LIMIT 1");
$stmt->execute([':id' => $id, ':cid' => $company_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    http_response_code(404);
    exit;
}

$full_path = __DIR__ . '/../' . $row['file_path'];

if (!file_exists($full_path) || !is_readable($full_path)) {
    http_response_code(404);
    exit;
}

// Stream the image
header('Content-Type: image/jpeg');
header('Content-Length: ' . filesize($full_path));
header('Cache-Control: private, max-age=3600');
readfile($full_path);
