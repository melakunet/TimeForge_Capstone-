<?php
/*
 * Shared partial: line-item table
 * Included by all 5 templates. $entries and $hourly_rate in scope.
 */
?>
<table>
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
      <tr><td colspan="6" style="text-align:center; padding:1.5rem; color:#6b7280;">No approved entries.</td></tr>
    <?php else: ?>
      <?php foreach ($entries as $e): ?>
      <tr>
        <td><?php echo date('M j, Y', strtotime($e['start_time'])); ?></td>
        <td><?php echo htmlspecialchars($e['freelancer_name'] ?? '—'); ?></td>
        <td><?php echo htmlspecialchars($e['description']     ?? '—'); ?></td>
        <td class="r"><?php echo number_format($e['hours'],     2); ?></td>
        <td class="r">$<?php echo number_format($hourly_rate,   2); ?></td>
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
    <tr class="total-row">
      <td colspan="5" class="r"><strong>Total Due</strong></td>
      <td class="r"><strong>$<?php echo number_format($invoice['total_amount'], 2); ?></strong></td>
    </tr>
  </tfoot>
</table>
