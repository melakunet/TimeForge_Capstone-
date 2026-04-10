# TimeForge — Freelancer Time Tracking & Invoicing Platform

**Student:** Etefworkie Melaku  
**Course:** Mobile and Web App Development  
**Institution:** triOS College  
**Last Updated:** April 10, 2026

---

## Project Description

TimeForge is a full-stack PHP/MariaDB web application that helps freelancers and small agencies capture every billable hour, generate professional invoices, and track payments through to completion.

Three roles are supported: **Admin** (full control), **Freelancer** (time logging), and **Client** (portal view). The UI supports dark mode and works on desktop and mobile.

---

## Features

### Phase 1 — Authentication
- Registration, login, and logout
- Role-based access control (Admin, Freelancer, Client)
- bcrypt password hashing and session management

### Phase 2 — Project Management
- Create, view, archive (soft delete), and restore projects
- Budget and deadline tracking with role-based visibility

### Phase 3 — Client Management
- Add and edit clients with duplicate email prevention
- Search and filter by name, company, email, and status

### Phase 4 — Time Tracking
- Real-time JavaScript timer with start/stop/pause
- Manual time entry for retrospective or offline work
- Live presence heartbeat API
- Admin approve/reject workflow per time entry

### Phase 5 — Client Portal
- Client dashboard with project summary and billed hours
- Per-project financial report
- 403 access-denied page

### Phase 6 — Idle Detection & Session Integrity
- Idle popup pauses the timer after inactivity
- Stale session guard prevents ghost timers on resume
- Cron job auto-closes abandoned sessions

### Phase 7 — Reporting & Invoicing
- 5 invoice templates: Classic, Modern, Bold, Minimal, Corporate
- PDF download via Dompdf (rootDir + chroot fix applied)
- Invoice history with status badges
- Admin financial reports and CSV export
- Invoice PDF header dynamically shows creator's company name and tagline (not hardcoded app branding)

### Phase 8 — Payment Lifecycle
Full 8-step invoice lifecycle with timestamps, partial payments, client feedback, and auto-overdue detection.

```
Draft → Sent → Viewed → [Overdue] → Partial → Paid → Completed
                                                     → Cancelled
```

| Component | Description |
|---|---|
| `invoices/payment_action.php` | POST handler for all lifecycle transitions |
| `invoices/view.php` | Stepper, action buttons, inline forms, feedback |
| `invoices/history.php` | All 8 status badges, overdue highlighting |
| `sql/06_Phase8_PaymentTracking.sql` | ENUM expansion + 8 new tracking columns |

---

## Phase 9 — Production Hardening (April 10, 2026)

This phase covers four areas of work completed in the final capstone session:

### 9.1 — Codebase Comment Cleanup
Removed all AI-generated Phase/Task/Step/Backward-compat inline comments across ~30 files. Code documentation was standardised to clean, minimal comments only where genuinely useful.

- **Commit:** `96e0bb3` — *Cleanup: remove outdated comments and simplify inline documentation*

---

### 9.2 — Invoice Email Sending with PDF Attachment

Added the ability for admins to email invoices directly from the platform as a PDF attachment using PHPMailer (Mailtrap sandbox).

**New files:**

| File | Purpose |
|---|---|
| `invoices/send.php` | Generates PDF in memory via Dompdf, attaches to email via PHPMailer, updates DB |
| `sql/09_InvoiceEmail.sql` | Adds `sent_to_email` and `email_sent_at` columns to `invoices` table |

**Changes to `invoices/view.php`:**
- Draft invoices: replaced "Mark as Sent" with a **📤 Send via Email** form (pre-filled client email + optional message)
- Sent/Overdue invoices: added **🔁 Resend Invoice** form showing the last-sent date
- Status chip shows **✉ Emailed Apr 10, 2026** badge (blue) with `title` tooltip showing recipient address

