-- Phase 12: Task Comments / Problem Reports
-- Freelancers can post notes, flag problems, and suggest solutions on any task.
-- Admins can see everything and reply.

CREATE TABLE IF NOT EXISTS `task_comments` (
  `id`          INT(11)      NOT NULL AUTO_INCREMENT,
  `task_id`     INT(11)      NOT NULL,
  `company_id`  INT(11)      NOT NULL,
  `user_id`     INT(11)      NOT NULL,
  `type`        ENUM('note','problem','solution') NOT NULL DEFAULT 'note',
  `body`        TEXT         NOT NULL,
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tc_task_id`    (`task_id`),
  KEY `tc_company_id` (`company_id`),
  KEY `tc_user_id`    (`user_id`),
  CONSTRAINT `tc_fk_task`    FOREIGN KEY (`task_id`)    REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tc_fk_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tc_fk_user`    FOREIGN KEY (`user_id`)    REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
