<?php
/**
 * about.php — TimeForge Feature Showcase
 * For capstone testers, instructors, and evaluators.
 */
$page_title = 'About TimeForge';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About TimeForge — Capstone Showcase</title>
  <link rel="stylesheet" href="/TimeForge_Capstone/css/style.css">
  <link rel="icon" type="image/png" href="/TimeForge_Capstone/icons/logo.png">
  <style>
    /* ── Reset / Base ──────────────────────────── */
    * { box-sizing: border-box; }
    body { background: var(--color-bg, #0f172a); color: var(--color-text, #e2e8f0); font-family: 'Segoe UI', system-ui, sans-serif; margin: 0; }

    /* ── Hero ──────────────────────────────────── */
    .hero {
      background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
      text-align: center; padding: 5rem 1.5rem 4rem;
      position: relative; overflow: hidden;
    }
    .hero::before {
      content: ''; position: absolute; inset: 0;
      background: radial-gradient(ellipse 80% 60% at 50% 0%, #3b82f620 0%, transparent 70%);
      pointer-events: none;
    }
    .hero-logo { width: 80px; height: 80px; border-radius: 16px; box-shadow: 0 0 40px #3b82f640; margin-bottom: 1.5rem; }
    .hero h1 { font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 800; margin: 0 0 .75rem;
      background: linear-gradient(135deg, #60a5fa, #a78bfa, #34d399); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
    .hero p  { font-size: 1.15rem; color: #94a3b8; max-width: 640px; margin: 0 auto 2rem; line-height: 1.7; }
    .hero-badges { display: flex; flex-wrap: wrap; gap: .75rem; justify-content: center; margin-bottom: 2rem; }
    .badge { display: inline-flex; align-items: center; gap: .4rem; background: #1e293b; border: 1px solid #334155; border-radius: 99px; padding: .35rem .85rem; font-size: .82rem; color: #94a3b8; }
    .badge strong { color: #e2e8f0; }
    .hero-cta { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
    .btn-hero-primary { background: linear-gradient(135deg,#3b82f6,#6366f1); color: #fff; border: none; border-radius: 8px; padding: .75rem 2rem; font-size: 1rem; font-weight: 700; cursor: pointer; text-decoration: none; transition: transform .15s, box-shadow .15s; }
    .btn-hero-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px #3b82f640; }
    .btn-hero-secondary { background: transparent; color: #e2e8f0; border: 1px solid #334155; border-radius: 8px; padding: .75rem 2rem; font-size: 1rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: border-color .15s; }
    .btn-hero-secondary:hover { border-color: #3b82f6; }

    /* ── Section ───────────────────────────────── */
    section { max-width: 1200px; margin: 0 auto; padding: 4rem 1.5rem; }
    .section-title { text-align: center; margin-bottom: 3rem; }
    .section-title h2 { font-size: clamp(1.5rem, 3vw, 2.2rem); font-weight: 800; margin: 0 0 .5rem; }
    .section-title p  { color: #94a3b8; font-size: .95rem; max-width: 560px; margin: 0 auto; }

    /* ── Feature Cards ─────────────────────────── */
    .feature-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }
    .feature-card {
      background: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 1.5rem;
      transition: transform .2s, border-color .2s, box-shadow .2s; color: #e2e8f0;
    }
    .feature-card:hover { transform: translateY(-4px); border-color: #3b82f6; box-shadow: 0 8px 30px #3b82f620; }
    .feature-icon { font-size: 2rem; margin-bottom: .75rem; }
    .feature-card h3 { font-size: 1rem; font-weight: 700; margin: 0 0 .5rem; color: #f1f5f9; }
    .feature-card p  { font-size: .85rem; color: #94a3b8; margin: 0; line-height: 1.6; }
    .feature-tag { display: inline-block; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; border-radius: 4px; padding: .15rem .45rem; margin-bottom: .6rem; }
    .tag-admin     { background: #7c3aed22; color: #a78bfa; border: 1px solid #7c3aed44; }
    .tag-freelancer{ background: #0284c722; color: #38bdf8; border: 1px solid #0284c744; }
    .tag-client    { background: #05966922; color: #34d399; border: 1px solid #05966944; }
    .tag-system    { background: #92400e22; color: #fbbf24; border: 1px solid #92400e44; }
    .tag-new       { background: #dc262622; color: #f87171; border: 1px solid #dc262644; }

    /* ── Demo Credentials ──────────────────────── */
    .cred-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }
    .cred-card { background: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 1.5rem; color: #e2e8f0; }
    .cred-card h3 { margin: 0 0 1rem; font-size: 1rem; display: flex; align-items: center; gap: .5rem; color: #f1f5f9; }
    .cred-row { display: flex; justify-content: space-between; align-items: center; padding: .4rem 0; border-bottom: 1px solid #1e293b; }
    .cred-row:last-child { border-bottom: none; }
    .cred-label { font-size: .75rem; color: #64748b; text-transform: uppercase; letter-spacing: .05em; font-weight: 600; }
    .cred-val { font-family: 'Courier New', monospace; font-size: .88rem; color: #e2e8f0; background: #0f172a; border-radius: 4px; padding: .2rem .5rem; cursor: pointer; border: 1px solid #334155; }
    .cred-val:hover { border-color: #3b82f6; color: #60a5fa; }

    /* ── Tech Stack ────────────────────────────── */
    .tech-grid { display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center; }
    .tech-pill {
      display: flex; align-items: center; gap: .5rem; background: #1e293b;
      border: 1px solid #334155; border-radius: 8px; padding: .6rem 1rem; font-size: .88rem; font-weight: 600;
      color: #e2e8f0;
    }
    .tech-pill .dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

    /* ── Dark cards — force readable text regardless of body theme ── */
    .feature-card, .feature-card p, .feature-card h3, .feature-card ul, .feature-card li { color: #e2e8f0; }
    .feature-card p, .feature-card li { color: #94a3b8; }
    .feature-card h3 { color: #f1f5f9; }
    .cred-card, .cred-card h3 { color: #e2e8f0; }
    .dark-role-card { color: #e2e8f0 !important; }
    .dark-role-card p, .dark-role-card li { color: #94a3b8; }
    .dark-role-card h3 { color: inherit; }
    .dark-role-card li span[style] { flex-shrink: 0; }

    /* ── Footer ────────────────────────────────── */
    .about-footer { text-align: center; padding: 3rem 1.5rem; color: #475569; font-size: .85rem; border-top: 1px solid #1e293b; }
    .about-footer a { color: #3b82f6; text-decoration: none; }
  </style>
</head>
<body>
<?php
$nav_role = $_SESSION['role'] ?? null;
include __DIR__ . '/includes/header_partial.php';
?>

<!-- ── Hero ──────────────────────────────────────────────────── -->
<div class="hero">
  <img src="/TimeForge_Capstone/icons/logo.png" class="hero-logo" alt="TimeForge Logo" onerror="this.style.display='none'">
  <h1>TimeForge</h1>
  <p>A full-stack time tracking & project management platform built as a Web Capstone at triOS College. Designed for agencies, freelancers, and clients.</p>

  <div class="hero-badges">
    <span class="badge">🏫 <strong>triOS College</strong> — Mobile & Web App Development</span>
    <span class="badge">👩‍💻 <strong>Etefworkie Melaku</strong></span>
    <span class="badge">📅 <strong>2024 – 2026</strong></span>
    <span class="badge">🛠️ PHP 8.1 · MariaDB · Vanilla JS</span>
  </div>

  <div class="hero-cta">
    <?php if (!isset($_SESSION['user_id'])): ?>
      <a href="/TimeForge_Capstone/login.php" class="btn-hero-primary">🔑 Login to Demo</a>
    <?php else: ?>
      <a href="/TimeForge_Capstone/index.php" class="btn-hero-primary">📋 Go to Dashboard</a>
    <?php endif; ?>
    <a href="https://github.com/melakunet/TimeForge_Capstone-" target="_blank" rel="noopener" class="btn-hero-secondary">⭐ GitHub Repo</a>
  </div>
</div>

<!-- ── Feature Showcase ───────────────────────────────────────── -->
<section>
  <div class="section-title">
    <h2>✨ Features</h2>
    <p>15 production-grade features across 12 development phases — all working on this live demo.</p>
  </div>
  <div class="feature-grid">

    <div class="feature-card">
      <div class="feature-tag tag-freelancer">Freelancer</div>
      <div class="feature-icon">⏱️</div>
      <h3>Live Time Tracker</h3>
      <p>Start/stop timer widget persists across page navigation. Tracks elapsed time, sends 1-minute heartbeats, and auto-submits on stop.</p>
    </div>

    <div class="feature-card">
      <div class="feature-tag tag-system">Auto</div>
      <div class="feature-icon">💤</div>
      <h3>Idle Detection Modal</h3>
      <p>After 10 minutes of inactivity, a modal pauses the timer and asks: keep idle time, discard it, or stop. Prevents bloated time entries.</p>
    </div>

    <div class="feature-card">
      <div class="feature-tag tag-system">Auto</div>
      <div class="feature-icon">🔄</div>
      <h3>Stale Session Guard</h3>
      <p>If a user returns after 30+ minutes with a saved timer, a prompt asks them what to do — preventing silent 21-hour ghost time entries.</p>
    </div>

    <div class="feature-card">
      <div class="feature-tag tag-system">Auto</div>
      <div class="feature-icon">📊</div>
      <h3>Activity Score Heartbeat</h3>
      <p>Mouse movements and keystrokes are counted per minute and sent with every heartbeat, generating an activity score per time entry.</p>
    </div>

    <div class="feature-card">
      <div class="feature-tag tag-system">Auto</div>
      <div class="feature-icon">📸</div>
      <h3>Random Screenshot Capture</h3>
      <p>Every 5–15 minutes while the timer runs, the visible page is captured via html2canvas and uploaded. Admin views screenshots per session.</p>
    </div>

    <div class="feature-card">
      <div class="feature-tag tag-admin">Admin</div>
      <div class="feature-icon">🖼️</div>
      <h3>Screenshot Gallery</h3>
      <p>Admin views all screenshots with filters by worker, project, and date. Images are served through a secure proxy — no direct file access.</p>
    </div>

    <div class="feature-card">
      <div class="feature-tag tag-admin">Admin</div>
      <div class="feature-icon">🟢</div>
      <h3>Live Presence Panel</h3>
      <p>Polls every 10 seconds. Shows Online / Active (timer running) / Idle / Offline status with last-seen time and current project for each team member.</p>
    </div>

    <div class="feature-card">
      <div class="feature-tag tag-admin">Admin</div>
      <div class="feature-icon">🔐</div>
      <h3>Login & Security Audit Logs</h3>
      <p>Every login attempt (success or fail), logout, and role escalation is logged with IP, user agent, and timestamp. Filterable by user/action.</p>
    </div>

    <div class="feature-card">
      <div class="feature-tag tag-admin">Admin</div>
      <div class="feature-icon">📁</div>
      <h3>Project Management</h3>
      <p>Create projects with budget, hourly rate, deadline, and client. Visual progress bar from time logged vs. estimated hours. Soft-delete with restore.</p>
    </div>

    <div class="feature-card">
      <div class="feature-tag tag-admin">Admin</div>
      <div class="feature-icon">👥</div>
      <h3>Client Management</h3>
      <p>Create client records linked to user accounts. Clients get their own portal login to view only their projects, reports, and invoices.</p>
    </div>

    <div class="feature-card">
      <div class="feature-tag tag-admin">Admin</div>
      <div class="feature-icon">📄</div>
      <h3>PDF Invoicing</h3>
      <p>Generate invoices from approved time entries using dompdf. Choose from 3 templates (Bold, Classic, Corporate), add notes, send via email.</p>
    </div>

    <div class="feature-card">
      <div class="feature-tag tag-admin">Admin</div>
      <div class="feature-icon">💳</div>
      <h3>Payment Tracking</h3>
      <p>Mark invoices as paid, record partial payments, track outstanding balances. Full payment history per invoice.</p>
    </div>

    <div class="feature-card">
      <div class="feature-tag tag-admin">Admin</div>
      <div class="feature-icon">📈</div>
      <h3>Financial Reports</h3>
      <p>Revenue breakdown by project and freelancer. Billable vs. non-billable hours. Export any report to CSV in one click.</p>
    </div>

    <div class="feature-card">
      <div class="feature-tag tag-client">Client</div>
      <div class="feature-icon">🌐</div>
      <h3>Client Portal</h3>
      <p>Dedicated client view — their projects, time logs, invoices, and payments only. Fully isolated from other company data.</p>
    </div>

    <div class="feature-card">
      <div class="feature-tag tag-system">System</div>
      <div class="feature-icon">🏢</div>
      <h3>Multi-Tenancy</h3>
      <p>Every record is scoped by <code>company_id</code>. Multiple companies can run on the same instance with zero data crossover.</p>
    </div>

    <div class="feature-card">
      <div class="feature-tag tag-new">NEW — Phase 11</div>
      <div class="feature-icon">📋</div>
      <h3>Task Management</h3>
      <p>Kanban board (Open → In Progress → Done) under each project. Tasks have assignee, priority, estimated hours, due date, and logged-time progress bars. Timer lets you pick a task before starting.</p>
    </div>

    <div class="feature-card">
      <div class="feature-tag tag-new">NEW — Phase 12</div>
      <div class="feature-icon">💬</div>
      <h3>Task Discussion Thread</h3>
      <p>Every task has a private 3-way chat thread between the <strong>Admin</strong>, the assigned <strong>Freelancer</strong>, and the <strong>Client</strong>. Four message types keep communication structured:</p>
      <ul style="margin:.6rem 0 0 1rem; padding:0; font-size:.82rem; color:#94a3b8; line-height:1.9; list-style:none;">
        <li>💬 <strong style="color:#a5b4fc;">Note</strong> — general update (all roles)</li>
        <li>🐛 <strong style="color:#f87171;">Problem Found</strong> — flag a blocker (admin + freelancer)</li>
        <li>💡 <strong style="color:#34d399;">Solution / Suggestion</strong> — propose a fix (admin + freelancer)</li>
        <li>⭐ <strong style="color:#fcd34d;">Feedback / Objection</strong> — client review or concern (client only)</li>
      </ul>
      <p style="margin-top:.6rem; font-size:.82rem; color:#64748b;">Task cards show a 🐛 badge when an unresolved problem exists. Thread is readable by all three parties but write access is role-scoped.</p>
    </div>

    <div class="feature-card">
      <div class="feature-tag tag-system">Everyone</div>
      <div class="feature-icon">🌙</div>
      <h3>Dark / Light Theme</h3>
      <p>Full CSS variable-driven theme switching. Preference saved in localStorage. Animated transitions on toggle.</p>
    </div>

    <div class="feature-card">
      <div class="feature-tag tag-freelancer">Freelancer</div>
      <div class="feature-icon">✍️</div>
      <h3>Manual Time Entry</h3>
      <p>Submit time entries for past work with start time, end time, and description. Admin can approve or reject entries before billing.</p>
    </div>

  </div>
</section>

<!-- ── Demo Credentials ───────────────────────────────────────── -->
<section style="background:#1e293b; border-radius:16px; margin-bottom:3rem; color:#e2e8f0;">
  <div class="section-title" style="padding-top:3rem;">
    <h2 style="color:#f1f5f9;">🔑 Demo Login Credentials</h2>
    <p style="color:#94a3b8;">Click any value to copy it. All accounts belong to the same demo company.</p>
  </div>
  <div class="cred-grid" style="padding: 0 1.5rem 3rem;">

    <div class="cred-card" style="border-color:#7c3aed44;">
      <h3>🛡️ Admin</h3>
      <div class="cred-row">
        <span class="cred-label">Email</span>
        <span class="cred-val" onclick="copyText(this)">etef@email.com</span>
      </div>
      <div class="cred-row">
        <span class="cred-label">Password</span>
        <span class="cred-val" onclick="copyText(this)">password123</span>
      </div>
      <div class="cred-row">
        <span class="cred-label">Access</span>
        <span style="font-size:.8rem; color:#a78bfa;">Full admin — all features</span>
      </div>
    </div>

    <div class="cred-card" style="border-color:#0284c744;">
      <h3>💼 Freelancer</h3>
      <div class="cred-row">
        <span class="cred-label">Email</span>
        <span class="cred-val" onclick="copyText(this)">abegaile@email.com</span>
      </div>
      <div class="cred-row">
        <span class="cred-label">Password</span>
        <span class="cred-val" onclick="copyText(this)">password123</span>
      </div>
      <div class="cred-row">
        <span class="cred-label">Access</span>
        <span style="font-size:.8rem; color:#38bdf8;">Timer, tasks, time entries</span>
      </div>
    </div>

    <div class="cred-card" style="border-color:#05966944;">
      <h3>🤝 Client</h3>
      <div class="cred-row">
        <span class="cred-label">Email</span>
        <span class="cred-val" onclick="copyText(this)">client@demo.com</span>
      </div>
      <div class="cred-row">
        <span class="cred-label">Password</span>
        <span class="cred-val" onclick="copyText(this)">password123</span>
      </div>
      <div class="cred-row">
        <span class="cred-label">Access</span>
        <span style="font-size:.8rem; color:#34d399;">Client portal — projects, tasks, feedback &amp; invoices</span>
      </div>
    </div>

  </div>
  <p style="text-align:center; padding: 0 1.5rem 2rem; color:#475569; font-size:.82rem;">⚠️ Demo account — please don't change passwords.</p>
</section>

<!-- ── Who It's For ───────────────────────────────────────────── -->
<section>
  <div class="section-title">
    <h2>� Who It's For</h2>
    <p>Three distinct login roles — each with a tailored interface and access level.</p>
  </div>

  <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px,1fr)); gap:2rem;">

    <!-- Admin -->
    <div class="dark-role-card" style="background:#1e293b; border:1px solid #7c3aed44; border-radius:14px; padding:2rem; position:relative; overflow:hidden;">
      <div style="position:absolute; top:0; left:0; right:0; height:4px; background:linear-gradient(90deg,#7c3aed,#a78bfa);"></div>
      <div style="font-size:2.5rem; margin-bottom:.75rem;">🛡️</div>
      <h3 style="font-size:1.15rem; font-weight:800; color:#a78bfa; margin:0 0 .4rem;">Admin</h3>
      <p style="color:#94a3b8; font-size:.85rem; margin:0 0 1.25rem; line-height:1.6;">
        Full platform control — manage the team, projects, billing, and monitoring from one dashboard.
      </p>
      <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:.55rem;">
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#a78bfa; flex-shrink:0;">✓</span> Create &amp; manage projects, set budgets and deadlines</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#a78bfa; flex-shrink:0;">✓</span> Approve or reject freelancer time entries</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#a78bfa; flex-shrink:0;">✓</span> Build Kanban task boards per project</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#a78bfa; flex-shrink:0;">✓</span> Post notes, solutions &amp; replies in per-task discussion threads</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#a78bfa; flex-shrink:0;">✓</span> Generate PDF invoices, send by email, track payments</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#a78bfa; flex-shrink:0;">✓</span> View live presence panel (Online / Active / Idle / Offline)</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#a78bfa; flex-shrink:0;">✓</span> Browse screenshot gallery and activity scores</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#a78bfa; flex-shrink:0;">✓</span> Financial reports with CSV export</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#a78bfa; flex-shrink:0;">✓</span> Security &amp; login audit logs</li>
      </ul>
      <div style="margin-top:1.5rem;">
        <a href="/TimeForge_Capstone/login.php" style="display:inline-block; background:#7c3aed22; color:#a78bfa; border:1px solid #7c3aed55; border-radius:7px; padding:.5rem 1.1rem; font-size:.85rem; font-weight:700; text-decoration:none;">
          Login as Admin →
        </a>
      </div>
    </div>

    <!-- Freelancer -->
    <div class="dark-role-card" style="background:#1e293b; border:1px solid #0284c744; border-radius:14px; padding:2rem; position:relative; overflow:hidden;">
      <div style="position:absolute; top:0; left:0; right:0; height:4px; background:linear-gradient(90deg,#0284c7,#38bdf8);"></div>
      <div style="font-size:2.5rem; margin-bottom:.75rem;">💼</div>
      <h3 style="font-size:1.15rem; font-weight:800; color:#38bdf8; margin:0 0 .4rem;">Freelancer</h3>
      <p style="color:#94a3b8; font-size:.85rem; margin:0 0 1.25rem; line-height:1.6;">
        Focus on the work — start a timer, pick a task, and let TimeForge handle the rest.
      </p>
      <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:.55rem;">
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#38bdf8; flex-shrink:0;">✓</span> Start/stop live timer — persists across page navigation</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#38bdf8; flex-shrink:0;">✓</span> Pick a task before starting (auto-links time to task)</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#38bdf8; flex-shrink:0;">✓</span> Submit manual time entries for past work</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#38bdf8; flex-shrink:0;">✓</span> View assigned tasks — move Open → In Progress → Done</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#38bdf8; flex-shrink:0;">✓</span> Post notes, flag problems 🐛, and suggest solutions 💡 on tasks</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#38bdf8; flex-shrink:0;">✓</span> Idle popup: decide what to do with inactive time</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#38bdf8; flex-shrink:0;">✓</span> Activity score tracked per session (mouse + keyboard)</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#38bdf8; flex-shrink:0;">✓</span> View all projects and their time history</li>
      </ul>
      <div style="margin-top:1.5rem;">
        <a href="/TimeForge_Capstone/login.php" style="display:inline-block; background:#0284c722; color:#38bdf8; border:1px solid #0284c755; border-radius:7px; padding:.5rem 1.1rem; font-size:.85rem; font-weight:700; text-decoration:none;">
          Login as Freelancer →
        </a>
      </div>
    </div>

    <!-- Client -->
    <div class="dark-role-card" style="background:#1e293b; border:1px solid #05966944; border-radius:14px; padding:2rem; position:relative; overflow:hidden;">
      <div style="position:absolute; top:0; left:0; right:0; height:4px; background:linear-gradient(90deg,#059669,#34d399);"></div>
      <div style="font-size:2.5rem; margin-bottom:.75rem;">🤝</div>
      <h3 style="font-size:1.15rem; font-weight:800; color:#34d399; margin:0 0 .4rem;">Client</h3>
      <p style="color:#94a3b8; font-size:.85rem; margin:0 0 1.25rem; line-height:1.6;">
        Stay informed without the noise — see only your projects, your bills, your status.
      </p>
      <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:.55rem;">
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#34d399; flex-shrink:0;">✓</span> Dedicated portal login — sees only their own data</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#34d399; flex-shrink:0;">✓</span> View active projects with progress &amp; time logged</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#34d399; flex-shrink:0;">✓</span> See task breakdown per project (status + progress bars)</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#34d399; flex-shrink:0;">✓</span> Open any task and post ⭐ feedback or flag concerns directly</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#34d399; flex-shrink:0;">✓</span> Read replies from the admin and assigned worker in real time</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#34d399; flex-shrink:0;">✓</span> Access invoices — view, download PDF</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#34d399; flex-shrink:0;">✓</span> View payment history per invoice</li>
        <li style="font-size:.85rem; display:flex; gap:.6rem; align-items:flex-start;"><span style="color:#34d399; flex-shrink:0;">✓</span> Project time reports showing who worked and when</li>
      </ul>
      <div style="margin-top:1.5rem;">
        <a href="/TimeForge_Capstone/login.php" style="display:inline-block; background:#05966922; color:#34d399; border:1px solid #05966955; border-radius:7px; padding:.5rem 1.1rem; font-size:.85rem; font-weight:700; text-decoration:none;">
          Login as Client →
        </a>
      </div>
    </div>

  </div>
</section>

<!-- ── Tech Stack ─────────────────────────────────────────────── -->
<section style="text-align:center;">
  <div class="section-title">
    <h2>🛠️ Tech Stack</h2>
    <p>Chosen for production relevance, capstone requirements, and XAMPP local hosting.</p>
  </div>
  <div class="tech-grid">
    <div class="tech-pill"><span class="dot" style="background:#7c52d9;"></span>PHP 8.1</div>
    <div class="tech-pill"><span class="dot" style="background:#00758f;"></span>MariaDB 10.4</div>
    <div class="tech-pill"><span class="dot" style="background:#f7df1e;"></span>Vanilla JavaScript (ES2022)</div>
    <div class="tech-pill"><span class="dot" style="background:#1572b6;"></span>CSS3 (Variables + Grid)</div>
    <div class="tech-pill"><span class="dot" style="background:#ff6600;"></span>XAMPP (Apache)</div>
    <div class="tech-pill"><span class="dot" style="background:#61dafb;"></span>React 18 (reports panel)</div>
    <div class="tech-pill"><span class="dot" style="background:#e34c26;"></span>HTML5 (html2canvas)</div>
    <div class="tech-pill"><span class="dot" style="background:#5c8ea1;"></span>dompdf (PDF)</div>
    <div class="tech-pill"><span class="dot" style="background:#ffd700;"></span>PHPMailer (email)</div>
    <div class="tech-pill"><span class="dot" style="background:#333;"></span>Composer (packages)</div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer_partial.php'; ?>
<script src="/TimeForge_Capstone/js/theme.js"></script>
<script>
function copyText(el) {
    navigator.clipboard.writeText(el.textContent.trim()).then(() => {
        const orig = el.textContent;
        el.textContent = '✓ Copied!';
        el.style.color = '#22c55e';
        setTimeout(() => { el.textContent = orig; el.style.color = ''; }, 1500);
    });
}
</script>
</body>
</html>
