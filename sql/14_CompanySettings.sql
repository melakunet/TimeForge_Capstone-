-- Phase 12: Company-level system settings
-- Key/value store per company. Loaded app-wide by config/settings.php helper.

CREATE TABLE IF NOT EXISTS `company_settings` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `company_id`  INT NOT NULL,
  `setting_key` VARCHAR(80) NOT NULL,
  `setting_val` TEXT,
  `updated_at`  DATETIME DEFAULT NOW() ON UPDATE NOW(),
  UNIQUE KEY `uq_company_setting` (`company_id`, `setting_key`),
  CONSTRAINT `fk_cs_company` FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed defaults for every existing company
INSERT IGNORE INTO `company_settings` (`company_id`, `setting_key`, `setting_val`)
SELECT id, 'company_display_name',     name          FROM companies;
INSERT IGNORE INTO `company_settings` (`company_id`, `setting_key`, `setting_val`)
SELECT id, 'idle_threshold_minutes',   '10'           FROM companies;
INSERT IGNORE INTO `company_settings` (`company_id`, `setting_key`, `setting_val`)
SELECT id, 'stale_threshold_minutes',  '30'           FROM companies;
INSERT IGNORE INTO `company_settings` (`company_id`, `setting_key`, `setting_val`)
SELECT id, 'default_currency',         'CAD'          FROM companies;
INSERT IGNORE INTO `company_settings` (`company_id`, `setting_key`, `setting_val`)
SELECT id, 'invoice_due_days',         '30'           FROM companies;
INSERT IGNORE INTO `company_settings` (`company_id`, `setting_key`, `setting_val`)
SELECT id, 'invoice_tax_rate',         '13'           FROM companies;
INSERT IGNORE INTO `company_settings` (`company_id`, `setting_key`, `setting_val`)
SELECT id, 'invoice_footer_note',      'Thank you for your business.' FROM companies;
INSERT IGNORE INTO `company_settings` (`company_id`, `setting_key`, `setting_val`)
SELECT id, 'screenshots_default_on',   '1'            FROM companies;
INSERT IGNORE INTO `company_settings` (`company_id`, `setting_key`, `setting_val`)
SELECT id, 'presence_active_window',   '180'          FROM companies;
