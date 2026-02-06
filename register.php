<?php
$page_title = 'Register';

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';

// If already logged in, redirect
if (isLoggedIn()) {
    header('Location: /TimeForge_Capstone/index.php');
    exit();
}

$errors = [];
$success_message = '';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $role = $_POST['role'] ?? 'freelancer';
    
    $result = registerUser($username, $email, $password, $confirm_password, $full_name, $role);
    
    if ($result['success']) {
        $success_message = $result['message'];
        // Clear form
        $username = '';
        $email = '';
        $full_name = '';
    } else {
        $errors = $result['errors'] ?? [];
    }
}

include_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="card" style="max-width: 500px; margin: 3rem auto;">
        <h1 style="text-align: center; margin-bottom: 2rem; color: var(--color-accent);">Create Account</h1>
        
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
                <p style="margin-top: 1rem;"><a href="/TimeForge_Capstone/login.php" class="btn btn-primary" style="padding: 0.5rem 1rem;">Go to Login</a></p>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (empty($success_message)): ?>
        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
                <small style="color: var(--color-text-secondary);">3+ characters, no spaces</small>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($full_name ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <small style="color: var(--color-text-secondary);">6+ characters</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <label for="role">I am a:</label>
                <select id="role" name="role" required>
                    <option value="freelancer">Freelancer</option>
                    <option value="client">Client</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Register</button>
        </form>
        
        <div style="text-align: center; margin-top: 1.5rem; color: var(--color-text-secondary);">
            <p>Already have an account? <a href="/TimeForge_Capstone/login.php" style="color: var(--color-accent); text-decoration: none; font-weight: 600;">Login here</a></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
