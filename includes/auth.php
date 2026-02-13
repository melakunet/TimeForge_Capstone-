<?php
// Authentication Helper Functions

require_once __DIR__ . '/../db.php';

// Development-time auth bypass helpers
function devBypassEnabled() {
    return defined('DEV_AUTH_BYPASS') && DEV_AUTH_BYPASS === true;
}

function ensureDevSession() {
    if (devBypassEnabled() && (!isset($_SESSION['user_id']) || empty($_SESSION['user_id']))) {
        $_SESSION['user_id'] = 0;
        $_SESSION['username'] = $_SESSION['username'] ?? 'dev_user';
        $_SESSION['email'] = $_SESSION['email'] ?? 'dev@example.com';
        $_SESSION['role'] = $_SESSION['role'] ?? 'admin';
        $_SESSION['full_name'] = $_SESSION['full_name'] ?? 'Developer';
        $_SESSION['is_active'] = 1;
        $_SESSION['login_time'] = time();
    }
}

// Hash password securely
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Get current logged-in user
function getCurrentUser() {
    if (devBypassEnabled()) {
        ensureDevSession();
        return $_SESSION;
    }
    if (isset($_SESSION['user_id'])) {
        return $_SESSION;
    }
    return null;
}

// Check if user is logged in
function isLoggedIn() {
    if (devBypassEnabled()) {
        ensureDevSession();
        return true;
    }
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Check if user has specific role
function hasRole($role) {
    if (devBypassEnabled()) {
        return true;
    }
    if (!isLoggedIn()) {
        return false;
    }
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Start authenticated session
function startSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['is_active'] = $user['is_active'];
    $_SESSION['login_time'] = time();
}

// Destroy session (logout)
function destroySession() {
    // Log the logout action
    if (isset($_SESSION['user_id'])) {
        logAuditAction($_SESSION['user_id'], 'logout', $_SERVER['REMOTE_ADDR']);
    }
    
    $_SESSION = array();
    session_destroy();
}

// Log audit action
function logAuditAction($userId, $action, $ipAddress = null) {
    global $pdo;
    
    if ($ipAddress === null) {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
    
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO audit_logs (user_id, action, ip_address, user_agent, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $action, $ipAddress, $userAgent]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Authenticate user
function authenticateUser($username, $password) {
    global $pdo;
    
    // Development bypass: accept any credentials and create a session
    if (devBypassEnabled()) {
        $user = [
            'id' => 0,
            'username' => !empty($username) ? $username : 'dev_user',
            'email' => 'dev@example.com',
            'password' => '',
            'role' => 'admin',
            'full_name' => 'Developer',
            'is_active' => 1,
            'last_login' => null,
        ];
        startSession($user);
        return ['success' => true, 'message' => 'Development bypass login', 'user' => $user];
    }

    try {
        $stmt = $pdo->prepare("
            SELECT id, username, email, password, role, full_name, is_active, last_login
            FROM users
            WHERE username = ? OR email = ?
            LIMIT 1
        ");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            logAuditAction(0, 'login_failed_invalid_user', $_SERVER['REMOTE_ADDR']);
            return ['success' => false, 'message' => 'Invalid username or password'];
        }
        
        if (!$user['is_active']) {
            logAuditAction($user['id'], 'login_failed_account_disabled', $_SERVER['REMOTE_ADDR']);
            return ['success' => false, 'message' => 'Your account has been disabled'];
        }
        
        if (!verifyPassword($password, $user['password'])) {
            logAuditAction($user['id'], 'login_failed_wrong_password', $_SERVER['REMOTE_ADDR']);
            return ['success' => false, 'message' => 'Invalid username or password'];
        }
        
        // Update last login
        $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $updateStmt->execute([$user['id']]);
        
        // Log successful login
        logAuditAction($user['id'], 'login_success', $_SERVER['REMOTE_ADDR']);
        
        // Start session
        startSession($user);
        
        return ['success' => true, 'message' => 'Login successful', 'user' => $user];
        
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

// Register new user
function registerUser($username, $email, $password, $confirmPassword, $fullName, $role = 'freelancer') {
    global $pdo;
    
    // Validation
    $errors = [];
    
    if (empty($username) || strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address';
    }
    
    if (empty($password) || strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }
    
    if (empty($fullName) || strlen($fullName) < 3) {
        $errors[] = 'Full name must be at least 3 characters';
    }
    
    if (!in_array($role, ['freelancer', 'client', 'admin'])) {
        $errors[] = 'Invalid role selected';
    }
    
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    try {
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'errors' => ['Username already taken']];
        }
        
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'errors' => ['Email already registered']];
        }
        
        // Hash password
        $hashedPassword = hashPassword($password);
        
        // Insert new user
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password, role, full_name, is_active, created_at)
            VALUES (?, ?, ?, ?, ?, 1, NOW())
        ");
        $stmt->execute([$username, $email, $hashedPassword, $role, $fullName]);
        
        $userId = $pdo->lastInsertId();
        
        // Log registration
        logAuditAction($userId, 'user_registered', $_SERVER['REMOTE_ADDR']);
        
        return ['success' => true, 'message' => 'Registration successful! Please log in.', 'user_id' => $userId];
        
    } catch (PDOException $e) {
        return ['success' => false, 'errors' => ['Database error: ' . $e->getMessage()]];
    }
}

// Require login (redirect if not logged in)
function requireLogin() {
    if (devBypassEnabled()) {
        ensureDevSession();
        return;
    }
    if (!isLoggedIn()) {
        header('Location: /TimeForge_Capstone/login.php?redirect=1');
        exit();
    }
}

// Require specific role
function requireRole($role) {
    if (devBypassEnabled()) {
        ensureDevSession();
        return;
    }
    requireLogin();
    if (!hasRole($role)) {
        http_response_code(403);
        die('Access Denied');
    }
}
?>
