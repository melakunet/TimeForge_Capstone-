<?php
/**
 * invoices/send.php
 * Sends the invoice PDF to a client via email.
 * Generates the PDF in memory using Dompdf, attaches it with PHPMailer,
 * then updates invoices: status = 'sent', sent_at, sent_to_email, email_sent_at.
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Core/Mailer.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /TimeForge_Capstone/invoices/history.php');
    exit;
}

$invoice_id = filter_input(INPUT_POST, 'invoice_id', FILTER_VALIDATE_INT);
$send_to    = trim($_POST['send_to_email'] ?? '');
$note       = trim($_POST['email_note']    ?? '');

if (!$invoice_id || !filter_var($send_to, FILTER_VALIDATE_EMAIL)) {
    setFlash('error', 'A valid invoice ID and recipient email address are required.');
    header('Location: /TimeForge_Capstone/invoices/history.php');
    exit;
}

// Load invoice with all data needed for PDF and email
try {
    $inv_stmt = $pdo->prepare("
        SELECT
            inv.*,
            p.project_name,
            p.hourly_rate,
            c.client_name,
            c.company_name,
            c.email        AS client_email,
            c.address      AS client_address,
            c.phone        AS client_phone,
            c.user_id      AS client_user_id,
            u.full_name        AS creator_name,
            u.email            AS creator_email,
            u.company_name     AS creator_company,
            u.business_tagline AS creator_tagline
        FROM invoices inv
        INNER JOIN projects p ON p.id = inv.project_id
        INNER JOIN clients  c ON c.id = inv.client_id
        LEFT  JOIN users    u ON u.id = inv.created_by
        WHERE inv.id = :id
          AND inv.company_id = :company_id
        LIMIT 1
    ");
    $inv_stmt->execute([':id' => $invoice_id, ':company_id' => $_SESSION['company_id']]);
    $invoice = $inv_stmt->fetch();
} catch (PDOException $e) {
    error_log('send.php invoice fetch: ' . $e->getMessage());
    setFlash('error', 'Could not load invoice.');
    header('Location: /TimeForge_Capstone/invoices/history.php');
    exit;
}

if (!$invoice) {
    setFlash('error', 'Invoice not found.');
    header('Location: /TimeForge_Capstone/invoices/history.php');
    exit;
}

$back = '/TimeForge_Capstone/invoices/view.php?id=' . $invoice_id;

// Approved billable time entries for PDF line items
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

// ── Build PDF HTML (mirrors download.php) ────────────────────────────────
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

ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
  body      { font-family: DejaVu Sans, Helvetica, Arial, sans-serif; font-size: 12px; color: #1f2937; margin: 0; padding: 0; }
  .page     { padding: 0; }
  .hdr-table  { width: 100%; border-collapse: collapse; background: <?php echo $t['hdr_bg']; ?>; margin-bottom: 0; }
  .hdr-table td { padding: 22px 30px; vertical-align: top; }
  .brand-name { font-size: 20px; font-weight: bold; color: <?php echo $t['hdr_fg']; ?>; }
  .brand-co   { font-size: 11px; color: <?php echo $t['hdr_fg']; ?>; opacity: 0.85; margin-top: 3px; }
  .brand-tag  { font-size: 9px;  color: <?php echo $t['hdr_fg']; ?>; opacity: 0.65; margin-top: 2px; }
  .inv-label  { font-size: 24px; font-weight: bold; color: <?php echo $t['hdr_fg']; ?>; text-align: right; }
  .inv-num    { font-size: 11px; color: <?php echo $t['hdr_fg']; ?>; text-align: right; opacity: 0.85; margin-top: 3px; }
  .inv-status { font-size: 9px; font-weight: bold; color: #fff; text-align: right; margin-top: 5px; text-transform: uppercase; letter-spacing: 1px; }
  .stripe-table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
  .stripe-table td { background: <?php echo $t['stripe']; ?>; height: 4px; padding: 0; font-size: 1px; }
  .body-pad { padding: 24px 30px; }
  .addr-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
  .addr-table td { vertical-align: top; width: 33%; font-size: 11px; padding: 0 12px 0 0; line-height: 1.6; }
  .addr-label { font-size: 8px; font-weight: bold; color: <?php echo $t['accent']; ?>; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; border-bottom: 1px solid <?php echo $t['accent']; ?>; padding-bottom: 2px; }
  .items-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
  .items-table thead th { background: <?php echo $t['accent']; ?>; color: #fff; padding: 8px 10px; text-align: left; font-size: 10px; font-weight: bold; letter-spacing: 0.5px; }
  .items-table th.r, .items-table td.r { text-align: right; }
  .items-table tbody tr { border-bottom: 1px solid #e5e7eb; }
  .items-table tbody td { padding: 7px 10px; font-size: 11px; }
  .items-table tbody tr.alt td { background: #f9fafb; }
  .items-table tfoot td { padding: 6px 10px; font-size: 11px; }
  .items-table tfoot .tot td { border-top: 2px solid <?php echo $t['accent']; ?>; font-size: 13px; font-weight: bold; padding-top: 8px; }
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
  <table class="hdr-table" cellspacing="0" cellpadding="0">
    <tr>
      <td style="width:60%;">
        <div class="brand-name"><?php echo htmlspecialchars($invoice['creator_company'] ?: ($invoice['creator_name'] ?? 'TimeForge')); ?></div>
        <?php if (!empty($invoice['creator_tagline'])): ?>
          <div class="brand-co"><?php echo htmlspecialchars($invoice['creator_tagline']); ?></div>
        <?php endif; ?>
        <?php if (!empty($invoice['creator_email'])): ?>
          <div class="brand-tag"><?php echo htmlspecialchars($invoice['creator_email']); ?></div>
        <?php endif; ?>
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
  <table class="stripe-table" cellspacing="0" cellpadding="0"><tr><td>&nbsp;</td></tr></table>
  <div class="body-pad">
    <table class="addr-table" cellspacing="0" cellpadding="0">
      <tr>
        <td>
          <div class="addr-label">From</div>
          <?php if (!empty($invoice['creator_company'])): ?>
            <strong><?php echo htmlspecialchars($invoice['creator_company']); ?></strong><br>
          <?php else: ?>
            <strong><?php echo htmlspecialchars($invoice['creator_name'] ?? 'TimeForge User'); ?></strong><br>
          <?php endif; ?>
          <?php if (!empty($invoice['creator_tagline'])): ?><?php echo htmlspecialchars($invoice['creator_tagline']); ?><br><?php endif; ?>
          <?php if (!empty($invoice['creator_email'])): ?><?php echo htmlspecialchars($invoice['creator_email']); ?><br><?php endif; ?>
        </td>
        <td>
          <div class="addr-label">Bill To</div>
          <strong><?php echo htmlspecialchars($invoice['client_name']); ?></strong><br>
          <?php if (!empty($invoice['company_name'])): ?><?php echo htmlspecialchars($invoice['company_name']); ?><br><?php endif; ?>
          <?php if (!empty($invoice['client_address'])): ?><?php echo nl2br(htmlspecialchars($invoice['client_address'])); ?><br><?php endif; ?>
          <?php echo htmlspecialchars($invoice['client_email']); ?>
          <?php if (!empty($invoice['client_phone'])): ?><br><?php echo htmlspecialchars($invoice['client_phone']); ?><?php endif; ?>
        </td>
        <td>
          <div class="addr-label">Invoice Details</div>
          <strong>Invoice #:</strong> <?php echo htmlspecialchars($invoice['invoice_number']); ?><br>
          <strong>Issue Date:</strong> <?php echo date('F j, Y', strtotime($invoice['issue_date'])); ?><br>
          <strong>Due Date:</strong>   <?php echo date('F j, Y', strtotime($invoice['due_date'])); ?><br>
          <strong>Project:</strong>    <?php echo htmlspecialchars($invoice['project_name']); ?><br>
          <strong>Rate:</strong>       $<?php echo number_format($hourly_rate, 2); ?>/hr
        </td>
      </tr>
    </table>
    <table class="items-table" cellspacing="0" cellpadding="0">
      <thead>
        <tr>
          <th>Date</th><th>Freelancer</th><th>Description</th>
          <th class="r">Hours</th><th class="r">Rate</th><th class="r">Amount</th>
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
        <tr><td colspan="5" class="r">Subtotal</td><td class="r">$<?php echo number_format($invoice['subtotal'], 2); ?></td></tr>
        <tr><td colspan="5" class="r">Tax (<?php echo number_format($invoice['tax_rate'], 2); ?>%)</td><td class="r">$<?php echo number_format($invoice['tax_amount'], 2); ?></td></tr>
        <tr class="tot"><td colspan="5" class="r">Total Due</td><td class="r">$<?php echo number_format($invoice['total_amount'], 2); ?></td></tr>
      </tfoot>
    </table>
    <?php if (!empty($invoice['notes'])): ?>
    <div class="notes-box"><strong>Notes:</strong><br><?php echo nl2br(htmlspecialchars($invoice['notes'])); ?></div>
    <?php endif; ?>
    <div class="foot-note">
      Thank you for your business &mdash; <?php echo htmlspecialchars($invoice['client_name']); ?><?php if (!empty($invoice['company_name'])): ?> / <?php echo htmlspecialchars($invoice['company_name']); ?><?php endif; ?>.<br>
      Please remit payment by <?php echo date('F j, Y', strtotime($invoice['due_date'])); ?>. &nbsp;&bull;&nbsp; <em>Generated by TimeForge</em>
    </div>
  </div>
</div>
</body>
</html>
<?php
$pdf_html = ob_get_clean();

// ── Render PDF to string ──────────────────────────────────────────────────
$dompdf_root = dirname(__DIR__) . '/vendor/dompdf/dompdf';
$font_dir    = $dompdf_root . '/lib/fonts';

$options = new Options();
$options->set('isRemoteEnabled', false);
$options->set('defaultFont', 'dejavu sans');
$options->setRootDir($dompdf_root);
$options->setChroot([$dompdf_root, dirname(__DIR__)]);
$options->set('fontDir',   $font_dir);
$options->set('fontCache', $font_dir);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($pdf_html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$pdf_string = $dompdf->output();

$pdf_filename = 'Invoice-' . preg_replace('/[^A-Za-z0-9\-]/', '_', $invoice['invoice_number']) . '.pdf';

// ── Send email via PHPMailer ──────────────────────────────────────────────
$config = require __DIR__ . '/../config/mail.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host    = $config['host'];
    $mail->Port    = $config['port'];
    $mail->CharSet = 'UTF-8';

    if (!empty($config['username']) && $config['username'] !== 'your_mailtrap_user') {
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['username'];
        $mail->Password   = $config['password'];
        $mail->SMTPSecure = $config['encryption'];
    } else {
        $mail->SMTPAuth   = false;
        $mail->SMTPAutoTLS = false;
    }

    $from_name = $invoice['creator_company'] ?: ($invoice['creator_name'] ?? 'TimeForge');
    $mail->setFrom($config['from_email'], $from_name);
    $mail->addAddress($send_to, $invoice['client_name']);

    if (!empty($invoice['creator_email'])) {
        $mail->addReplyTo($invoice['creator_email'], $from_name);
    }

    $due_formatted = date('F j, Y', strtotime($invoice['due_date']));

    $note_html = $note
        ? '<p style="color:#374151; margin-bottom:1rem;">' . nl2br(htmlspecialchars($note)) . '</p>'
        : '';

    $mail->isHTML(true);
    $mail->Subject = 'Invoice ' . $invoice['invoice_number'] . ' from ' . $from_name;
    $mail->Body = '
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"></head>
<body style="font-family:Arial,Helvetica,sans-serif; background:#f3f4f6; padding:2rem; color:#1f2937;">
  <div style="max-width:560px; margin:0 auto; background:#fff; border-radius:8px; padding:2rem; box-shadow:0 2px 8px rgba(0,0,0,0.07);">
    <h2 style="margin-top:0; color:#14532d;">Invoice ' . htmlspecialchars($invoice['invoice_number']) . '</h2>
    <p>Hi ' . htmlspecialchars($invoice['client_name']) . ',</p>
    ' . $note_html . '
    <p>Please find your invoice attached. Here is a summary:</p>
    <table style="width:100%; border-collapse:collapse; margin-bottom:1rem; font-size:0.9rem;">
      <tr><td style="padding:6px 0; color:#6b7280;">Project</td><td style="padding:6px 0;"><strong>' . htmlspecialchars($invoice['project_name']) . '</strong></td></tr>
      <tr><td style="padding:6px 0; color:#6b7280;">Invoice #</td><td style="padding:6px 0;"><strong>' . htmlspecialchars($invoice['invoice_number']) . '</strong></td></tr>
      <tr><td style="padding:6px 0; color:#6b7280;">Amount Due</td><td style="padding:6px 0;"><strong>$' . number_format($invoice['total_amount'], 2) . '</strong></td></tr>
      <tr><td style="padding:6px 0; color:#6b7280;">Due Date</td><td style="padding:6px 0;"><strong>' . $due_formatted . '</strong></td></tr>
    </table>
    <p style="font-size:0.85rem; color:#6b7280; margin-top:2rem;">
      This email was sent by ' . htmlspecialchars($from_name) . ' via TimeForge.
    </p>
  </div>
</body></html>';

    $mail->AltBody = 'Invoice ' . $invoice['invoice_number'] . ' — Amount due: $'
        . number_format($invoice['total_amount'], 2)
        . ' by ' . $due_formatted . '. See attached PDF.';

    // Attach the in-memory PDF
    $mail->addStringAttachment($pdf_string, $pdf_filename, 'base64', 'application/pdf');

    $mail->send();

} catch (Exception $e) {
    error_log('Invoice email send failed: ' . $mail->ErrorInfo);
    setFlash('error', 'Email could not be sent. Please try again. (' . htmlspecialchars($mail->ErrorInfo) . ')');
    header('Location: ' . $back);
    exit;
}

// ── Update invoices table ─────────────────────────────────────────────────
try {
    $is_first_send = in_array($invoice['status'], ['draft']);

    $upd = $pdo->prepare("
        UPDATE invoices
        SET status        = 'sent',
            sent_at       = COALESCE(sent_at, NOW()),
            sent_to_email = :email,
            email_sent_at = NOW()
        WHERE id = :id
    ");
    $upd->execute([':email' => $send_to, ':id' => $invoice_id]);

} catch (PDOException $e) {
    error_log('Invoice status update after send: ' . $e->getMessage());
    // Email was sent — don't block the user, just warn
    setFlash('error', 'Invoice emailed but status could not be updated. Please refresh.');
    header('Location: ' . $back);
    exit;
}

setFlash('success', 'Invoice ' . htmlspecialchars($invoice['invoice_number']) . ' sent to ' . htmlspecialchars($send_to) . '.');
header('Location: ' . $back);
exit;
