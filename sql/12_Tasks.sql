-- Phase 11: Task Management
-- Adds tasks as a layer between Project and Time Entry

CREATE TABLE IF NOT EXISTS `tasks` (
  `id`               INT(11)      NOT NULL AUTO_INCREMENT,
  `project_id`       INT(11)      NOT NULL,
  `company_id`       INT(11)      NOT NULL,
  `assigned_to`      INT(11)      DEFAULT NULL,
  `title`            VARCHAR(200) NOT NULL,
  `description`      TEXT         DEFAULT NULL,
  `status`           ENUM('open','in_progress','done') NOT NULL DEFAULT 'open',
  `priority`         ENUM('low','medium','high')        NOT NULL DEFAULT 'medium',
  `estimated_hours`  DECIMAL(6,2) DEFAULT NULL,
  `due_date`         DATE         DEFAULT NULL,
  `created_by`       INT(11)      NOT NULL,
  `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tasks_project_id`  (`project_id`),
  KEY `tasks_assigned_to` (`assigned_to`),
  KEY `tasks_company_id`  (`company_id`),
  CONSTRAINT `tasks_fk_project`  FOREIGN KEY (`project_id`)  REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_fk_assignee` FOREIGN KEY (`assigned_to`) REFERENCES `users`    (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Link time entries to tasks (optional — existing entries unaffected)
ALTER TABLE `time_entries`
  ADD COLUMN IF NOT EXISTS `task_id` INT(11) DEFAULT NULL AFTER `project_id`,
  ADD KEY IF NOT EXISTS `te_task_id` (`task_id`),
  ADD CONSTRAINT `te_fk_task` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE SET NULL;
