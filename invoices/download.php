<?php
/*
 * Task 7.2 / 7.6 — PDF Invoice Download
 *
 * Generates a PDF copy of a saved invoice using the Dompdf library and
 * streams it directly to the browser as a file download.
 *
 * WHY styles are embedded in this file (not in css/invoice.css):
 * Dompdf renders HTML/CSS in an isolated context and cannot load external
 * stylesheets over HTTP. Every rule the PDF needs must be inlined inside
 * the HTML string passed to $dompdf->loadHtml(). This is a documented
 * library constraint, not a structural choice.
 *
 * CSS for the browser-rendered invoice lives in css/invoice.css.
 * Any visual change to PDF output must be made here as well.
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isLoggedIn()) {
    header('Location: /TimeForge_Capstone/login.php');
    exit;
}

$invoice_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$invoice_id) {
    http_response_code(400);
    exit('Invalid invoice ID.');
}

// Pull invoice + related data
$inv_stmt = $pdo->prepare("
    SELECT
        inv.*,
        p.project_name,
        p.hourly_rate,
        c.client_name,
        c.company_name,
        c.email    AS client_email,
        c.address  AS client_address,
        c.phone    AS client_phone,
        c.user_id  AS client_user_id
    FROM invoices inv
    INNER JOIN projects p ON p.id = inv.project_id
    INNER JOIN clients  c ON c.id = inv.client_id
    WHERE inv.id = :id
    LIMIT 1
");
$inv_stmt->execute([':id' => $invoice_id]);
$invoice = $inv_stmt->fetch();

if (!$invoice) {
    http_response_code(404);
    exit('Invoice not found.');
}

// Clients may only download their own invoices
if (hasRole('client') && $invoice['client_user_id'] != $_SESSION['user_id']) {
    http_response_code(403);
    exit('Access denied.');
}

// Approved billable entries for the line-items table
$entries_stmt = $pdo->prepare("
    SELECT
        te.start_time,
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
$entries     = $entries_stmt->fetchAll();
$hourly_rate = (float)$invoice['hourly_rate'];

foreach ($entries as &$e) {
    $e['hours']     = round(($e['total_seconds'] ?? 0) / 3600, 2);
    $e['line_cost'] = round($e['hours'] * $hourly_rate, 2);
}
unset($e);

// ── Per-template colour palette ────────────────────────────────────────────
// Each entry maps to inline CSS values used in the HTML below.
// Dompdf cannot fetch external URLs, so everything is embedded here.
$allowed_templates = ['classic', 'modern', 'bold', 'minimal', 'corporate'];
$tpl = in_array($invoice['template'] ?? '', $allowed_templates)
     ? $invoice['template'] : 'classic';

$themes = [
    'classic'   => ['hdr_bg' => '#14532d', 'hdr_fg' => '#ffffff', 'stripe' => '#14532d', 'accent' => '#14532d'],
    'modern'    => ['hdr_bg' => '#1e3a5f', 'hdr_fg' => '#ffffff', 'stripe' => '#3b82f6', 'accent' => '#1e3a5f'],
    'bold'      => ['hdr_bg' => '#1f2937', 'hdr_fg' => '#f59e0b', 'stripe' => '#1f2937', 'accent' => '#f59e0b'],
    'minimal'   => ['hdr_bg' => '#ffffff', 'hdr_fg' => '#111827', 'stripe' => '#374151', 'accent' => '#374151'],
    'corporate' => ['hdr_bg' => '#4c1d95', 'hdr_fg' => '#ffffff', 'stripe' => '#7c3aed', 'accent' => '#4c1d95'],
];
$t = $themes[$tpl];

$status_colors = ['draft' => '#6b7280', 'sent' => '#2563eb', 'paid' => '#16a34a'];
$status_labels = ['draft' => 'Draft',   'sent' => 'Sent',   'paid' => 'Paid'];

// ── Build the HTML document for Dompdf ────────────────────────────────────
// Dompdf does not support CSS flexbox or grid — layout must use HTML tables.
// All colours are injected from $t so the PDF visually matches the chosen template.
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
  body      { font-family: DejaVu Sans, Helvetica, Arial, sans-serif; font-size: 12px; color: #1f2937; margin: 0; padding: 0; }
  .page     { padding: 0; }

  /* Coloured header band — uses HTML table so Dompdf renders it correctly */
  .hdr-table  { width: 100%; border-collapse: collapse; background: <?php echo $t['hdr_bg']; ?>; margin-bottom: 0; }
  .hdr-table td { padding: 22px 30px; vertical-align: top; }
  .brand-name { font-size: 20px; font-weight: bold; color: <?php echo $t['hdr_fg']; ?>; }
  .brand-co   { font-size: 11px; color: <?php echo $t['hdr_fg']; ?>; opacity: 0.85; margin-top: 3px; }
  .brand-tag  { font-size: 9px;  color: <?php echo $t['hdr_fg']; ?>; opacity: 0.65; margin-top: 2px; }
  .inv-label  { font-size: 24px; font-weight: bold; color: <?php echo $t['hdr_fg']; ?>; text-align: right; }
  .inv-num    { font-size: 11px; color: <?php echo $t['hdr_fg']; ?>; text-align: right; opacity: 0.85; margin-top: 3px; }
  .inv-status { font-size: 9px; font-weight: bold; color: #fff; text-align: right; margin-top: 5px; text-transform: uppercase; letter-spacing: 1px; }

  /* Thin accent stripe below header */
  .stripe-table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
  .stripe-table td { background: <?php echo $t['stripe']; ?>; height: 4px; padding: 0; font-size: 1px; }

  /* Body padding */
  .body-pad { padding: 24px 30px; }

  /* Address three-column table */
  .addr-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
  .addr-table td { vertical-align: top; width: 33%; font-size: 11px; padding: 0 12px 0 0; line-height: 1.6; }
  .addr-label { font-size: 8px; font-weight: bold; color: <?php echo $t['accent']; ?>; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; border-bottom: 1px solid <?php echo $t['accent']; ?>; padding-bottom: 2px; }

  /* Line-items table */
  .items-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
  .items-table thead th { background: <?php echo $t['accent']; ?>; color: #fff; padding: 8px 10px; text-align: left; font-size: 10px; font-weight: bold; letter-spacing: 0.5px; }
  .items-table th.r, .items-table td.r { text-align: right; }
  .items-table tbody tr { border-bottom: 1px solid #e5e7eb; }
  .items-table tbody td { padding: 7px 10px; font-size: 11px; }
  .items-table tbody tr.alt td { background: #f9fafb; }
  .items-table tfoot td { padding: 6px 10px; font-size: 11px; }
  .items-table tfoot .tot td { border-top: 2px solid <?php echo $t['accent']; ?>; font-size: 13px; font-weight: bold; padding-top: 8px; }

  /* Notes and footer */
  .notes-box  { background: #f3f4f6; border-left: 3px solid <?php echo $t['accent']; ?>; padding: 10px 14px; font-size: 11px; margin-bottom: 14px; }
  .foot-note  { font-size: 9px; color: #6b7280; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 10px; margin-top: 6px; }

  <?php if ($tpl === 'minimal'): ?>
  .hdr-table  { background: #ffffff; border-bottom: 3px solid #374151; }
  .brand-name { color: #111827; }
  .brand-co   { color: #374151; opacity: 1; }
  .brand-tag  { color: #6b7280; opacity: 1; }
  .inv-label  { color: #111827; }
  .inv-num    { color: #6b7280; opacity: 1; }
  .stripe-table td { background: #ffffff; height: 0; }
  <?php endif; ?>
</style>
</head>
<body>
<div class="page">

  <!-- Header band -->
  <table class="hdr-table" cellspacing="0" cellpadding="0">
    <tr>
      <td style="width:60%;">
        <div class="brand-name">TimeForge</div>
        <?php if (!empty($invoice['company_name'])): ?>
          <div class="brand-co"><?php echo htmlspecialchars($invoice['company_name']); ?></div>
        <?php endif; ?>
        <div class="brand-tag">Professional Time Tracking &amp; Project Management</div>
      </td>
      <td style="width:40%; text-align:right;">
        <div class="inv-label">INVOICE</div>
        <div class="inv-num"><?php echo htmlspecialchars($invoice['invoice_number']); ?></div>
        <div class="inv-status" style="background:<?php echo $status_colors[$invoice['status']] ?? '#6b7280'; ?>; display:inline-block; padding:2px 10px; border-radius:3px; margin-top:6px;">
          <?php echo $status_labels[$invoice['status']] ?? 'Draft'; ?>
        </div>
      </td>
    </tr>
  </table>

  <!-- Accent stripe -->
  <table class="stripe-table" cellspacing="0" cellpadding="0"><tr><td>&nbsp;</td></tr></table>

  <div class="body-pad">

    <!-- From / Bill To / Details -->
    <table class="addr-table" cellspacing="0" cellpadding="0">
      <tr>
        <td>
          <div class="addr-label">From</div>
          <strong>TimeForge Services</strong><br>
          Professional Time Management<br>
          triOS College &mdash; Web Capstone<br>
          <em style="font-size:10px; color:#6b7280;">Prepared for: <?php echo htmlspecialchars($invoice['client_name']); ?><?php if (!empty($invoice['company_name'])): ?> / <?php echo htmlspecialchars($invoice['company_name']); ?><?php endif; ?></em>
        </td>
        <td>
          <div class="addr-label">Bill To</div>
          <strong><?php echo htmlspecialchars($invoice['client_name']); ?></strong><br>
          <?php if (!empty($invoice['company_name'])): ?>
            <?php echo htmlspecialchars($invoice['company_name']); ?><br>
          <?php endif; ?>
          <?php if (!empty($invoice['client_address'])): ?>
            <?php echo nl2br(htmlspecialchars($invoice['client_address'])); ?><br>
          <?php endif; ?>
          <?php echo htmlspecialchars($invoice['client_email']); ?>
          <?php if (!empty($invoice['client_phone'])): ?>
            <br><?php echo htmlspecialchars($invoice['client_phone']); ?>
          <?php endif; ?>
        </td>
        <td>
          <div class="addr-label">Invoice Details</div>
          <strong>Invoice #:</strong> <?php echo htmlspecialchars($invoice['invoice_number']); ?><br>
          <strong>Issue Date:</strong> <?php echo date('F j, Y', strtotime($invoice['issue_date'])); ?><br>
          <strong>Due Date:</strong>   <?php echo date('F j, Y', strtotime($invoice['due_date'])); ?><br>
          <strong>Project:</strong>    <?php echo htmlspecialchars($invoice['project_name']); ?><br>
          <strong>Rate:</strong>       $<?php echo number_format($hourly_rate, 2); ?>/hr<br>
          <strong>Template:</strong>   <?php echo ucfirst($tpl); ?>
        </td>
      </tr>
    </table>

    <!-- Line-items -->
    <table class="items-table" cellspacing="0" cellpadding="0">
      <thead>
        <tr>
          <th>Date</th>
          <th>Freelancer</th>
          <th>Description</th>
          <th class="r">Hours</th>
          <th class="r">Rate</th>
          <th class="r">Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($entries)): ?>
        <tr><td colspan="6" style="text-align:center; color:#6b7280; padding:14px;">No approved billable entries found.</td></tr>
        <?php else: ?>
        <?php foreach ($entries as $i => $e): ?>
        <tr <?php if ($i % 2 === 1) echo 'class="alt"'; ?>>
          <td><?php echo date('M j, Y', strtotime($e['start_time'])); ?></td>
          <td><?php echo htmlspecialchars($e['freelancer_name'] ?? '—'); ?></td>
          <td><?php echo htmlspecialchars($e['description'] ?? '—'); ?></td>
          <td class="r"><?php echo number_format($e['hours'], 2); ?></td>
          <td class="r">$<?php echo number_format($hourly_rate, 2); ?></td>
          <td class="r">$<?php echo number_format($e['line_cost'], 2); ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="5" class="r">Subtotal</td>
          <td class="r">$<?php echo number_format($invoice['subtotal'], 2); ?></td>
        </tr>
        <tr>
          <td colspan="5" class="r">Tax (<?php echo number_format($invoice['tax_rate'], 2); ?>%)</td>
          <td class="r">$<?php echo number_format($invoice['tax_amount'], 2); ?></td>
        </tr>
        <tr class="tot">
          <td colspan="5" class="r">Total Due</td>
          <td class="r">$<?php echo number_format($invoice['total_amount'], 2); ?></td>
        </tr>
      </tfoot>
    </table>

    <?php if (!empty($invoice['notes'])): ?>
    <div class="notes-box"><strong>Notes:</strong><br><?php echo nl2br(htmlspecialchars($invoice['notes'])); ?></div>
    <?php endif; ?>

    <div class="foot-note">
      Thank you for your business &mdash; <?php echo htmlspecialchars($invoice['client_name']); ?><?php if (!empty($invoice['company_name'])): ?> / <?php echo htmlspecialchars($invoice['company_name']); ?><?php endif; ?>.<br>
      Please remit payment by <?php echo date('F j, Y', strtotime($invoice['due_date'])); ?>.
      Generated by TimeForge &bull; triOS College Web Capstone
    </div>

  </div><!-- /.body-pad -->
</div>
</body>
</html>
<?php
$html = ob_get_clean();

// ── Configure and run Dompdf ──────────────────────────────────────────────
// Font directory must be set explicitly so Dompdf can locate the .ttf files
// and write its font cache. Without this, fopen('','rb+') throws a ValueError.
// Use dirname(__DIR__) to build the absolute path — realpath() can return false
// if Apache runs as a different user and causes path resolution to fail.
$font_dir = dirname(__DIR__) . '/vendor/dompdf/dompdf/lib/fonts';
if (!is_dir($font_dir)) {
    error_log('[download.php] Font directory not found: ' . $font_dir);
    http_response_code(500);
    exit('PDF generation error: font directory missing.');
}

$options = new Options();
$options->set('isRemoteEnabled', false);   // no external HTTP resources — all CSS is inline
$options->set('defaultFont', 'dejavu sans'); // must match key in installed-fonts.dist.json
$options->set('fontDir',  $font_dir);
$options->set('fontCache', $font_dir);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Discard any stray buffered output (whitespace from includes, etc.)
// that would corrupt the binary PDF stream sent to the browser.
while (ob_get_level()) ob_end_clean();

$filename = 'Invoice-' . preg_replace('/[^A-Za-z0-9\-]/', '_', $invoice['invoice_number']) . '.pdf';
$dompdf->stream($filename, ['Attachment' => true]);

