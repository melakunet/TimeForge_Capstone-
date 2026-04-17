-- Add company_name and business_tagline to users for invoice sender details

ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `company_name`     varchar(150) DEFAULT NULL AFTER `full_name`,
  ADD COLUMN IF NOT EXISTS `business_tagline` varchar(200) DEFAULT NULL AFTER `company_name`;
