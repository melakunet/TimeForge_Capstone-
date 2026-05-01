# TimeForge — Freelancer Time Tracking & Project Management

**Student:** Etefworkie Melaku  
**Program:** Mobile and Web App Development  
**Institution:** triOS College  
**Capstone:** 2024 – 2025  

---

## About

TimeForge is a full-stack PHP/MariaDB platform for freelancers, agencies, and their clients to track billable hours, manage projects and tasks, generate invoices, and monitor team activity in real time.

Three roles: **Admin**, **Freelancer**, **Client** — each with a dedicated interface.

---

## Features

| # | Feature | Role |
|---|---|---|
| 1 | Session-based auth, role system, company isolation | System |
| 2 | Project & client management (budget, deadline, hourly rate) | Admin |
| 3 | Live timer widget — heartbeat, idle detection, stale session guard | Freelancer |
| 4 | Activity score heartbeat (mouse + keyboard events per minute) | Auto |
| 5 | Random screenshot capture (html2canvas, configurable interval) | Auto |
| 6 | Screenshot gallery with secure proxy — filter by user/project/date | Admin |
| 7 | Live presence panel — Online / Active / Idle / Offline, polls every 10s | Admin |
| 8 | Login & security audit logs | Admin |
| 9 | Admin approve/reject workflow for time entries | Admin |
| 10 | Manual time entry (past work submission) | Freelancer |
| 11 | PDF invoicing — 3 templates, email send, payment tracking | Admin |
| 12 | Financial reports with CSV export | Admin |
| 13 | Client portal — projects, tasks, time logs, invoices | Client |
| 14 | Multi-tenancy — all data scoped by `company_id` | System |
| 15 | **Task management** — Kanban board, priority, estimates, due dates | All |
| 16 | Dark / Light theme toggle | Everyone |

---

## Setup

**Requirements:** XAMPP (Apache + MariaDB + PHP 8.1+), Composer

```bash
# 1. Place project in XAMPP htdocs
cp -r TimeForge_Capstone /Applications/XAMPP/xamppfiles/htdocs/

# 2. Start Apache + MySQL in XAMPP Control Panel

# 3. Import the full database dump
/Applications/XAMPP/xamppfiles/bin/mysql -u root TimeForge_Capstone < sql/TimeForge_Capstone.sql

# 4. Install PHP dependencies
cd /Applications/XAMPP/xamppfiles/htdocs/TimeForge_Capstone
composer install

# 5. Open in browser
open http://localhost/TimeForge_Capstone/
```

> **Note:** If running migrations individually, execute `sql/00_schema.sql` through `sql/13_ScreenshotInterval.sql` in order.

---

## Test Credentials

| Role | Email | Password |
|---|---|---|
| Admin | etef@email.com | password123 |
| Freelancer | abegaile@email.com | password123 |
| Client | client@demo.com | password123 |

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.1 |
| Database | MariaDB 10.4 (PDO) |
| PDF generation | dompdf |
| Email | PHPMailer |
| Screenshot capture | html2canvas |
| Frontend | HTML5, CSS3 (variables), Vanilla JS ES2022 |
| Reports panel | React 18 (CDN) |
| Server | Apache via XAMPP |
| Packages | Composer |

---

## Development Phases

| Phase | Feature |
|---|---|
| 1 | Foundation — auth, roles, DB schema, company scoping |
| 2 | Project & client management — CRUD, soft-delete, restore |
| 3 | Time entry core — manual entry, approve/reject workflow |
| 4 | Live timer widget — heartbeat API, persistent state |
| 5 | Client portal — isolated dashboard, projects, invoices |
| 6 | Activity tracking — idle modal, stale session guard, activity score |
| 7 | Invoicing + PDF — 3 templates, email, payment lifecycle |
| 8 | Reports + CSV export — financial breakdown by project/user |
| 9 | Screenshot monitoring — random capture, secure proxy, gallery |
| 9b | Configurable screenshot intervals — min/max per project, presets |
| 10 | Presence panel + audit logs — live status, login history |
| 11 | Task management — Kanban board, timer task picker, My Tasks |

---

## Security

- bcrypt password hashing (`password_hash`)
- PDO prepared statements on every query
- `htmlspecialchars()` on all output
- `requireRole()` enforced on every protected page
- `company_id` isolation on all queries
- Screenshot images served through a secure proxy (no direct file access)
- SMTP credentials excluded from version control

---

## Author

**Etefworkie Melaku** — triOS College, 2025  
Web Capstone — Mobile and Web App Development Program
