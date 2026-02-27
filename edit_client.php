<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/db.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Only admin and freelancers can edit clients
if (!hasRole('admin') && !hasRole('freelancer')) {
    $_SESSION['error_message'] = 'You do not have permission to edit clients.';
    header('Location: clients.php');
    exit();
}

// Get client ID
$client_id = $_GET['id'] ?? null;

if (!$client_id || !is_numeric($client_id)) {
    $_SESSION['error_message'] = 'Invalid client ID.';
    header('Location: clients.php');
    exit();
}

// Fetch client data
$query = "SELECT * FROM clients WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute([':id' => $client_id]);
$client = $stmt->fetch();

if (!$client) {
    $_SESSION['error_message'] = 'Client not found.';
    header('Location: clients.php');
    exit();
}

$page_title = 'Edit Client';
$error_message = $_SESSION['error_message'] ?? '';
$success_message = $_SESSION['success_message'] ?? '';

unset($_SESSION['error_message'], $_SESSION['success_message']);

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
            <span>Edit Client</span>
        </div>

        <div class="card form-card">
            <h2 class="page-title">Edit Client: <?php echo htmlspecialchars($client['client_name']); ?></h2>
            
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

            <form action="edit_client_process.php" method="post" id="edit_client_form">
                <input type="hidden" name="client_id" value="<?php echo $client['id']; ?>">

                <div class="form-group">
                    <label for="client_name">Client Name: <span class="required">*</span></label>
                    <input type="text" 
                           id="client_name" 
                           name="client_name" 
                           value="<?php echo htmlspecialchars($client['client_name']); ?>"
                           placeholder="Enter client's full name" 
                           required 
                           maxlength="100">
                </div>

                <div class="form-group">
                    <label for="company_name">Company Name:</label>
                    <input type="text" 
                           id="company_name" 
                           name="company_name" 
                           value="<?php echo htmlspecialchars($client['company_name'] ?? ''); ?>"
                           placeholder="Optional company/organization name" 
                           maxlength="100">
                </div>

                <div class="form-group">
                    <label for="email">Email: <span class="required">*</span></label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?php echo htmlspecialchars($client['email']); ?>"
                           placeholder="client@example.com" 
                           required 
                           maxlength="100">
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="<?php echo htmlspecialchars($client['phone'] ?? ''); ?>"
                           placeholder="+1 (555) 123-4567" 
                           maxlength="20">
                </div>

                <div class="form-group">
                    <label for="address">Address:</label>
                    <textarea id="address" 
                              name="address" 
                              rows="3" 
                              placeholder="Optional street address, city, state, zip code"><?php echo htmlspecialchars($client['address'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" 
                               name="is_active" 
                               value="1" 
                               <?php echo $client['is_active'] ? 'checked' : ''; ?>>
                        Active Client
                    </label>
                    <small class="form-text">Inactive clients won't appear in project creation dropdowns</small>
                </div>

                <div class="form-group buttons">
                    <button type="submit" class="btn btn-primary">Update Client</button>
                    <a href="clients.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>

            <div class="client-meta">
                <p><strong>Added on:</strong> <?php echo date('F j, Y', strtotime($client['created_at'])); ?></p>
                <p><strong>Last updated:</strong> <?php echo date('F j, Y g:i A', strtotime($client['updated_at'])); ?></p>
            </div>
        </div>
    </main>

    <?php include_once __DIR__ . '/includes/footer_partial.php'; ?>
    
    <script src="js/theme.js"></script>
    <script src="js/animations.js"></script>
</body>
</html>
