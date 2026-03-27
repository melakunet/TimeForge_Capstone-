# TimeForge — Freelancer Time Tracking & Invoicing Platform

**Student:** Etefworkie Melaku
**Course:** Mobile and Web App Development
**Institution:** triOS College
**Last Updated:** March 2026

---

## Project Description

TimeForge is a full-stack PHP/MariaDB web application that helps freelancers and small agencies capture every billable hour, generate professional invoices, and track payments through to completion — replacing manual spreadsheets with a structured, role-aware workflow.

The system supports three roles: **Admin** (full control), **Freelancer** (time logging), and **Client** (portal view). Every feature is accessible from a clean, dark-mode-capable UI that works on desktop and mobile.

---

## Why This Project Matters

### Problem It Solves

Freelancers routinely lose 10–20% of their billable revenue through forgotten time, informal payment agreements, and no visibility into whether an invoice has even been seen by the client. TimeForge addresses all three:

| Pain Point | TimeForge Solution |
|---|---|
| Missed billable minutes | Real-time timer + idle detection + manual entry |
| No proof of work | Approval workflow — every entry reviewed before billing |
| Invoices sent into the void | 8-step payment lifecycle with timestamps and follow-up notes |
| Client disputes | Built-in client feedback field on every invoice |
| Late payments undetected | Auto-overdue flip + due-date countdown on every invoice |

### Academic Significance

This project demonstrates end-to-end full-stack development: database design (normalized schema, foreign keys, multi-phase migrations), server-side PHP with PDO, role-based access control, PDF generation (Dompdf), a REST API layer, CSV export, and a multi-template invoicing system — all built without a framework.

---

## Features Implemented

### Phase 1 — Authentication System
- User registration and login
- Role-based access control (Admin, Freelancer, Client)
- Secure bcrypt password hashing
- Session management and logout

### Phase 2 — Project Management
- Create, view, archive (soft delete), and restore projects
- Role-based project visibility
- Budget and deadline tracking

### Phase 3 — Client Management
- Add and edit clients with duplicate prevention
- Client list with search (name, company, email, phone) and status filter
- Role-based permissions — Admin and Freelancer manage, Client views own

### Phase 4 — Time Tracking
- Real-time JavaScript timer with start/stop/pause and localStorage persistence
- Manual time entry modal for offline or retrospective work
- Live presence heartbeat API
- Mobile-responsive layout

### Phase 4.5 — Project Hub & Approval Workflow
- `project_details.php` — centralized hub for entries, financials, and approvals
- Budget tracking with real-time remaining-budget calculation
- Admin approve/reject time entries with reason logging
- Edit and delete controls with role-based guards

### Phase 5 — Client Portal
- Client dashboard with total projects, approved hours, and total billed
- My Projects page with status filter and budget progress bars
- Per-project report with full financial breakdown
- 403 access-denied page replacing all raw `die()` calls

### Phase 6 — Idle Detection & Session Integrity
- Idle popup modal — pauses timer after inactivity
- Stale session guard — prevents ghost timers on laptop resume
- Activity score heartbeat — keyboard and mouse events tracked per minute
- Server-side cron auto-closes abandoned sessions
- Discard idle toggle for admins

### Phase 7 — Reporting & Invoicing
- Pre-flight modal on `project_details.php` — shows entry count, rate, and logo preview before generating
- `invoices/generate.php` — 5 visual templates (Classic, Modern, Bold, Minimal, Corporate) with live color swatches
- `invoices/view.php` — rendered invoice with template switcher in toolbar
- `invoices/download.php` — Dompdf PDF generation with DejaVu font and UTF-8 support
- `invoices/history.php` — invoice list with status badges for admin and client
- `admin/reports.php` — financial summary and profitability analysis
- `api/export_csv.php` — CSV export of approved time entries per project

### Phase 8 — Payment Tracking & Invoice Lifecycle

This phase upgrades the invoice system from a simple Draft/Sent/Paid dropdown into a complete 8-step payment follow-up workflow that gives the freelancer full visibility into where every invoice stands.

**Invoice lifecycle:**

```
Draft -> Sent -> Viewed -> [Overdue] -> Partial -> Paid -> Completed
                                                        -> Cancelled
```

**What was built:**

| Component | What it does |
|---|---|
| `sql/Phase8_PaymentTracking.sql` | Expands status ENUM to 8 values; adds 8 tracking columns |
| `invoices/payment_action.php` | Central POST handler for all transitions (admin) and client feedback |
| Visual stepper in `view.php` | 6-dot progress bar showing current position in the lifecycle |
| Smart action buttons | Only valid next-steps shown per current status |
| Inline action forms | Payment method, reference/transaction ID, and notes open on click |
| Auto-overdue detection | Sent/Viewed invoices past due date flip to Overdue automatically |
| Due-date countdown | "Due in 3 days" / "Due today!" / "5 days overdue" chip |
| Partial payment recording | Logs partial amount, shows remaining balance |
| Client feedback | Clients submit dispute notes directly on the invoice |
| Admin follow-up notes | Save internal notes at any stage without changing status |
| History page upgrade | All 8 status badges, overdue row highlighting, partial amount shown |
| Dark mode | Full dark-mode support for all new payment panel components |

