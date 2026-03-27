# TimeForge — Freelancer Time Tracking & Invoicing Platform

**Student:** Etefworkie Melaku  
**Course:** Mobile and Web App Development  
**Institution:** triOS College  
**Last Updated:** March 2026

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
- PDF download via Dompdf
- Invoice history with status badges
- Admin financial reports and CSV export

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

3. Run SQL migrations in order:
   ```
   sql/00_schema.sql
   sql/01_Phase4_TimeTracking.sql
   sql/02_Phase4_ManualEntry_Update.sql
   sql/03_Phase6_IdleTracking.sql
   sql/04_Phase7_Invoicing.sql
   sql/05_Phase7b_Templates.sql
   sql/06_Phase8_PaymentTracking.sql
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
├── public/
│   └── assets/            # css/, js/, icons/ mirrored here
├── src/
│   ├── Core/              # Auth, Flash, Redirect helpers
│   └── Controllers/       # Business logic (Client, Project, Time)
├── config/
│   ├── session.php
│   ├── theme.php
│   └── database.php       # Single PDO connection source
├── includes/              # Backward-compat wrappers + layout partials
├── admin/                 # Admin portal pages
├── client/                # Client portal pages
├── freelancer/            # Freelancer portal pages
├── invoices/              # Generate, view, download, history, payment
├── api/                   # Time tracking, presence, CSV export
├── cron/                  # Auto-close abandoned timers
├── css/                   # Global stylesheets
├── js/                    # Frontend scripts
├── sql/                   # Numbered migration files (00 → 06)
├── composer.json
├── composer.lock
├── db.php                 # Wrapper → config/database.php
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
- Soft deletes — no accidental data loss from UI
- `src/` directory blocked from direct browser access (403)

---

## Author

**Etefworkie Melaku**  
Mobile and Web App Development — Capstone Project  
triOS College, 2026
