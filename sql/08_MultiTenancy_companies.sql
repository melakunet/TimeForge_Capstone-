-- Multi-tenancy: companies table + company_id isolation
-- Each admin account owns a company; all data rows belong to exactly one company.

-- Companies registry
CREATE TABLE IF NOT EXISTS `companies` (
  `id`         int(11)      NOT NULL AUTO_INCREMENT,
  `name`       varchar(150) NOT NULL,
  `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Add company_id foreign key to all tenant-scoped tables
ALTER TABLE `users`
  ADD COLUMN `company_id` int(11) DEFAULT NULL AFTER `id`,
  ADD KEY `company_id` (`company_id`),
  ADD CONSTRAINT `users_fk_company`
    FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

ALTER TABLE `clients`
  ADD COLUMN `company_id` int(11) DEFAULT NULL AFTER `id`,
  ADD KEY `clients_company_id` (`company_id`),
  ADD CONSTRAINT `clients_fk_company`
    FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

ALTER TABLE `projects`
  ADD COLUMN `company_id` int(11) DEFAULT NULL AFTER `id`,
  ADD KEY `projects_company_id` (`company_id`),
  ADD CONSTRAINT `projects_fk_company`
    FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

ALTER TABLE `invoices`
  ADD COLUMN `company_id` int(11) DEFAULT NULL AFTER `id`,
  ADD KEY `invoices_company_id` (`company_id`),
  ADD CONSTRAINT `invoices_fk_company`
    FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

ALTER TABLE `time_entries`
  ADD COLUMN `company_id` int(11) DEFAULT NULL AFTER `id`,
  ADD KEY `time_entries_company_id` (`company_id`),
  ADD CONSTRAINT `time_entries_fk_company`
    FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;


-- Seed one company per existing admin user
INSERT INTO `companies` (`name`)
SELECT COALESCE(NULLIF(TRIM(`company_name`), ''), `full_name`)
FROM   `users`
WHERE  `role` = 'admin'
ORDER BY `id`;

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

-- Assign non-admin users to company 1 (update manually if users belong elsewhere)
UPDATE `users`
SET    `company_id` = (SELECT MIN(id) FROM `companies`)
WHERE  `role` IN ('freelancer', 'client')
  AND  `company_id` IS NULL;


-- Backfill company_id on existing rows via created_by chain
UPDATE `clients` cl
JOIN   `users`   u  ON u.id = cl.created_by
SET    cl.company_id = u.company_id
WHERE  cl.company_id IS NULL;

UPDATE `projects` p
JOIN   `users`    u  ON u.id = p.created_by
SET    p.company_id = u.company_id
WHERE  p.company_id IS NULL;

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
