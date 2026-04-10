-- Invoice email tracking columns
-- Stores recipient address and timestamp of last email send per invoice

ALTER TABLE `invoices`
  ADD COLUMN `sent_to_email` varchar(100) DEFAULT NULL
    COMMENT 'Email address the invoice PDF was sent to'
    AFTER `sent_at`,
  ADD COLUMN `email_sent_at` datetime DEFAULT NULL
    COMMENT 'Timestamp of the most recent email send'
    AFTER `sent_to_email`;
