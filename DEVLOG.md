# TimeForge — Developer Log

---

## Session: May 1, 2026

**Developer:** Etefworkie Melaku  
**Total commits this session:** 8 (including pre-session carry-overs that landed today)  
**Branch:** `main`

---

### ✅ Features Built

---

#### Phase 12 — Task Discussion Threads (3-way chat)

**What it is:**  
Every task now has a private, threaded discussion visible to all three parties — the **Admin**, the assigned **Freelancer**, and the project **Client**. Messages are typed and colour-coded by role and message type.

**Message types:**

| Type | Icon | Who can post | Purpose |
|---|---|---|---|
| Note | 💬 | Admin, Freelancer, Client | General update or question |
| Problem Found | 🐛 | Admin, Freelancer | Flag a blocker or bug |
| Solution / Suggestion | 💡 | Admin, Freelancer | Propose a fix or approach |
| Feedback / Objection | ⭐ | Client only | Client review, concern, or approval |

**Files created / changed:**

| File | What changed |
|---|---|
| `sql/15_TaskComments.sql` | New table: `task_comments(id, task_id, company_id, user_id, type ENUM, body, created_at)` with FKs to tasks, companies, users |
| `task_comment.php` | New POST handler — validates role, verifies task ownership, inserts comment, redirects back |
| `task_detail.php` | New page — full task header + threaded comment list + post form. Role-aware (client/freelancer/admin view) |
| `tasks.php` | Added 💬/🐛 badge on every task card linking to `task_detail.php`; badge turns red if a `problem` comment exists |
| `freelancer/dashboard.php` | Added 💬 Notes link on each task row |
| `client/project_report.php` | Added full **📋 Project Tasks** table showing all tasks with status, priority, assignee, due date, and a 💬/🐛 link to the discussion thread |

**DB migration run:**
```sql
ALTER TABLE task_comments MODIFY COLUMN type ENUM('note','problem','solution','feedback');
```

**Access rules (enforced server-side):**
- Client → task query uses `INNER JOIN clients c ON c.id = p.client_id WHERE c.user_id = :uid` — only sees tasks on their own projects
- Freelancer → can only comment on tasks assigned to them or unassigned
- Admin → full access to all tasks in their company
- `company_id` for client inserts is pulled from the task's project (`SELECT company_id FROM tasks WHERE id = ?`) since clients have `company_id = NULL` on their session

---

### 🐛 Bugs Fixed

---

#### Bug 1 — Screenshots stop capturing after page navigation

**Symptom:** Screenshots worked on page load but stopped after navigating to another page.  
**Root cause:** `saveState()` was not saving `screenshotsEnabled`, `screenshotMinMs`, `screenshotMaxMs`, or `screenshotCount` to localStorage. When `_doRestore()` ran on the next page, it had no screenshot config and never called `scheduleNextScreenshot()`.  
**Fix:** `saveState()` now saves all screenshot config. `_doRestore()` reads those values and calls `scheduleNextScreenshot()` if `screenshotsEnabled === true`.  
**File:** `js/time_tracker.js`  
**Commit:** `8d94120`

> ⚠️ **Status: Fixed in code. Not yet fully verified end-to-end by user.**  
> Screenshots are still a known area — if they stop again check that `screenshotsEnabled` is being saved to localStorage before navigation.

---

#### Bug 2 — Presence panel showing wrong "last seen X hours ago" / running timer shown as Offline

**Symptom 1:** A freelancer with a running timer was shown as Offline in the admin presence panel.  
**Symptom 2:** "Last seen" label was showing relative time ("6 hours ago") which was often wrong.  
**Root cause 1:** `api/presence.php` checked `last_active_at` timestamp for active status but didn't check for an open `time_entries` record — a running timer without recent heartbeats looked Offline.  
**Root cause 2:** Relative time was calculated client-side from a stale server value.  
**Fix:** `api/presence.php` — if a user has an open `entry_id` (running timer), force `status = 'active'` regardless of `last_active_at`. `js/presence.js` — show exact clock time (`"Last seen at 11:03"`) not relative.  
**Files:** `api/presence.php`, `js/presence.js`  
**Commit:** `989d891`

---

#### Bug 3 — Task Start button not moving task to "In Progress"

**Symptom:** Clicking Start on a task card did nothing — task stayed in Open status.  
**Root cause (attempt 1):** JS `fetch()` approach was unreliable; `taskName` was not being passed; `alert()` was blocking the page reload.  
**Fix (attempt 1):** Removed `alert()`, added `taskName`, switched to `throw err`. Commit `0301a36`.  
**Root cause (attempt 2):** Still failing — JS fetch is unreliable for state changes; `task_action.php` wasn't being hit properly.  
**Fix (attempt 2):** Replaced JS fetch entirely with a real `<form method="POST">`. Added `storeTimerIntent(taskId, projectId, taskTitle)` which saves to `sessionStorage` before form submit. On page reload, `DOMContentLoaded` reads `sessionStorage` and auto-calls `startTimer()`.  
**File:** `tasks.php`, `task_action.php`  
**Commits:** `0301a36`, `cfc05ff`

---

#### Bug 4 — Double `<header>` rendering on `edit_task.php`

