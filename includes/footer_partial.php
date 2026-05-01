<?php
// Footer Partial (markup only) for instructor-style pages
?>
<footer>
  <p>&copy; <?php echo date('Y'); ?> TimeForge. All rights reserved.</p>
  <p>Professional Time Tracking & Project Management Solution</p>
  <p>Web Capstone Project by Etefworkie Melaku — triOS College, Mobile and Web App Development</p>
</footer>

<!-- ── How to Use Guide ────────────────────────────────────────────────────── -->
<div id="tf-guide-bar" style="background:var(--color-card); border-top:1px solid #1e293b; font-size:.85rem;">
  <button onclick="document.getElementById('tf-guide-body').classList.toggle('tf-guide-open')"
          style="width:100%; padding:.9rem 1.5rem; background:none; border:none; cursor:pointer;
                 color:var(--color-text-secondary); font-size:.85rem; text-align:left; display:flex;
                 align-items:center; gap:.5rem; font-weight:600;">
    <span id="tf-guide-arrow" style="font-size:.7rem; transition:transform .3s;">▶</span>
    📖 How to Use TimeForge — Quick Guide
  </button>
  <div id="tf-guide-body" style="display:none; padding:0 1.5rem 2rem; max-width:1200px; margin:0 auto;">

    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:1.5rem; margin-top:1rem;">

      <!-- Getting Started -->
      <div style="background:var(--color-bg); border-radius:10px; padding:1.2rem; border-left:3px solid #3b82f6;">
        <h4 style="margin:0 0 .75rem; color:#3b82f6;">🚀 Getting Started</h4>
        <ol style="margin:0; padding-left:1.2rem; color:var(--color-text-secondary); line-height:1.9;">
          <li>Register an account — your first account becomes <strong>Admin</strong></li>
          <li>Admin: go to <strong>Admin Dashboard → Users</strong> to invite or view team members</li>
          <li>Add <strong>Clients</strong> via the Clients menu</li>
          <li>Create a <strong>Project</strong> — set hourly rate, budget, deadline, and assign a client</li>
          <li>Share the app URL with your freelancers so they can register and start tracking</li>
        </ol>
      </div>

      <!-- Tasks -->
      <div style="background:var(--color-bg); border-radius:10px; padding:1.2rem; border-left:3px solid #f59e0b;">
        <h4 style="margin:0 0 .75rem; color:#f59e0b;">📋 Tasks & the Kanban Board</h4>
        <ol style="margin:0; padding-left:1.2rem; color:var(--color-text-secondary); line-height:1.9;">
          <li>Open a project → click <strong>Tasks</strong> to see the Kanban board</li>
          <li>Admin: click <strong>+ Add Task</strong> — set title, priority, due date, estimate, and assign to a freelancer</li>
          <li>Freelancer: click <strong>▶ Start</strong> on any Open task — a modal appears</li>
          <li>Confirm the description and click <strong>▶ Start Timer</strong> — the task moves to In Progress AND the timer starts, linked to that task</li>
          <li>When finished, click <strong>✔ Done</strong> to mark the task complete</li>
          <li>Logged hours vs. estimated hours appear on every task card automatically</li>
        </ol>
      </div>

      <!-- Time Tracking -->
      <div style="background:var(--color-bg); border-radius:10px; padding:1.2rem; border-left:3px solid #22c55e;">
        <h4 style="margin:0 0 .75rem; color:#22c55e;">⏱ Time Tracking</h4>
        <ol style="margin:0; padding-left:1.2rem; color:var(--color-text-secondary); line-height:1.9;">
          <li>Start a timer from the <strong>Home dashboard</strong> (▶ Start next to a project) or from any <strong>task card</strong></li>
          <li>The floating <strong>timer widget</strong> (bottom-right) shows elapsed time and the active project · task</li>
          <li>If you go idle, a popup asks: <em>Keep all time / Discard idle / Stop timer</em></li>
          <li>Click <strong>Stop Timer</strong> when done — the session is saved automatically</li>
          <li>Missed a session? Use <strong>Add Time Manually</strong> from the project details page</li>
          <li>Admin can <strong>Approve / Reject</strong> any time entry from Project Details</li>
        </ol>
      </div>

      <!-- Screenshots -->
      <div style="background:var(--color-bg); border-radius:10px; padding:1.2rem; border-left:3px solid #a855f7;">
        <h4 style="margin:0 0 .75rem; color:#a855f7;">📷 Activity Screenshots</h4>
        <ol style="margin:0; padding-left:1.2rem; color:var(--color-text-secondary); line-height:1.9;">
          <li>Screenshots are taken <strong>automatically</strong> while the timer runs (if enabled on the project)</li>
          <li>Interval is configurable per project — go to <strong>Edit Project → Screenshot Settings</strong></li>
          <li>Admin views all screenshots at <strong>Admin Dashboard → Activity Screenshots</strong></li>
          <li>Red border = zero activity at capture time; no border = worker was active</li>
          <li>Screenshots are <strong>private</strong> — served through a secure proxy, never directly accessible via URL</li>
          <li>To disable for a project: Edit Project → uncheck <em>Enable Screenshots</em></li>
        </ol>
      </div>

      <!-- Invoices -->
      <div style="background:var(--color-bg); border-radius:10px; padding:1.2rem; border-left:3px solid #f97316;">
        <h4 style="margin:0 0 .75rem; color:#f97316;">🧾 Invoices</h4>
        <ol style="margin:0; padding-left:1.2rem; color:var(--color-text-secondary); line-height:1.9;">
          <li>Go to <strong>Project Details</strong> → click <strong>Generate Invoice</strong></li>
          <li>Choose a template (Classic / Corporate / Bold), set tax rate and notes</li>
          <li>Send the invoice to the client by email directly from the invoice view</li>
          <li>Track status: Draft → Sent → Viewed → Paid in <strong>Invoices → History</strong></li>
          <li>Record partial or full payments via <strong>Record Payment</strong> on the invoice</li>
          <li>Download as PDF at any time</li>
        </ol>
      </div>

      <!-- Reports -->
      <div style="background:var(--color-bg); border-radius:10px; padding:1.2rem; border-left:3px solid #06b6d4;">
        <h4 style="margin:0 0 .75rem; color:#06b6d4;">📊 Reports & Exports</h4>
        <ol style="margin:0; padding-left:1.2rem; color:var(--color-text-secondary); line-height:1.9;">
          <li>Admin: <strong>Reports</strong> in the nav shows earnings, hours, and project breakdown</li>
          <li>Filter by date range, project, or freelancer</li>
          <li>Export any view as <strong>CSV</strong> for payroll or client billing</li>
          <li>Client portal: clients log in and see their own projects and time reports only</li>
          <li><strong>Session Audit</strong> (Admin menu) shows idle time, activity scores, and close reasons per session</li>
        </ol>
      </div>

      <!-- Admin -->
      <div style="background:var(--color-bg); border-radius:10px; padding:1.2rem; border-left:3px solid #e74c3c;">
        <h4 style="margin:0 0 .75rem; color:#e74c3c;">⚙️ Admin Controls</h4>
        <ol style="margin:0; padding-left:1.2rem; color:var(--color-text-secondary); line-height:1.9;">
          <li><strong>Users</strong> — view all accounts, change roles, deactivate members</li>
          <li><strong>Audit Logs</strong> — every login, change, and action is recorded</li>
          <li><strong>System Settings</strong> — set default invoice tax, screenshot defaults, company logo</li>
          <li><strong>Session Audit</strong> — deep-dive into every time entry: idle seconds, activity score, screenshots</li>
          <li>Live <strong>Freelancer Presence</strong> panel on Admin Dashboard shows who is online right now</li>
          <li>Timers left running overnight are <strong>auto-closed</strong> by a cron job to protect accuracy</li>
        </ol>
      </div>

      <!-- Tips -->
      <div style="background:var(--color-bg); border-radius:10px; padding:1.2rem; border-left:3px solid #94a3b8;">
        <h4 style="margin:0 0 .75rem; color:#94a3b8;">💡 Tips & Notes</h4>
        <ul style="margin:0; padding-left:1.2rem; color:var(--color-text-secondary); line-height:1.9;">
          <li>The timer widget stays visible as you navigate between pages — you never lose your session</li>
          <li>If you close the browser while the timer runs, it asks what to do when you return</li>
          <li>Dark / Light mode toggle is in the top navigation bar</li>
          <li>Idle detection kicks in after <strong>10 minutes</strong> of no mouse or keyboard activity</li>
          <li>Only approved time entries count toward invoice totals and financial reports</li>
          <li>Each company's data is completely isolated — freelancers from different companies never see each other</li>
        </ul>
      </div>

    </div><!-- /grid -->
  </div><!-- /body -->
