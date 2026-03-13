-- Phase 4: Time Tracking & Advanced Features Migration Script
-- Run this script to upgrade the database schema for Phase 4

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- -----------------------------------------------------------------------------
-- 1. Upgrade `users` table for "Live Presence" features
-- -----------------------------------------------------------------------------
-- Add columns to track when a user was last active and what they are working on
ALTER TABLE `users`
ADD COLUMN `last_active_at` DATETIME NULL DEFAULT NULL AFTER `last_login`,
ADD COLUMN `current_project_id` INT(11) NULL DEFAULT NULL AFTER `last_active_at`,
ADD CONSTRAINT `users_fk_current_project` FOREIGN KEY (`current_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;

-- -----------------------------------------------------------------------------
-- 2. Upgrade `time_entries` table for "Smart Timer" features
-- -----------------------------------------------------------------------------
-- Add columns to support calculated duration, idle detection, and session status
ALTER TABLE `time_entries`
ADD COLUMN `total_seconds` INT(11) DEFAULT 0 AFTER `end_time`, -- Stores actual seconds worked (useful if manual adjustments made)
ADD COLUMN `is_idle_detected` TINYINT(1) DEFAULT 0 AFTER `is_billable`, -- Flag if this session had significant idle time detected
ADD COLUMN `status` ENUM('running', 'paused', 'completed') DEFAULT 'completed' AFTER `is_idle_detected`;

-- -----------------------------------------------------------------------------
-- 3. Create `time_sessions` table (Optional: For highly granular pause/resume history)
-- -----------------------------------------------------------------------------


-- Update existing entries to have calculated total_seconds and status
UPDATE `time_entries`
SET `status` = 'completed',
    `total_seconds` = IF(end_time IS NOT NULL, TIMESTAMPDIFF(SECOND, start_time, end_time), 0);

SET FOREIGN_KEY_CHECKS=1;
COMMIT;
