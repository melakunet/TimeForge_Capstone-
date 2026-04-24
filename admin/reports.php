<?php
/*
 * Financial Summary Report (admin only)
 * Shows profitability across projects: billed vs budget, margins,
 * per-freelancer hours. Supports date-range and client filtering.
 */
$page_title = 'Financial Reports';

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../db.php';

requireRole('admin');

// ── Filter inputs ─────────────────────────────────────────────────────────────
$filter_client    = filter_input(INPUT_GET, 'client_id',  FILTER_VALIDATE_INT);
$filter_date_from = trim($_GET['date_from'] ?? '');
$filter_date_to   = trim($_GET['date_to']   ?? '');

// Sanitise date strings — only accept valid dates
if ($filter_date_from && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $filter_date_from)) $filter_date_from = '';
if ($filter_date_to   && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $filter_date_to))   $filter_date_to   = '';

// ── Summary totals across all projects ───────────────────────────────────────
// Approved entries only — these are the hours the client signed off on
$summary_params = [':company_id' => $_SESSION['company_id']];
$summary_where  = "WHERE te.status = 'approved' AND te.end_time IS NOT NULL AND p.deleted_at IS NULL AND p.company_id = :company_id";

if ($filter_client) {
    $summary_where .= " AND p.client_id = :client_id";
    $summary_params[':client_id'] = $filter_client;
}
if ($filter_date_from) {
    $summary_where .= " AND DATE(te.start_time) >= :date_from";
    $summary_params[':date_from'] = $filter_date_from;
}
if ($filter_date_to) {
    $summary_where .= " AND DATE(te.start_time) <= :date_to";
    $summary_params[':date_to'] = $filter_date_to;
}

