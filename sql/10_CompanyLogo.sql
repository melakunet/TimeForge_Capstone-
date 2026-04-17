-- Add company_logo to users (relative path, NULL = no logo uploaded)

ALTER TABLE users
    ADD COLUMN company_logo VARCHAR(255) DEFAULT NULL AFTER business_tagline;