</div><!-- /guide-bar -->
<script>
(function(){
  const btn   = document.querySelector('#tf-guide-bar button');
  const body  = document.getElementById('tf-guide-body');
  const arrow = document.getElementById('tf-guide-arrow');
  if (!btn) return;
  btn.addEventListener('click', () => {
    const open = body.style.display !== 'none';
    body.style.display  = open ? 'none' : 'block';
    arrow.style.transform = open ? '' : 'rotate(90deg)';
  });
})();
</script>

<!-- Scripts -->
<script src="/TimeForge_Capstone/js/theme.js"></script>
<script src="/TimeForge_Capstone/js/animations.js"></script>
<!-- Phase 9: html2canvas — used by time_tracker.js for DOM screenshots -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<?php if (isset($_SESSION['user_id'])): ?>
<!-- Presence ping: keeps last_active_at fresh so admin can see who is online -->
<script>
(function () {
    function sendPing() {
        // Only ping if the timer is NOT already sending heartbeats
        if (window.timeTracker && window.timeTracker.startTime) return;
        const fd = new FormData();
        fd.append('action', 'ping');
        navigator.sendBeacon('/TimeForge_Capstone/api/time_tracking.php', fd);
    }
    // Ping immediately on page load, then every 60 seconds
    sendPing();
    setInterval(sendPing, 60000);
})();
</script>
<?php endif; ?>
