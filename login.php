<?php
$page_title = 'Login';

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';


if (isLoggedIn()) {
    header('Location: /TimeForge_Capstone/index.php');
    exit();
}

$error_message = '';
$timeout_message = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password';
    } else {
        $result = authenticateUser($username, $password);
        if ($result['success']) {
            // Redirect based on role
            $role = $_SESSION['role'];
            if ($role === 'admin') {
                header('Location: /TimeForge_Capstone/admin/dashboard.php');
            } elseif ($role === 'freelancer') {
                header('Location: /TimeForge_Capstone/freelancer/dashboard.php');
            } elseif ($role === 'client') {
                header('Location: /TimeForge_Capstone/client/dashboard.php');
            }
            exit();
        } else {
            $error_message = $result['message'];
        }
    }
}

// Check for session timeout
if (isset($_GET['timeout'])) {
    $timeout_message = 'Your session has expired. Please log in again.';
}

// Check for redirect message
$show_redirect_message = isset($_GET['redirect']);

include_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="card" style="max-width: 400px; margin: 4rem auto;">
        <h1 style="text-align: center; margin-bottom: 2rem; color: var(--color-accent);">TimeForge Login</h1>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($timeout_message)): ?>
            <div class="alert alert-warning">
                <?php echo htmlspecialchars($timeout_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($show_redirect_message): ?>
            <div class="alert alert-info">
                Please log in to continue
            </div>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Login</button>
        </form>
        
        <div style="text-align: center; margin-top: 1.5rem; color: var(--color-text-secondary);">
            <p>Don't have an account? <a href="/TimeForge_Capstone/register.php" style="color: var(--color-accent); text-decoration: none; font-weight: 600;">Register here</a></p>
        </div>
        
        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--color-border); text-align: center; font-size: 0.9rem; color: var(--color-text-secondary);">
            <p><strong>Test Credentials:</strong></p>
            <p><strong>Admin:</strong> admin_user / password123</p>
            <p><strong>Freelancer:</strong> dev_sarah / password123</p>
            <p><strong>Client:</strong> client_bob / password123</p>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
