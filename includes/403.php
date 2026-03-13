<?php
/**
 * 403 Access Denied Page
 * Shown when a logged-in user attempts to access a page their role cannot reach.
 * Called by requireRole() in auth.php and any manual role checks across the project.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

http_response_code(403);

// Determine a safe "go back" destination based on the user's role
$role      = $_SESSION['role'] ?? null;
$full_name = $_SESSION['full_name'] ?? 'User';

$home_url = '/TimeForge_Capstone/index.php';
if ($role === 'client') {
    $home_url = '/TimeForge_Capstone/client/dashboard.php';
} elseif ($role === 'freelancer') {
    $home_url = '/TimeForge_Capstone/freelancer/dashboard.php';
} elseif ($role === 'admin') {
    $home_url = '/TimeForge_Capstone/admin/dashboard.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Access Denied - TimeForge</title>
    <link rel="stylesheet" href="/TimeForge_Capstone/css/style.css">
    <link rel="icon" type="image/png" href="/TimeForge_Capstone/icons/logo.png">
</head>
<body>
    <main class="container page-403">

        <div class="error-403-wrap">
            <div class="error-403-code">403</div>
            <h1 class="error-403-title">Access Denied</h1>
            <p class="error-403-message">
                Sorry, <?php echo htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8'); ?> —
                you don't have permission to view this page.
            </p>
            <p class="error-403-hint">
                If you believe this is a mistake, please contact your administrator.
            </p>
            <div class="error-403-actions">
                <a href="<?php echo htmlspecialchars($home_url, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                    &larr; Back to My Dashboard
                </a>
                <a href="/TimeForge_Capstone/includes/logout.php" class="btn btn-secondary">
                    Logout
                </a>
            </div>
        </div>

    </main>

    <script src="/TimeForge_Capstone/js/theme.js"></script>
</body>
</html>
