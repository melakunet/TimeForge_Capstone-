<?php
$page_title = 'My Profile';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

if (!isLoggedIn()) {
    header('Location: ' . APP_BASE . '/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();

    // ── Password change ──────────────────────────────────────────────────────
    if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
        $current_pw  = $_POST['current_password'] ?? '';
        $new_pw      = $_POST['new_password']      ?? '';
        $confirm_pw  = $_POST['confirm_password']  ?? '';

        $row = $pdo->prepare("SELECT password FROM users WHERE id = :id");
        $row->execute([':id' => $user_id]);
        $hash = $row->fetchColumn();

        $pw_errors = [];
        if (!password_verify($current_pw, $hash))          $pw_errors[] = 'Current password is incorrect.';
        if (strlen($new_pw) < 8)                           $pw_errors[] = 'New password must be at least 8 characters.';
        if (!preg_match('/[A-Z]/', $new_pw))               $pw_errors[] = 'New password must contain an uppercase letter.';
        if (!preg_match('/[a-z]/', $new_pw))               $pw_errors[] = 'New password must contain a lowercase letter.';
        if (!preg_match('/[0-9]/', $new_pw))               $pw_errors[] = 'New password must contain a number.';
        if ($new_pw !== $confirm_pw)                       $pw_errors[] = 'Passwords do not match.';

        if ($pw_errors) {
            setFlash('error', implode(' ', $pw_errors));
        } else {
            $upd = $pdo->prepare("UPDATE users SET password = :pw WHERE id = :id");
            $upd->execute([':pw' => password_hash($new_pw, PASSWORD_DEFAULT), ':id' => $user_id]);
            setFlash('success', 'Password changed successfully.');
        }
        header('Location: ' . APP_BASE . '/profile.php');
        exit;
    }
    $full_name        = trim(filter_input(INPUT_POST, 'full_name'));
    $company_name     = trim(filter_input(INPUT_POST, 'company_name'));
    $business_tagline = trim(filter_input(INPUT_POST, 'business_tagline'));
    $email            = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));

    if (!$full_name || !$email) {
        setFlash('error', 'Full name and email are required.');
    } else {
        // Handle logo upload
        $logo_path = null;
        $logo_error = null;

        if (!empty($_FILES['company_logo']['name'])) {
            $file     = $_FILES['company_logo'];
            $allowed  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
            $max_size = 2 * 1024 * 1024; // 2 MB

            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $mime     = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, $allowed)) {
                $logo_error = 'Logo must be a JPG, PNG, GIF, WebP, or SVG file.';
            } elseif ($file['size'] > $max_size) {
                $logo_error = 'Logo file must be under 2 MB.';
            } else {
                $ext       = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename  = $user_id . '_logo.' . strtolower($ext);
                $upload_dir = __DIR__ . '/images/logos/';
                // Ensure directory exists and is writable
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                if (!is_writable($upload_dir)) {
                    chmod($upload_dir, 0777);
                }
                $dest = $upload_dir . $filename;
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $logo_path = 'images/logos/' . $filename;
                } else {
                    $logo_error = 'Could not save logo. Ensure the images/logos folder is writable by the web server.';
                }
            }
        }

        // Handle logo removal
        if (isset($_POST['remove_logo']) && $_POST['remove_logo'] === '1') {
            $logo_path = '__REMOVE__';
        }

        if ($logo_error) {
            setFlash('error', $logo_error);
        } else {
            // Build update query — only change logo column if something happened
            if ($logo_path === '__REMOVE__') {
                $stmt = $pdo->prepare("
                    UPDATE users SET
                        full_name        = :full_name,
                        company_name     = :company_name,
                        business_tagline = :business_tagline,
                        email            = :email,
                        company_logo     = NULL
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':full_name'        => $full_name,
                    ':company_name'     => $company_name ?: null,
                    ':business_tagline' => $business_tagline ?: null,
                    ':email'            => $email,
                    ':id'               => $user_id,
                ]);
            } elseif ($logo_path) {
                $stmt = $pdo->prepare("
                    UPDATE users SET
                        full_name        = :full_name,
                        company_name     = :company_name,
                        business_tagline = :business_tagline,
                        email            = :email,
                        company_logo     = :logo
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':full_name'        => $full_name,
                    ':company_name'     => $company_name ?: null,
                    ':business_tagline' => $business_tagline ?: null,
                    ':email'            => $email,
                    ':logo'             => $logo_path,
                    ':id'               => $user_id,
                ]);
            } else {
                $stmt = $pdo->prepare("
                    UPDATE users SET
                        full_name        = :full_name,
                        company_name     = :company_name,
                        business_tagline = :business_tagline,
                        email            = :email
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':full_name'        => $full_name,
                    ':company_name'     => $company_name ?: null,
                    ':business_tagline' => $business_tagline ?: null,
                    ':email'            => $email,
                    ':id'               => $user_id,
                ]);
            }

            $_SESSION['full_name'] = $full_name;
            setFlash('success', 'Profile updated successfully.');
            header('Location: ' . APP_BASE . '/profile.php');
            exit;
        }
    }
}

