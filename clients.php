<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$page_title = 'Clients';
$current_user = getCurrentUser();
$user_role = $_SESSION['role'];

// Get filter and search parameters
$status_filter = $_GET['status'] ?? 'active';

// `search` can be non-string (e.g., array) which would break trim() and cause HTTP 500.
$search_query_raw = $_GET['search'] ?? '';
$search_query = is_string($search_query_raw) ? trim($search_query_raw) : '';

// Build query based on role and filters
$sql = "SELECT c.*, u.full_name as user_name, creator.full_name as created_by_name 
        FROM clients c
        LEFT JOIN users u ON c.user_id = u.id
        LEFT JOIN users creator ON c.created_by = creator.id
        WHERE 1=1";

$params = [];

// Filter by status
if ($status_filter === 'active') {
    $sql .= " AND c.is_active = 1";
} elseif ($status_filter === 'inactive') {
    $sql .= " AND c.is_active = 0";
}
// 'all' shows both active and inactive

// Search functionality
if (!empty($search_query)) {
    $sql .= " AND (c.client_name LIKE :search 
              OR c.company_name LIKE :search 
              OR c.email LIKE :search 
              OR c.phone LIKE :search)";
    $params[':search'] = '%' . $search_query . '%';
}

// Role-based filtering
if ($user_role === 'client') {
    // Clients can only see their own record
    $sql .= " AND c.user_id = :user_id";
    $params[':user_id'] = $_SESSION['user_id'];
}

$sql .= " ORDER BY c.client_name ASC";

$clients = [];
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $clients = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('clients.php query error: ' . $e->getMessage());
    setFlash('error', 'Unable to load clients right now. Please try again.');
}

$flash = getFlash();
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
            <span>Clients</span>
        </div>

        <div class="card dashboard-card">
            <div class="dashboard-header">
                <h2>Client Management</h2>
                <?php if ($user_role === 'admin' || $user_role === 'freelancer'): ?>
                    <div class="dashboard-actions">
                        <button type="button" class="btn btn-secondary" data-modal-target="quickAddClientModal">Quick Add Client</button>
                        <a href="add_client.php" class="btn btn-primary">+ Add Client</a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($flash) && !empty($flash['message'])): ?>
                <div class="alert alert-<?php echo htmlspecialchars($flash['type'] ?? 'info'); ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <!-- Search and Filter Section -->
            <div class="filter-section">
                <form method="get" action="clients.php" class="filter-form">
                    <div class="search-box">
                        <input type="text" 
                               name="search" 
                               placeholder="Search clients by name, company, email..." 
                               value="<?php echo htmlspecialchars($search_query); ?>"
                               class="search-input">
                        <button type="submit" class="btn btn-primary btn-sm">Search</button>
                        <?php if ($search_query): ?>
                            <a href="clients.php?status=<?php echo htmlspecialchars($status_filter); ?>" 
                               class="btn btn-secondary btn-sm">Clear</a>
                        <?php endif; ?>
                    </div>

                    <div class="filter-tabs">
                        <a href="?status=active<?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?>" 
                           class="filter-tab <?php echo $status_filter === 'active' ? 'active' : ''; ?>">
                            Active
                        </a>
                        <a href="?status=inactive<?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?>" 
                           class="filter-tab <?php echo $status_filter === 'inactive' ? 'active' : ''; ?>">
                            Inactive
                        </a>
                        <a href="?status=all<?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?>" 
                           class="filter-tab <?php echo $status_filter === 'all' ? 'active' : ''; ?>">
                            All
                        </a>
                    </div>
                </form>
            </div>

            <!-- Clients Table -->
            <div class="table-responsive">
                <table class="project-table">
                    <thead>
                        <tr>
                            <th>Client Name</th>
                            <th>Company</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Added By</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($clients)): ?>
                            <?php foreach ($clients as $client): ?>
                            <tr>
                                <td class="client-name">
                                    <?php echo htmlspecialchars($client['client_name']); ?>
                                </td>
                                <td><?php echo $client['company_name'] ? htmlspecialchars($client['company_name']) : '<span class="placeholder-text">--</span>'; ?></td>
                                <td><?php echo htmlspecialchars($client['email']); ?></td>
                                <td><?php echo $client['phone'] ? htmlspecialchars($client['phone']) : '<span class="placeholder-text">--</span>'; ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $client['is_active'] ? 'active' : 'inactive'; ?>">
                                        <?php echo $client['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($client['created_by_name'] ?? 'Unknown'); ?></td>
                                <td class="text-center">
                                    <?php if ($user_role === 'admin' || $user_role === 'freelancer'): ?>
                                        <a href="edit_client.php?id=<?php echo $client['id']; ?>" 
                                           class="action-btn-edit">Edit</a>
                                    <?php else: ?>
                                        <span class="placeholder-text">--</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <div class="empty-state-content">
                                        <h3>No clients found</h3>
                                        <?php if (empty($search_query)): ?>
                                            <p>No clients have been added yet.</p>
                                            <?php if ($user_role === 'admin' || $user_role === 'freelancer'): ?>
                                                <a href="add_client.php" class="btn btn-primary">Add Your First Client</a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <p>No clients match your search criteria.</p>
                                            <a href="clients.php?status=<?php echo htmlspecialchars($status_filter); ?>" 
                                               class="btn btn-secondary">Clear Search</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php include_once __DIR__ . '/includes/footer_partial.php'; ?>

    <?php if ($user_role === 'admin' || $user_role === 'freelancer'): ?>
        <div class="modal" id="quickAddClientModal" aria-hidden="true" role="dialog" aria-modal="true">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h3 class="modal-title">Quick Add Client</h3>
                    <button type="button" class="modal-close" data-modal-close>Close</button>
                </div>

                <form method="post" action="add_client_process.php">
                    <div class="modal-body">
                        <div class="form-row">
                            <label for="qc_client_name">Client Name <span class="required">*</span></label>
                            <input id="qc_client_name" name="client_name" type="text" required>
                        </div>

                        <div class="form-row">
                            <label for="qc_email">Email <span class="required">*</span></label>
                            <input id="qc_email" name="email" type="email" required>
                        </div>

                        <div class="form-row">
                            <label for="qc_company_name">Company Name</label>
                            <input id="qc_company_name" name="company_name" type="text">
                        </div>

                        <div class="form-row">
                            <label for="qc_phone">Phone</label>
                            <input id="qc_phone" name="phone" type="text">
                        </div>

                        <div class="form-row">
                            <label for="qc_address">Address</label>
                            <input id="qc_address" name="address" type="text">
                        </div>

                        <div class="form-row">
                            <label class="checkbox">
                                <input type="checkbox" name="is_active" checked>
                                Active
                            </label>
                        </div>
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Client</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
    
    <script src="js/theme.js"></script>
    <script src="js/animations.js"></script>
    <script src="js/modal.js"></script>
</body>
</html>
