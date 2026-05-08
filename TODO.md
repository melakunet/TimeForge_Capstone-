# TimeForge — Remaining Improvement TODO

Generated: May 2026  
Base commit: bce1cc5 (78/78 tests passing)

---

## 🔴 HIGH PRIORITY

### TODO-01 · Forgot Password flow
**Status:** ⬜ Pending  
**Files to create:** `forgot_password.php`, `reset_password.php`, `includes/forgot_process.php`, `includes/reset_process.php`  
**DB:** `password_reset_token` + `password_reset_expires` columns **already exist** in `users` table  
**Mailer:** `sendEmail()` in `src/Core/Mailer.php` already works  
**Plan:**
- `forgot_password.php` — form asking for email
- `includes/forgot_process.php` — generate `bin2hex(random_bytes(32))` token, store hashed in DB with 1-hour expiry, send link via `sendEmail()`
- `reset_password.php` — form shown when valid token in URL
- `includes/reset_process.php` — validate token, enforce password complexity, update password hash, clear token
- Add "Forgot password?" link to `login.php`

---

### TODO-02 · CORS wildcard fix on analytics API  
**Status:** ⬜ Pending  
**File:** `api/v1/analytics.php` line 14  
**Change:** Replace `Access-Control-Allow-Origin: *` with same-origin header  
```php
// Before
header('Access-Control-Allow-Origin: *');
// After — only allow same origin (React SPA is served from same host)
header('Access-Control-Allow-Origin: ' . (isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : ''));
header('Access-Control-Allow-Credentials: true');
```

---

## 🟠 MEDIUM PRIORITY

### TODO-03 · Password complexity enforcement  
**Status:** ✅ Already done — `register_process.php` enforces: min 8 chars, uppercase, lowercase, digit  
**Remaining gap:** `profile.php` password change has no complexity check  
**Fix:** Add same regex checks to `profile.php` POST handler password-change block

---

### TODO-04 · Admin user management CRUD  
**Status:** ⬜ Pending  
**File:** `admin/users.php` — currently read-only list (111 lines, no edit/deactivate UI)  
**Plan:**
- Add toggle-active button (POST to `admin/users.php?action=toggle`)
- Add inline role-change dropdown (POST to `admin/users.php?action=role`)
- Add "Invite User" form (creates account + sends welcome email)
- Add CSRF tokens to all new forms

---

### TODO-05 · Freelancer invoice visibility  
**Status:** ⬜ Pending  
**File:** `invoices/history.php` — handles `admin` and `client` roles, freelancer falls through with no query  
**Fix:** Add `elseif ($role === 'freelancer')` branch — show invoices for projects where `created_by = $user_id`  
**Also add** a nav link in `includes/header_partial.php` for freelancer role pointing to `invoices/history.php`

---

## 🟡 LOW PRIORITY

### TODO-06 · Kanban search + priority/assignee filter  
**Status:** ⬜ Pending  
**File:** `tasks.php`  
**Plan:** Add a filter bar above the Kanban board:
- Text search (JS client-side — filter `.task-card` elements by title text)
- Priority filter dropdown (high/medium/low/all) — toggle visibility by `.priority-{x}` class
- Assignee filter — build from existing assignee data on cards
- All pure JS, no server round-trip needed

---

### TODO-07 · Pagination on time entry tables  
**Status:** ⬜ Pending  
**File:** `project_details.php`  
**Plan:** Add `?page=N` param, `LIMIT 25 OFFSET N*25` on time entry query, prev/next controls in HTML

---

### TODO-08 · Notification system  
**Status:** ⬜ Pending  
**Scope:** Large — defer to separate sprint  
**Triggers needed:** time entry approved/rejected, task assigned, invoice overdue  
**Approach:** Add `notifications` table + badge in header + email via `sendEmail()`

---

### TODO-09 · Mobile responsiveness  
**Status:** ⬜ Pending  
**Scope:** CSS pass across admin pages — add `@media (max-width: 768px)` breakpoints  
**Worst offenders:** `project_details.php` time entry table, `tasks.php` Kanban columns

---

## ✅ COMPLETED THIS SESSION

- [x] CSRF tokens on all 12 POST forms (F group)
- [x] `verifyCsrfToken()` in all 10 handlers (G group)
- [x] `session_regenerate_id(true)` in `startSession()` (D group)
- [x] Login rate limiting — 5 attempts / 15 min (H group)
- [x] Deleted `fix_permissions.php` + `test_screenshot.php` (I group)
- [x] Budget overage warning banner on `project_details.php` (J group)
- [x] All 78 tests passing — committed `bce1cc5`
