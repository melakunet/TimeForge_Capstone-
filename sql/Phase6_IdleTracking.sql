-- ============================================================
-- Phase 6: Smart Idle Detection & Honest Time

-- ============================================================

-- Step 1: Add idle/activity columns to time_entries
-- These make every entry auditable: how much idle existed and what was done with it.

ALTER TABLE time_entries
  ADD COLUMN idle_seconds INT DEFAULT 0 COMMENT 'Total idle seconds detected during this session',
  ADD COLUMN discarded_idle_seconds INT DEFAULT 0 COMMENT 'Idle seconds the user chose to discard',
  ADD COLUMN activity_score_avg FLOAT DEFAULT NULL COMMENT 'Average activity events per heartbeat minute',
  ADD COLUMN close_reason ENUM('manual','auto','abandoned') DEFAULT 'manual' COMMENT 'How the session was closed';

-- Step 2: Add 'abandoned' to the status enum
-- abandoned = session closed by server because heartbeats stopped, not by user

ALTER TABLE time_entries
  MODIFY COLUMN status ENUM('running','paused','completed','pending','approved','rejected','abandoned')
  DEFAULT 'completed';

-- Step 3: Create the session_activity table
-- Stores per-minute activity snapshots from every heartbeat pulse

CREATE TABLE IF NOT EXISTS session_activity (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  time_entry_id   INT NOT NULL,
  user_id         INT NOT NULL,
  recorded_at     DATETIME NOT NULL,
  mouse_events    INT DEFAULT 0  COMMENT 'Mouse moves + clicks in this minute',
  key_events      INT DEFAULT 0  COMMENT 'Keystrokes in this minute',
  activity_score  INT DEFAULT 0  COMMENT 'Total events (mouse + key) in this minute',
  FOREIGN KEY (time_entry_id) REFERENCES time_entries(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id)       REFERENCES users(id)        ON DELETE CASCADE,
  INDEX idx_entry  (time_entry_id),
  INDEX idx_user   (user_id),
  INDEX idx_time   (recorded_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Step 4: Add budget alert flags to projects for Phase 8 readiness
-- (Added here so the column exists before Phase 8 needs it)
ALTER TABLE projects
  ADD COLUMN budget_alert_75  TINYINT(1) DEFAULT 0,
  ADD COLUMN budget_alert_90  TINYINT(1) DEFAULT 0,
  ADD COLUMN budget_alert_100 TINYINT(1) DEFAULT 0;
