<?php
$page_title = 'Login';

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: /TimeForge_Capstone/index.php');
    exit();
}

$error_message = '';
$success_message = '';

if (isset($_SESSION['login_error'])) {
    $error_message = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}

if (isset($_SESSION['register_success'])) {
    $success_message = $_SESSION['register_success'];
    unset($_SESSION['register_success']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TimeForge</title>
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
        
        <h2>Welcome</h2>
        
        <div class="auth-tabs">
            <a href="login.php" class="active">Login</a>
            <a href="register.php">Sign Up</a>
        </div>

        <div class="social-login">
            <span style="font-weight: 600;">google</span>
            <span class="or-divider">or email address</span>
        </div>

        <?php if (!empty($success_message)): ?>
            <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <form action="includes/login_process.php" method="post" class="auth-form">
            <div class="form-group">
                <label for="username">Email Address or Username</label>
                <input type="text" name="username" id="username" 
                       value="<?php echo htmlspecialchars($_SESSION['login_username'] ?? ''); unset($_SESSION['login_username']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>

            <button type="submit" class="btn-auth-primary">Signin to TimeForge</button>
            
            <a href="#" class="forgot-password">Forgot password?</a>
        </form>
    </div>
</div>

<script src="js/theme.js"></script>
</body>
</html>
<?php // End of file
