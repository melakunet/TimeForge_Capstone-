<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;
$project_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$project_id) {
    setFlash('error', 'Invalid project ID.');
    header("Location: index.php");
    exit;
}

// Fetch project details with client info
$query = "
    SELECT 
        p.*, 
        c.client_name, 
        c.company_name, 
        c.email as client_email,
        u.full_name as created_by_name
    FROM projects p
    LEFT JOIN clients c ON p.client_id = c.id
    LEFT JOIN users u ON p.created_by = u.id
    WHERE p.id = :id
";

$stmt = $pdo->prepare($query);
$stmt->bindValue(':id', $project_id, PDO::PARAM_INT);
$stmt->execute();
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    setFlash('error', 'Project not found.');
    header("Location: index.php");
    exit;
}

// Access Control
if ($role === 'client') {
    // Check if the project belongs to this client
    $clientCheckQuery = "SELECT id FROM clients WHERE id = :client_id AND user_id = :user_id";
    $cStmt = $pdo->prepare($clientCheckQuery);
    $cStmt->bindValue(':client_id', $project['client_id']);
    $cStmt->bindValue(':user_id', $user_id);
    $cStmt->execute();
    
    if (!$cStmt->fetch()) {
        setFlash('error', 'Access denied.');
        header("Location: index.php");
        exit;
    }
}

// Fetch Time Entries
$timeQuery = "
    SELECT 
        te.*, 
        u.full_name as user_name,
        TIME_FORMAT(TIMEDIFF(IFNULL(te.end_time, NOW()), te.start_time), '%H:%i') as duration
    FROM time_entries te
    LEFT JOIN users u ON te.user_id = u.id
    WHERE te.project_id = :project_id
    ORDER BY te.start_time DESC
";
$tStmt = $pdo->prepare($timeQuery);
$tStmt->bindValue(':project_id', $project_id, PDO::PARAM_INT);
$tStmt->execute();
$time_entries = $tStmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate Totals (ONLY Approved or Completed entries)
$total_seconds = 0;
foreach ($time_entries as $entry) {
    // Skip rejected or pending entries for cost calculation
    if ($entry['status'] === 'rejected' || $entry['status'] === 'pending') {
        continue;
    }

    if ($entry['end_time']) {
        $total_seconds += strtotime($entry['end_time']) - strtotime($entry['start_time']);
    } elseif ($entry['status'] == 'running') {
        $total_seconds += time() - strtotime($entry['start_time']);
    }
}
$total_hours = $total_seconds / 3600;
$total_cost = $total_hours * ($project['hourly_rate'] ?? 0);
$budget = $project['budget'] ?? 0;
$budget_remaining = $budget - $total_cost;

