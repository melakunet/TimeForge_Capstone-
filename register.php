<?php
$page_title = 'Register';

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: /TimeForge_Capstone/index.php');
    exit();
}

$errors = [];
$form_data = [];

if (isset($_SESSION['register_errors'])) {
    $errors = $_SESSION['register_errors'];
    unset($_SESSION['register_errors']);
}

if (isset($_SESSION['register_form_data'])) {
    $form_data = $_SESSION['register_form_data'];
    unset($_SESSION['register_form_data']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TimeForge</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth_layout.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
</head>
<body>

<div class="auth-wrapper">
    <!-- Left Side -->
    <div class="auth-left">
        <div class="auth-brand">
            <a href="index.php" class="brand-link">
                <img src="icons/logo.png" alt="Logo">
                <span>TIMEFORGE</span>
            </a>
        </div>
        
        <div class="auth-hero-text">
            <h1>Master your time.<br>Secure your earnings.</h1>
            <p>The comprehensive time tracking solution for modern professionals.</p>
        </div>

        <div class="auth-stats">
            <div class="auth-stat-item">
                <strong class="blue-text">15k+</strong>
                <span>Users Globally</span>
            </div>
            <div class="auth-stat-item">
                <strong class="blue-text">4.9/5</strong>
                <span>users rating</span>
            </div>
        </div>
    </div>

    <!-- Right Side -->
    <div class="auth-right">
        
        <h2 class="top-margin">Create Account</h2>
        
        <div class="auth-tabs">
            <a href="login.php" class="inactive-tab">Login</a>
            <a href="register.php" class="active">Sign Up</a>
        </div>

        <div class="social-login">
            <span class="google-text">google</span>
            <span class="or-divider">or create new account</span>
        </div>

        <?php if (!empty($errors)): ?>
             <div class="alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="includes/register_process.php" method="post" class="auth-form auth-form-scrollable">
            
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($form_data['full_name'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <label>Register as</label>
                <select name="role">
                    <option value="freelancer" <?php echo ($form_data['role'] ?? '') === 'freelancer' ? 'selected' : ''; ?>>Freelancer</option>
                    <option value="client" <?php echo ($form_data['role'] ?? '') === 'client' ? 'selected' : ''; ?>>Client</option>
                    <option value="admin" <?php echo ($form_data['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>

            <div class="form-group form-group-top-margin">
                <label class="checkbox-label">
                    <input type="checkbox" name="terms" value="1" required>
                    <span>I agree to the Terms & Conditions</span>
                </label>
            </div>

            <button type="submit" class="btn-auth-primary">Create Account</button>
        </form>
    </div>
</div>

<script src="js/theme.js"></script>
</body>
</html>
<?php // End of file
