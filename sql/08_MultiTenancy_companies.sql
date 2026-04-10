-- ============================================================
-- Migration 08 — Multi-Tenancy: Add companies table
-- Run this in phpMyAdmin against: TimeForge_Capstone
-- ============================================================

-- ── Step 1: Create the companies table ───────────────────────
CREATE TABLE IF NOT EXISTS `companies` (
  `id`         int(11)      NOT NULL AUTO_INCREMENT,
  `name`       varchar(150) NOT NULL,
  `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ── Step 2: Add company_id to users ──────────────────────────
ALTER TABLE `users`
  ADD COLUMN `company_id` int(11) DEFAULT NULL AFTER `id`,
  ADD KEY `company_id` (`company_id`),
  ADD CONSTRAINT `users_fk_company`
    FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;


-- ── Step 3: Add company_id to clients ────────────────────────
ALTER TABLE `clients`
  ADD COLUMN `company_id` int(11) DEFAULT NULL AFTER `id`,
  ADD KEY `clients_company_id` (`company_id`),
  ADD CONSTRAINT `clients_fk_company`
    FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;


-- ── Step 4: Add company_id to projects ───────────────────────
ALTER TABLE `projects`
  ADD COLUMN `company_id` int(11) DEFAULT NULL AFTER `id`,
  ADD KEY `projects_company_id` (`company_id`),
  ADD CONSTRAINT `projects_fk_company`
    FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;


-- ── Step 5: Add company_id to invoices ───────────────────────
ALTER TABLE `invoices`
  ADD COLUMN `company_id` int(11) DEFAULT NULL AFTER `id`,
  ADD KEY `invoices_company_id` (`company_id`),
  ADD CONSTRAINT `invoices_fk_company`
    FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;


-- ── Step 6: Add company_id to time_entries ───────────────────
ALTER TABLE `time_entries`
  ADD COLUMN `company_id` int(11) DEFAULT NULL AFTER `id`,
  ADD KEY `time_entries_company_id` (`company_id`),
  ADD CONSTRAINT `time_entries_fk_company`
    FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;


-- ── Step 7: Seed — create one company per existing admin ─────
-- This assigns each existing admin their own company so existing
-- data is not orphaned. Admins without a company_name use their
-- full_name as the company name.
INSERT INTO `companies` (`name`)
SELECT COALESCE(NULLIF(TRIM(`company_name`), ''), `full_name`)
FROM   `users`
WHERE  `role` = 'admin'
ORDER BY `id`;

-- Assign each admin the company that was just created for them.
-- The sub-query matches position by row_number using a variable.
SET @rn = 0;
SET @base_id = (SELECT MIN(id) FROM `companies`);

UPDATE `users` u
JOIN (
    SELECT id,
           (@rn := @rn + 1) AS rn
    FROM   `users`
    WHERE  `role` = 'admin'
    ORDER BY `id`
) ranked ON u.id = ranked.id
SET u.company_id = @base_id + ranked.rn - 1
WHERE u.role = 'admin';


-- ── Step 8: Assign existing non-admin users to company 1 ─────
-- Freelancers and clients in the seed data all belong to the
-- first company (admin_user / Super Admin). Adjust if needed.
UPDATE `users`
SET    `company_id` = (SELECT MIN(id) FROM `companies`)
WHERE  `role` IN ('freelancer', 'client')
  AND  `company_id` IS NULL;


-- ── Step 9: Assign existing clients/projects/invoices/entries ─
-- All existing rows were created by admins that belong to company
-- determined via created_by → users.company_id.
UPDATE `clients` cl
JOIN   `users`   u  ON u.id = cl.created_by
SET    cl.company_id = u.company_id
WHERE  cl.company_id IS NULL;

UPDATE `projects` p
JOIN   `users`    u  ON u.id = p.created_by
SET    p.company_id = u.company_id
WHERE  p.company_id IS NULL;

-- projects with NULL created_by (seed data): assign to company 1
UPDATE `projects`
SET    `company_id` = (SELECT MIN(id) FROM `companies`)
WHERE  `company_id` IS NULL;

UPDATE `invoices` inv
JOIN   `users`    u   ON u.id = inv.created_by
SET    inv.company_id = u.company_id
WHERE  inv.company_id IS NULL;

UPDATE `time_entries` te
JOIN   `projects`     p  ON p.id = te.project_id
SET    te.company_id = p.company_id
WHERE  te.company_id IS NULL;
