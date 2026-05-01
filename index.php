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
    
    // Filter logic
    $filter = $_GET['filter'] ?? 'active';
    $whereClause = "WHERE p.deleted_at IS NULL";
    
    if ($filter === 'archived') {
        $whereClause = "WHERE p.deleted_at IS NOT NULL";
    } elseif ($filter === 'all') {
        $whereClause = "WHERE 1=1";
    }

    // Dashboard Data Logic
    if ($role === 'admin') {
        $query = "SELECT p.*, c.client_name, c.company_name,
                  (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id AND t.status != 'done') AS open_task_count
                  FROM projects p
                  LEFT JOIN clients c ON c.id = p.client_id
                  $whereClause AND p.company_id = :company_id
                  ORDER BY p.id DESC";
        $p_stmt = $pdo->prepare($query);
        $p_stmt->bindValue(':company_id', $_SESSION['company_id'], PDO::PARAM_INT);
        $p_stmt->execute();
    } elseif ($role === 'client') {
        // Clients can view ONLY their own projects.
        // Map logged-in user -> clients.user_id -> projects.client_id
        $query = "SELECT p.*, c.client_name, c.company_name,
                  (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id AND t.status != 'done') AS open_task_count
                  FROM projects p
                  INNER JOIN clients c ON c.id = p.client_id
                  $whereClause AND c.user_id = :user_id
                  ORDER BY p.id DESC";
        $p_stmt = $pdo->prepare($query);
        $p_stmt->bindValue(':user_id', $user_id);
        $p_stmt->execute();
    } else {
        // Freelancer — scoped to same company
        $query = "SELECT p.*, c.client_name, c.company_name,
                  (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id AND t.status != 'done') AS open_task_count
                  FROM projects p
                  LEFT JOIN clients c ON c.id = p.client_id
                  $whereClause AND p.company_id = :company_id
                  ORDER BY p.id DESC";
        $p_stmt = $pdo->prepare($query);
        $p_stmt->bindValue(':company_id', $_SESSION['company_id'], PDO::PARAM_INT);
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
    <link rel="stylesheet" href="css/time_tracker.css">
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
                <div class="header-actions">
                    <?php if ($role === 'admin'): ?>
                        <form method="GET" class="filter-form">
                            <select name="filter" onchange="this.form.submit()" class="form-select-sm">
                                <option value="active" <?php echo ($filter === 'active') ? 'selected' : ''; ?>>Active Projects</option>
                                <option value="archived" <?php echo ($filter === 'archived') ? 'selected' : ''; ?>>Archived</option>
                                <option value="all" <?php echo ($filter === 'all') ? 'selected' : ''; ?>>All</option>
                            </select>
                        </form>
                    <?php endif; ?>
                    
                    <?php if ($role === 'admin' || $role === 'freelancer'): ?>
                        <a href="add_project.php" class="btn btn-primary">+ New Project</a>
                    <?php endif; ?>
                </div>
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
                                    <a class="action-link" href="project_details.php?id=<?php echo (int)$project['id']; ?>">View</a>
                                    
                                    <?php if ($role === 'admin' || $role === 'freelancer'): ?>
                                    <span class="action-sep">|</span>
                                    <a class="action-link" href="tasks.php?project_id=<?php echo (int)$project['id']; ?>">📋 Tasks<?php if (!empty($project['open_task_count']) && $project['open_task_count'] > 0): ?> <span style="background:#f59e0b;color:#000;border-radius:99px;padding:.05rem .4rem;font-size:.7rem;font-weight:700;vertical-align:middle;"><?php echo (int)$project['open_task_count']; ?></span><?php endif; ?></a>
                                    <span class="action-sep">|</span>
                                    <a class="action-link" href="edit_project.php?id=<?php echo (int)$project['id']; ?>">Edit</a>
                                    <?php endif; ?>

                                    <?php if ($role === 'admin'): ?>
                                        <span class="action-sep">|</span>
                                        <?php if (!empty($project['deleted_at'])): ?>
                                            <form action="restore_project.php" method="post" class="d-inline">
                                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                                <button type="submit" class="action-btn-restore">Restore</button>
                                            </form>
                                        <?php else: ?>
                                            <form action="delete_project.php" method="post" onsubmit="return confirm('Are you sure you want to archive this project? It can be restored later.');" class="d-inline">
                                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                                <button type="submit" class="action-btn-delete">Archive</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php elseif ($role === 'freelancer'): ?>
                                        <button class="action-btn-start" onclick="event.preventDefault(); window.startProjectTimer(<?php echo (int)$project['id']; ?>, '<?php echo htmlspecialchars($project['project_name'], ENT_QUOTES); ?>');" style="background: none; border: none; color: #2ecc71; cursor: pointer; font-weight: bold; margin-right: 5px;">▶ Start</button>
                                    <?php else: ?>
                                        <!-- client has no additional actions -->
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="js/time_tracker.js"></script>
    <style>
      #tf-proj-modal { position:fixed;inset:0;background:rgba(0,0,0,.6);display:flex;align-items:center;justify-content:center;z-index:9999; }
      #tf-proj-modal.hidden { display:none; }
      .tf-proj-modal-box { background:var(--color-card);border-radius:12px;padding:1.75rem 2rem;max-width:440px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.5); }
      .tf-proj-modal-box h3 { margin:0 0 .25rem;color:var(--color-accent);font-size:1.1rem; }
      .tf-proj-modal-box p  { margin:0 0 1.1rem;color:var(--color-text-secondary);font-size:.85rem; }
      .tf-proj-modal-box label { display:block;font-size:.78rem;color:var(--color-text-secondary);margin-bottom:.3rem;text-transform:uppercase;letter-spacing:.04em; }
      .tf-proj-modal-box select,
      .tf-proj-modal-box input  { width:100%;background:var(--color-bg);border:1px solid #334155;color:var(--color-text);border-radius:6px;padding:.55rem .75rem;font-size:.9rem;margin-bottom:1rem;box-sizing:border-box; }
      .tf-proj-modal-actions { display:flex;gap:.75rem;justify-content:flex-end; }
      .tf-proj-modal-actions button { padding:.55rem 1.2rem;border-radius:6px;border:none;cursor:pointer;font-weight:600;font-size:.88rem; }
      .btn-pm-start  { background:#3b82f6;color:#fff; }
      .btn-pm-start:hover { background:#2563eb; }
      .btn-pm-cancel { background:#334155;color:#94a3b8; }
      .btn-pm-cancel:hover { background:#475569;color:#fff; }
      #tf-proj-task-row { display:none; }
    </style>

    <!-- Project-timer start modal (with optional task picker) -->
    <div id="tf-proj-modal" class="hidden">
      <div class="tf-proj-modal-box">
        <h3>▶ Start Timer</h3>
        <p>Project: <strong id="tf-pm-project-name"></strong></p>
        <div id="tf-proj-task-row">
          <label>Task <span style="font-weight:400;text-transform:none;">(optional — leave "No task" to track project-level time)</span></label>
          <select id="tf-pm-task-select">
            <option value="">— No task —</option>
          </select>
        </div>
        <label>Description</label>
        <input type="text" id="tf-pm-desc" placeholder="What are you working on?" maxlength="200">
        <div class="tf-proj-modal-actions">
          <button class="btn-pm-cancel" onclick="closeProjModal()">Cancel</button>
          <button class="btn-pm-start"  onclick="confirmProjStart()">▶ Start Timer</button>
        </div>
      </div>
    </div>

    <script>
        let _pmProjectId   = null;
        let _pmProjectName = null;
        let _pmTasks       = [];

        window.startProjectTimer = async function(id, name) {
            if (window.timeTracker && window.timeTracker.projectId) {
                alert('A timer is already running. Please stop it first.');
                return;
            }
            _pmProjectId   = id;
            _pmProjectName = name;
            document.getElementById('tf-pm-project-name').textContent = name;
            document.getElementById('tf-pm-desc').value = 'General work';

            // Fetch open tasks for this project
            const sel = document.getElementById('tf-pm-task-select');
            sel.innerHTML = '<option value="">— No task —</option>';
            _pmTasks = [];
            try {
                const r = await fetch(`/TimeForge_Capstone/api/project_tasks.php?project_id=${id}`);
                const data = await r.json();
                if (data.tasks && data.tasks.length > 0) {
                    _pmTasks = data.tasks;
                    data.tasks.forEach(t => {
                        const o = document.createElement('option');
                        o.value       = t.id;
                        o.textContent = `${t.title} [${t.priority}]`;
                        sel.appendChild(o);
                    });
                    document.getElementById('tf-proj-task-row').style.display = 'block';
                } else {
                    document.getElementById('tf-proj-task-row').style.display = 'none';
                }
            } catch(e) {
                document.getElementById('tf-proj-task-row').style.display = 'none';
            }

            // Pre-fill description from task if one is selected
            sel.onchange = () => {
                const chosen = _pmTasks.find(t => t.id == sel.value);
                if (chosen) document.getElementById('tf-pm-desc').value = chosen.title;
                else        document.getElementById('tf-pm-desc').value = 'General work';
            };

            document.getElementById('tf-proj-modal').classList.remove('hidden');
            setTimeout(() => document.getElementById('tf-pm-desc').select(), 80);
        };

        function closeProjModal() {
            document.getElementById('tf-proj-modal').classList.add('hidden');
            _pmProjectId = null; _pmProjectName = null;
        }

        async function confirmProjStart() {
            if (!_pmProjectId) return;
            const pid  = _pmProjectId;
            const desc = document.getElementById('tf-pm-desc').value.trim() || 'General work';
            const sel  = document.getElementById('tf-pm-task-select');
            const tid  = sel.value ? parseInt(sel.value) : null;
            const tname = tid ? (_pmTasks.find(t => t.id == tid)?.title || null) : null;
            closeProjModal();
            if (window.timeTracker) {
                await window.timeTracker.startTimer(pid, desc, null, tid, tname);
            }
        }

        document.getElementById('tf-proj-modal').addEventListener('click', function(e) {
            if (e.target === this) closeProjModal();
        });
    </script>
</body>
</html>

