<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/db.php';

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;

// Check if user wants to view the welcome/landing page
$view = $_GET['view'] ?? '';
$showLanding = (!isLoggedIn() || $view === 'welcome');

// Determine view based on login status and URL parameter
if (isLoggedIn() && $view !== 'welcome') {
    $page_title = 'Dashboard';
    
    // Dashboard Data Logic
    if ($role === 'admin') {
        $query = 'SELECT * FROM projects ORDER BY id DESC';
        $p_stmt = $pdo->prepare($query);
        $p_stmt->execute();
    } elseif ($role === 'client') {
        $query = 'SELECT * FROM projects WHERE client_id = :uid ORDER BY id DESC';
        $p_stmt = $pdo->prepare($query);
        $p_stmt->bindValue(':uid', $user_id);
        $p_stmt->execute();
    } else {
        // Fallback or Freelancer logic if any specific logic needed
        $query = 'SELECT * FROM projects ORDER BY id DESC';
        $p_stmt = $pdo->prepare($query);
        $p_stmt->execute();
    }
    $projects = $p_stmt->fetchAll();
    $p_stmt->closeCursor();

} else {
    $page_title = 'Welcome';
}

// Use the main Header component (contains DOCTYPE, <head>, <body> start, and Nav)
include __DIR__ . '/includes/header.php';
?>

<?php if (isLoggedIn() && $view !== 'welcome'): ?>
    <!-- LOGGED IN VIEW: DASHBOARD -->
    <main class="container">
        <div class="card dashboard-card">
            <div class="dashboard-header">
                <h2>Project Dashboard</h2>
                <?php if ($role === 'admin' || $role === 'client'): ?>
                    <a href="add_project.php" class="btn btn-primary">+ New Project</a>
                <?php endif; ?>
            </div>
            
            <div class="table-responsive">
                <table class="project-table">
                    <thead>
                        <tr>
                            <th>Project Name</th>
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
                                <td>$<?php echo number_format($project['hourly_rate'], 2); ?>/hr</td>
                                <td><span class="status-badge status-<?php echo htmlspecialchars($project['status']); ?>"><?php echo ucfirst(htmlspecialchars($project['status'])); ?></span></td>
                                <td class="text-center">
                                    <?php if ($role === 'admin' || $role === 'client'): ?>
                                    <form action="delete_project.php" method="post" onsubmit="return confirm('Are you sure you want to delete this project?');" class="d-inline">
                                        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                        <button type="submit" class="action-btn-delete">Delete</button>
                                    </form>
                                    <?php else: ?>
                                    <span class="placeholder-text">--</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="empty-state">No projects found.</td>
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

<?php include __DIR__ . '/includes/footer.php'; ?>

