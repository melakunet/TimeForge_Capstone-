-- Manual time entry and approval workflow
-- Adds pending/approved/rejected statuses and reviewer columns to time_entries

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- -----------------------------------------------------------------------------
-- Upgrade `time_entries` table for Approval Workflow
-- -----------------------------------------------------------------------------

-- 1. Extend the 'status' ENUM to include approval states
ALTER TABLE `time_entries`
MODIFY COLUMN `status` ENUM('running', 'paused', 'completed', 'pending', 'approved', 'rejected') DEFAULT 'completed';

-- 2. Add columns to track origin (Manual vs Timer) and Reviewer details
ALTER TABLE `time_entries`
ADD COLUMN `entry_type` ENUM('timer', 'manual') DEFAULT 'timer' AFTER `status`,
ADD COLUMN `reviewed_by` INT(11) NULL DEFAULT NULL AFTER `entry_type`,
ADD COLUMN `reviewed_at` DATETIME NULL DEFAULT NULL AFTER `reviewed_by`,
ADD COLUMN `rejection_reason` VARCHAR(255) NULL DEFAULT NULL AFTER `reviewed_at`;

-- 3. Add foreign key for the reviewer
ALTER TABLE `time_entries`
ADD CONSTRAINT `time_entries_fk_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

SET FOREIGN_KEY_CHECKS=1;
COMMIT;
