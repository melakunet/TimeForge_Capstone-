<?php
/*
 * Invoice Generator
 * Builds the invoice form pre-populated with approved time entries for a project.
 * On POST writes to the invoices table and redirects to view.php.
 */

$page_title = 'Generate Invoice';

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../config/settings.php';

requireRole('admin');

$project_id = filter_input(INPUT_GET, 'project_id', FILTER_VALIDATE_INT);

if (!$project_id) {
    setFlash('error', 'No project selected.');
    header('Location: /TimeForge_Capstone/index.php');
    exit;
}

// Fetch project with client info — we need name, address, rate, etc.
$proj_stmt = $pdo->prepare("
    SELECT
        p.id,
        p.project_name,
        p.description,
        p.hourly_rate,
        p.tax_rate,
        p.budget,
        c.id          AS client_id,
        c.client_name,
        c.company_name,
        c.email       AS client_email,
        c.address     AS client_address,
        c.phone       AS client_phone
    FROM projects p
    INNER JOIN clients c ON c.id = p.client_id
    WHERE p.id = :id
      AND p.deleted_at IS NULL
    LIMIT 1
");
$proj_stmt->execute([':id' => $project_id]);
$project = $proj_stmt->fetch();

if (!$project) {
    setFlash('error', 'Project not found.');
    header('Location: /TimeForge_Capstone/index.php');
    exit;
}

// All approved, billable time entries for this project
$entries_stmt = $pdo->prepare("
    SELECT
        te.id,
        te.start_time,
        te.end_time,
        te.total_seconds,
        te.description,
        u.full_name AS freelancer_name
    FROM time_entries te
    LEFT JOIN users u ON u.id = te.user_id
    WHERE te.project_id = :project_id
      AND te.status     = 'approved'
      AND te.is_billable = 1
      AND te.end_time   IS NOT NULL
    ORDER BY te.start_time ASC
");
$entries_stmt->execute([':project_id' => $project_id]);
$entries = $entries_stmt->fetchAll();

if (empty($entries)) {
    setFlash('error', 'No approved billable time entries found for this project. Approve time entries before generating an invoice.');
    header('Location: /TimeForge_Capstone/project_details.php?id=' . $project_id);
    exit;
}

// Compute line-item totals so the form can show a live preview
$hourly_rate = (float)$project['hourly_rate'];
$subtotal    = 0.0;
foreach ($entries as &$e) {
    $hours          = ($e['total_seconds'] ?? 0) / 3600;
    $e['hours']     = round($hours, 2);
    $e['line_cost'] = round($hours * $hourly_rate, 2);
    $subtotal      += $e['line_cost'];
}
unset($e);

$company_id = (int)$_SESSION['company_id'];
$_co_settings = getCompanySettings($pdo, $company_id);

$tax_rate   = (float)($project['tax_rate'] ?? $_co_settings['invoice_tax_rate'] ?? 0);
$tax_amount = round($subtotal * ($tax_rate / 100), 2);
$total      = round($subtotal + $tax_amount, 2);

// Auto-generate invoice number: INV-YYYYMM-PPPP (project id padded)
// If that number is already taken, keep incrementing the suffix until we find a free slot.
$invoice_number_base = 'INV-' . date('Ym') . '-' . str_pad($project_id, 4, '0', STR_PAD_LEFT);
$invoice_number_default = $invoice_number_base;
$suffix = 1;
while (true) {
    $chk = $pdo->prepare("SELECT id FROM invoices WHERE invoice_number = ? LIMIT 1");
    $chk->execute([$invoice_number_default]);
    if (!$chk->fetch()) break;           // number is free — use it
    $suffix++;
    $invoice_number_default = $invoice_number_base . '-R' . $suffix;  // e.g. INV-202603-0004-R2
}

// If an invoice already exists for this project, offer to view it instead of regenerating
$existing_invoices = $pdo->prepare("
    SELECT id, invoice_number, status, created_at
    FROM invoices WHERE project_id = ? ORDER BY created_at DESC
");
$existing_invoices->execute([$project_id]);
$project_invoices = $existing_invoices->fetchAll();

// Pre-select the template passed from the project_details pre-flight modal (?tpl=)
$allowed_tpls_get = ['classic', 'modern', 'bold', 'minimal', 'corporate'];
$tpl_preselect    = in_array($_GET['tpl'] ?? '', $allowed_tpls_get) ? $_GET['tpl'] : 'classic';

// ── Handle form submission ────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoice_number = trim($_POST['invoice_number'] ?? '');
    $issue_date     = trim($_POST['issue_date']     ?? '');
    $due_date       = trim($_POST['due_date']       ?? '');
    $tax_rate_post  = (float)($_POST['tax_rate']    ?? 0);
    $notes          = trim($_POST['notes']          ?? '');
    $allowed_tpls   = ['classic', 'modern', 'bold', 'minimal', 'corporate'];
    $template_post  = in_array($_POST['template'] ?? '', $allowed_tpls)
                      ? $_POST['template'] : 'classic';

    // Recalculate with the submitted tax rate
    $tax_amount_post = round($subtotal * ($tax_rate_post / 100), 2);
    $total_post      = round($subtotal + $tax_amount_post, 2);

    // Basic validation — all required fields must be present
    if (!$invoice_number || !$issue_date || !$due_date) {
        setFlash('error', 'Invoice number, issue date, and due date are required.');
    } else {
        try {
            $insert = $pdo->prepare("
                INSERT INTO invoices
                    (project_id, client_id, invoice_number, issue_date, due_date,
                     tax_rate, subtotal, tax_amount, total_amount, notes, status, template, created_by)
                VALUES
                    (:project_id, :client_id, :invoice_number, :issue_date, :due_date,
                     :tax_rate, :subtotal, :tax_amount, :total_amount, :notes, 'draft', :template, :created_by)
            ");
            $insert->execute([
                ':project_id'     => $project_id,
                ':client_id'      => $project['client_id'],
                ':invoice_number' => $invoice_number,
                ':issue_date'     => $issue_date,
                ':due_date'       => $due_date,
                ':tax_rate'       => $tax_rate_post,
                ':subtotal'       => $subtotal,
                ':tax_amount'     => $tax_amount_post,
                ':total_amount'   => $total_post,
                ':notes'          => $notes ?: null,
                ':template'       => $template_post,
                ':created_by'     => $_SESSION['user_id'],
            ]);
            $invoice_id = $pdo->lastInsertId();
            setFlash('success', 'Invoice #' . htmlspecialchars($invoice_number) . ' created successfully.');
            header('Location: /TimeForge_Capstone/invoices/view.php?id=' . $invoice_id);
            exit;
        } catch (PDOException $e) {
            // Duplicate invoice number is the most likely constraint violation
            if ($e->getCode() === '23000') {
                setFlash('error', 'Invoice number already exists. Choose a different one.');
            } else {
                error_log('Invoice insert error: ' . $e->getMessage());
                setFlash('error', 'Database error. Could not save invoice.');
            }
        }
    }
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
    <link rel="stylesheet" href="/TimeForge_Capstone/css/invoice.css">
    <link rel="icon" type="image/png" href="/TimeForge_Capstone/icons/logo.png">
</head>
<body>
<?php include __DIR__ . '/../includes/header_partial.php'; ?>

<div class="container">
    <div style="margin-bottom:1.5rem;">
        <a href="/TimeForge_Capstone/project_details.php?id=<?php echo $project_id; ?>" class="btn btn-secondary">&larr; Back to Project</a>
    </div>

    <h1 class="heading-serif" style="color:var(--color-accent); margin-bottom:0.25rem;">Generate Invoice</h1>
    <p style="color:var(--color-text-secondary); margin-bottom:2rem;">
        <?php echo htmlspecialchars($project['project_name']); ?> &mdash;
        <?php echo htmlspecialchars($project['client_name']); ?>
        <?php if ($project['company_name']): ?>
            (<?php echo htmlspecialchars($project['company_name']); ?>)
        <?php endif; ?>
    </p>

    <?php if ($flash): ?>
        <div class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>" style="margin-bottom:1rem;">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($project_invoices)): ?>
    <!-- Existing invoices notice — shown whenever 1+ invoices already exist for this project -->
    <div style="background:rgba(37,99,235,0.07); border:1px solid #93c5fd; border-radius:8px;
                padding:1rem 1.25rem; margin-bottom:1.5rem;">
        <div style="font-weight:700; margin-bottom:0.5rem; color:var(--color-accent);">
            ⚠ This project already has <?php echo count($project_invoices); ?> invoice<?php echo count($project_invoices)>1?'s':''; ?>:
        </div>
        <div style="display:flex; flex-wrap:wrap; gap:0.5rem; align-items:center;">
            <?php foreach ($project_invoices as $pi): ?>
            <a href="/TimeForge_Capstone/invoices/view.php?id=<?php echo $pi['id']; ?>"
               class="btn btn-secondary" style="font-size:0.82rem; padding:4px 12px;">
                <?php echo htmlspecialchars($pi['invoice_number']); ?>
                <span style="opacity:0.7; font-weight:400;">(<?php echo ucfirst($pi['status']); ?>)</span>
            </a>
            <?php endforeach; ?>
            <span style="font-size:0.82rem; color:var(--color-text-secondary);">
                — or fill the form below to generate a new one.
            </span>
        </div>
    </div>
    <?php endif; ?>

    <div class="invoice-gen-grid">
        <!-- Left: form -->
        <div class="card">
            <h2 style="margin-bottom:1.5rem;">Invoice Details</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="invoice_number">Invoice Number</label>
                    <input type="text" id="invoice_number" name="invoice_number"
                           value="<?php echo htmlspecialchars($_POST['invoice_number'] ?? $invoice_number_default); ?>"
                           required class="form-control">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="issue_date">Issue Date</label>
                        <input type="date" id="issue_date" name="issue_date"
                               value="<?php echo htmlspecialchars($_POST['issue_date'] ?? date('Y-m-d')); ?>"
                               required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="due_date">Due Date</label>
                        <input type="date" id="due_date" name="due_date"
                               value="<?php echo htmlspecialchars($_POST['due_date'] ?? date('Y-m-d', strtotime('+30 days'))); ?>"
                               required class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="tax_rate">Tax Rate (%)</label>
                    <input type="number" id="tax_rate" name="tax_rate" min="0" max="100" step="0.01"
                           value="<?php echo htmlspecialchars($_POST['tax_rate'] ?? $tax_rate); ?>"
                           class="form-control" id="tax_rate_input">
                    <small>Set to 0 for no tax.</small>
                </div>
                <div class="form-group">
                    <label for="notes">Notes (optional)</label>
                    <textarea id="notes" name="notes" rows="3" class="form-control"><?php echo htmlspecialchars($_POST['notes'] ?? $_co_settings['invoice_footer_note'] ?? ''); ?></textarea>
                </div>

                <!-- ── Template Picker ─────────────────────────── -->
                <div class="form-group">
                    <label style="display:block; margin-bottom:0.75rem;">Invoice Template</label>
                    <div class="tpl-picker">
                        <?php
                        $templates = [
                            'classic'   => ['name' => 'Classic',   'color' => '#14532d', 'desc' => 'Clean green header'],
                            'modern'    => ['name' => 'Modern',    'color' => '#1e3a5f', 'desc' => 'Navy with gradient stripe'],
                            'bold'      => ['name' => 'Bold',      'color' => '#1f2937', 'desc' => 'Charcoal & amber accents'],
                            'minimal'   => ['name' => 'Minimal',   'color' => '#374151', 'desc' => 'Typographic, no color bands'],
                            'corporate' => ['name' => 'Corporate', 'color' => '#4c1d95', 'desc' => 'Deep purple, two-column'],
                        ];
                        $selected_tpl = $_POST['template'] ?? $tpl_preselect;
                        foreach ($templates as $key => $t):
                        ?>
                        <label class="tpl-card <?php echo $selected_tpl === $key ? 'tpl-card--active' : ''; ?>" for="tpl_<?php echo $key; ?>">
                            <input type="radio" name="template" id="tpl_<?php echo $key; ?>"
                                   value="<?php echo $key; ?>"
                                   <?php echo $selected_tpl === $key ? 'checked' : ''; ?>
                                   onchange="this.closest('.tpl-picker').querySelectorAll('.tpl-card').forEach(function(c){c.classList.remove('tpl-card--active')}); this.closest('.tpl-card').classList.add('tpl-card--active');">
                            <span class="tpl-swatch" style="background:<?php echo $t['color']; ?>;"></span>
                            <span class="tpl-card-name"><?php echo $t['name']; ?></span>
                            <span class="tpl-card-desc"><?php echo $t['desc']; ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- ── /Template Picker ────────────────────────── -->

                <button type="submit" class="btn btn-primary" style="width:100%;">Save Invoice as Draft</button>
            </form>
        </div>

        <!-- Right: live line-item preview -->
        <div class="card">
            <h2 style="margin-bottom:1.5rem;">Line Items Preview</h2>
            <p style="font-size:0.85rem; color:var(--color-text-secondary); margin-bottom:1rem;">
                Rate: $<?php echo number_format($hourly_rate, 2); ?>/hr &nbsp;&bull;&nbsp;
                <?php echo count($entries); ?> approved entr<?php echo count($entries) === 1 ? 'y' : 'ies'; ?>
            </p>
            <table class="inv-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Freelancer</th>
                        <th>Description</th>
                        <th class="num">Hours</th>
                        <th class="num">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entries as $e): ?>
                    <tr>
                        <td><?php echo date('M j, Y', strtotime($e['start_time'])); ?></td>
                        <td><?php echo htmlspecialchars($e['freelancer_name'] ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($e['description'] ?? '—'); ?></td>
                        <td class="num"><?php echo $e['hours']; ?></td>
                        <td class="num">$<?php echo number_format($e['line_cost'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="num"><strong>Subtotal</strong></td>
                        <td class="num"><strong>$<?php echo number_format($subtotal, 2); ?></strong></td>
                    </tr>
                    <tr id="tax-preview-row">
                        <td colspan="4" class="num">Tax (<span id="tax-pct"><?php echo $tax_rate; ?></span>%)</td>
                        <td class="num" id="tax-preview-amt">$<?php echo number_format($tax_amount, 2); ?></td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="4" class="num"><strong>Total</strong></td>
                        <td class="num" id="total-preview"><strong>$<?php echo number_format($total, 2); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer_partial.php'; ?>
<script src="/TimeForge_Capstone/js/theme.js"></script>
<script>
// Live tax recalculation — updates the preview tfoot when the tax rate input changes
(function () {
    var subtotal = <?php echo $subtotal; ?>;
    var rateInput = document.getElementById('tax_rate');

    function recalc() {
        var rate = parseFloat(rateInput.value) || 0;
        var tax  = Math.round(subtotal * (rate / 100) * 100) / 100;
        var tot  = Math.round((subtotal + tax) * 100) / 100;

        document.getElementById('tax-pct').textContent       = rate.toFixed(2);
        document.getElementById('tax-preview-amt').textContent = '$' + tot.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        document.getElementById('total-preview').innerHTML   = '<strong>$' + tot.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',') + '</strong>';
    }

    rateInput.addEventListener('input', recalc);
})();
</script>
</body>
</html>
