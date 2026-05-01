<?php
/**
 * admin/system_settings.php
 * Company-level system settings — admin only.
 * Groups: Company Profile · Timer Behaviour · Invoice Defaults · Monitoring
 */
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../config/settings.php';

requireRole('admin');

$company_id = (int)$_SESSION['company_id'];

/* ── Handle Save ──────────────────────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize & validate each field individually

    // Company Profile
    $display_name = trim($_POST['company_display_name'] ?? '');
    if ($display_name !== '') saveSetting($pdo, $company_id, 'company_display_name', $display_name);

    // Timer Behaviour
    $idle_min   = max(1,  min(120, (int)($_POST['idle_threshold_minutes']  ?? 10)));
    $stale_min  = max(5,  min(480, (int)($_POST['stale_threshold_minutes'] ?? 30)));
    $presence_s = max(60, min(600, (int)($_POST['presence_active_window']  ?? 180)));
    saveSetting($pdo, $company_id, 'idle_threshold_minutes',  (string)$idle_min);
    saveSetting($pdo, $company_id, 'stale_threshold_minutes', (string)$stale_min);
    saveSetting($pdo, $company_id, 'presence_active_window',  (string)$presence_s);

    // Invoice Defaults
    $currency     = in_array($_POST['default_currency'] ?? '', ['CAD','USD','EUR','GBP','AUD']) ? $_POST['default_currency'] : 'CAD';
    $due_days     = max(1, min(365, (int)($_POST['invoice_due_days'] ?? 30)));
    $tax_rate     = max(0, min(50,  (float)str_replace('%', '', $_POST['invoice_tax_rate'] ?? '13')));
    $footer_note  = trim($_POST['invoice_footer_note'] ?? '');
    saveSetting($pdo, $company_id, 'default_currency',    $currency);
    saveSetting($pdo, $company_id, 'invoice_due_days',    (string)$due_days);
    saveSetting($pdo, $company_id, 'invoice_tax_rate',    (string)$tax_rate);
    saveSetting($pdo, $company_id, 'invoice_footer_note', $footer_note);

    // Monitoring
    $ss_default = isset($_POST['screenshots_default_on']) ? '1' : '0';
    saveSetting($pdo, $company_id, 'screenshots_default_on', $ss_default);

    setFlash('success', 'Settings saved successfully.');
    header('Location: /TimeForge_Capstone/admin/system_settings.php');
    exit;
}

/* ── Load current settings ──────────────────────────────────────────── */
$s = getCompanySettings($pdo, $company_id);
$g = fn(string $key, string $def) => $s[$key] ?? $def; // shorthand getter

