<?php
/*
 * Template: Modern
 * Full-width navy header band with white text, gradient accent stripe.
 * Styles live in css/invoice.css under section 12c (.inv-doc.tpl-modern).
 * Variables required: $invoice, $entries, $hourly_rate, $company_name,
 *                     $status_label, $status_color
 */
?>

<div class="inv-doc tpl-modern">
  <div class="inv-head">
    <div class="brand-wrap">
      <img src="/TimeForge_Capstone/icons/logo.png" alt="TimeForge logo" class="brand-logo">
      <div>
        <div class="brand-name">TimeForge</div>
        <div class="brand-tagline">Professional Time Tracking &amp; Project Management</div>
        <?php if (!empty($company_name)): ?>
          <div class="brand-sub"><?php echo htmlspecialchars($company_name); ?></div>
        <?php endif; ?>
      </div>
    </div>
    <div class="inv-right">
      <div class="inv-title">INVOICE</div>
      <div class="inv-num"><?php echo htmlspecialchars($invoice['invoice_number']); ?></div>
      <br>
      <span class="inv-badge"><?php echo $status_label; ?></span>
    </div>
  </div>

  <!-- Gradient accent stripe below the navy header -->
  <div class="stripe"></div>

  <?php include __DIR__ . '/_addr_dates.php'; ?>
  <?php include __DIR__ . '/_table.php'; ?>
  <?php include __DIR__ . '/_totals_notes.php'; ?>
</div>
