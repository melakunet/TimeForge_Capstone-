<?php
// Header Component

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/auth.php';

$current_user = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - TimeForge' : 'TimeForge'; ?></title>
    <link rel="stylesheet" href="/TimeForge_Capstone/css/style.css">
    <link rel="icon" type="image/png" href="/TimeForge_Capstone/icons/logo.png">
</head>
<body>
    <header>
        <div class="logo">
            <a href="/TimeForge_Capstone/index.php" class="logo-link">
                <img src="/TimeForge_Capstone/icons/logo.png" alt="TimeForge Logo">
                <span>TimeForge</span>
            </a>
        </div>
        
        <nav>
            <a href="/TimeForge_Capstone/index.php">Home</a>
            <?php if (isLoggedIn()): ?>
                <span class="nav-text">Welcome, <?php echo htmlspecialchars($current_user['full_name']); ?></span>
                
                <?php if (hasRole('admin')): ?>
                    <a href="/TimeForge_Capstone/admin/dashboard.php">Admin Dashboard</a>
                <?php elseif (hasRole('freelancer')): ?>
                    <a href="/TimeForge_Capstone/freelancer/dashboard.php">Freelancer Portal</a>
                <?php elseif (hasRole('client')): ?>
                    <a href="/TimeForge_Capstone/client/dashboard.php">Client Portal</a>
                <?php endif; ?>
                
                <button id="themeToggle" class="theme-toggle">
                    Theme
                </button>
                
                <a href="/TimeForge_Capstone/includes/logout.php" class="btn btn-danger btn-compact">Logout</a>
            <?php else: ?>
                <button id="themeToggle" class="theme-toggle">
                    Theme
                </button>
                
                <a href="/TimeForge_Capstone/login.php">Login</a>
                <a href="/TimeForge_Capstone/register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>
    
    <script src="/TimeForge_Capstone/js/theme.js"></script>
