<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/db.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$page_title = 'Add Project';
$error_message = '';
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
            <h2 class="page-title">Add Project</h2>
            
            <form action="add_project_process.php" method="post" id="add_project_form">
                <div class="form-group">
                    <label>Project Name:</label>
                    <input type="text" name="project_name" required><br>
                </div>

                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" rows="4"></textarea><br>
                </div>

                <div class="form-group">
                    <label>Budget:</label>
                    <input type="number" name="budget" step="0.01"><br>
                </div>

                <div class="form-group">
                    <label>Deadline:</label>
                    <input type="date" name="deadline"><br>
                </div>

                <div class="form-group">
                    <label>Status:</label>
                    <select name="status">
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="archived">Archived</option>
                    </select><br>
                </div>

                <div class="form-group buttons">
                    <input type="submit" value="Add Project" class="btn btn-primary"><br>
                </div>
            </form>
            
            <p><a href="index.php">View Project List</a></p>
        </div>
    </main>

    <?php include_once __DIR__ . '/includes/footer_partial.php'; ?>
</body>
</html>
