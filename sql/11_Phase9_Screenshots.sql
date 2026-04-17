-- Phase 9: Worker Activity Screenshots
-- Creates the screenshots table and adds screenshots_enabled to projects.

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- ── 1. Screenshots table ──────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `screenshots` (
  `id`                       INT(11)      NOT NULL AUTO_INCREMENT,
  `entry_id`                 INT(11)      NOT NULL,
  `user_id`                  INT(11)      NOT NULL,
  `project_id`               INT(11)      NOT NULL,
  `company_id`               INT(11)      NOT NULL,
  `file_path`                VARCHAR(255) NOT NULL COMMENT 'Relative path under uploads/screenshots/',
  `file_size_kb`             INT(11)      NOT NULL DEFAULT 0,
  `activity_score_at_capture` INT(11)     NOT NULL DEFAULT 0 COMMENT 'Mouse+key events recorded at time of capture',
  `captured_at`              DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_entry`   (`entry_id`),
  KEY `idx_user`    (`user_id`),
  KEY `idx_company` (`company_id`),
  KEY `idx_project` (`project_id`),
  CONSTRAINT `screenshots_fk_entry`   FOREIGN KEY (`entry_id`)   REFERENCES `time_entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `screenshots_fk_user`    FOREIGN KEY (`user_id`)    REFERENCES `users`         (`id`) ON DELETE CASCADE,
  CONSTRAINT `screenshots_fk_project` FOREIGN KEY (`project_id`) REFERENCES `projects`      (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ── 2. Add screenshots_enabled flag to projects ───────────────────────────────
ALTER TABLE `projects`
  ADD COLUMN `screenshots_enabled` TINYINT(1) NOT NULL DEFAULT 1
  COMMENT '1 = auto-capture screenshots while timer runs, 0 = disabled'
  AFTER `budget_alert_100`;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
