<?php
// admin/users.php — Manage Users (list + toggle active + change role + invite)

$page_title = 'Manage Users';

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/mailer.php';

requireRole('admin');

$company_id  = (int)$_SESSION['company_id'];
$current_uid = (int)$_SESSION['user_id'];

// ── Handle POST actions ───────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle') {
        $target_id = (int)($_POST['user_id'] ?? 0);
        if ($target_id && $target_id !== $current_uid) {
            try {
                $pdo->prepare("UPDATE users SET is_active = 1 - is_active WHERE id = ? AND company_id = ?")->execute([$target_id, $company_id]);
                setFlash('success', 'User status updated.');
            } catch (PDOException $e) { setFlash('error', 'Could not update user status.'); }
        } else { setFlash('error', 'Cannot deactivate your own account.'); }
    }

    if ($action === 'role') {
        $target_id = (int)($_POST['user_id'] ?? 0);
        $new_role  = $_POST['new_role'] ?? '';
        if ($target_id && in_array($new_role, ['admin','freelancer','client'], true) && $target_id !== $current_uid) {
            try {
                $pdo->prepare("UPDATE users SET role = ? WHERE id = ? AND company_id = ?")->execute([$new_role, $target_id, $company_id]);
                setFlash('success', 'Role updated.');
            } catch (PDOException $e) { setFlash('error', 'Could not update role.'); }
        } else { setFlash('error', 'Invalid role change.'); }
    }

    if ($action === 'invite') {
        $inv_email    = trim(filter_input(INPUT_POST, 'inv_email', FILTER_VALIDATE_EMAIL) ?? '');
        $inv_name     = trim($_POST['inv_name']     ?? '');
        $inv_username = trim($_POST['inv_username'] ?? '');
        $inv_role     = in_array($_POST['inv_role'] ?? '', ['admin','freelancer','client']) ? $_POST['inv_role'] : 'freelancer';

        if (!$inv_email || !$inv_name || !$inv_username) {
            setFlash('error', 'All invite fields are required.');
        } else {
            $chk = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1");
            $chk->execute([$inv_email, $inv_username]);
            if ($chk->fetch()) {
                setFlash('error', 'Email or username already taken.');
            } else {
                $temp_pass = bin2hex(random_bytes(8));
                try {
                    $co = $pdo->prepare("SELECT display_name FROM company_settings WHERE company_id = ? LIMIT 1");
                    $co->execute([$company_id]);
                    $company_name = $co->fetchColumn() ?: 'your company';

                    $pdo->prepare("INSERT INTO users (username,email,password,role,full_name,is_active,company_id,created_at,updated_at) VALUES (?,?,?,?,?,1,?,NOW(),NOW())")
                        ->execute([$inv_username, $inv_email, hashPassword($temp_pass), $inv_role, $inv_name, $company_id]);

                    $reset_link = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . ' . APP_BASE . '/forgot_password.php';
                    $html = "<div style='font-family:sans-serif;max-width:520px;margin:auto;'><h2 style='color:#3b82f6;'>Welcome to TimeForge</h2><p>Hi <strong>" . htmlspecialchars($inv_name) . "</strong>,</p><p>You have been invited to join <strong>" . htmlspecialchars($company_name) . "</strong> as a <strong>" . ucfirst($inv_role) . "</strong>.</p><p>Username: <strong>" . htmlspecialchars($inv_username) . "</strong><br>Temporary password: <strong>" . htmlspecialchars($temp_pass) . "</strong></p><p><a href='" . htmlspecialchars($reset_link) . "' style='background:#3b82f6;color:#fff;padding:.75rem 2rem;border-radius:6px;text-decoration:none;font-weight:700;'>Set Your Own Password</a></p><p style='color:#94a3b8;font-size:.8rem;'>Please change your password on first login.</p></div>";
                    sendEmail($inv_email, $inv_name, 'You have been invited to TimeForge', $html);
                    setFlash('success', "Invitation sent to {$inv_email}.");
                } catch (PDOException $e) {
                    error_log('users.php invite: ' . $e->getMessage());
                    setFlash('error', 'Could not create user.');
                }
            }
        }
    }

    header('Location: ' . APP_BASE . '/admin/users.php'); exit;
}

