<?php
/**
 * config/mail.php — Centralized SMTP Configuration for PHPMailer
 * 
 * If you are testing locally, MailHog (port 1025) or Mailtrap is recommended.
 */

return [
    'host'       => 'sandbox.smtp.mailtrap.io', // Mailtrap SMTP host
    'port'       => 2525,                       // Mailtrap SMTP port
    'username'   => 'your_mailtrap_user',       // Replace with real Mailtrap username
    'password'   => 'your_mailtrap_password',   // Replace with real Mailtrap password
    'encryption' => 'tls',                      // ENCRYPTION_STARTTLS or 'tls'
    'from_email' => 'notifications@timeforge.local',
    'from_name'  => 'TimeForge Admin',
];
