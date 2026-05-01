-- Phase 9b: Configurable screenshot intervals per project
-- Adds screenshot_min_interval and screenshot_max_interval columns.
-- Default: random 5–15 min (the original hardcoded behaviour).

ALTER TABLE projects
    ADD COLUMN `screenshot_min_interval` TINYINT UNSIGNED NOT NULL DEFAULT 5
        COMMENT 'Min minutes between screenshots (1–120)' AFTER screenshots_enabled,
    ADD COLUMN `screenshot_max_interval` TINYINT UNSIGNED NOT NULL DEFAULT 15
        COMMENT 'Max minutes between screenshots (1–120, >= min)' AFTER screenshot_min_interval;
