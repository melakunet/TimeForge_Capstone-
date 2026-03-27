-- ============================================================
-- Phase 7b: Invoice Templates
-- Adds template selection to the invoices table so each
-- invoice remembers which visual layout was used.
-- ============================================================

ALTER TABLE invoices
  ADD COLUMN template VARCHAR(20) NOT NULL DEFAULT 'classic'
  COMMENT 'Which visual template was chosen at generation time';
