<?php
$page_title = 'Client Portal';

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('client');

$current_user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - TimeForge</title>
    <link rel="stylesheet" href="/TimeForge_Capstone/css/style.css">
    <link rel="icon" type="image/png" href="/TimeForge_Capstone/icons/logo.png">
</head>
<body>
    <?php include_once __DIR__ . '/../includes/header_partial.php'; ?>

<div class="container">
    <h1 class="text-accent mb-2">Client Portal</h1>
    
    <div class="dashboard-grid">
        <div class="card">
            <h3 class="text-accent mb-1">Your Projects</h3>
            <p class="stat-number">2</p>
            <p class="text-secondary">Active projects</p>
        </div>
        
        <div class="card">
            <h3 class="text-accent mb-1">Total Hours</h3>
            <p class="stat-number">5.5</p>
            <p class="text-secondary">Hours tracked across projects</p>
        </div>
        
        <div class="card">
            <h3 class="text-accent mb-1">Total Spent</h3>
            <p class="stat-number">$312.50</p>
            <p class="text-secondary">Total project cost</p>
        </div>
    </div>
    
    <div class="card">
        <h2 class="text-accent mb-1-5">Client Functions</h2>
        <ul class="list-unstyled">
            <li class="mb-1">
                <a href="#" class="btn btn-primary">View Projects</a>
            </li>
            <li class="mb-1">
                <a href="#" class="btn btn-primary">View Time Reports</a>
            </li>
            <li class="mb-1">
                <a href="#" class="btn btn-primary">Download Invoices</a>
            </li>
        </ul>
    </div>
</div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> TimeForge. All rights reserved.</p>
        <p>Professional Time Tracking & Project Management Solution</p>
        <p>Web Capstone Project by Etefworkie Melaku — triOS College, Mobile and Web App Development</p>
    </footer>
    
    <script src="/TimeForge_Capstone/js/theme.js"></script>
    <script src="/TimeForge_Capstone/js/animations.js"></script>
</body>
</html>
