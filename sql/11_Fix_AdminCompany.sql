-- 11_Fix_AdminCompany.sql
-- Fix: Move admin user 'Etef' (Etefworkie Melaku) into company_id=1
-- so they can see the freelancers, projects, and screenshots in the main company.
--
-- Root cause: Migration 08 auto-creates one company per admin user,
-- giving each admin their own isolated company. In production you would
-- manually assign admin users to the correct tenant company.

UPDATE `users`
SET    `company_id` = 1
WHERE  `username` = 'Etef'
  AND  `role` = 'admin';

-- Verify
SELECT id, username, full_name, role, company_id
FROM   `users`
WHERE  `username` = 'Etef';
