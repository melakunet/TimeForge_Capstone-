<?php
/*
 * Shared partial: address block + invoice dates
 * Included by all 5 templates. Variables come from the calling view.
 * $invoice, $hourly_rate already in scope.
 */
?>
<div class="addr-row">
  <div class="addr-block">
    <div class="addr-label">From</div>
    <strong>TimeForge Services</strong><br>
    <?php if (!empty($company_name)): ?>
      <?php echo htmlspecialchars($company_name); ?><br>
    <?php endif; ?>
    Professional Time Management<br>
    triOS College — Web Capstone Project
  </div>

  <div class="addr-block">
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
  </div>

  <div class="addr-block">
    <div class="addr-label">Invoice Details</div>
    <strong>Issue Date:</strong> <?php echo date('F j, Y', strtotime($invoice['issue_date'])); ?><br>
    <strong>Due Date:</strong>   <?php echo date('F j, Y', strtotime($invoice['due_date'])); ?><br>
    <strong>Project:</strong>    <?php echo htmlspecialchars($invoice['project_name']); ?><br>
    <strong>Rate:</strong>       $<?php echo number_format($hourly_rate, 2); ?>/hr
  </div>
</div>