**Email flow:**
1. Admin fills in recipient email + optional note → POST to `invoices/send.php`
2. PHP generates full invoice PDF in memory (no temp files on disk)
3. PHPMailer attaches PDF as `Invoice-{number}.pdf`
4. On success: `invoices` table updated — `status='sent'`, `sent_at`, `sent_to_email`, `email_sent_at`
5. Reply-To is set to the logged-in user's email address

- **Commit:** `99f48f5` — *Feature: invoice email sending with PDF attachment and resend support*

---

### 9.3 — Multi-Tenancy Isolation Audit & Fixes

A full audit of all data queries was performed to verify that every page correctly scopes its data to the logged-in user's `company_id`. Four gaps were found and fixed.

**Gaps found and fixed:**

| File | Problem | Fix Applied |
|---|---|---|
| `project_details.php` | Project fetch had no `company_id` check — any user could view any project by ID | Added `AND p.company_id = :company_id` to the SELECT |
| `invoices/history.php` | Admin could see all companies' invoices | Added `WHERE inv.company_id = :company_id` |
| `admin/reports.php` | All 3 revenue queries + client dropdown unscoped | All queries scoped to `company_id` |
| `admin/dashboard.php` | Stats (projects/invoices/clients) were hardcoded dummy values (3, 2, 2) | Replaced with live DB queries filtered by `company_id` |
| `freelancer/dashboard.php` | Earnings and hours were hardcoded dummy values ($312.50, 5.5hrs) | Replaced with live per-user queries from `time_entries` and `invoices` |

- **Commit:** `5e38834` — *Fix: company isolation on project details, invoice history, reports, and dashboards*

---

### 9.4 — Time Tracking System Audit

User reported the time tracking system appeared to be broken. A complete audit was performed across all components. **All files were confirmed intact.** One multi-tenancy gap in the presence API was found and fixed.

**System confirmed fully operational:**

| Component | Lines | Status |
|---|---|---|
| `js/time_tracker.js` | 481 | ✅ Full `TimeTracker` class intact |
| `css/time_tracker.css` | 274 | ✅ Floating widget styles intact |
| `api/time_tracking.php` | 179 | ✅ start/stop/pulse/idle_resolved all working |
| `api/presence.php` | 93 | ✅ Fixed — was missing `company_id` scope |
| `admin/dashboard_quick_start.php` | 100 | ✅ Project list + task-type modal intact |
| `freelancer/quick_start.php` | — | ✅ Matching quick-start panel intact |
| `cron/auto_close_timers.php` | — | ✅ Auto-closes abandoned running sessions |

**`api/presence.php` fix:** The presence endpoint returned freelancers from all companies. Added `AND u.company_id = :company_id` to the query and switched from `->query()` to a prepared statement with bound parameter.

**Timer widget load points confirmed:**
- `index.php` — widget loaded, `startProjectTimer()` defined inline
- `project_details.php` — ▶ Start Timer button + `window.timeTracker.startTimer()`
- `admin/dashboard.php` — widget loaded via `<script src>`
- `freelancer/dashboard.php` — widget loaded via `<script src>`
- `includes/footer_partial.php` — widget loaded as global fallback

**How the timer works end-to-end:**
1. User clicks ▶ Start Timer on any project page or dashboard quick-start panel
2. `TimeTracker.startTimer()` POSTs `action=start` to `api/time_tracking.php` → inserts `time_entries` row, returns `entry_id`
3. Floating widget appears bottom-right, counting live elapsed time + updating browser tab title
4. Heartbeat POSTs `action=pulse` every 60 seconds with mouse/key event counts and activity score → stored in `session_activity` table
5. Idle detection fires after 10 minutes of no input → modal prompts "Keep", "Discard Idle Time", or "Stop Timer"
6. Stale session guard on page reload: if last save > 30 minutes ago, prompts to discard or continue
7. Stop → confirm dialog → POSTs `action=stop` → `time_entries` row updated with `end_time`, `total_seconds`, `idle_seconds`, `activity_score_avg`

