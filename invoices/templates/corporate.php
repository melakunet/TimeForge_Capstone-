<?php
/*
 * Template: Corporate
 * Two-column header grid, deep purple brand colour, formal serif labels.
 * Styles live in css/invoice.css under section 12f (.inv-doc.tpl-corporate).
 * Variables required: $invoice, $entries, $hourly_rate, $company_name,
 *                     $status_label, $status_color
 */
?>

<div class="inv-doc tpl-corporate">
  <div class="inv-head">
    <div class="brand-wrap">
      <div class="brand-logo-row">
        <img src="/TimeForge_Capstone/icons/logo.png" alt="logo" class="brand-logo">
        <div>
          <div class="brand-name"><?php echo htmlspecialchars($creator_company ?: $creator_name); ?></div>
          <?php if (!empty($creator_tagline)): ?>
            <div class="brand-tagline"><?php echo htmlspecialchars($creator_tagline); ?></div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="inv-meta-block">
      <div class="inv-title">INVOICE</div>
      <div class="inv-num"><?php echo htmlspecialchars($invoice['invoice_number']); ?></div>
      <span class="inv-badge" style="background:<?php echo $status_color; ?>">
        <?php echo $status_label; ?>
      </span>
    </div>
  </div>

  <!-- Purple gradient stripe below the header -->
  <div class="purple-stripe"></div>

  <?php include __DIR__ . '/_addr_dates.php'; ?>
  <?php include __DIR__ . '/_table.php'; ?>
  <?php include __DIR__ . '/_totals_notes.php'; ?>
</div>
