<?php
$page_title = 'Admin Dashboard';

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';

// Require admin role
requireRole('admin');

$current_user = getCurrentUser();

include_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 style="margin-bottom: 2rem; color: var(--color-accent);">Admin Dashboard</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        <div class="card">
            <h3 style="color: var(--color-accent); margin-bottom: 1rem;">Total Users</h3>
            <p style="font-size: 2rem; font-weight: bold;">3</p>
            <p style="color: var(--color-text-secondary);">1 Admin, 1 Freelancer, 1 Client</p>
        </div>
        
        <div class="card">
            <h3 style="color: var(--color-accent); margin-bottom: 1rem;">Active Projects</h3>
            <p style="font-size: 2rem; font-weight: bold;">2</p>
            <p style="color: var(--color-text-secondary);">Website Redesign, SEO Audit</p>
        </div>
        
        <div class="card">
            <h3 style="color: var(--color-accent); margin-bottom: 1rem;">Time Entries</h3>
            <p style="font-size: 2rem; font-weight: bold;">2</p>
            <p style="color: var(--color-text-secondary);">Total billable hours tracked</p>
        </div>
    </div>
    
    <div class="card">
        <h2 style="color: var(--color-accent); margin-bottom: 1.5rem;">Admin Functions</h2>
        <ul style="list-style: none; padding: 0;">
            <li style="margin-bottom: 1rem;">
                <a href="#" class="btn btn-primary" style="display: inline-block;">Manage Users</a>
            </li>
            <li style="margin-bottom: 1rem;">
                <a href="#" class="btn btn-primary" style="display: inline-block;">View Audit Logs</a>
            </li>
            <li style="margin-bottom: 1rem;">
                <a href="#" class="btn btn-primary" style="display: inline-block;">System Settings</a>
            </li>
        </ul>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
