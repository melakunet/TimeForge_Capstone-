<?php
/**
 * public/index.php — Lightweight front controller (optional entry point)
 *
 * Maps clean URL slugs to the existing PHP page files.
 * Existing direct-file URLs (e.g. /TimeForge_Capstone/login.php) still work.
 *
 * Usage:  http://localhost/TimeForge_Capstone/public/index.php/dashboard
 *         or configure Apache/Nginx to route all requests here.
 *
 * To enable as the default router, add to .htaccess in project root:
 *   RewriteEngine On
 *   RewriteCond %{REQUEST_FILENAME} !-f
 *   RewriteRule ^(.*)$ public/index.php/$1 [L,QSA]
 *
 * (The .htaccess is NOT activated automatically — existing direct URLs keep working.)
 */

define('APP_ROOT', dirname(__DIR__));
require_once APP_ROOT . '/config/app.php';
require_once APP_ROOT . '/config/session.php';

/* ── Route map: slug => relative file path from APP_ROOT ──────────────── */
$routes = [
    ''                  => 'index.php',
    'home'              => 'index.php',
    'login'             => 'login.php',
    'logout'            => 'includes/logout.php',
    'register'          => 'register.php',
    'forgot-password'   => 'forgot_password.php',
    'reset-password'    => 'reset_password.php',
    'dashboard'         => 'index.php',          // role-redirects inside
    'profile'           => 'profile.php',
    'projects'          => 'index.php',
    'project'           => 'project_details.php',
    'tasks'             => 'tasks.php',
    'task'              => 'task_detail.php',
    'clients'           => 'clients.php',
    'notifications'     => 'notifications.php',
    'about'             => 'about.php',
    // Admin
    'admin'             => 'admin/dashboard.php',
    'admin/users'       => 'admin/users.php',
    'admin/reports'     => 'admin/reports.php',
    'admin/logs'        => 'admin/audit_logs.php',
    'admin/screenshots' => 'admin/screenshots.php',
    'admin/settings'    => 'admin/system_settings.php',
    // Client portal
    'client'            => 'client/dashboard.php',
    'client/projects'   => 'client/projects.php',
    // Freelancer portal
    'freelancer'        => 'freelancer/dashboard.php',
    // Invoices
    'invoices'          => 'invoices/history.php',
    'invoices/view'     => 'invoices/view.php',
    'invoices/generate' => 'invoices/generate.php',
];

/* ── Resolve the slug from PATH_INFO or QUERY_STRING ─────────────────── */
$slug = trim($_SERVER['PATH_INFO'] ?? ($_GET['route'] ?? ''), '/');

if (isset($routes[$slug])) {
    $file = APP_ROOT . '/' . $routes[$slug];
    if (is_file($file)) {
        // Forward any query-string parameters so existing pages work unmodified
        require $file;
        exit;
    }
}

/* ── 404 fallback ────────────────────────────────────────────────────── */
http_response_code(404);
require_once APP_ROOT . '/includes/403.php'; // reuse error template