$page_title = 'Project Details: ' . $project['project_name'];
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
    <div class="page-header">
        <div class="header-content">
            <h1><?php echo htmlspecialchars($project['project_name']); ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li><a href="index.php">Dashboard</a></li>
                    <li class="active"><?php echo htmlspecialchars($project['project_name']); ?></li>
                </ol>
            </nav>
        </div>
        <div class="header-actions">
            <?php if ($role === 'admin' || $role === 'freelancer'): ?>
                <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="btn btn-secondary">Edit Project</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($flash['message'])): ?>
        <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>

    <div class="grid-layout">
        <div class="main-column">
            <div class="card">
                <div class="card-header">
                    <h3>Project Overview</h3>
                    <span class="status-badge status-<?php echo htmlspecialchars($project['status']); ?>">
                        <?php echo ucfirst(htmlspecialchars($project['status'])); ?>
                    </span>
                </div>
                <div class="card-body">
                    <p class="text-label">Description</p>
                    <div class="description-box">
                        <?php echo nl2br(htmlspecialchars($project['description'] ?? 'No description provided.')); ?>
                    </div>
                    
                    <div class="info-grid user-info-grid">
                        <div class="info-item">
                            <span class="label">Total Budget</span>
                            <span class="value">$<?php echo number_format($project['budget'] ?? 0, 2); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Hourly Rate</span>
                            <span class="value">$<?php echo number_format($project['hourly_rate'] ?? 0, 2); ?>/hr</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Hours Logged</span>
                            <span class="value"><?php echo number_format($total_hours, 2); ?> hrs</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Total Cost</span>
                            <span class="value text-accent">$<?php echo number_format($total_cost, 2); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Budget Remaining</span>
                            <span class="value <?php echo ($budget_remaining < 0) ? 'text-danger' : 'text-success'; ?>">
                                $<?php echo number_format($budget_remaining, 2); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Time Entries -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3>Time Entries</h3>
                    <?php if ($role === 'admin' || $role === 'freelancer'): ?>
                        <div class="header-actions">
                            <button class="btn btn-secondary btn-sm" onclick="openManualEntryModal()">
                                + Manual Entry
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="startProjectTimer()">
                                ▶ Start Timer
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <!-- Timer Integration Script -->
                    <script>
                        function startProjectTimer() {
                            const projectId = <?php echo json_encode($project['id']); ?>;
                            const projectName = <?php echo json_encode($project['project_name']); ?>;
                            
                            // Check if global timer exists
                            if (window.timeTracker) {
                                window.timeTracker.startTimer(projectId, projectName);
                            } else {
                                alert('Timer module not loaded. Please refresh the page.');
                            }
                        }
                    </script>
                    
                    <div class="table-responsive">
                        <table class="table project-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Description</th>
                                    <th>Start - End</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($time_entries) > 0): ?>
                                    <?php foreach ($time_entries as $entry): ?>
                                        <tr>
                                            <td><?php echo date('M d, Y', strtotime($entry['start_time'])); ?></td>
                                            <td>
                                                <div class="user-avatar-sm" title="<?php echo htmlspecialchars($entry['user_name']); ?>">
                                                    <?php echo strtoupper(substr($entry['user_name'] ?? 'U', 0, 2)); ?>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($entry['description']); ?></td>
                                            <td>
                                                <?php 
                                                    echo date('H:i', strtotime($entry['start_time']));
                                                    if ($entry['end_time']) {
                                                        echo ' - ' . date('H:i', strtotime($entry['end_time']));
                                                    } else {
                                                        echo ' - ...';
                                                    }
                                                ?>
                                            </td>
                                            <td><strong><?php echo $entry['duration']; ?></strong></td>
                                            <td>
                                                <?php if ($entry['status'] === 'running'): ?>
                                                    <span class="status-badge status-active">Running</span>
                                                <?php elseif ($entry['status'] === 'pending'): ?>
                                                    <span class="status-badge status-warning">Pending Approval</span>
                                                    <?php if ($role === 'admin'): ?>
                                                        <div class="approval-actions mt-1">
                                                            <form action="approve_time_entry.php" method="POST" class="d-inline">
                                                                <input type="hidden" name="entry_id" value="<?php echo $entry['id']; ?>">
                                                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                                                <button type="submit" name="action" value="approve" class="btn-approve" title="Approve">✓</button>
                                                            </form>
                                                            <form action="approve_time_entry.php" method="POST" class="d-inline">
                                                                <input type="hidden" name="entry_id" value="<?php echo $entry['id']; ?>">
                                                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                                                <button type="submit" name="action" value="reject" class="btn-reject" title="Reject">✕</button>
                                                            </form>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php elseif ($entry['status'] === 'rejected'): ?>
                                                    <span class="status-badge status-danger" title="<?php echo htmlspecialchars($entry['rejection_reason'] ?? ''); ?>">Rejected</span>
                                                <?php elseif ($entry['status'] === 'approved'): ?>
                                                    <span class="status-badge status-success">Approved</span>
                                                <?php else: ?>
                                                    <span class="status-badge status-completed">Completed</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (($entry['user_id'] == $user_id && $entry['status'] == 'pending') || $role === 'admin'): ?>
                                                    <a href="edit_time_entry.php?id=<?php echo $entry['id']; ?>" class="btn-icon" title="Edit">✎</a>
                                                    <form action="delete_time_entry.php" method="POST" class="d-inline" onsubmit="return confirm('Delete this time entry?');">
                                                        <input type="hidden" name="entry_id" value="<?php echo $entry['id']; ?>">
                                                        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                                        <button type="submit" class="btn-icon btn-icon-danger" title="Delete">🗑</button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No time logs yet. Start the timer to begin tracking!</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Detailed Time Entries Table -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3>Detailed Time Entries</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($time_entries)): ?>
                        <div class="empty-state">
                            <p>No time entries found for this project.</p>
                        </div>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Status</th>
                                    <th>Duration</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($time_entries as $entry): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($entry['user_name']); ?></td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($entry['start_time'])); ?></td>
                                        <td>
                                            <?php if ($entry['end_time']): ?>
                                                <?php echo date('Y-m-d H:i', strtotime($entry['end_time'])); ?>
                                            <?php else: ?>
                                                <span class="text-warning">In Progress</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo htmlspecialchars($entry['status']); ?>">
                                                <?php echo ucfirst(htmlspecialchars($entry['status'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($entry['duration']): ?>
                                                <?php echo htmlspecialchars($entry['duration']); ?>
                                            <?php else: ?>
                                                <span class="text-warning">Calculating...</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <!-- Action buttons for time entry -->
                                            <?php if ($role === 'admin' || $role === 'freelancer'): ?>
                                                <div class="action-buttons">
                                                    <?php if ($entry['status'] === 'running'): ?>
                                                        <form action="stop_timer.php" method="POST" class="d-inline">
                                                            <input type="hidden" name="entry_id" value="<?php echo $entry['id']; ?>">
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Stop the timer?');">
                                                                ■ Stop Timer
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form action="start_timer.php" method="POST" class="d-inline">
                                                            <input type="hidden" name="entry_id" value="<?php echo $entry['id']; ?>">
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                ▶ Resume Timer
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    
                                                    <a href="edit_time_entry.php?id=<?php echo $entry['id']; ?>" class="btn btn-secondary btn-sm">
                                                        ✎ Edit
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="sidebar-column">
            <div class="card">
                <div class="card-header">
                    <h3>Client Details</h3>
                </div>
                <div class="card-body">
                    <h4 class="client-name"><?php echo htmlspecialchars($project['client_name']); ?></h4>
                    <?php if (!empty($project['company_name'])): ?>
                        <p class="company-name"><?php echo htmlspecialchars($project['company_name']); ?></p>
                    <?php endif; ?>
                    
                    <hr class="divider">
                    
                    <div class="contact-info">
                        <?php if (!empty($project['client_email'])): ?>
                            <div class="contact-item">
                                <span class="icon">✉</span>
                                <a href="mailto:<?php echo htmlspecialchars($project['client_email']); ?>">
                                    <?php echo htmlspecialchars($project['client_email']); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($role === 'admin'): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h3>Admin Actions</h3>
                    </div>
                    <div class="card-body">
                         <?php if ($project['deleted_at']): ?>
                             <form action="restore_project.php" method="POST">
                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                <button type="submit" class="btn btn-success btn-full">Restore Project</button>
                             </form>
                         <?php else: ?>
                            <form action="delete_project.php" method="POST" onsubmit="return confirm('Archive this project?');">
                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                <button type="submit" class="btn btn-archive btn-full">Archive Project</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Manual Entry Modal -->
    <div id="manualEntryModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeManualEntryModal()">&times;</span>
            <h2>Log Time Manually</h2>
            <form action="add_time_manual.php" method="POST">
                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="start_date" required value="<?php echo date('Y-m-d'); ?>" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Start Time</label>
                    <input type="time" name="start_time" required value="09:00" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Duration (Hours)</label>
                    <input type="number" name="duration_hours" step="0.25" min="0.25" required placeholder="e.g. 1.5" class="form-control">
                    <small class="form-text">Enter decimal hours (1.5 = 1h 30m)</small>
                </div>
                
                <div class="form-group">
                    <label>Description of Work</label>
                    <textarea name="description" required placeholder="What did you work on?" class="form-control"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeManualEntryModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Time</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Scripts -->
    <script>
        const modal = document.getElementById('manualEntryModal');
        function openManualEntryModal() {
            modal.style.display = "block";
        }
        function closeManualEntryModal() {
            modal.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target == modal) {
                closeManualEntryModal();
            }
        }
    </script>
</main>

<?php include_once __DIR__ . '/includes/footer_partial.php'; ?>
</body>
</html>