// Load current user data
$stmt = $pdo->prepare("SELECT full_name, email, company_name, business_tagline, company_logo, role FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user  = $stmt->fetch();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> — TimeForge</title>
    <link rel="stylesheet" href="<?= APP_BASE ?>/css/style.css">
    <link rel="icon" type="image/png" href="<?= APP_BASE ?>/icons/logo.png">
</head>
<body>
<?php include __DIR__ . '/includes/header_partial.php'; ?>

<div class="container" style="max-width: 640px;">
    <h1 class="heading-serif" style="color:var(--color-accent); margin-bottom: 0.25rem;">My Profile</h1>
    <p style="color:var(--color-text-secondary); margin-bottom: 2rem;">
        Your name, company details, and logo appear on invoices you generate.
    </p>

    <?php if ($flash): ?>
        <div class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>" style="margin-bottom:1rem;">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>

    <div class="card" style="padding: 2rem;">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

            <div style="margin-bottom: 1.25rem;">
                <label style="display:block; font-weight:600; margin-bottom:0.4rem;">Full Name <span style="color:#dc2626;">*</span></label>
                <input type="text" name="full_name" required class="form-input"
                       value="<?php echo htmlspecialchars($user['full_name']); ?>"
                       style="width:100%;">
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display:block; font-weight:600; margin-bottom:0.4rem;">Email <span style="color:#dc2626;">*</span></label>
                <input type="email" name="email" required class="form-input"
                       value="<?php echo htmlspecialchars($user['email']); ?>"
                       style="width:100%;">
            </div>

            <hr style="border:none; border-top:1px solid var(--color-border); margin: 1.5rem 0;">
            <p style="font-size:0.85rem; color:var(--color-text-secondary); margin-bottom:1rem;">
                The fields below appear in the <strong>FROM</strong> section of any invoice you generate.
            </p>

            <div style="margin-bottom: 1.25rem;">
                <label style="display:block; font-weight:600; margin-bottom:0.4rem;">Company / Business Name</label>
                <input type="text" name="company_name" class="form-input"
                       placeholder="e.g. Melaku Digital Inc."
                       value="<?php echo htmlspecialchars($user['company_name'] ?? ''); ?>"
                       style="width:100%;">
                <small style="color:var(--color-text-secondary);">Leave blank to use your full name instead.</small>
            </div>

            <div style="margin-bottom: 1.75rem;">
                <label style="display:block; font-weight:600; margin-bottom:0.4rem;">Business Tagline</label>
                <input type="text" name="business_tagline" class="form-input"
                       placeholder="e.g. Web Design & Development"
                       value="<?php echo htmlspecialchars($user['business_tagline'] ?? ''); ?>"
                       style="width:100%;">
                <small style="color:var(--color-text-secondary);">One short line shown under your company name on the invoice.</small>
            </div>

            <!-- ── Company Logo ──────────────────────────────────── -->
            <hr style="border:none; border-top:1px solid var(--color-border); margin: 1.5rem 0;">

            <div style="margin-bottom: 1.75rem;">
                <label style="display:block; font-weight:600; margin-bottom:0.75rem;">Company Logo <span style="font-weight:400; color:var(--color-text-secondary);">(optional)</span></label>

                <?php if (!empty($user['company_logo']) && file_exists(__DIR__ . '/' . $user['company_logo'])): ?>
                <!-- Current logo preview -->
                <div id="logo-current" style="display:flex; align-items:center; gap:1rem; margin-bottom:1rem; padding:0.75rem 1rem; background:var(--color-bg-secondary, #f9fafb); border:1px solid var(--color-border); border-radius:8px;">
                    <img src="<?= APP_BASE ?>/<?php echo htmlspecialchars($user['company_logo']); ?>"
                         alt="Your logo" id="logo-preview-img"
                         style="max-height:60px; max-width:160px; object-fit:contain;">
                    <div style="flex:1;">
                        <div style="font-size:0.85rem; color:var(--color-text-secondary);">Current logo — appears on all your invoices.</div>
                        <label style="display:inline-flex; align-items:center; gap:0.4rem; margin-top:0.4rem; font-size:0.82rem; cursor:pointer; color:#dc2626;">
                            <input type="checkbox" name="remove_logo" value="1" onchange="document.getElementById('logo-current').style.opacity=this.checked?'0.4':'1'">
                            Remove logo
                        </label>
                    </div>
                </div>
                <?php else: ?>
                <!-- No logo yet — show placeholder -->
                <div id="logo-preview-box" style="display:none; margin-bottom:0.75rem; padding:0.75rem 1rem; background:var(--color-bg-secondary,#f9fafb); border:1px solid var(--color-border); border-radius:8px; text-align:center;">
                    <img id="logo-preview-img" src="" alt="Preview" style="max-height:60px; max-width:160px; object-fit:contain;">
                </div>
                <?php endif; ?>

                <input type="file" name="company_logo" id="logo-upload" accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml"
                       style="width:100%; padding:0.4rem 0;"
                       onchange="previewLogo(this)">
                <small style="color:var(--color-text-secondary);">
                    JPG, PNG, GIF, WebP or SVG &bull; max 2 MB &bull; shown top-left on invoices instead of the TimeForge logo.
                </small>
            </div>
            <!-- ── /Company Logo ─────────────────────────────────── -->

            <button type="submit" class="btn btn-primary" style="width:100%;">Save Profile</button>
        </form>
    </div>

    <p style="margin-top:1rem; font-size:0.85rem; color:var(--color-text-secondary); text-align:center;">
        Role: <strong><?php echo ucfirst($user['role']); ?></strong>
        &nbsp;&bull;&nbsp;
        <a href="<?= APP_BASE ?>/index.php">Back to Dashboard</a>
    </p>

    <!-- ── Change Password ─────────────────────────────────────────────────── -->
    <div class="card" style="padding: 2rem; margin-top: 2rem;">
        <h2 style="font-size:1.1rem; font-weight:700; margin-bottom:1.25rem;">Change Password</h2>
        <form method="POST" id="pw-form" novalidate>
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            <input type="hidden" name="action" value="change_password">

            <div style="margin-bottom:1.1rem;">
                <label style="display:block; font-weight:600; margin-bottom:.4rem;">Current Password</label>
                <input type="password" name="current_password" required class="form-input" style="width:100%;">
            </div>

            <div style="margin-bottom:1.1rem;">
                <label style="display:block; font-weight:600; margin-bottom:.4rem;">New Password</label>
                <input type="password" name="new_password" id="new_pw" required class="form-input" style="width:100%;" oninput="checkStrength(this.value)">
                <div id="strength-bar" style="height:4px; border-radius:2px; margin-top:.4rem; background:#e5e7eb; overflow:hidden;">
                    <div id="strength-fill" style="height:100%; width:0; transition:width .3s, background .3s;"></div>
                </div>
                <ul id="pw-rules" style="font-size:.78rem; margin:.5rem 0 0 1rem; color:var(--color-text-secondary); list-style:disc;">
                    <li id="rule-len">At least 8 characters</li>
                    <li id="rule-upper">One uppercase letter</li>
                    <li id="rule-lower">One lowercase letter</li>
                    <li id="rule-digit">One number</li>
                </ul>
            </div>

            <div style="margin-bottom:1.5rem;">
                <label style="display:block; font-weight:600; margin-bottom:.4rem;">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_pw" required class="form-input" style="width:100%;" oninput="checkMatch()">
                <small id="match-msg" style="font-size:.78rem;"></small>
            </div>

            <button type="submit" class="btn btn-primary" id="pw-submit" style="width:100%;" disabled>Update Password</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer_partial.php'; ?>
<script src="<?= APP_BASE ?>/js/theme.js"></script>
<script>
function previewLogo(input) {
    if (!input.files || !input.files[0]) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        var box = document.getElementById('logo-preview-box');
        var img = document.getElementById('logo-preview-img');
        if (box) { box.style.display = 'block'; }
        if (img) { img.src = e.target.result; }
    };
    reader.readAsDataURL(input.files[0]);
}