// ── Fetch users ───────────────────────────────────────────────────────────────
try {
    $stmt = $pdo->prepare("SELECT id,username,email,full_name,role,is_active,last_login,created_at FROM users WHERE company_id=? ORDER BY created_at DESC");
    $stmt->execute([$company_id]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { die('Error: ' . $e->getMessage()); }

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> – TimeForge</title>
    <link rel="stylesheet" href="<?= APP_BASE ?>/css/style.css">
    <link rel="icon" type="image/png" href="<?= APP_BASE ?>/icons/logo.png">
    <style>
        .role-badge{padding:.25rem .55rem;border-radius:4px;font-size:.8rem;font-weight:600;text-transform:uppercase}
        .role-admin{background:#dbeafe;color:#1d4ed8}.role-freelancer{background:#dcfce7;color:#15803d}.role-client{background:#ffedd5;color:#c2410c}
        .dot{display:inline-block;width:9px;height:9px;border-radius:50%;margin-right:5px}
        .dot-on{background:#22c55e}.dot-off{background:#ef4444}
        .users-table{width:100%;border-collapse:collapse}
        .users-table th,.users-table td{padding:.75rem 1rem;text-align:left;border-bottom:1px solid var(--color-border,#e5e7eb)}
        .users-table th{font-size:.8rem;text-transform:uppercase;letter-spacing:.04em;color:var(--color-text-secondary,#6b7280)}
        .invite-card{border:1px solid var(--color-border,#e5e7eb);border-radius:10px;padding:1.5rem;margin-top:2rem}
        .invite-grid{display:grid;grid-template-columns:1fr 1fr 1fr 1fr auto;gap:.75rem;align-items:end}
        @media(max-width:900px){.invite-grid{grid-template-columns:1fr 1fr}}
    </style>
</head>
<body>
<?php include_once __DIR__ . '/../includes/header_partial.php'; ?>
<div class="container" style="padding-top:2rem;padding-bottom:3rem;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <h1 style="margin:0;">Manage Users</h1>
        <a href="<?= APP_BASE ?>/admin/dashboard.php" class="btn btn-secondary btn-sm">← Dashboard</a>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>" style="margin-bottom:1rem;"><?= htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>

    <div class="card">
        <div style="overflow-x:auto;">
            <table class="users-table">
                <thead><tr>
                    <th>#</th><th>User</th><th>Role</th><th>Status</th><th>Last Login</th><th>Joined</th><th>Actions</th>
                </tr></thead>
                <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td style="color:var(--color-text-secondary,#6b7280);font-size:.85rem;"><?= $u['id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($u['full_name']) ?></strong><br>
                        <span style="font-size:.85rem;color:var(--color-text-secondary,#6b7280);">@<?= htmlspecialchars($u['username']) ?> · <?= htmlspecialchars($u['email']) ?></span>
                    </td>
                    <td>
                        <?php if ($u['id'] !== $current_uid): ?>
                        <form method="POST" style="display:inline-flex;gap:.3rem;align-items:center;">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <input type="hidden" name="action"  value="role">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <select name="new_role" class="form-control" style="padding:.25rem .5rem;font-size:.85rem;height:auto;">
                                <option value="admin"      <?= $u['role']==='admin'      ?'selected':'' ?>>Admin</option>
                                <option value="freelancer" <?= $u['role']==='freelancer' ?'selected':'' ?>>Freelancer</option>
                                <option value="client"     <?= $u['role']==='client'     ?'selected':'' ?>>Client</option>
                            </select>
                            <button type="submit" class="btn btn-secondary btn-sm" style="padding:.2rem .6rem;">✓</button>
                        </form>
                        <?php else: ?>
                            <span class="role-badge role-<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span>
                            <span style="font-size:.75rem;color:var(--color-text-secondary,#6b7280);">(you)</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="dot <?= $u['is_active']?'dot-on':'dot-off' ?>"></span><?= $u['is_active']?'Active':'Disabled' ?></td>
                    <td style="font-size:.85rem;"><?= $u['last_login'] ? date('M j, Y g:ia', strtotime($u['last_login'])) : '—' ?></td>
                    <td style="font-size:.85rem;"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <?php if ($u['id'] !== $current_uid): ?>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('<?= $u['is_active']?'Deactivate':'Reactivate' ?> this user?')">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <input type="hidden" name="action"  value="toggle">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <button type="submit" class="btn btn-sm <?= $u['is_active']?'btn-danger':'btn-success' ?>">
                                <?= $u['is_active']?'Deactivate':'Reactivate' ?>
                            </button>
                        </form>
                        <?php else: ?><span style="color:var(--color-text-secondary,#6b7280);font-size:.8rem;">—</span><?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Invite New User -->
    <div class="invite-card">
        <h3 style="margin-top:0;margin-bottom:1rem;">Invite New User</h3>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            <input type="hidden" name="action" value="invite">
            <div class="invite-grid">
                <div><label style="font-size:.8rem;color:var(--color-text-secondary,#6b7280);">Full Name *</label><input type="text"  name="inv_name"     class="form-control" required placeholder="Jane Doe"></div>
                <div><label style="font-size:.8rem;color:var(--color-text-secondary,#6b7280);">Username *</label><input type="text"  name="inv_username" class="form-control" required placeholder="janedoe"></div>
                <div><label style="font-size:.8rem;color:var(--color-text-secondary,#6b7280);">Email *</label><input type="email" name="inv_email"    class="form-control" required placeholder="jane@company.com"></div>
                <div><label style="font-size:.8rem;color:var(--color-text-secondary,#6b7280);">Role *</label>
                    <select name="inv_role" class="form-control">
                        <option value="freelancer">Freelancer</option>
                        <option value="client">Client</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div><button type="submit" class="btn btn-primary">Send Invite</button></div>
            </div>
            <p style="margin:.75rem 0 0;font-size:.8rem;color:var(--color-text-secondary,#6b7280);">A welcome email with a temporary password will be sent. The user should reset it via Forgot Password on first login.</p>
        </form>
    </div>

</div>
<?php include_once __DIR__ . '/../includes/footer_partial.php'; ?>
<script src="<?= APP_BASE ?>/js/theme.js"></script>
</body>
</html>

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('admin');

// Fetch users belonging to the same company
try {
    $stmt = $pdo->prepare("SELECT id, username, email, full_name, role, is_active, last_login, created_at FROM users WHERE company_id = :company_id ORDER BY created_at DESC");
    $stmt->execute([':company_id' => $_SESSION['company_id']]);
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
    <link rel="stylesheet" href="<?= APP_BASE ?>/css/style.css">
    <link rel="icon" type="image/png" href="<?= APP_BASE ?>/icons/logo.png">
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
            <a href="<?= APP_BASE ?>/admin/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
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
    <script src="<?= APP_BASE ?>/js/theme.js"></script>
</body>
</html>
