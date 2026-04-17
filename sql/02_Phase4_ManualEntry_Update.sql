-- Manual time entry and approval workflow

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- Extend status ENUM and add reviewer tracking columns
ALTER TABLE `time_entries`
MODIFY COLUMN `status` ENUM('running', 'paused', 'completed', 'pending', 'approved', 'rejected') DEFAULT 'completed';

ALTER TABLE `time_entries`
ADD COLUMN `entry_type` ENUM('timer', 'manual') DEFAULT 'timer' AFTER `status`,
ADD COLUMN `reviewed_by` INT(11) NULL DEFAULT NULL AFTER `entry_type`,
ADD COLUMN `reviewed_at` DATETIME NULL DEFAULT NULL AFTER `reviewed_by`,
ADD COLUMN `rejection_reason` VARCHAR(255) NULL DEFAULT NULL AFTER `reviewed_at`;

ALTER TABLE `time_entries`
ADD CONSTRAINT `time_entries_fk_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

SET FOREIGN_KEY_CHECKS=1;
COMMIT;
