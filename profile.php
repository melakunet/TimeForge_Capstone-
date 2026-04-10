<?php
$page_title = 'My Profile';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

if (!isLoggedIn()) {
    header('Location: /TimeForge_Capstone/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                $dest      = __DIR__ . '/images/logos/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $logo_path = 'images/logos/' . $filename;
                } else {
                    $logo_error = 'Could not save logo. Check folder permissions.';
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
            header('Location: /TimeForge_Capstone/profile.php');
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
    <link rel="stylesheet" href="/TimeForge_Capstone/css/style.css">
    <link rel="icon" type="image/png" href="/TimeForge_Capstone/icons/logo.png">
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
                    <img src="/TimeForge_Capstone/<?php echo htmlspecialchars($user['company_logo']); ?>"
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
        <a href="/TimeForge_Capstone/index.php">Back to Dashboard</a>
    </p>
</div>

<?php include __DIR__ . '/includes/footer_partial.php'; ?>
<script src="/TimeForge_Capstone/js/theme.js"></script>
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
</script>
</body>
</html>


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name        = trim(filter_input(INPUT_POST, 'full_name'));
    $company_name     = trim(filter_input(INPUT_POST, 'company_name'));
    $business_tagline = trim(filter_input(INPUT_POST, 'business_tagline'));
    $email            = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));

    if (!$full_name || !$email) {
        setFlash('error', 'Full name and email are required.');
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
        $_SESSION['full_name'] = $full_name;
        setFlash('success', 'Profile updated successfully.');
        header('Location: /TimeForge_Capstone/profile.php');
        exit;
    }
}

// Load current user data
$stmt = $pdo->prepare("SELECT full_name, email, company_name, business_tagline, role FROM users WHERE id = :id");
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
    <link rel="stylesheet" href="/TimeForge_Capstone/css/style.css">
    <link rel="icon" type="image/png" href="/TimeForge_Capstone/icons/logo.png">
</head>
<body>
<?php include __DIR__ . '/includes/header_partial.php'; ?>

<div class="container" style="max-width: 640px;">
    <h1 class="heading-serif" style="color:var(--color-accent); margin-bottom: 0.25rem;">My Profile</h1>
    <p style="color:var(--color-text-secondary); margin-bottom: 2rem;">
        Your name and company details appear on invoices you generate.
    </p>

    <?php if ($flash): ?>
        <div class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>" style="margin-bottom:1rem;">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>

    <div class="card" style="padding: 2rem;">
        <form method="POST">
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

            <button type="submit" class="btn btn-primary" style="width:100%;">Save Profile</button>
        </form>
    </div>

    <p style="margin-top:1rem; font-size:0.85rem; color:var(--color-text-secondary); text-align:center;">
        Role: <strong><?php echo ucfirst($user['role']); ?></strong>
        &nbsp;&bull;&nbsp;
        <a href="/TimeForge_Capstone/index.php">Back to Dashboard</a>
    </p>
</div>

<?php include __DIR__ . '/includes/footer_partial.php'; ?>
<script src="/TimeForge_Capstone/js/theme.js"></script>
</body>
</html>
