<?php
$page_title = 'View Invoice';

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../db.php';

if (!isLoggedIn()) {
    header('Location: /TimeForge_Capstone/login.php');
    exit;
}

$invoice_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$invoice_id) {
    setFlash('error', 'Invalid invoice ID.');
    header('Location: /TimeForge_Capstone/invoices/history.php');
    exit;
}

// Fetch the invoice with project and client data
try {
    $inv_stmt = $pdo->prepare("
        SELECT
            inv.*,
            p.project_name,
            p.description  AS project_description,
            p.hourly_rate,
            c.client_name,
            c.company_name,
            c.email        AS client_email,
            c.address      AS client_address,
            c.phone        AS client_phone,
            c.user_id      AS client_user_id,
            u.full_name    AS created_by_name
        FROM invoices inv
        INNER JOIN projects p ON p.id = inv.project_id
        INNER JOIN clients  c ON c.id = inv.client_id
        INNER JOIN users    u ON u.id = inv.created_by
        WHERE inv.id = :id
        LIMIT 1
    ");
    $inv_stmt->execute([':id' => $invoice_id]);
    $invoice = $inv_stmt->fetch();
} catch (PDOException $e) {
    error_log('view.php invoice fetch error: ' . $e->getMessage());
    setFlash('error', 'Could not load invoice. Please try again.');
    header('Location: /TimeForge_Capstone/invoices/history.php');
    exit;
}

if (!$invoice) {
    setFlash('error', 'Invoice not found.');
    header('Location: /TimeForge_Capstone/invoices/history.php');
    exit;
}

// Clients can only view their own invoices
if (hasRole('client') && $invoice['client_user_id'] != $_SESSION['user_id']) {
    setFlash('error', 'Access denied.');
    header('Location: /TimeForge_Capstone/client/dashboard.php');
    exit;
}

// Approved billable time entries that belong to this invoice's project
try {
    $entries_stmt = $pdo->prepare("
        SELECT
            te.start_time,
            te.end_time,
            te.total_seconds,
            te.description,
            u.full_name AS freelancer_name
        FROM time_entries te
        LEFT JOIN users u ON u.id = te.user_id
        WHERE te.project_id = :project_id
          AND te.status      = 'approved'
          AND te.is_billable = 1
          AND te.end_time   IS NOT NULL
        ORDER BY te.start_time ASC
    ");
    $entries_stmt->execute([':project_id' => $invoice['project_id']]);
    $entries = $entries_stmt->fetchAll();
} catch (PDOException $e) {
    error_log('view.php entries fetch error: ' . $e->getMessage());
    $entries = [];
}

$hourly_rate = (float)$invoice['hourly_rate'];

foreach ($entries as &$e) {
    $e['hours']     = round(($e['total_seconds'] ?? 0) / 3600, 2);
    $e['line_cost'] = round($e['hours'] * $hourly_rate, 2);
}
unset($e);

// Handle admin template change (still handled inline — fast round-trip)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && hasRole('admin') &&
    isset($_POST['action']) && $_POST['action'] === 'change_template') {
    $new_tpl = $_POST['template'] ?? '';
    $allowed_templates_post = ['classic', 'modern', 'bold', 'minimal', 'corporate'];
    if (in_array($new_tpl, $allowed_templates_post)) {
        try {
            $pdo->prepare("UPDATE invoices SET template = :tpl WHERE id = :id")
                ->execute([':tpl' => $new_tpl, ':id' => $invoice_id]);
            setFlash('success', 'Template changed to ' . ucfirst($new_tpl) . '.');
        } catch (PDOException $e) {
            error_log('view.php template update error: ' . $e->getMessage());
            setFlash('error', 'Could not change template.');
        }
    }
    header('Location: /TimeForge_Capstone/invoices/view.php?id=' . $invoice_id);
    exit;
}

// Auto-mark overdue: if sent/viewed and past due_date, flip to overdue
if (in_array($invoice['status'], ['sent', 'viewed']) &&
    strtotime($invoice['due_date']) < strtotime('today')) {
    try {
        $pdo->prepare("UPDATE invoices SET status = 'overdue' WHERE id = :id")
            ->execute([':id' => $invoice_id]);
        $invoice['status'] = 'overdue';
    } catch (PDOException $e) {
        error_log('view.php auto-overdue: ' . $e->getMessage());
    }
}

$flash = getFlash();

// ── Status metadata ──────────────────────────────────────────────────────
$status_labels = [
    'draft'     => 'Draft',
    'sent'      => 'Sent',
    'viewed'    => 'Viewed',
    'overdue'   => 'Overdue',
    'partial'   => 'Partial',
    'paid'      => 'Paid',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled',
];
$status_colors = [
    'draft'     => '#6b7280',
    'sent'      => '#2563eb',
    'viewed'    => '#7c3aed',
    'overdue'   => '#dc2626',
    'partial'   => '#d97706',
    'paid'      => '#16a34a',
    'completed' => '#0f766e',
    'cancelled' => '#374151',
];
$status_label = $status_labels[$invoice['status']] ?? 'Draft';
$status_color = $status_colors[$invoice['status']] ?? '#6b7280';

// Days until/since due
$days_diff = (int)round((strtotime($invoice['due_date']) - strtotime('today')) / 86400);
$company_name  = $invoice['company_name'] ?? '';   // client's company name shown in template

// Resolve which template file to load — fall back to classic if unknown
$allowed_templates = ['classic', 'modern', 'bold', 'minimal', 'corporate'];
$tpl = in_array($invoice['template'] ?? '', $allowed_templates)
     ? $invoice['template']
     : 'classic';
$template_file = __DIR__ . '/templates/' . $tpl . '.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?php echo htmlspecialchars($invoice['invoice_number']); ?> — TimeForge</title>
    <link rel="stylesheet" href="/TimeForge_Capstone/css/style.css">
    <link rel="stylesheet" href="/TimeForge_Capstone/css/invoice.css">
    <link rel="icon" type="image/png" href="/TimeForge_Capstone/icons/logo.png">
</head>
<body>
<?php include __DIR__ . '/../includes/header_partial.php'; ?>

<div class="container">
    <?php if ($flash): ?>
        <div class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>" style="margin-bottom:1rem;">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>

    <!-- ═══════════════════════════════════════════════════════════════════
         TOP TOOLBAR — navigation, template switcher, download
         Hidden when printing.
    ════════════════════════════════════════════════════════════════════ -->
    <div class="inv-toolbar no-print">
        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            <a href="/TimeForge_Capstone/invoices/history.php" class="btn btn-secondary">&larr; All Invoices</a>
            <?php if (hasRole('admin')): ?>
                <a href="/TimeForge_Capstone/project_details.php?id=<?php echo $invoice['project_id']; ?>"
                   class="btn btn-secondary">Project Details</a>
            <?php endif; ?>
        </div>
        <div style="display:flex; gap:0.75rem; align-items:center; flex-wrap:wrap;">
            <?php if (hasRole('admin')): ?>
            <!-- Template switcher -->
            <div style="display:flex; align-items:center; gap:0.4rem;">
                <span style="font-size:0.78rem; color:var(--color-text-secondary); white-space:nowrap;">Template:</span>
                <?php
                $tpl_swatches = [
                    'classic'   => ['label'=>'Classic',   'color'=>'#14532d'],
                    'modern'    => ['label'=>'Modern',    'color'=>'#1e40af'],
                    'bold'      => ['label'=>'Bold',      'color'=>'#7c2d12'],
                    'minimal'   => ['label'=>'Minimal',   'color'=>'#374151'],
                    'corporate' => ['label'=>'Corporate', 'color'=>'#0f172a'],
                ];
                foreach ($tpl_swatches as $key => $sw): ?>
                <form method="post" action="" style="display:inline;">
                    <input type="hidden" name="action"   value="change_template">
                    <input type="hidden" name="template" value="<?php echo $key; ?>">
                    <button type="submit" title="<?php echo $sw['label']; ?>" style="
                        width:22px; height:22px; border-radius:50%; cursor:pointer; padding:0;
                        border: <?php echo $tpl === $key ? '3px solid #000' : '2px solid #e5e7eb'; ?>;
                        background: <?php echo $sw['color']; ?>;
                        box-shadow: <?php echo $tpl === $key ? '0 0 0 2px #fff inset' : 'none'; ?>;
                        vertical-align: middle;
                    "></button>
                </form>
                <?php endforeach; ?>
                <span style="font-size:0.78rem; font-weight:600; color:var(--color-text-secondary);"><?php echo ucfirst($tpl); ?></span>
            </div>
            <?php else: ?>
            <span style="font-size:0.8rem; color:var(--color-text-secondary);">
                Template: <strong><?php echo ucfirst($tpl); ?></strong>
            </span>
            <?php endif; ?>

            <a href="/TimeForge_Capstone/invoices/download.php?id=<?php echo $invoice_id; ?>"
               class="btn btn-primary" target="_blank" rel="noopener">⬇ Download PDF</a>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════
         PAYMENT STATUS PANEL — lifecycle stepper + action forms
         Shown to admin (full controls) and client (feedback only).
    ════════════════════════════════════════════════════════════════════ -->
    <?php
    $cur = $invoice['status'];
    $steps = ['draft','sent','viewed','partial','paid','completed'];
    // For overdue, we highlight between sent→paid on the stepper with a red marker
    $step_labels = [
        'draft'     => ['icon'=>'📝', 'label'=>'Draft'],
        'sent'      => ['icon'=>'📤', 'label'=>'Sent'],
        'viewed'    => ['icon'=>'👁',  'label'=>'Viewed'],
        'partial'   => ['icon'=>'💰', 'label'=>'Partial'],
        'paid'      => ['icon'=>'✅', 'label'=>'Paid'],
        'completed' => ['icon'=>'🏁', 'label'=>'Done'],
    ];
    $step_index = array_search(in_array($cur, ['overdue','cancelled']) ? 'sent' : $cur, $steps);
    if ($step_index === false) $step_index = 0;
    ?>
    <div class="pmt-panel no-print">

        <!-- ── Lifecycle stepper ── -->
        <div class="pmt-stepper">
            <?php foreach ($steps as $i => $s):
                $is_done   = $i < $step_index;
                $is_active = ($i === $step_index && !in_array($cur, ['overdue','cancelled']));
                $sl = $step_labels[$s];
            ?>
            <div class="pmt-step <?php echo $is_done ? 'done' : ($is_active ? 'active' : ''); ?>">
                <div class="pmt-step-dot"><?php echo $sl['icon']; ?></div>
                <div class="pmt-step-label"><?php echo $sl['label']; ?></div>
            </div>
            <?php if ($i < count($steps)-1): ?>
            <div class="pmt-step-line <?php echo $is_done ? 'done' : ''; ?>"></div>
            <?php endif; ?>
            <?php endforeach; ?>

            <?php if ($cur === 'overdue'): ?>
            <div class="pmt-overdue-flag">⚠ Overdue</div>
            <?php endif; ?>
            <?php if ($cur === 'cancelled'): ?>
            <div class="pmt-cancelled-flag">✕ Cancelled</div>
            <?php endif; ?>
        </div>

        <!-- ── Status badge + due-date reminder ── -->
        <div class="pmt-meta-row">
            <span class="pmt-badge" style="background:<?php echo $status_color; ?>;">
                <?php echo $status_label; ?>
            </span>
            <?php if (!in_array($cur, ['paid','completed','cancelled'])): ?>
                <?php if ($days_diff > 0): ?>
                    <span class="pmt-due-chip pmt-due-ok">Due in <?php echo $days_diff; ?> day<?php echo $days_diff===1?'':'s'; ?></span>
                <?php elseif ($days_diff === 0): ?>
                    <span class="pmt-due-chip pmt-due-today">Due today!</span>
                <?php else: ?>
                    <span class="pmt-due-chip pmt-due-late"><?php echo abs($days_diff); ?> day<?php echo abs($days_diff)===1?'':'s'; ?> overdue</span>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($invoice['paid_at']): ?>
                <span class="pmt-due-chip pmt-due-ok">Paid <?php echo date('M j, Y', strtotime($invoice['paid_at'])); ?></span>
            <?php endif; ?>
            <?php if ($invoice['partial_amount']): ?>
                <span class="pmt-due-chip" style="background:rgba(217,119,6,0.1);color:#92400e;border-color:#fcd34d;">
                    Partial: $<?php echo number_format($invoice['partial_amount'],2); ?> of $<?php echo number_format($invoice['total_amount'],2); ?>
                </span>
            <?php endif; ?>
            <?php if ($invoice['payment_method']): ?>
                <span class="pmt-due-chip" style="background:rgba(15,118,110,0.08);color:#0f766e;border-color:#5eead4;">
                    <?php echo htmlspecialchars($invoice['payment_method']); ?>
                    <?php if ($invoice['payment_reference']): ?>
                        · <?php echo htmlspecialchars($invoice['payment_reference']); ?>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
        </div>

        <?php if (hasRole('admin') && !in_array($cur, ['completed','cancelled'])): ?>
        <!-- ── Admin action buttons ── -->
        <div class="pmt-actions">

            <?php if ($cur === 'draft'): ?>
            <!-- DRAFT → SENT -->
            <button class="btn btn-primary pmt-btn" onclick="pmt_show('form-sent')">📤 Mark as Sent</button>

            <?php elseif (in_array($cur, ['sent','overdue'])): ?>
            <!-- SENT/OVERDUE → next steps -->
            <button class="btn btn-secondary pmt-btn" onclick="pmt_show('form-viewed')">👁 Mark Viewed</button>
            <button class="btn pmt-btn pmt-btn-warn" onclick="pmt_show('form-partial')">💰 Record Partial</button>
            <button class="btn btn-primary pmt-btn" onclick="pmt_show('form-paid')">✅ Mark Paid</button>
            <?php if ($cur !== 'overdue'): ?>
            <button class="btn btn-secondary pmt-btn pmt-btn-red" onclick="pmt_show('form-overdue')">⚠ Mark Overdue</button>
            <?php endif; ?>

            <?php elseif ($cur === 'viewed'): ?>
            <!-- VIEWED → next -->
            <button class="btn pmt-btn pmt-btn-warn" onclick="pmt_show('form-partial')">💰 Record Partial</button>
            <button class="btn btn-primary pmt-btn" onclick="pmt_show('form-paid')">✅ Mark Paid</button>
            <button class="btn btn-secondary pmt-btn pmt-btn-red" onclick="pmt_show('form-overdue')">⚠ Mark Overdue</button>

            <?php elseif ($cur === 'partial'): ?>
            <!-- PARTIAL → paid -->
            <span class="pmt-hint">Remaining: <strong>$<?php echo number_format($invoice['total_amount'] - ($invoice['partial_amount']??0), 2); ?></strong></span>
            <button class="btn btn-primary pmt-btn" onclick="pmt_show('form-paid')">✅ Mark Fully Paid</button>

            <?php elseif ($cur === 'paid'): ?>
            <!-- PAID → completed -->
            <button class="btn btn-primary pmt-btn" onclick="pmt_show('form-completed')">🏁 Mark Completed</button>
            <?php endif; ?>

            <!-- Always available: notes + cancel -->
            <button class="btn btn-secondary pmt-btn" onclick="pmt_show('form-notes')">📝 Notes</button>
            <button class="btn btn-secondary pmt-btn pmt-btn-red" onclick="pmt_show('form-cancel')">✕ Cancel Invoice</button>
        </div>

        <!-- ── Inline action forms (shown one at a time via JS) ── -->
        <?php
        $act_url = '/TimeForge_Capstone/invoices/payment_action.php';
        $hid     = '<input type="hidden" name="invoice_id" value="' . $invoice_id . '">';

        $method_options = ['', 'Bank Transfer', 'PayPal', 'Stripe', 'Cheque', 'Cash', 'Credit Card', 'Other'];
        $method_select  = '<select name="payment_method" class="form-control" style="flex:1;min-width:120px;"><option value="">— Method —</option>';
        foreach ($method_options as $m) {
            $sel = ($invoice['payment_method'] === $m) ? ' selected' : '';
            if ($m !== '') $method_select .= '<option value="' . htmlspecialchars($m) . '"' . $sel . '>' . htmlspecialchars($m) . '</option>';
        }
        $method_select .= '</select>';
        ?>

        <!-- SENT form -->
        <div id="form-sent" class="pmt-form" style="display:none;">
            <form method="post" action="<?php echo $act_url; ?>">
                <?php echo $hid; ?><input type="hidden" name="action" value="mark_sent">
                <div class="pmt-form-row">
                    <textarea name="payment_notes" class="form-control" placeholder="Optional: note to self about sending (e.g. emailed via Gmail)" rows="2" style="flex:1;"><?php echo htmlspecialchars($invoice['payment_notes']??''); ?></textarea>
                    <button type="submit" class="btn btn-primary">📤 Confirm Sent</button>
                    <button type="button" class="btn btn-secondary" onclick="pmt_hide_all()">Cancel</button>
                </div>
            </form>
        </div>

        <!-- VIEWED form -->
        <div id="form-viewed" class="pmt-form" style="display:none;">
            <form method="post" action="<?php echo $act_url; ?>">
                <?php echo $hid; ?><input type="hidden" name="action" value="mark_viewed">
                <div class="pmt-form-row">
                    <span class="pmt-hint">Record that the client has opened/seen this invoice.</span>
                    <button type="submit" class="btn btn-secondary">👁 Confirm Viewed</button>
                    <button type="button" class="btn btn-secondary" onclick="pmt_hide_all()">Cancel</button>
                </div>
            </form>
        </div>

        <!-- OVERDUE form -->
        <div id="form-overdue" class="pmt-form" style="display:none;">
            <form method="post" action="<?php echo $act_url; ?>">
                <?php echo $hid; ?><input type="hidden" name="action" value="mark_overdue">
                <div class="pmt-form-row">
                    <textarea name="payment_notes" class="form-control" placeholder="Optional: follow-up note (e.g. sent reminder email)" rows="2" style="flex:1;"><?php echo htmlspecialchars($invoice['payment_notes']??''); ?></textarea>
                    <button type="submit" class="btn pmt-btn-red">⚠ Mark Overdue</button>
                    <button type="button" class="btn btn-secondary" onclick="pmt_hide_all()">Cancel</button>
                </div>
            </form>
        </div>

        <!-- PARTIAL form -->
        <div id="form-partial" class="pmt-form" style="display:none;">
            <form method="post" action="<?php echo $act_url; ?>">
                <?php echo $hid; ?><input type="hidden" name="action" value="record_partial">
                <div class="pmt-form-grid">
                    <label>Partial amount received ($)</label>
                    <input type="number" name="partial_amount" class="form-control" step="0.01" min="0.01"
                           max="<?php echo $invoice['total_amount'] - 0.01; ?>"
                           value="<?php echo htmlspecialchars($invoice['partial_amount']??''); ?>"
                           placeholder="0.00" required>
                    <label>Payment method</label>
                    <?php echo $method_select; ?>
                    <label>Reference / Transaction ID</label>
                    <input type="text" name="payment_reference" class="form-control"
                           value="<?php echo htmlspecialchars($invoice['payment_reference']??''); ?>"
                           placeholder="TXN-12345">
                    <label>Notes</label>
                    <textarea name="payment_notes" class="form-control" rows="2"
                              placeholder="e.g. First instalment received"><?php echo htmlspecialchars($invoice['payment_notes']??''); ?></textarea>
                </div>
                <div class="pmt-form-row" style="margin-top:0.75rem;">
                    <button type="submit" class="btn pmt-btn-warn">💰 Record Partial</button>
                    <button type="button" class="btn btn-secondary" onclick="pmt_hide_all()">Cancel</button>
                </div>
            </form>
        </div>

        <!-- PAID form -->
        <div id="form-paid" class="pmt-form" style="display:none;">
            <form method="post" action="<?php echo $act_url; ?>">
                <?php echo $hid; ?><input type="hidden" name="action" value="mark_paid">
                <div class="pmt-form-grid">
                    <label>Payment method</label>
                    <?php echo $method_select; ?>
                    <label>Reference / Transaction ID</label>
                    <input type="text" name="payment_reference" class="form-control"
                           value="<?php echo htmlspecialchars($invoice['payment_reference']??''); ?>"
                           placeholder="TXN-12345">
                    <label>Notes</label>
                    <textarea name="payment_notes" class="form-control" rows="2"
                              placeholder="e.g. Full payment received via bank transfer"><?php echo htmlspecialchars($invoice['payment_notes']??''); ?></textarea>
                </div>
                <div class="pmt-form-row" style="margin-top:0.75rem;">
                    <button type="submit" class="btn btn-primary">✅ Confirm Paid</button>
                    <button type="button" class="btn btn-secondary" onclick="pmt_hide_all()">Cancel</button>
                </div>
            </form>
        </div>

        <!-- COMPLETED form -->
        <div id="form-completed" class="pmt-form" style="display:none;">
            <form method="post" action="<?php echo $act_url; ?>">
                <?php echo $hid; ?><input type="hidden" name="action" value="mark_completed">
                <div class="pmt-form-row">
                    <span class="pmt-hint">This archives the invoice as fully complete. No further status changes will be possible.</span>
                    <button type="submit" class="btn btn-primary">🏁 Yes, Complete</button>
                    <button type="button" class="btn btn-secondary" onclick="pmt_hide_all()">Cancel</button>
                </div>
            </form>
        </div>

        <!-- NOTES form -->
        <div id="form-notes" class="pmt-form" style="display:none;">
            <form method="post" action="<?php echo $act_url; ?>">
                <?php echo $hid; ?><input type="hidden" name="action" value="save_notes">
                <div class="pmt-form-grid">
                    <label>Payment method</label>
                    <?php echo $method_select; ?>
                    <label>Reference / Transaction ID</label>
                    <input type="text" name="payment_reference" class="form-control"
                           value="<?php echo htmlspecialchars($invoice['payment_reference']??''); ?>"
                           placeholder="TXN-12345">
                    <label>Follow-up notes</label>
                    <textarea name="payment_notes" class="form-control" rows="3"
                              placeholder="e.g. Reminder sent on March 27, waiting on client response..."><?php echo htmlspecialchars($invoice['payment_notes']??''); ?></textarea>
                </div>
                <div class="pmt-form-row" style="margin-top:0.75rem;">
                    <button type="submit" class="btn btn-primary">💾 Save Notes</button>
                    <button type="button" class="btn btn-secondary" onclick="pmt_hide_all()">Cancel</button>
                </div>
            </form>
        </div>

        <!-- CANCEL form -->
        <div id="form-cancel" class="pmt-form" style="display:none;">
            <form method="post" action="<?php echo $act_url; ?>">
                <?php echo $hid; ?><input type="hidden" name="action" value="mark_cancelled">
                <div class="pmt-form-row">
                    <textarea name="payment_notes" class="form-control" placeholder="Reason for cancellation" rows="2" style="flex:1;"></textarea>
                    <button type="submit" class="btn pmt-btn-red">✕ Yes, Cancel Invoice</button>
                    <button type="button" class="btn btn-secondary" onclick="pmt_hide_all()">Back</button>
                </div>
            </form>
        </div>

        <?php endif; /* admin actions */ ?>

        <!-- ── Client feedback section ── -->
        <?php if (hasRole('client') || hasRole('admin')): ?>
        <div class="pmt-feedback-wrap">
            <div class="pmt-feedback-label">Client Feedback / Dispute Notes</div>
            <?php if ($invoice['client_feedback']): ?>
                <blockquote class="pmt-feedback-text"><?php echo nl2br(htmlspecialchars($invoice['client_feedback'])); ?></blockquote>
            <?php endif; ?>
            <?php if (hasRole('client') || hasRole('admin')): ?>
            <form method="post" action="/TimeForge_Capstone/invoices/payment_action.php" style="margin-top:0.5rem;">
                <input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>">
                <input type="hidden" name="action" value="add_feedback">
                <textarea name="client_feedback" class="form-control" rows="2"
                          placeholder="Questions, disputes, or notes about this invoice..."
                          style="width:100%; margin-bottom:0.5rem;"></textarea>
                <button type="submit" class="btn btn-secondary" style="font-size:0.85rem;">💬 Submit Feedback</button>
            </form>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- ── Payment notes (admin-visible) ── -->
        <?php if (hasRole('admin') && $invoice['payment_notes']): ?>
        <div class="pmt-notes-display">
            <strong>📋 Follow-up Notes:</strong>
            <p><?php echo nl2br(htmlspecialchars($invoice['payment_notes'])); ?></p>
        </div>
        <?php endif; ?>
    </div><!-- /.pmt-panel -->

    <!-- Invoice document — rendered by the chosen template -->
    <div class="invoice-doc" id="invoice-print">
        <?php include $template_file; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer_partial.php'; ?>
<script src="/TimeForge_Capstone/js/theme.js"></script>
<script>
// Payment panel: show one form at a time, collapse others
function pmt_hide_all() {
    document.querySelectorAll('.pmt-form').forEach(el => el.style.display = 'none');
}
function pmt_show(id) {
    var el = document.getElementById(id);
    if (!el) return;
    var wasVisible = el.style.display !== 'none';
    pmt_hide_all();
    if (!wasVisible) {
        el.style.display = 'block';
        el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}
</script>
</body>
</html>
