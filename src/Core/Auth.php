<?php
/**
 * src/Core/Auth.php — Authentication and authorisation helpers
 * Required via includes/auth.php
 */

require_once __DIR__ . '/../../db.php';

// ── Dev bypass ────────────────────────────────────────────────────────────

function devBypassEnabled() {
    return defined('DEV_AUTH_BYPASS') && DEV_AUTH_BYPASS === true;
}

function ensureDevSession() {
    if (devBypassEnabled() && (!isset($_SESSION['user_id']) || empty($_SESSION['user_id']))) {
        $_SESSION['user_id']   = 0;
        $_SESSION['username']  = $_SESSION['username']  ?? 'dev_user';
        $_SESSION['email']     = $_SESSION['email']     ?? 'dev@example.com';
        $_SESSION['role']      = $_SESSION['role']      ?? 'admin';
        $_SESSION['full_name'] = $_SESSION['full_name'] ?? 'Developer';
        $_SESSION['is_active'] = 1;
        $_SESSION['login_time'] = time();
    }
}

// ── Session helpers ───────────────────────────────────────────────────────

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function getCurrentUser() {
    if (devBypassEnabled()) { ensureDevSession(); return $_SESSION; }
    return isset($_SESSION['user_id']) ? $_SESSION : null;
}

function isLoggedIn() {
    if (devBypassEnabled()) { ensureDevSession(); return true; }
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function hasRole($role) {
    if (devBypassEnabled()) { return true; }
    if (!isLoggedIn()) { return false; }
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function startSession($user) {
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['username']   = $user['username'];
    $_SESSION['email']      = $user['email'];
    $_SESSION['role']       = $user['role'];
    $_SESSION['full_name']  = $user['full_name'];
    $_SESSION['is_active']  = $user['is_active'];
    $_SESSION['company_id'] = $user['company_id'] ?? null;
    $_SESSION['login_time'] = time();
}

function destroySession() {
    if (isset($_SESSION['user_id'])) {
        logAuditAction($_SESSION['user_id'], 'logout', $_SERVER['REMOTE_ADDR']);
    }
    $_SESSION = [];
    session_destroy();
}

// ── Audit log ─────────────────────────────────────────────────────────────

function logAuditAction($userId, $action, $ipAddress = null) {
    global $pdo;
    $ipAddress = $ipAddress ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    // Resolve company_id: prefer session, fall back to DB lookup
    $companyId = $_SESSION['company_id'] ?? null;
    if (!$companyId && $userId) {
        try {
            $cs = $pdo->prepare("SELECT company_id FROM users WHERE id = ? LIMIT 1");
            $cs->execute([$userId]);
            $companyId = $cs->fetchColumn() ?: null;
        } catch (PDOException $e) { $companyId = null; }
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO audit_logs (user_id, company_id, action, ip_address, user_agent, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $companyId, $action, $ipAddress, $userAgent]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// ── Authentication ────────────────────────────────────────────────────────

function authenticateUser($username, $password) {
    global $pdo;

    if (devBypassEnabled()) {
        $user = [
            'id' => 0, 'username' => !empty($username) ? $username : 'dev_user',
            'email' => 'dev@example.com', 'password' => '',
            'role' => 'admin', 'full_name' => 'Developer',
            'is_active' => 1, 'last_login' => null,
        ];
        startSession($user);
        return ['success' => true, 'message' => 'Development bypass login', 'user' => $user];
    }

    try {
        $stmt = $pdo->prepare("
            SELECT id, username, email, password, role, full_name, is_active, last_login, company_id
            FROM users WHERE username = ? OR email = ? LIMIT 1
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

        // Update last_login AND last_active_at so they appear online immediately after login
        $pdo->prepare("UPDATE users SET last_login = NOW(), last_active_at = NOW() WHERE id = ?")->execute([$user['id']]);
        logAuditAction($user['id'], 'login_success', $_SERVER['REMOTE_ADDR']);
        startSession($user);
        return ['success' => true, 'message' => 'Login successful', 'user' => $user];

    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

// ── Registration ──────────────────────────────────────────────────────────

function registerUser($username, $email, $password, $confirmPassword, $fullName, $role = 'freelancer', $companyName = '') {
    global $pdo;
    $errors = [];

    if (empty($username) || strlen($username) < 3)          $errors[] = 'Username must be at least 3 characters';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address';
    if (empty($password) || strlen($password) < 6)          $errors[] = 'Password must be at least 6 characters';
    if ($password !== $confirmPassword)                      $errors[] = 'Passwords do not match';
    if (empty($fullName) || strlen($fullName) < 3)          $errors[] = 'Full name must be at least 3 characters';
    if (!in_array($role, ['freelancer', 'client', 'admin'])) $errors[] = 'Invalid role selected';
    if ($role === 'admin' && empty(trim($companyName)))      $errors[] = 'Company name is required for admin accounts';

    if (!empty($errors)) return ['success' => false, 'errors' => $errors];

    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) return ['success' => false, 'errors' => ['Username already taken']];

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) return ['success' => false, 'errors' => ['Email already registered']];

        $pdo->beginTransaction();

        // Admins create a new company; freelancers/clients get NULL (assigned later by admin)
        $company_id = null;
        if ($role === 'admin') {
            $pdo->prepare("INSERT INTO companies (name) VALUES (?)")->execute([trim($companyName)]);
            $company_id = (int)$pdo->lastInsertId();
        }

        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password, role, full_name, company_id, is_active, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 1, NOW())
        ");
        $stmt->execute([$username, $email, hashPassword($password), $role, $fullName, $company_id]);
        $userId = (int)$pdo->lastInsertId();

        $pdo->commit();

        logAuditAction($userId, 'user_registered', $_SERVER['REMOTE_ADDR']);
        return ['success' => true, 'message' => 'Registration successful! Please log in.', 'user_id' => $userId];

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        return ['success' => false, 'errors' => ['Database error: ' . $e->getMessage()]];
    }
}

// ── Access control ────────────────────────────────────────────────────────

function requireLogin() {
    if (devBypassEnabled()) { ensureDevSession(); return; }
    if (!isLoggedIn()) {
        header('Location: /TimeForge_Capstone/login.php?redirect=1');
        exit;
    }
}

function requireRole($role) {
    if (devBypassEnabled()) { ensureDevSession(); return; }
    requireLogin();
    if (!hasRole($role)) {
        include __DIR__ . '/../../includes/403.php';
        exit;
    }
}
