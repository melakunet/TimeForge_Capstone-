-- Time tracking schema: live presence, smart timer, session status

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- Live presence columns on users
ALTER TABLE `users`
ADD COLUMN `last_active_at` DATETIME NULL DEFAULT NULL AFTER `last_login`,
ADD COLUMN `current_project_id` INT(11) NULL DEFAULT NULL AFTER `last_active_at`,
ADD CONSTRAINT `users_fk_current_project` FOREIGN KEY (`current_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;

-- Smart timer columns on time_entries
ALTER TABLE `time_entries`
ADD COLUMN `total_seconds` INT(11) DEFAULT 0 AFTER `end_time`,
ADD COLUMN `is_idle_detected` TINYINT(1) DEFAULT 0 AFTER `is_billable`,
ADD COLUMN `status` ENUM('running', 'paused', 'completed') DEFAULT 'completed' AFTER `is_idle_detected`;

UPDATE `time_entries`
SET `status` = 'completed',
    `total_seconds` = IF(end_time IS NOT NULL, TIMESTAMPDIFF(SECOND, start_time, end_time), 0);

SET FOREIGN_KEY_CHECKS=1;
COMMIT;
