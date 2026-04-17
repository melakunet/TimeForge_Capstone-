-- Payment tracking: expands invoice lifecycle to draft → sent → viewed → overdue → partial → paid → completed

ALTER TABLE invoices
  MODIFY COLUMN status
    ENUM('draft','sent','viewed','overdue','partial','paid','completed','cancelled')
    NOT NULL DEFAULT 'draft';

ALTER TABLE invoices
  ADD COLUMN IF NOT EXISTS sent_at           DATETIME      DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS viewed_at         DATETIME      DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS paid_at           DATETIME      DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS partial_amount    DECIMAL(10,2) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS payment_method    VARCHAR(50)   DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS payment_reference VARCHAR(100)  DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS payment_notes     TEXT          DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS client_feedback   TEXT          DEFAULT NULL;

ALTER TABLE invoices
  ADD INDEX IF NOT EXISTS idx_sent_at (sent_at),
  ADD INDEX IF NOT EXISTS idx_paid_at (paid_at);
