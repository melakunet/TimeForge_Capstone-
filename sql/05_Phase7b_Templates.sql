-- Invoice template column
-- Records which visual layout was selected when the invoice was generated.

ALTER TABLE invoices
  ADD COLUMN template VARCHAR(20) NOT NULL DEFAULT 'classic'
  COMMENT 'Which visual template was chosen at generation time';
