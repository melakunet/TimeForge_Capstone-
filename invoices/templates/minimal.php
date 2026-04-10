<?php
/*
 * Template: Minimal
 * No colour bands — purely typographic. Thin rules, generous whitespace.
 * Styles live in css/invoice.css under section 12e (.inv-doc.tpl-minimal).
 * Variables required: $invoice, $entries, $hourly_rate, $company_name,
 *                     $status_label, $status_color
 */
?>

<div class="inv-doc tpl-minimal">
  <div class="inv-head">
    <div class="brand-wrap">
      <img src="/TimeForge_Capstone/icons/logo.png" alt="logo" class="brand-logo">
      <div>
        <div class="brand-name"><?php echo htmlspecialchars($creator_company ?: $creator_name); ?></div>
        <?php if (!empty($creator_tagline)): ?>
          <div class="brand-tagline"><?php echo htmlspecialchars($creator_tagline); ?></div>
        <?php endif; ?>
      </div>
    </div>
    <div class="inv-right">
      <div class="inv-title">Invoice</div>
      <div class="inv-num"><?php echo htmlspecialchars($invoice['invoice_number']); ?></div>
      <br>
      <span class="inv-badge"><?php echo $status_label; ?></span>
    </div>
  </div>

  <?php include __DIR__ . '/_addr_dates.php'; ?>
  <?php include __DIR__ . '/_table.php'; ?>
  <?php include __DIR__ . '/_totals_notes.php'; ?>
</div>
