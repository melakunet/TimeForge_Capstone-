<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/db.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Only admin and freelancers can add clients
if (!hasRole('admin') && !hasRole('freelancer')) {
    header('Location: index.php');
    exit();
}

$page_title = 'Add Client';
$error_message = $_SESSION['error_message'] ?? '';
$success_message = $_SESSION['success_message'] ?? '';

// Clear messages after displaying
unset($_SESSION['error_message']);
unset($_SESSION['success_message']);

$current_user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - TimeForge</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="icons/logo.png">
</head>
<body>
    <?php include_once __DIR__ . '/includes/header_partial.php'; ?>

    <main class="container">
        <div class="breadcrumb">
            <a href="index.php">Dashboard</a> &gt; 
            <a href="clients.php">Clients</a> &gt; 
            <span>Add Client</span>
        </div>

        <div class="card form-card">
            <h2 class="page-title">Add New Client</h2>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <form action="add_client_process.php" method="post" id="add_client_form">
                <div class="form-group">
                    <label for="client_name">Client Name: <span class="required">*</span></label>
                    <input type="text" 
                           id="client_name" 
                           name="client_name" 
                           placeholder="Enter client's full name" 
                           required 
                           maxlength="100">
                </div>

                <div class="form-group">
                    <label for="company_name">Company Name:</label>
                    <input type="text" 
                           id="company_name" 
                           name="company_name" 
                           placeholder="Optional company/organization name" 
                           maxlength="100">
                </div>

                <div class="form-group">
                    <label for="email">Email: <span class="required">*</span></label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           placeholder="client@example.com" 
                           required 
                           maxlength="100">
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           placeholder="+1 (555) 123-4567" 
                           maxlength="20">
                </div>

                <div class="form-group">
                    <label for="address">Address:</label>
                    <textarea id="address" 
                              name="address" 
                              rows="3" 
                              placeholder="Optional street address, city, state, zip code"></textarea>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" value="1" checked>
                        Active Client
                    </label>
                    <small class="form-text">Inactive clients won't appear in project creation dropdowns</small>
                </div>

                <div class="form-group buttons">
                    <button type="submit" class="btn btn-primary">Add Client</button>
                    <a href="clients.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>

    <?php include_once __DIR__ . '/includes/footer_partial.php'; ?>
    
    <script src="js/theme.js"></script>
    <script src="js/animations.js"></script>
</body>
</html>
