<?php

$page_title = 'Home';

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';

$current_user = getCurrentUser();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TimeForge - Home</title>
    <!-- Google Fonts: DM Serif Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/TimeForge_Capstone/css/style.css" />
    <link rel="icon" type="image/png" href="/TimeForge_Capstone/icons/logo.png" />
    <script defer src="/TimeForge_Capstone/js/theme.js"></script>
    <script defer src="/TimeForge_Capstone/js/animations.js"></script>
</head>
<body>
<?php include __DIR__ . '/includes/header_partial.php'; ?>


<main class="container">
    <div class="card card-home">
    <h1 class="text-accent mb-1 heading-serif">Welcome to TimeForge</h1>
        <p class="text-secondary mb-0-75">Track time and projects with clarity—built for freelancers, teams, and clients.</p>
    <p class="text-secondary">Start timers, organize tasks by project, and share clear progress with clients through simple role‑based dashboards.</p>
        
        
        <div class="hero-clock-wrap mt-1">
            <div class="hero-clock" aria-label="Current time">
                <div class="clock-sheen"></div>
                <div class="hand hour" id="clockHour"></div>
                <div class="hand minute" id="clockMinute"></div>
                <div class="hand second" id="clockSecond"></div>
                <div class="center-dot"></div>
            </div>
            <div class="stat">
                <div class="label">Time running</div>
                <div class="value" id="statTime">--:--:--</div>
                <div class="stat-bar"><div class="fill" id="statTimeFill"></div></div>
            </div>
            <div class="stat">
                <div class="label">Earnings (demo)</div>
                <div class="value" id="statCash">$0.00</div>
                <div class="stat-bar"><div class="fill" id="statCashFill"></div></div>
            </div>
        </div>

        <?php if (isLoggedIn()): ?>
            <div class="form-group mt-1">
                <label>Signed in as</label>
                <div>
                    <?php echo htmlspecialchars(($current_user['full_name'] ?? $current_user['username']) ?: 'User'); ?>
                    (role: <?php echo htmlspecialchars($current_user['role'] ?? 'admin'); ?>)
                </div>
            </div>

            <div class="grid mt-1-25">
                <a class="btn btn-primary" href="/TimeForge_Capstone/admin/dashboard.php">Admin Dashboard</a>
                <a class="btn btn-primary" href="/TimeForge_Capstone/freelancer/dashboard.php">Freelancer Portal</a>
                <a class="btn btn-primary" href="/TimeForge_Capstone/client/dashboard.php">Client Portal</a>
            </div>

            <p class="mt-1">
                <a class="btn btn-danger" href="/TimeForge_Capstone/includes/logout.php">Logout</a>
            </p>
        <?php else: ?>
            <div class="grid mt-1-25">
                <a class="btn btn-primary" href="/TimeForge_Capstone/login.php">Login</a>
                <a class="btn btn-primary" href="/TimeForge_Capstone/register.php">Register</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="card quick-links-card">
    <h2 class="text-accent mb-0-75 heading-serif">Quick Links</h2>
        <ul class="list-reset pl-1-25">
            <li><a href="/TimeForge_Capstone/project_details.php?project_id=1">Project Details (Example)</a></li>
            <li><a href="/TimeForge_Capstone/sql/TimeForge_Capstone.sql" target="_blank">View SQL Export</a></li>
        </ul>
    </div>
 </main>

 <?php include __DIR__ . '/includes/footer_partial.php'; ?>
 </body>
</html>