**Symptom:** Edit task page showed the navigation header twice.  
**Root cause:** `edit_task.php` had two `include header_partial.php` calls — one in the correct place and one accidentally left inside `<body>`.  
**Fix:** Removed the duplicate include.  
**File:** `edit_task.php`  
**Commit:** `cfc05ff`

---

#### Bug 5 — Client could post comments but could NOT read admin/freelancer comments

**Symptom:** From the client's task thread page, the comment list was empty even though admin and freelancer had posted. From the freelancer side, client comments were visible correctly.  
**Root cause (investigated, not reproduced):** The DB query was correct — comments matched by `company_id` which was correctly resolved from `p.company_id` via the task JOIN for client sessions. Manual DB simulation confirmed all 5 comments were returned correctly.  
**Actual root cause found:** The CSS used `var(--color-card)`, `var(--color-bg)`, `var(--color-text)` etc. but **these CSS variables are never defined in `style.css`** — only a `body.dark-mode { }` class-based system exists. As a result:
- `.bubble-freelancer` had `background:rgba(30,41,59,1)` (always dark) but inherited the body's light text color (`#1f2937` = dark on dark = **invisible**)
- Card backgrounds were `transparent` (var undefined) — comments were there in the DOM but visually invisible
**Fix:** Replaced all `var(--color-*)` in `task_detail.php` styles with explicit hardcoded dark-theme values. Added explicit `color:#e2e8f0` to all comment bubbles. Set `body` background to `#0f172a`.  
**File:** `task_detail.php`  
**Commit:** `1af893d`

---

#### Bug 6 — Dead duplicate code in `task_comment.php`

**Symptom:** Not a runtime crash, but ~30 lines of unreachable code existed after the first `exit` — a second `if (!$task)` check, a second `INSERT`, and a second `setFlash` + redirect. This was leftover from a corrupted rewrite.  
**Fix:** Removed all dead code after line 87's `exit`.  
**File:** `task_comment.php`  
**Commit:** `1af893d`

---

#### Bug 7 — `header_partial.php` placed inside `<head>` instead of `<body>`

**Symptom:** `task_detail.php` had `<?php include header_partial.php ?>` inside the HTML `<head>` tag, which outputs a full `<header>` nav element — causing broken HTML structure.  
**Fix:** Moved the include to immediately after `<body>`, before the flash message display.  
**File:** `task_detail.php`  
**Commit:** `1af893d`

---

### 📝 Documentation Updated

| File | What was added |
|---|---|
| `about.php` | New **💬 Task Discussion Thread** feature card (Phase 12) with all 4 message types; added discussion bullet to Admin, Freelancer, and Client role cards; updated feature count to "15 features, 12 phases"; updated client demo credential description |
| `README.md` | Feature table row #17; Phase 12 in dev phases table; new **Task Discussion (Phase 12)** section with full message-type table and access rules |

**Commit:** `ff6e88d`

---

### ⚠️ Known Issues / Not Fully Resolved

| Issue | Status | Notes |
|---|---|---|
| **Screenshot capture after navigation** | ⚠️ Code fixed, not re-tested by user | Fix is in `js/time_tracker.js` — `saveState()` + `_doRestore()` now handle screenshot config. If screenshots stop again, check localStorage for `screenshotsEnabled=true` before navigating |
| **Screenshots not verified uploading** | ⚠️ Unknown | The `/images/logos/` permission fix was done in a prior session. Screenshot upload directory permissions should be `775`. Run task `fix images folder permissions` in VS Code if upload fails |
| **Client portal nav on `task_detail.php`** | ⚠️ Minor | The header nav shown to clients on `task_detail.php` is the global nav (shows "Home", "My Dashboard" etc.) — not specifically styled for the client portal. Functional but not perfectly polished |

---

### 📦 Commit Summary (Today)

| Commit | Message |
|---|---|
| `8d94120` | Fix: screenshots stop after page navigation — persist + restore screenshot state in localStorage |
| `989d891` | Fix: presence panel — running timer always shows Active; last seen shows exact time not relative hours |
| `0301a36` | Fix: Start button — task now always moves to In Progress; pass taskName to timer; no alert blocking reload |
| `cfc05ff` | Fix: Start button now uses form POST (reliable); fix double header in edit_task.php; timer auto-starts after reload |
| `63e6dc1` | Feature: task comments — freelancers can post notes, report problems, suggest solutions per task |
| `a02cdba` | Feature: 3-way task chat — clients can view and comment on tasks |
| `1af893d` | Fix: task_detail CSS vars undefined (invisible comments in light theme); remove dead code in task_comment; fix header_partial placement |
| `ff6e88d` | Docs: add Phase 12 Task Discussion feature to about.php and README |

---

### 🗃️ Database Changes

| Migration | What it does |
|---|---|
| `sql/15_TaskComments.sql` | Creates `task_comments` table with `type ENUM('note','problem','solution')` |
| Manual ALTER (run in CLI) | `ALTER TABLE task_comments MODIFY COLUMN type ENUM('note','problem','solution','feedback')` — adds `feedback` type for client posts |

> If restoring the DB from scratch, run `15_TaskComments.sql` then the ALTER manually — or re-run the full `TimeForge_Capstone.sql` dump once it has been updated.

---
