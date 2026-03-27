<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/auth.php';
$current_user = getCurrentUser();
?>
<header>
  <div class="logo">
    <a href="/TimeForge_Capstone/index.php?view=welcome" class="logo-link">
      <img src="/TimeForge_Capstone/icons/logo.png" alt="TimeForge Logo">
      <span>TimeForge</span>
    </a>
  </div>
  <nav>
    <a href="/TimeForge_Capstone/index.php">Home</a>
    <?php if (isLoggedIn()): ?>

      <?php if (hasRole('client')): ?>
        <!-- Client-only nav -->
        <a href="/TimeForge_Capstone/client/dashboard.php">My Dashboard</a>
        <a href="/TimeForge_Capstone/client/projects.php">My Projects</a>
        <a href="/TimeForge_Capstone/invoices/history.php">Invoices</a>
      <?php else: ?>
        <!-- Admin & Freelancer nav -->
        <a href="/TimeForge_Capstone/clients.php">Clients</a>
        <?php if (hasRole('admin')): ?>
          <a href="/TimeForge_Capstone/admin/dashboard.php">Admin Dashboard</a>
          <a href="/TimeForge_Capstone/admin/reports.php">Reports</a>
          <a href="/TimeForge_Capstone/invoices/history.php">Invoices</a>
        <?php elseif (hasRole('freelancer')): ?>
          <a href="/TimeForge_Capstone/freelancer/dashboard.php">Freelancer Portal</a>
        <?php endif; ?>
      <?php endif; ?>

      <span class="nav-text">Welcome, <?php echo htmlspecialchars($current_user['full_name'] ?? 'User'); ?></span>
      <button id="themeToggle" class="theme-toggle">Dark mode</button>
      <a href="/TimeForge_Capstone/includes/logout.php" class="btn btn-danger btn-compact">Logout</a>

    <?php else: ?>
      <button id="themeToggle" class="theme-toggle">Dark mode</button>
      <a href="/TimeForge_Capstone/login.php">Login</a>
      <a href="/TimeForge_Capstone/register.php">Register</a>
    <?php endif; ?>
  </nav>
</header>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="/TimeForge_Capstone/css/style.css">
<link rel="stylesheet" href="/TimeForge_Capstone/css/time_tracker.css">