$currencies = ['CAD' => 'CAD — Canadian Dollar', 'USD' => 'USD — US Dollar',
               'EUR' => 'EUR — Euro', 'GBP' => 'GBP — British Pound', 'AUD' => 'AUD — Australian Dollar'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>System Settings — TimeForge</title>
  <style>
    .settings-grid { display: grid; grid-template-columns: 240px 1fr; gap: 2rem; align-items: start; max-width: 960px; }
    @media(max-width:700px){ .settings-grid { grid-template-columns: 1fr; } }
    .settings-nav { position: sticky; top: 1.5rem; }
    .settings-nav a { display: flex; align-items: center; gap: .6rem; padding: .6rem .85rem; border-radius: 7px; text-decoration: none; color: var(--color-text-secondary); font-size: .88rem; font-weight: 500; margin-bottom: .25rem; transition: background .15s, color .15s; }
    .settings-nav a:hover, .settings-nav a.active { background: #1e293b; color: var(--color-accent); }
    .settings-section { scroll-margin-top: 1.5rem; margin-bottom: 2.5rem; }
    .settings-section h2 { font-size: 1rem; font-weight: 700; color: var(--color-accent); margin: 0 0 1.25rem; padding-bottom: .65rem; border-bottom: 1px solid #334155; display: flex; align-items: center; gap: .5rem; }
    .setting-row { display: grid; grid-template-columns: 220px 1fr; gap: 1.25rem; align-items: start; padding: .9rem 0; border-bottom: 1px solid #1e293b; }
    .setting-row:last-child { border-bottom: none; }
    .setting-label { font-size: .88rem; font-weight: 600; padding-top: .45rem; }
    .setting-hint  { font-size: .75rem; color: #64748b; margin-top: .25rem; line-height: 1.4; }
    .form-control  { width: 100%; background: var(--color-bg); border: 1px solid #334155; color: var(--color-text); border-radius: 6px; padding: .5rem .75rem; font-size: .9rem; }
    .form-control:focus { outline: none; border-color: var(--color-accent); }
    .input-suffix  { display: flex; align-items: center; gap: .5rem; }
    .input-suffix span { color: #64748b; font-size: .85rem; white-space: nowrap; }
    .toggle-row    { display: flex; align-items: center; gap: .85rem; }
    .toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; inset: 0; background: #334155; border-radius: 24px; cursor: pointer; transition: .2s; }
    .toggle-slider::before { content: ''; position: absolute; width: 18px; height: 18px; left: 3px; bottom: 3px; background: #94a3b8; border-radius: 50%; transition: .2s; }
    .toggle-switch input:checked + .toggle-slider { background: #3b82f6; }
    .toggle-switch input:checked + .toggle-slider::before { transform: translateX(20px); background: #fff; }
    .save-bar { position: sticky; bottom: 0; background: var(--color-card); border-top: 1px solid #334155; padding: 1rem 0; margin-top: 1rem; display: flex; gap: 1rem; align-items: center; z-index: 10; }
    .badge-live { display: inline-flex; align-items: center; gap: .3rem; background: #22c55e22; color: #22c55e; border: 1px solid #22c55e44; border-radius: 99px; padding: .15rem .55rem; font-size: .72rem; font-weight: 700; }
  </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header_partial.php'; ?>

<div class="container" style="max-width:1100px; padding: 2rem 1rem;">

  <?php include __DIR__ . '/../includes/flash.php'; ?>

  <div style="margin-bottom:1.75rem;">
    <h1 style="margin:0; color:var(--color-accent);">⚙️ System Settings</h1>
    <p style="color:var(--color-text-secondary); margin:.4rem 0 0; font-size:.9rem;">
      Company-wide defaults — these apply to all projects, users, and features in your account.
    </p>
  </div>

  <form method="POST">
  <div class="settings-grid">

    <!-- ── Sidebar nav ── -->
    <nav class="settings-nav card" style="padding:1rem;">
      <a href="#company"   class="active">🏢 Company Profile</a>
      <a href="#timer">    ⏱️ Timer Behaviour</a>
      <a href="#invoice">  🧾 Invoice Defaults</a>
      <a href="#monitoring">📷 Monitoring</a>
    </nav>

    <!-- ── Main content ── -->
    <div>

      <!-- ── Company Profile ── -->
      <div class="card settings-section" id="company">
        <h2>🏢 Company Profile</h2>

        <div class="setting-row">
          <div>
            <div class="setting-label">Display Name</div>
            <div class="setting-hint">Shown in invoice headers, emails, and the app header.</div>
          </div>
          <input type="text" name="company_display_name" class="form-control"
                 value="<?= htmlspecialchars($g('company_display_name', '')) ?>"
                 placeholder="e.g. Acme Agency">
        </div>
      </div>

      <!-- ── Timer Behaviour ── -->
      <div class="card settings-section" id="timer">
        <h2>⏱️ Timer Behaviour</h2>

        <div class="setting-row">
          <div>
            <div class="setting-label">Idle Timeout</div>
            <div class="setting-hint">Minutes of inactivity before the idle popup appears. Currently hardcoded in JS at 10 min — changing this here documents your policy.</div>
          </div>
          <div class="input-suffix">
            <input type="number" name="idle_threshold_minutes" class="form-control" style="width:100px;"
                   min="1" max="120" value="<?= (int)$g('idle_threshold_minutes','10') ?>">
            <span>minutes</span>
          </div>
        </div>

        <div class="setting-row">
          <div>
            <div class="setting-label">Stale Session Gap</div>
            <div class="setting-hint">If a freelancer returns after this many minutes with a saved timer, they'll be asked what to do with the gap time.</div>
          </div>
          <div class="input-suffix">
            <input type="number" name="stale_threshold_minutes" class="form-control" style="width:100px;"
                   min="5" max="480" value="<?= (int)$g('stale_threshold_minutes','30') ?>">
            <span>minutes</span>
          </div>
        </div>

        <div class="setting-row">
          <div>
            <div class="setting-label">Presence Active Window</div>
            <div class="setting-hint">How long (in seconds) after the last ping a user stays "Online" on the presence panel before showing Idle.</div>
          </div>
          <div class="input-suffix">
            <input type="number" name="presence_active_window" class="form-control" style="width:100px;"
                   min="60" max="600" step="30" value="<?= (int)$g('presence_active_window','180') ?>">
            <span>seconds</span>
            <span class="badge-live">● Live</span>
          </div>
        </div>
      </div>

      <!-- ── Invoice Defaults ── -->
      <div class="card settings-section" id="invoice">
        <h2>🧾 Invoice Defaults</h2>

        <div class="setting-row">
          <div>
            <div class="setting-label">Currency</div>
            <div class="setting-hint">Applied to all new invoices. Existing invoices are not affected.</div>
          </div>
          <select name="default_currency" class="form-control" style="max-width:280px;">
            <?php foreach ($currencies as $code => $label): ?>
              <option value="<?= $code ?>" <?= $g('default_currency','CAD') === $code ? 'selected' : '' ?>>
                <?= $label ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="setting-row">
          <div>
            <div class="setting-label">Payment Due (days)</div>
            <div class="setting-hint">Default net days for new invoices (e.g. 30 = Net 30). Can be overridden per invoice.</div>
          </div>
          <div class="input-suffix">
            <input type="number" name="invoice_due_days" class="form-control" style="width:100px;"
                   min="1" max="365" value="<?= (int)$g('invoice_due_days','30') ?>">
            <span>days</span>
          </div>
        </div>

        <div class="setting-row">
          <div>
            <div class="setting-label">Default Tax Rate</div>
            <div class="setting-hint">Percentage applied on new invoices. Set 0 for tax-exempt. Can be overridden per invoice.</div>
          </div>
          <div class="input-suffix">
            <input type="number" name="invoice_tax_rate" class="form-control" style="width:100px;"
                   min="0" max="50" step="0.5" value="<?= htmlspecialchars($g('invoice_tax_rate','13')) ?>">
            <span>%</span>
          </div>
        </div>

        <div class="setting-row">
          <div>
            <div class="setting-label">Invoice Footer Note</div>
            <div class="setting-hint">Appears at the bottom of every generated invoice PDF.</div>
          </div>
          <textarea name="invoice_footer_note" class="form-control" rows="2"
                    style="resize:vertical;"><?= htmlspecialchars($g('invoice_footer_note','Thank you for your business.')) ?></textarea>
        </div>
      </div>

      <!-- ── Monitoring ── -->
      <div class="card settings-section" id="monitoring">
        <h2>📷 Monitoring</h2>

        <div class="setting-row">
          <div>
            <div class="setting-label">Screenshots ON by Default</div>
            <div class="setting-hint">When a new project is created, screenshots are enabled by default if this is ON. You can still toggle it per project in Edit Project.</div>
          </div>
          <div class="toggle-row">
            <label class="toggle-switch">
              <input type="checkbox" name="screenshots_default_on" value="1"
                     <?= $g('screenshots_default_on','1') === '1' ? 'checked' : '' ?>>
              <span class="toggle-slider"></span>
            </label>
            <span style="font-size:.88rem; color:var(--color-text-secondary);">
              New projects will have screenshots <strong id="ss_label"><?= $g('screenshots_default_on','1') === '1' ? 'enabled' : 'disabled' ?></strong>
            </span>
          </div>
        </div>

        <!-- Read-only info rows -->
        <div style="margin-top:1rem; padding:1rem; background:var(--color-bg); border-radius:8px; border:1px solid #334155;">
          <div style="font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#64748b; margin-bottom:.75rem;">Current Project Screenshot Settings</div>
          <?php
          $proj_ss = $pdo->prepare("SELECT id, project_name, screenshots_enabled, screenshot_min_interval, screenshot_max_interval FROM projects WHERE company_id = :cid AND deleted_at IS NULL ORDER BY project_name");
          $proj_ss->execute([':cid' => $company_id]);
          $proj_rows = $proj_ss->fetchAll(PDO::FETCH_ASSOC);
          ?>
          <?php if ($proj_rows): ?>
          <table style="width:100%; border-collapse:collapse; font-size:.82rem;">
            <thead>
              <tr style="color:#64748b; border-bottom:1px solid #334155; text-align:left;">
                <th style="padding:.35rem .5rem;">Project</th>
                <th style="padding:.35rem .5rem;">Screenshots</th>
                <th style="padding:.35rem .5rem;">Interval</th>
                <th style="padding:.35rem .5rem;"></th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($proj_rows as $pr):
              $mn = (int)$pr['screenshot_min_interval'];
              $mx = (int)$pr['screenshot_max_interval'];
              $interval = ($mn === $mx) ? "every {$mn} min" : "every {$mn}–{$mx} min";
            ?>
            <tr style="border-bottom:1px solid #1e293b;">
              <td style="padding:.4rem .5rem;"><?= htmlspecialchars($pr['project_name']) ?></td>
              <td style="padding:.4rem .5rem;">
                <?php if ($pr['screenshots_enabled']): ?>
                  <span style="color:#22c55e; font-weight:600;">✓ On</span>
                <?php else: ?>
                  <span style="color:#64748b;">✗ Off</span>
                <?php endif; ?>
              </td>
              <td style="padding:.4rem .5rem; color:#94a3b8;">
                <?= $pr['screenshots_enabled'] ? $interval : '—' ?>
              </td>
              <td style="padding:.4rem .5rem;">
                <a href="/TimeForge_Capstone/edit_project.php?id=<?= $pr['id'] ?? '' ?>" 
                   style="color:#3b82f6; font-size:.78rem;">Edit →</a>
              </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
          <?php else: ?>
            <p style="color:#475569; font-size:.85rem; margin:0;">No active projects yet.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- ── Sticky Save Bar ── -->
      <div class="save-bar">
        <button type="submit" class="btn btn-primary">💾 Save Settings</button>
        <a href="/TimeForge_Capstone/admin/dashboard.php" class="btn btn-secondary">Cancel</a>
        <span style="font-size:.8rem; color:#64748b; margin-left:auto;">
          Changes take effect immediately for new invoices, projects, and timer sessions.
        </span>
      </div>

    </div><!-- /main -->
  </div><!-- /grid -->
  </form>

</div>

<?php include __DIR__ . '/../includes/footer_partial.php'; ?>
<script>
// Smooth scroll for sidebar nav
document.querySelectorAll('.settings-nav a').forEach(a => {
    a.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('.settings-nav a').forEach(x => x.classList.remove('active'));
        a.classList.add('active');
        const target = document.querySelector(a.getAttribute('href'));
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});

// Toggle label live update
const ssCheck = document.querySelector('[name="screenshots_default_on"]');
const ssLabel = document.getElementById('ss_label');
if (ssCheck && ssLabel) {
    ssCheck.addEventListener('change', () => {
        ssLabel.textContent = ssCheck.checked ? 'enabled' : 'disabled';
    });
}

// Highlight active section on scroll
const sections = document.querySelectorAll('.settings-section');
const navLinks  = document.querySelectorAll('.settings-nav a');
window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(s => { if (window.scrollY >= s.offsetTop - 80) current = '#' + s.id; });
    navLinks.forEach(a => a.classList.toggle('active', a.getAttribute('href') === current));
}, { passive: true });
</script>
</body>
</html>
