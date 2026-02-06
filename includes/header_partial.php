<?php
// Header Partial (markup only) for instructor-style pages
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/auth.php';
$current_user = getCurrentUser();
?>
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
      <span class="nav-text">Welcome, <?php echo htmlspecialchars($current_user['full_name'] ?? 'User'); ?></span>
      <a href="/TimeForge_Capstone/admin/dashboard.php">Admin Dashboard</a>
      <a href="/TimeForge_Capstone/freelancer/dashboard.php">Freelancer Portal</a>
      <a href="/TimeForge_Capstone/client/dashboard.php">Client Portal</a>
      <button id="themeToggle" class="theme-toggle">Theme</button>
  <a href="/TimeForge_Capstone/includes/logout.php" class="btn btn-danger btn-compact">Logout</a>
    <?php else: ?>
      <button id="themeToggle" class="theme-toggle">Theme</button>
      <a href="/TimeForge_Capstone/login.php">Login</a>
      <a href="/TimeForge_Capstone/register.php">Register</a>
    <?php endif; ?>
  </nav>
</header>
