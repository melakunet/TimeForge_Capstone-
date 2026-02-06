<?php
$page_title = 'Freelancer Portal';

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';

// Require freelancer role
requireRole('freelancer');

$current_user = getCurrentUser();

include_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 style="margin-bottom: 2rem; color: var(--color-accent);">Freelancer Portal</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        <div class="card">
            <h3 style="color: var(--color-accent); margin-bottom: 1rem;">Active Projects</h3>
            <p style="font-size: 2rem; font-weight: bold;">2</p>
            <p style="color: var(--color-text-secondary);">Projects assigned to you</p>
        </div>
        
        <div class="card">
            <h3 style="color: var(--color-accent); margin-bottom: 1rem;">Hours This Month</h3>
            <p style="font-size: 2rem; font-weight: bold;">5.5</p>
            <p style="color: var(--color-text-secondary);">Total billable hours</p>
        </div>
        
        <div class="card">
            <h3 style="color: var(--color-accent); margin-bottom: 1rem;">Earnings</h3>
            <p style="font-size: 2rem; font-weight: bold;">$312.50</p>
            <p style="color: var(--color-text-secondary);">Based on current rates</p>
        </div>
    </div>
    
    <div class="card">
        <h2 style="color: var(--color-accent); margin-bottom: 1.5rem;">Freelancer Functions</h2>
        <ul style="list-style: none; padding: 0;">
            <li style="margin-bottom: 1rem;">
                <a href="#" class="btn btn-primary" style="display: inline-block;">View Projects</a>
            </li>
            <li style="margin-bottom: 1rem;">
                <a href="#" class="btn btn-primary" style="display: inline-block;">Track Time</a>
            </li>
            <li style="margin-bottom: 1rem;">
                <a href="#" class="btn btn-primary" style="display: inline-block;">View Invoices</a>
            </li>
        </ul>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