// ── Password strength ─────────────────────────────────────────────────────────
let pwValid = false;
function checkStrength(val) {
    const rules = {
        'rule-len':   val.length >= 8,
        'rule-upper': /[A-Z]/.test(val),
        'rule-lower': /[a-z]/.test(val),
        'rule-digit': /[0-9]/.test(val),
    };
    let score = Object.values(rules).filter(Boolean).length;
    Object.entries(rules).forEach(([id, ok]) => {
        const el = document.getElementById(id);
        if (el) el.style.color = ok ? '#16a34a' : '';
    });
    const fill  = document.getElementById('strength-fill');
    const colors = ['#dc2626','#f97316','#eab308','#16a34a'];
    if (fill) { fill.style.width = (score * 25) + '%'; fill.style.background = colors[score - 1] || '#e5e7eb'; }
    pwValid = (score === 4);
    updateSubmit();
}
function checkMatch() {
    const msg   = document.getElementById('match-msg');
    const match = document.getElementById('new_pw').value === document.getElementById('confirm_pw').value;
    if (msg) { msg.textContent = document.getElementById('confirm_pw').value ? (match ? '✓ Passwords match' : '✗ Passwords do not match') : ''; msg.style.color = match ? '#16a34a' : '#dc2626'; }
    updateSubmit();
}
function updateSubmit() {
    const match = document.getElementById('new_pw').value === document.getElementById('confirm_pw').value;
    const btn   = document.getElementById('pw-submit');
    if (btn) btn.disabled = !(pwValid && match && document.getElementById('confirm_pw').value);
}
</script>
</body>
</html>
