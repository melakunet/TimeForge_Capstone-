-- Phase 12b: Add 'feedback' comment type for client portal
-- Adds the 'feedback' value to the task_comments.type ENUM
-- so clients can post ⭐ Feedback / Objection comments.
-- Run after 15_TaskComments.sql.

ALTER TABLE `task_comments`
  MODIFY COLUMN `type` ENUM('note','problem','solution','feedback')
    NOT NULL DEFAULT 'note';
