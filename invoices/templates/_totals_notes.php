<?php
/*
 * Shared partial: notes block + footer line
 * Included by all 5 templates. $invoice in scope.
 */
?>
<?php if (!empty($invoice['notes'])): ?>
  <div class="inv-notes">
    <strong>Notes:</strong><br>
    <?php echo nl2br(htmlspecialchars($invoice['notes'])); ?>
  </div>
<?php endif; ?>

<div class="inv-foot">
  Thank you for your business. Please remit payment by
  <?php echo date('F j, Y', strtotime($invoice['due_date'])); ?>.
</div>
