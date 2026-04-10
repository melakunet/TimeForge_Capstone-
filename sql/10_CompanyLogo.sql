-- Migration 10: Add company_logo column to users table
-- Stores the relative path to the uploaded logo image (e.g. images/logos/42_logo.png)
-- NULL means no logo uploaded — templates will fall back to the TimeForge app logo

ALTER TABLE users
    ADD COLUMN company_logo VARCHAR(255) DEFAULT NULL AFTER business_tagline;
