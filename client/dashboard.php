<?php
$page_title = 'Client Portal';

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';

// Require client role
requireRole('client');

$current_user = getCurrentUser();

include_once __DIR__ . '/../includes/header.php';
?>

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

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
