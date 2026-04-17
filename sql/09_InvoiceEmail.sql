-- Invoice email tracking: recipient address and send timestamp

ALTER TABLE `invoices`
  ADD COLUMN `sent_to_email` varchar(100) DEFAULT NULL AFTER `sent_at`,
  ADD COLUMN `email_sent_at` datetime     DEFAULT NULL AFTER `sent_to_email`;