$summary_stmt = $pdo->prepare("
    SELECT
        COUNT(DISTINCT p.id)                                          AS project_count,
        COALESCE(SUM(te.total_seconds), 0)                           AS total_seconds,
        COALESCE(SUM((te.total_seconds / 3600) * p.hourly_rate), 0) AS total_billed,
        COALESCE(SUM(p.budget), 0)                                   AS total_budget
    FROM time_entries te
    INNER JOIN projects p ON p.id = te.project_id
    $summary_where
");

// All three queries are wrapped together — a failure in any one is caught and
// logged without a 500; the page renders with empty data and a flash message.
try {
    $summary_stmt->execute($summary_params);
    $summary = $summary_stmt->fetch();
} catch (PDOException $e) {
    error_log('reports.php summary query error: ' . $e->getMessage());
    $summary = ['project_count' => 0, 'total_seconds' => 0, 'total_billed' => 0, 'total_budget' => 0];
}

$total_hours  = round(($summary['total_seconds'] ?? 0) / 3600, 2);
$total_billed = round($summary['total_billed']   ?? 0, 2);
$total_budget = round($summary['total_budget']   ?? 0, 2);

// ── Per-project profitability ─────────────────────────────────────────────────
$proj_params = [':company_id' => $_SESSION['company_id']];
$proj_where  = "WHERE p.deleted_at IS NULL AND p.company_id = :company_id";

if ($filter_client) {
    $proj_where .= " AND p.client_id = :client_id";
    $proj_params[':client_id'] = $filter_client;
}

// The date filter applies to time entries via a conditional SUM inside CASE WHEN.
// Because $entry_date_cond is interpolated twice (once per CASE column), each
// placeholder would appear twice in the prepared statement — PDO forbids that.
// We use distinct suffixes (_a / _b) so every named parameter is unique.
$entry_date_cond_a = "te.status = 'approved' AND te.end_time IS NOT NULL";
$entry_date_cond_b = "te.status = 'approved' AND te.end_time IS NOT NULL";

if ($filter_date_from) {
    $entry_date_cond_a .= " AND DATE(te.start_time) >= :date_from_a";
    $entry_date_cond_b .= " AND DATE(te.start_time) >= :date_from_b";
    $proj_params[':date_from_a'] = $filter_date_from;
    $proj_params[':date_from_b'] = $filter_date_from;
}
if ($filter_date_to) {
    $entry_date_cond_a .= " AND DATE(te.start_time) <= :date_to_a";
    $entry_date_cond_b .= " AND DATE(te.start_time) <= :date_to_b";
    $proj_params[':date_to_a'] = $filter_date_to;
    $proj_params[':date_to_b'] = $filter_date_to;
}

$proj_stmt = $pdo->prepare("
    SELECT
        p.id,
        p.project_name,
        p.hourly_rate,
        p.budget,
        p.status,
        c.client_name,
        c.company_name,
        COALESCE(SUM(CASE WHEN $entry_date_cond_a THEN te.total_seconds ELSE 0 END), 0)                             AS approved_seconds,
        COALESCE(SUM(CASE WHEN $entry_date_cond_b THEN (te.total_seconds / 3600) * p.hourly_rate ELSE 0 END), 0)   AS billed_amount
    FROM projects p
    INNER JOIN clients c ON c.id = p.client_id
    LEFT  JOIN time_entries te ON te.project_id = p.id
    $proj_where
    GROUP BY p.id
    ORDER BY billed_amount DESC
");
try {
    $proj_stmt->execute($proj_params);
    $projects = $proj_stmt->fetchAll();
} catch (PDOException $e) {
    error_log('reports.php per-project query error: ' . $e->getMessage());
    $projects = [];
}

// ── Per-freelancer earnings ───────────────────────────────────────────────────
$fl_params = [':company_id' => $_SESSION['company_id']];
$fl_where  = "WHERE te.status = 'approved' AND te.end_time IS NOT NULL AND p.deleted_at IS NULL AND p.company_id = :company_id";

if ($filter_client) {
    $fl_where .= " AND p.client_id = :client_id";
    $fl_params[':client_id'] = $filter_client;
}
if ($filter_date_from) {
    $fl_where .= " AND DATE(te.start_time) >= :date_from";
    $fl_params[':date_from'] = $filter_date_from;
}
if ($filter_date_to) {
    $fl_where .= " AND DATE(te.start_time) <= :date_to";
    $fl_params[':date_to'] = $filter_date_to;
}

$fl_stmt = $pdo->prepare("
    SELECT
        u.id,
        u.full_name,
        u.email,
        COUNT(te.id)                                                 AS entry_count,
        COALESCE(SUM(te.total_seconds), 0)                          AS total_seconds,
        COALESCE(SUM((te.total_seconds / 3600) * p.hourly_rate), 0) AS total_earned
    FROM time_entries te
    INNER JOIN projects p ON p.id = te.project_id
    INNER JOIN users    u ON u.id = te.user_id
    $fl_where
    GROUP BY u.id
    ORDER BY total_earned DESC
");
try {
    $fl_stmt->execute($fl_params);
    $freelancers = $fl_stmt->fetchAll();
} catch (PDOException $e) {
    error_log('reports.php freelancer query error: ' . $e->getMessage());
    $freelancers = [];
}

// Client dropdown for the filter — fallback to empty list on failure
try {
    $client_list = $pdo->prepare("SELECT id, client_name, company_name FROM clients WHERE is_active = 1 AND company_id = :company_id ORDER BY client_name");
    $client_list->execute([':company_id' => $_SESSION['company_id']]);
    $client_list = $client_list->fetchAll();
} catch (PDOException $e) {
    error_log('reports.php client list error: ' . $e->getMessage());
    $client_list = [];
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> — TimeForge</title>
    <link rel="stylesheet" href="/TimeForge_Capstone/css/style.css">
    <link rel="stylesheet" href="/TimeForge_Capstone/css/reports.css">
    <link rel="icon" type="image/png" href="/TimeForge_Capstone/icons/logo.png">
</head>
<body>
<?php include __DIR__ . '/../includes/header_partial.php'; ?>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
        <h1 class="heading-serif" style="color:var(--color-accent); margin:0;">Financial Reports</h1>
        <a href="/TimeForge_Capstone/admin/dashboard.php" class="btn btn-secondary">&larr; Dashboard</a>
    </div>

    <?php if ($flash): ?>
        <div class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>" style="margin-bottom:1rem;">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>

    <!-- Filter bar -->
    <div class="card report-filters">
        <form method="get" action="" style="display:flex; gap:1rem; flex-wrap:wrap; align-items:flex-end;">
            <div class="form-group" style="margin:0; min-width:160px;">
                <label for="client_id">Client</label>
                <select name="client_id" id="client_id" class="form-control">
                    <option value="">All Clients</option>
                    <?php foreach ($client_list as $cl): ?>
                        <option value="<?php echo $cl['id']; ?>" <?php if ($filter_client == $cl['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($cl['client_name']); ?>
                            <?php if ($cl['company_name']): ?>(<?php echo htmlspecialchars($cl['company_name']); ?>)<?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label for="date_from">From</label>
                <input type="date" id="date_from" name="date_from" class="form-control" value="<?php echo htmlspecialchars($filter_date_from); ?>">
            </div>
            <div class="form-group" style="margin:0;">
                <label for="date_to">To</label>
                <input type="date" id="date_to" name="date_to" class="form-control" value="<?php echo htmlspecialchars($filter_date_to); ?>">
            </div>
            <div style="display:flex; gap:0.5rem;">
                <button type="submit" class="btn btn-primary">Apply Filter</button>
                <a href="/TimeForge_Capstone/admin/reports.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <!-- Summary stat cards -->
    <div class="report-stat-grid">
        <div class="card report-stat">
            <div class="stat-label">Total Approved Hours</div>
            <div class="stat-value"><?php echo number_format($total_hours, 1); ?> hrs</div>
        </div>
        <div class="card report-stat">
            <div class="stat-label">Total Billed</div>
            <div class="stat-value">$<?php echo number_format($total_billed, 2); ?></div>
        </div>
        <div class="card report-stat">
            <div class="stat-label">Total Budget (active projects)</div>
            <div class="stat-value">$<?php echo number_format($total_budget, 2); ?></div>
        </div>
        <div class="card report-stat">
            <div class="stat-label">Projects in Range</div>
            <div class="stat-value"><?php echo (int)$summary['project_count']; ?></div>
        </div>
    </div>

    <!-- Per-project table -->
    <div class="card" style="padding:0; overflow:hidden; margin-bottom:2rem;">
        <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--color-border);">
            <h2 style="margin:0; font-size:1.1rem;">Project Profitability</h2>
        </div>
        <table class="project-table" style="margin:0;">
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Client</th>
                    <th style="text-align:right;">Rate/hr</th>
                    <th style="text-align:right;">Approved Hours</th>
                    <th style="text-align:right;">Billed</th>
                    <th style="text-align:right;">Budget</th>
                    <th style="text-align:right;">Margin</th>
                    <th>Status</th>
                    <th style="text-align:center;">Export</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($projects)): ?>
                <tr><td colspan="9" style="text-align:center; padding:1.5rem; color:var(--color-text-secondary);">No data for the selected filters.</td></tr>
                <?php else: ?>
                <?php foreach ($projects as $p):
                    $p_hours   = round(($p['approved_seconds'] ?? 0) / 3600, 2);
                    $p_billed  = round($p['billed_amount'] ?? 0, 2);
                    $p_budget  = $p['budget'] ? (float)$p['budget'] : null;
                    $margin    = $p_budget ? round((($p_budget - $p_billed) / $p_budget) * 100, 1) : null;
                    $margin_color = '#6b7280';
                    if ($margin !== null) {
                        $margin_color = $margin >= 20 ? '#16a34a' : ($margin >= 0 ? '#d97706' : '#dc2626');
                    }
                ?>
                <tr>
                    <td>
                        <a href="/TimeForge_Capstone/project_details.php?id=<?php echo $p['id']; ?>">
                            <?php echo htmlspecialchars($p['project_name']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($p['client_name']); ?></td>
                    <td style="text-align:right;">$<?php echo number_format($p['hourly_rate'], 2); ?></td>
                    <td style="text-align:right;"><?php echo number_format($p_hours, 2); ?></td>
                    <td style="text-align:right; font-weight:600;">$<?php echo number_format($p_billed, 2); ?></td>
                    <td style="text-align:right;"><?php echo $p_budget !== null ? '$' . number_format($p_budget, 2) : '—'; ?></td>
                    <td style="text-align:right; color:<?php echo $margin_color; ?>; font-weight:600;">
                        <?php echo $margin !== null ? $margin . '%' : '—'; ?>
                    </td>
                    <td>
                        <span style="font-size:0.8rem; color:<?php echo $p['status'] === 'active' ? '#16a34a' : '#6b7280'; ?>; font-weight:600;">
                            <?php echo ucfirst($p['status']); ?>
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <a href="/TimeForge_Capstone/api/export_csv.php?project_id=<?php echo $p['id']; ?>" class="btn btn-secondary" style="padding:4px 10px; font-size:0.8rem;" title="Export CSV for this project">CSV</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Per-freelancer table -->
    <div class="card" style="padding:0; overflow:hidden;">
        <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--color-border);">
            <h2 style="margin:0; font-size:1.1rem;">Freelancer Earnings</h2>
        </div>
        <table class="project-table" style="margin:0;">
            <thead>
                <tr>
                    <th>Freelancer</th>
                    <th>Email</th>
                    <th style="text-align:right;">Entries</th>
                    <th style="text-align:right;">Total Hours</th>
                    <th style="text-align:right;">Total Earned</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($freelancers)): ?>
                <tr><td colspan="5" style="text-align:center; padding:1.5rem; color:var(--color-text-secondary);">No freelancer data for the selected filters.</td></tr>
                <?php else: ?>
                <?php foreach ($freelancers as $fl):
                    $fl_hours = round(($fl['total_seconds'] ?? 0) / 3600, 2);
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($fl['full_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($fl['email']); ?></td>
                    <td style="text-align:right;"><?php echo (int)$fl['entry_count']; ?></td>
                    <td style="text-align:right;"><?php echo number_format($fl_hours, 2); ?> hrs</td>
                    <td style="text-align:right; font-weight:600;">$<?php echo number_format($fl['total_earned'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- Phase 10: React Analytics Charts -->
<div class="container" style="margin-top: 2rem;">
    <h2 style="color: var(--color-accent); margin-bottom: 1.5rem;">📊 Analytics Charts</h2>
    <div id="react-hours-chart"></div>
    <div id="react-project-chart"></div>
    <div id="react-heatmap"></div>
</div>

<?php include __DIR__ . '/../includes/footer_partial.php'; ?>
<script src="/TimeForge_Capstone/js/theme.js"></script>
<!-- Phase 10: React bundle -->
<script type="module" src="/TimeForge_Capstone/public/assets/react/app.js"></script>
</body>
</html>