- **Commit:** `487df06` — *Fix: scope presence API to company_id*

---

## Getting Started

### Requirements
- XAMPP (Apache + MariaDB + PHP 8.1+)
- Composer (`composer install` to restore `vendor/`)

### Setup

1. Copy project to XAMPP htdocs:
   ```
   /Applications/XAMPP/xamppfiles/htdocs/TimeForge_Capstone
   ```

2. Start Apache and MySQL in XAMPP.

3. Run SQL migrations **in order**:
   ```
   sql/00_schema.sql
   sql/01_Phase4_TimeTracking.sql
   sql/02_Phase4_ManualEntry_Update.sql
   sql/03_Phase6_IdleTracking.sql
   sql/04_Phase7_Invoicing.sql
   sql/05_Phase7b_Templates.sql
   sql/06_Phase8_PaymentTracking.sql
   sql/07_UserCompanyProfile.sql
   sql/08_MultiTenancy_companies.sql
   sql/09_InvoiceEmail.sql
   ```

4. Open: `http://localhost/TimeForge_Capstone/`

---

## Test Credentials

| Role | Username | Password |
|---|---|---|
| Admin | admin_user | password123 |
| Freelancer | dev_sarah | password123 |
| Client | client_bob | password123 |

*See `CREDENTIALS.md` (local only — not in repo).*

---

## Project Structure

```
TimeForge_Capstone/
├── config/
│   ├── session.php
│   ├── theme.php
│   └── database.php       # Single PDO connection source
├── includes/              # Layout partials, auth, flash, redirect
├── admin/                 # Admin portal pages + quick-start panel
├── client/                # Client portal pages
├── freelancer/            # Freelancer portal pages
├── invoices/              # Generate, view, download, send, history, payment
├── api/                   # time_tracking.php, presence.php, export_csv.php
├── cron/                  # Auto-close abandoned timers
├── css/                   # Global stylesheets (style.css, time_tracker.css, …)
├── js/                    # Frontend scripts (time_tracker.js, presence.js, …)
├── sql/                   # Numbered migration files (00 → 09)
├── composer.json
├── composer.lock
├── db.php                 # Thin wrapper → config/database.php
└── index.php
```

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.1 |
| Database | MariaDB 10.4 via PDO |
| PDF | Dompdf 3.x |
| Frontend | HTML5, CSS3, Vanilla JS |
| Server | Apache via XAMPP |
| Version Control | Git + GitHub |

---

## Security

- bcrypt password hashing
- PDO prepared statements throughout
- `htmlspecialchars()` on all output
- `requireRole()` enforced on every protected page
- `company_id` isolation on every query — full multi-tenancy
- Soft deletes — no accidental data loss from UI
- `config/mail.php` excluded from version control (contains SMTP credentials)

---

## Commit History (Selected)

| Hash | Description |
|---|---|
| `487df06` | Fix: scope presence API to company_id |
| `5e38834` | Fix: company isolation on project details, invoice history, reports, and dashboards |
| `99f48f5` | Feature: invoice email sending with PDF attachment and resend support |
| `96e0bb3` | Cleanup: remove outdated comments and simplify inline documentation |
| `78c9b32` | Feature: Multi-tenancy — company isolation across all tables and queries |
| `05684a0` | Fix: invoice header uses creator company name — no hardcoded app branding |
| `262c5ed` | DB: update master dump — add company_name and business_tagline to users |
| `c23869b` | Feature: dynamic invoice sender, user profile page, company branding |
| `5668362` | Add full database dump for fresh environment setup |
| `0bfddaf` | Fix: resolve Dompdf font path by setting rootDir and chroot explicitly |
| `fe31498` | Phase 7.1: Set up Email Infrastructure (PHPMailer, Config, Templates) |

---

## Author

**Etefworkie Melaku**  
Mobile and Web App Development — Capstone Project  
triOS College, 2026
