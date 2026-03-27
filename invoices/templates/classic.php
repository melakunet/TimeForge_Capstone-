<?php
/*
 * Template: Classic
 * Clean forest-green header, left-aligned logo, standard table layout.
 * Styles live in css/invoice.css under section 12b (.inv-doc.tpl-classic).
 * Variables required: $invoice, $entries, $hourly_rate, $company_name,
 *                     $status_label, $status_color
 */
?>

<div class="inv-doc tpl-classic">
  <div class="inv-head">
    <div class="brand-wrap">
      <img src="/TimeForge_Capstone/icons/logo.png" alt="TimeForge logo" class="brand-logo">
      <div>
        <div class="brand-name">TimeForge</div>
        <div class="brand-tagline">Professional Time Tracking &amp; Project Management</div>
        <?php if (!empty($company_name)): ?>
          <div class="company-from" style="margin-top:4px; font-weight:600; color:#14532d;">
            <?php echo htmlspecialchars($company_name); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <div class="inv-right">
      <div class="inv-title">INVOICE</div>
      <div class="inv-num"><?php echo htmlspecialchars($invoice['invoice_number']); ?></div>
      <span class="inv-badge" style="background:<?php echo $status_color; ?>">
        <?php echo $status_label; ?>
      </span>
    </div>
  </div>

  <?php include __DIR__ . '/_addr_dates.php'; ?>
  <?php include __DIR__ . '/_table.php'; ?>
  <?php include __DIR__ . '/_totals_notes.php'; ?>
</div>
