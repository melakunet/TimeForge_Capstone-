<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

requireLogin();

$role = $_SESSION['role'] ?? null;
if (!in_array($role, ['admin', 'freelancer'], true)) {
    include __DIR__ . '/includes/403.php';
    exit();
}

$project_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$project_id) {
    header('Location: index.php');
    exit();
}

// Fetch active clients for dropdown — scoped to this company
$clients_stmt = $pdo->prepare("SELECT id, client_name, company_name FROM clients WHERE is_active = 1 AND company_id = :company_id ORDER BY client_name");
$clients_stmt->execute([':company_id' => $_SESSION['company_id']]);
$clients = $clients_stmt->fetchAll();

// Fetch project
$stmt = $pdo->prepare('SELECT * FROM projects WHERE id = :id AND deleted_at IS NULL LIMIT 1');
$stmt->bindValue(':id', $project_id, PDO::PARAM_INT);
$stmt->execute();
$project = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();

if (!$project) {
    http_response_code(404);
    die('Project not found');
}

$page_title = 'Edit Project';
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - TimeForge</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="icons/logo.png">
</head>
<body>
    <?php include_once __DIR__ . '/includes/header_partial.php'; ?>

    <main class="container">
        <div class="card form-card">
            <h2 class="page-title">Edit Project</h2>

            <?php if (!empty($flash) && !empty($flash['message'])): ?>
                <div class="alert alert-<?php echo htmlspecialchars($flash['type'] ?? 'info'); ?>"><?php echo htmlspecialchars($flash['message']); ?></div>
            <?php endif; ?>

            <form action="edit_project_process.php" method="post">
                <input type="hidden" name="project_id" value="<?php echo (int)$project['id']; ?>">

                <div class="form-group">
                    <label>Project Name:</label>
                    <input type="text" name="project_name" required value="<?php echo htmlspecialchars($project['project_name']); ?>">
                </div>

                <div class="form-group">
                    <label>Client:</label>
                    <select name="client_id" required>
                        <option value="">-- Select Client --</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?php echo (int)$client['id']; ?>" <?php echo ((int)$project['client_id'] === (int)$client['id']) ? 'selected' : ''; ?> >
                                <?php
                                    echo htmlspecialchars($client['client_name']);
                                    if (!empty($client['company_name'])) {
                                        echo ' (' . htmlspecialchars($client['company_name']) . ')';
                                    }
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Hourly Rate ($):</label>
                    <input type="number" name="hourly_rate" step="0.01" min="0" required value="<?php echo htmlspecialchars((string)$project['hourly_rate']); ?>">
                </div>

                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" rows="4"><?php echo htmlspecialchars($project['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Budget:</label>
                    <input type="number" name="budget" step="0.01" min="0" value="<?php echo htmlspecialchars((string)($project['budget'] ?? '')); ?>">
                </div>

                <div class="form-group">
                    <label>Deadline:</label>
                    <input type="date" name="deadline" value="<?php echo htmlspecialchars((string)($project['deadline'] ?? '')); ?>">
                </div>

                <div class="form-group">
                    <label>Status:</label>
                    <select name="status" required>
                        <?php
                            $statuses = ['active' => 'Active', 'completed' => 'Completed', 'archived' => 'Archived'];
                            foreach ($statuses as $value => $label) {
                                $selected = ($project['status'] === $value) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($value) . '" ' . $selected . '>' . htmlspecialchars($label) . '</option>';
                            }
                        ?>
                    </select>
                </div>

                <div class="form-group buttons">
                    <input type="submit" value="Save Changes" class="btn btn-primary">
                    <a href="index.php" class="btn btn-secondary" style="margin-left:8px;">Cancel</a>
                </div>
            </form>
        </div>
    </main>

    <?php include_once __DIR__ . '/includes/footer_partial.php'; ?>
</body>
</html>
