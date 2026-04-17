# TimeForge — Freelancer Time Tracking & Invoicing

**Student:** Etefworkie Melaku  
**Course:** Mobile and Web App Development  
**Institution:** triOS College  
**Last Updated:** April 2026

---

## About

TimeForge is a full-stack PHP/MariaDB web application for freelancers and small agencies to track billable hours, generate invoices, and manage payments.

Three roles: **Admin**, **Freelancer**, **Client**. Supports dark mode and mobile.

---

## Features

- Registration, login, role-based access control
- Project and client management
- Real-time timer with idle detection and manual time entry
- Admin approve/reject workflow for time entries
- Client portal with project reports
- 5 invoice templates with PDF download and email send
- Full payment lifecycle: Draft → Sent → Viewed → Partial → Paid → Completed
- Admin reports and CSV export
- Multi-tenant: all data scoped per company

---

## Setup

**Requirements:** XAMPP (Apache + MariaDB + PHP 8.1+), Composer

1. Copy project to `/Applications/XAMPP/xamppfiles/htdocs/TimeForge_Capstone`
2. Start Apache and MySQL in XAMPP
3. Run SQL migrations in order (`sql/00_schema.sql` through `sql/10_CompanyLogo.sql`)
4. Run `composer install`
5. Open `http://localhost/TimeForge_Capstone/`

---

## Test Credentials

| Role | Username | Password |
|---|---|---|
| Admin | admin_user | password123 |
| Freelancer | dev_sarah | password123 |
| Client | client_bob | password123 |

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.1 |
| Database | MariaDB 10.4 via PDO |
| PDF | Dompdf |
| Email | PHPMailer |
| Frontend | HTML5, CSS3, Vanilla JS |
| Server | Apache via XAMPP |

---

## Security

- bcrypt password hashing
- PDO prepared statements
- `htmlspecialchars()` on all output
- `requireRole()` on every protected page
- `company_id` isolation on all queries
- SMTP credentials excluded from version control

---

## Author

**Etefworkie Melaku** — triOS College, 2026
