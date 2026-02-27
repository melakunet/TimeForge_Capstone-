<?php
$page_title = 'Welcome';

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';
require_once __DIR__ . '/db.php';

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;

// Check if user wants to view the welcome/landing page
$view = $_GET['view'] ?? '';
$showLanding = (!isLoggedIn() || $view === 'welcome');

// Determine view based on login status and URL parameter
if (isLoggedIn() && $view !== 'welcome') {
    $page_title = 'Dashboard';
    
    // Dashboard Data Logic - Exclude soft-deleted projects
    if ($role === 'admin') {
        $query = 'SELECT p.*, c.client_name, c.company_name
                  FROM projects p
                  INNER JOIN clients c ON c.id = p.client_id
                  WHERE p.deleted_at IS NULL
                  ORDER BY p.id DESC';
        $p_stmt = $pdo->prepare($query);
        $p_stmt->execute();
    } elseif ($role === 'client') {
        // Clients can view ONLY their own projects.
        // Map logged-in user -> clients.user_id -> projects.client_id
        $query = 'SELECT p.*, c.client_name, c.company_name
                  FROM projects p
                  INNER JOIN clients c ON c.id = p.client_id
                  WHERE c.user_id = :user_id AND p.deleted_at IS NULL
                  ORDER BY p.id DESC';
        $p_stmt = $pdo->prepare($query);
        $p_stmt->bindValue(':user_id', $user_id);
        $p_stmt->execute();
    } else {
        // Fallback or Freelancer logic if any specific logic needed
        $query = 'SELECT p.*, c.client_name, c.company_name
                  FROM projects p
                  INNER JOIN clients c ON c.id = p.client_id
                  WHERE p.deleted_at IS NULL
                  ORDER BY p.id DESC';
        $p_stmt = $pdo->prepare($query);
        $p_stmt->execute();
    }
    $projects = $p_stmt->fetchAll();
    $p_stmt->closeCursor();
}

$current_user = getCurrentUser();
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

<?php if (isLoggedIn() && $view !== 'welcome'): ?>
    <!-- LOGGED IN VIEW: DASHBOARD -->
    <main class="container">
        <div class="card dashboard-card">
            <div class="dashboard-header">
                <h2>Project Dashboard</h2>
                <?php if ($role === 'admin' || $role === 'freelancer'): ?>
                    <a href="add_project.php" class="btn btn-primary">+ New Project</a>
                <?php endif; ?>
            </div>

            <?php if (!empty($flash) && !empty($flash['message'])): ?>
                <div class="alert alert-<?php echo htmlspecialchars($flash['type'] ?? 'info'); ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="project-table">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Client</th>
                            <th>Hourly Rate</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($projects)): ?>
                            <?php foreach ($projects as $project): ?>
                            <tr>
                                <td class="project-name"><?php echo htmlspecialchars($project['project_name']); ?></td>
                                <td>
                                    <?php
                                        $clientLabel = $project['client_name'] ?? '';
                                        $company = $project['company_name'] ?? '';
                                        echo htmlspecialchars($clientLabel);
                                        if (!empty($company)) {
                                            echo '<br><small>' . htmlspecialchars($company) . '</small>';
                                        }
                                    ?>
                                </td>
                                <td>$<?php echo number_format($project['hourly_rate'], 2); ?>/hr</td>
                                <td><span class="status-badge status-<?php echo htmlspecialchars($project['status']); ?>"><?php echo ucfirst(htmlspecialchars($project['status'])); ?></span></td>
                                <td class="text-center">
                                    <?php if ($role === 'admin' || $role === 'freelancer'): ?>
                                    <a class="action-link" href="edit_project.php?id=<?php echo (int)$project['id']; ?>">Edit</a>
                                    <span class="action-sep">|</span>
                                    <?php endif; ?>

                                    <?php if ($role === 'admin'): ?>
                                    <form action="delete_project.php" method="post" onsubmit="return confirm('Are you sure you want to archive this project? It can be restored later.');" class="d-inline">
                                        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                        <button type="submit" class="action-btn-delete">Archive</button>
                                    </form>
                                    <?php else: ?>
                                    <span class="placeholder-text">--</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="empty-state">No projects found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

<?php else: ?>
    <!-- LOGGED OUT VIEW: LANDING PAGE -->
    <div class="home-wrapper">
        <div class="home-hero">
            <h1 class="home-title">
                Master your time. <br>
                <span class="text-accent">Secure your earnings.</span>
            </h1>
            <p class="home-subtitle">
                 The simple, professional way to track time, manage projects, and get paid faster.
            </p>
            
            <div class="home-btn-group">
                <a href="register.php" class="btn btn-pill btn-pill-primary">Get Started</a>
                <a href="login.php" class="btn btn-pill btn-pill-secondary">Login</a>
            </div>
        </div>

        <!-- Hero Clock & Stats Widget -->
        <div class="hero-clock-wrap" style="margin: 2rem auto; justify-content: center; max-width: 600px;">
            <div class="hero-clock">
                <div class="clock-sheen"></div>
                <div class="center-dot"></div>
                <!-- ID hooks for JS -->
                <div class="hand hour" id="clockHour"></div>
                <div class="hand minute" id="clockMinute"></div>
                <div class="hand second" id="clockSecond"></div>
            </div>
            
            <div class="hero-stats">
                 <div class="stat">
                    <div class="label">Time Saved</div>
                    <div class="value" id="statTime">00:00:00</div>
                    <div class="stat-bar"><div class="fill" id="statTimeFill" style="width: 0%"></div></div>
                 </div>
                 <div class="stat">
                    <div class="label">Est. Earnings</div>
                    <div class="value" id="statCash">$0.00</div>
                    <div class="stat-bar"><div class="fill" id="statCashFill" style="width: 0%"></div></div>
                 </div>
            </div>
        </div>

        <div class="home-features">
            <div class="feature-card">
                <h3>Time Tracking</h3>
                <p>Log hours in seconds and keep your productivity in check.</p>
            </div>
            <div class="feature-card">
                <h3>Project Management</h3>
                <p>Stay on top of deadlines and budgets with ease.</p>
            </div>
            <div class="feature-card">
                <h3>Reporting</h3>
                <p>Generate insights to share with clients or your team.</p>
            </div>
        </div>
    </div>

<?php endif; ?>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> TimeForge. All rights reserved.</p>
        <p>Professional Time Tracking & Project Management Solution</p>
        <p>Web Capstone Project by Etefworkie Melaku — triOS College, Mobile and Web App Development</p>
    </footer>
    
    <script src="js/theme.js"></script>
    <script src="js/animations.js"></script>
    <script src="js/hero.js"></script>
</body>
</html>

