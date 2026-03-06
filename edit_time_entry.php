<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$entry_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$entry_id) {
    setFlash('error', 'Invalid time entry ID.');
    header("Location: index.php");
    exit;
}

// Fetch entry
$query = "
    SELECT te.*, p.project_name 
    FROM time_entries te
    JOIN projects p ON te.project_id = p.id
    WHERE te.id = :id
";
$stmt = $pdo->prepare($query);
$stmt->execute([':id' => $entry_id]);
$entry = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$entry) {
    setFlash('error', 'Entry not found.');
    header("Location: index.php");
    exit;
}

// Permissions Check
$can_edit = false;
if (hasRole('admin')) {
    $can_edit = true;
} elseif (hasRole('freelancer') && $entry['user_id'] == $_SESSION['user_id'] && $entry['status'] === 'pending') {
    $can_edit = true;
}

if (!$can_edit) {
    setFlash('error', 'Unauthorized to edit this entry.');
    header("Location: project_details.php?id=" . $entry['project_id']);
    exit;
}

// Processing Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = trim($_POST['description'] ?? '');
    $start_date = $_POST['start_date'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $duration_hours = filter_input(INPUT_POST, 'duration_hours', FILTER_VALIDATE_FLOAT);

    if (empty($description) || empty($start_date) || empty($start_time) || !$duration_hours) {
        setFlash('error', 'All fields are required.');
    } else {
        // Recalculate times
        $start_ts = date('Y-m-d H:i:s', strtotime("$start_date $start_time"));
        $minutes = round($duration_hours * 60);
        $end_ts = date('Y-m-d H:i:s', strtotime("$start_ts + $minutes minutes"));
        $total_seconds = $minutes * 60;

        try {
            $updateSql = "UPDATE time_entries 
                          SET description = :desc, 
                              start_time = :start, 
                              end_time = :end,
                              total_seconds = :seconds,
                              updated_at = NOW()
                          WHERE id = :id";
            $uStmt = $pdo->prepare($updateSql);
            $uStmt->execute([
                ':desc' => $description,
                ':start' => $start_ts,
                ':end' => $end_ts,
                ':seconds' => $total_seconds,
                ':id' => $entry_id
            ]);
            
            setFlash('success', 'Time entry updated.');
            header("Location: project_details.php?id=" . $entry['project_id']);
            exit;
        } catch (PDOException $e) {
            error_log("Edit Time Entry Error: " . $e->getMessage());
            setFlash('error', 'Database error.');
        }
    }
}

// Pre-fill form data
$start_date_val = date('Y-m-d', strtotime($entry['start_time']));
$start_time_val = date('H:i', strtotime($entry['start_time']));
$duration_val = ($entry['total_seconds'] / 3600); // Convert seconds to hours
if ($duration_val == 0 && $entry['end_time']) {
    // Fallback if total_seconds wasn't set correctly before
    $duration_val = (strtotime($entry['end_time']) - strtotime($entry['start_time'])) / 3600;
}
$duration_val = round($duration_val, 2);

$page_title = 'Edit Time Entry';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($page_title); ?> - TimeForge</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="icons/logo.png">
</head>
<body>
    <?php include_once __DIR__ . '/includes/header_partial.php'; ?>
    
    <main class="container">
        <div class="auth-wrapper" style="max-width: 600px; margin: 40px auto;">
            <div class="card">
                <div class="card-header">
                    <h2>Edit Time Entry</h2>
                    <p class="text-label"><?php echo htmlspecialchars($entry['project_name']); ?></p>
                </div>
                <div class="card-body">
                    <?php 
                    $flash = getFlash();
                    if (!empty($flash['message'])): ?>
                        <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>">
                            <?php echo htmlspecialchars($flash['message']); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="start_date" required value="<?php echo $start_date_val; ?>" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label>Start Time</label>
                            <input type="time" name="start_time" required value="<?php echo $start_time_val; ?>" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label>Duration (Hours)</label>
                            <input type="number" name="duration_hours" step="0.25" min="0.25" required value="<?php echo $duration_val; ?>" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" required class="form-control" rows="3"><?php echo htmlspecialchars($entry['description']); ?></textarea>
                        </div>
                        
                        <div class="form-actions" style="justify-content: space-between;">
                            <a href="project_details.php?id=<?php echo $entry['project_id']; ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <?php include_once __DIR__ . '/includes/footer_partial.php'; ?>
</body>
</html>
