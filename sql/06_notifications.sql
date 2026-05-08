-- Phase N: Notification system
-- Run once against the TimeForge database

CREATE TABLE IF NOT EXISTS `notifications` (
  `id`         INT              NOT NULL AUTO_INCREMENT,
  `user_id`    INT              NOT NULL,
  `type`       VARCHAR(60)      NOT NULL,          -- e.g. 'time_approved', 'task_assigned', 'invoice_overdue'
  `message`    VARCHAR(500)     NOT NULL,
  `link`       VARCHAR(300)     DEFAULT NULL,       -- optional URL the bell click links to
  `is_read`    TINYINT(1)       NOT NULL DEFAULT 0,
  `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notif_user` (`user_id`, `is_read`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