**New database columns added to `invoices`:**

| Column | Type | Purpose |
|---|---|---|
| `sent_at` | DATETIME | Timestamp when marked Sent |
| `viewed_at` | DATETIME | Timestamp when client viewed |
| `paid_at` | DATETIME | Timestamp when full payment confirmed |
| `partial_amount` | DECIMAL(10,2) | Amount received in a partial payment |
| `payment_method` | VARCHAR(50) | Bank Transfer, PayPal, Stripe, Cheque, Cash, etc. |
| `payment_reference` | VARCHAR(100) | Transaction ID or cheque number |
| `payment_notes` | TEXT | Admin internal follow-up notes |
| `client_feedback` | TEXT | Client dispute or question notes |

---

## Getting Started

### Prerequisites
- XAMPP (Apache + MariaDB + PHP 8.1+)
- Composer dependencies already in `vendor/` (Dompdf)
- Modern web browser

### Installation

1. Copy project to XAMPP htdocs:
   ```
   /Applications/XAMPP/xamppfiles/htdocs/TimeForge_Capstone
   ```

2. Start XAMPP — Apache and MySQL.

3. Import SQL files in order:
   ```
   sql/TimeForge_Capstone.sql        -- base schema + seed data
   sql/Phase7_Invoicing.sql          -- invoices table
   sql/Phase7b_Templates.sql         -- template column
   sql/Phase8_PaymentTracking.sql    -- payment lifecycle columns
   ```

4. Open: `http://localhost/TimeForge_Capstone/`

---

## Test Credentials

| Role | Username | Password |
|---|---|---|
| Admin | admin_user | password123 |
| Freelancer | dev_sarah | password123 |
| Client | client_bob | password123 |

*See `CREDENTIALS.md` for complete list.*

---

## Project Structure

```
TimeForge_Capstone/
├── index.php                          # Landing / main dashboard
├── login.php / register.php           # Auth pages
├── project_details.php                # Project hub — entries, budget, approvals, invoice trigger
├── clients.php                        # Client list
├── admin/
│   ├── dashboard.php
│   ├── reports.php                    # Financial reports
│   ├── users.php
│   └── audit_logs.php
├── client/
│   ├── dashboard.php
│   ├── projects.php
│   └── project_report.php
├── freelancer/
│   ├── dashboard.php
│   └── quick_start.php
├── invoices/
│   ├── generate.php                   # Template picker + line-item preview
│   ├── view.php                       # Invoice viewer + payment tracking panel (Phase 8)
│   ├── download.php                   # Dompdf PDF generation
│   ├── history.php                    # Invoice list with status badges
│   ├── payment_action.php             # POST handler for all lifecycle transitions (Phase 8)
│   └── templates/                     # classic / modern / bold / minimal / corporate
├── api/
│   ├── time_tracking.php              # Timer API
│   ├── presence.php                   # Heartbeat API
│   └── export_csv.php                 # CSV export
├── cron/
│   └── auto_close_timers.php          # Abandons stale running timers
├── css/
│   ├── style.css                      # Global styles + dark mode
│   ├── invoice.css                    # Invoice + payment panel styles (14 sections)
│   ├── reports.css
│   └── ...
├── js/
│   ├── theme.js
│   ├── time_tracker.js
│   ├── presence.js
│   └── ...
├── sql/
│   ├── TimeForge_Capstone.sql
│   ├── Phase7_Invoicing.sql
│   ├── Phase7b_Templates.sql
│   └── Phase8_PaymentTracking.sql     # Payment lifecycle migration
├── vendor/                            # Dompdf (Composer)
├── composer.json
└── db.php                             # PDO connection
```

---

## Technologies Used

| Layer | Technology |
|---|---|
| Backend | PHP 8.1 |
| Database | MariaDB 10.4 via PDO |
| PDF Generation | Dompdf 3.x |
| Frontend | HTML5, CSS3, Vanilla JavaScript |
| Server | Apache via XAMPP |
| Version Control | Git + GitHub |

---

## Security

- bcrypt password hashing
- PDO prepared statements — zero raw SQL interpolation
- `htmlspecialchars()` on all output
- `requireRole()` enforced on every page
- Soft deletes — no accidental data loss from UI
- Session-based guards on all destructive actions

---

## Author

**Etefworkie Melaku**
Mobile and Web App Development — Capstone Project
triOS College, 2026
