<?php
// admin/users.php
// Manage Users - List all users

$page_title = 'Manage Users';

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('admin');

// Fetch all users
try {
    $stmt = $pdo->query("SELECT id, username, email, full_name, role, is_active, last_login, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - TimeForge</title>
    <link rel="stylesheet" href="/TimeForge_Capstone/css/style.css">
    <link rel="icon" type="image/png" href="/TimeForge_Capstone/icons/logo.png">
    <style>
        .role-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        .role-admin { background-color: #e3f2fd; color: #0d47a1; }
        .role-freelancer { background-color: #e8f5e9; color: #1b5e20; }
        .role-client { background-color: #fff3e0; color: #e65100; }
        
        .status-badge {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .status-active { background-color: #4caf50; }
        .status-inactive { background-color: #f44336; }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../includes/header_partial.php'; ?>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1>Manage Users</h1>
            <a href="/TimeForge_Capstone/admin/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <div class="card">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #eee; text-align: left;">
                            <th style="padding: 1rem;">ID</th>
                            <th style="padding: 1rem;">User</th>
                            <th style="padding: 1rem;">Role</th>
                            <th style="padding: 1rem;">Status</th>
                            <th style="padding: 1rem;">Last Login</th>
                            <th style="padding: 1rem;">Joined</th>
                            <th style="padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 1rem;">#<?= $user['id'] ?></td>
                            <td style="padding: 1rem;">
                                <strong><?= htmlspecialchars($user['full_name']) ?></strong><br>
                                <span style="font-size: 0.9em; color: #666;"><?= htmlspecialchars($user['email']) ?></span>
                            </td>
                            <td style="padding: 1rem;">
                                <span class="role-badge role-<?= $user['role'] ?>"><?= ucfirst($user['role']) ?></span>
                            </td>
                            <td style="padding: 1rem;">
                                <span class="status-badge <?= $user['is_active'] ? 'status-active' : 'status-inactive' ?>"></span>
                                <?= $user['is_active'] ? 'Active' : 'Disabled' ?>
                            </td>
                            <td style="padding: 1rem;">
                                <?= $user['last_login'] ? date('M j, Y g:ia', strtotime($user['last_login'])) : 'Never' ?>
                            </td>
                            <td style="padding: 1rem;">
                                <?= date('M j, Y', strtotime($user['created_at'])) ?>
                            </td>
                            <td style="padding: 1rem;">
                                <button class="btn btn-secondary btn-sm" disabled>Edit</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/../includes/footer_partial.php'; ?>
    <script src="/TimeForge_Capstone/js/theme.js"></script>
</body>
</html>
