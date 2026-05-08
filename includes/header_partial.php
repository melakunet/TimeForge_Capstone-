<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/notify.php';
$current_user  = getCurrentUser();
// Guard: only query if logged in AND $pdo is available
$_notif_count  = (isLoggedIn() && isset($pdo)) ? unreadNotificationCount($pdo, (int)$_SESSION['user_id']) : 0;
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
          <a href="/TimeForge_Capstone/invoices/history.php">Invoices</a>
        <?php endif; ?>
      <?php endif; ?>

      <span class="nav-text">Welcome, <?php echo htmlspecialchars($current_user['full_name'] ?? 'User'); ?></span>
      <a href="/TimeForge_Capstone/notifications.php" class="btn btn-secondary btn-compact" title="Notifications" style="position:relative;">
        🔔<?php if ($_notif_count > 0): ?><span style="position:absolute;top:2px;right:2px;background:#dc2626;color:#fff;font-size:.6rem;border-radius:999px;padding:0 .35rem;line-height:1.4;"><?= $_notif_count ?></span><?php endif; ?>
      </a>
      <a href="/TimeForge_Capstone/about.php" class="btn btn-secondary btn-compact">About</a>
      <a href="/TimeForge_Capstone/profile.php" class="btn btn-secondary btn-compact">My Profile</a>
      <button id="themeToggle" class="theme-toggle">Dark mode</button>
      <a href="/TimeForge_Capstone/includes/logout.php" class="btn btn-danger btn-compact">Logout</a>

    <?php else: ?>
      <a href="/TimeForge_Capstone/about.php">About</a>
      <button id="themeToggle" class="theme-toggle">Dark mode</button>
      <a href="/TimeForge_Capstone/login.php">Login</a>
      <a href="/TimeForge_Capstone/register.php">Register</a>
    <?php endif; ?>
  </nav>
</header>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="/TimeForge_Capstone/css/style.css">
<link rel="stylesheet" href="/TimeForge_Capstone/css/time_tracker.css">
