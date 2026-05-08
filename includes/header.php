<?php
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
    <link rel="stylesheet" href="<?= APP_BASE ?>/css/style.css">
    <link rel="stylesheet" href="<?= APP_BASE ?>/css/time_tracker.css">
    <link rel="icon" type="image/png" href="<?= APP_BASE ?>/icons/logo.png">
    <script>/* Expose server config to JS */const APP_BASE = '<?= APP_BASE ?>';</script>
</head>
<body>
    <header>
        <div class="logo">
            <a href="<?= APP_BASE ?>/index.php?view=welcome" class="logo-link">
                <img src="<?= APP_BASE ?>/icons/logo.png" alt="TimeForge Logo">
                <span>TimeForge</span>
            </a>
        </div>
        
        <nav>
            <a href="<?= APP_BASE ?>/index.php">Home</a>
            <?php if (isLoggedIn()): ?>
                <span class="nav-text">Welcome, <?php echo htmlspecialchars($current_user['full_name']); ?></span>
                
                <?php if (hasRole('admin')): ?>
                    <a href="<?= APP_BASE ?>/admin/dashboard.php">Admin Dashboard</a>
                <?php elseif (hasRole('freelancer')): ?>
                    <a href="<?= APP_BASE ?>/freelancer/dashboard.php">Freelancer Portal</a>
                <?php elseif (hasRole('client')): ?>
                    <a href="<?= APP_BASE ?>/client/dashboard.php">Client Portal</a>
                <?php endif; ?>
                
                <button id="themeToggle" class="theme-toggle">
                    Dark mode
                </button>
                
                <a href="<?= APP_BASE ?>/includes/logout.php" class="btn btn-danger btn-compact">Logout</a>
            <?php else: ?>
                <button id="themeToggle" class="theme-toggle">
                    Dark mode
                </button>
                
                <a href="<?= APP_BASE ?>/login.php">Login</a>
                <a href="<?= APP_BASE ?>/register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>

