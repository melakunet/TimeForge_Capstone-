-- =============================================================
-- Phase 8: Payment Tracking & Invoice Lifecycle
-- Expands the invoices table to support a full payment
-- follow-up workflow:
--   draft → sent → viewed → overdue (auto) → partial → paid → completed
-- Also adds:
--   • sent_at / viewed_at / paid_at timestamps
--   • payment_method, payment_reference, payment_notes
--   • partial_amount for partial payment tracking
--   • client_feedback for notes from the client side
-- =============================================================

-- 1. Expand the status ENUM
ALTER TABLE invoices
  MODIFY COLUMN status
    ENUM('draft','sent','viewed','overdue','partial','paid','completed','cancelled')
    NOT NULL DEFAULT 'draft'
    COMMENT 'Full invoice lifecycle status';

-- 2. Add payment tracking columns
ALTER TABLE invoices
  ADD COLUMN IF NOT EXISTS sent_at        DATETIME  DEFAULT NULL COMMENT 'When the invoice was marked Sent',
  ADD COLUMN IF NOT EXISTS viewed_at      DATETIME  DEFAULT NULL COMMENT 'When the client first viewed the invoice',
  ADD COLUMN IF NOT EXISTS paid_at        DATETIME  DEFAULT NULL COMMENT 'When full payment was confirmed',
  ADD COLUMN IF NOT EXISTS partial_amount DECIMAL(10,2) DEFAULT NULL COMMENT 'Amount received for a partial payment',
  ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50)   DEFAULT NULL COMMENT 'e.g. Bank Transfer, PayPal, Cheque, Cash',
  ADD COLUMN IF NOT EXISTS payment_reference VARCHAR(100) DEFAULT NULL COMMENT 'Transaction ID or cheque number',
  ADD COLUMN IF NOT EXISTS payment_notes  TEXT DEFAULT NULL COMMENT 'Admin notes about payment / follow-up',
  ADD COLUMN IF NOT EXISTS client_feedback TEXT DEFAULT NULL COMMENT 'Client feedback or dispute notes';

-- 3. Index the new timestamp columns for reporting queries
ALTER TABLE invoices
  ADD INDEX IF NOT EXISTS idx_sent_at (sent_at),
  ADD INDEX IF NOT EXISTS idx_paid_at (paid_at);
