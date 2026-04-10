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
