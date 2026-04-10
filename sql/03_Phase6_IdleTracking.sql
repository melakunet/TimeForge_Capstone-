-- Idle detection: per-session idle tracking, activity scoring, abandoned state

-- Idle and activity audit columns on time_entries
ALTER TABLE time_entries
  ADD COLUMN idle_seconds INT DEFAULT 0 COMMENT 'Total idle seconds detected during this session',
  ADD COLUMN discarded_idle_seconds INT DEFAULT 0 COMMENT 'Idle seconds the user chose to discard',
  ADD COLUMN activity_score_avg FLOAT DEFAULT NULL COMMENT 'Average activity events per heartbeat minute',
  ADD COLUMN close_reason ENUM('manual','auto','abandoned') DEFAULT 'manual' COMMENT 'How the session was closed';

-- Extend status enum to include abandoned state
ALTER TABLE time_entries
  MODIFY COLUMN status ENUM('running','paused','completed','pending','approved','rejected','abandoned')
  DEFAULT 'completed';

-- Per-minute activity snapshots from every heartbeat pulse
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

-- Budget alert flags on projects
ALTER TABLE projects
  ADD COLUMN budget_alert_75  TINYINT(1) DEFAULT 0,
  ADD COLUMN budget_alert_90  TINYINT(1) DEFAULT 0,
  ADD COLUMN budget_alert_100 TINYINT(1) DEFAULT 0;
