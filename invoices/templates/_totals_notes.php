<?php
/*
 * Shared partial: notes block + footer line
 * Included by all 5 templates. $invoice, $logo_is_custom in scope.
 */
?>
<?php if (!empty($invoice['notes'])): ?>
  <div class="inv-notes">
    <strong>Notes:</strong><br>
    <?php echo nl2br(htmlspecialchars($invoice['notes'])); ?>
  </div>
<?php endif; ?>

<div class="inv-foot">
  <span>Thank you for your business. Please remit payment by
  <?php echo date('F j, Y', strtotime($invoice['due_date'])); ?>.</span>
  <span class="inv-powered-by">
    <img src="/TimeForge_Capstone/icons/logo.png" alt="TimeForge" style="height:14px; width:14px; vertical-align:middle; opacity:0.6; margin-right:3px;">
    Powered by <strong>TimeForge</strong>
  </span>
</div>
