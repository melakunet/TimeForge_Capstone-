<?php
/**
 * src/Core/Mailer.php — Email utility wrapper using PHPMailer
 *
 * Provides a clean interface to send HTML emails without duplicating SMTP logic.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Send an email using the central SMTP configuration
 *
 * @param string $toEmail Recipient's email address
 * @param string $toName  Recipient's friendly name
 * @param string $subject Email subject line
 * @param string $htmlBody Complete HTML body
 * @return bool True if successful, false on error
 */
function sendEmail(string $toEmail, string $toName, string $subject, string $htmlBody): bool {
    $config = require __DIR__ . '/../../config/mail.php';
    
    $mail = new PHPMailer(true);

    try {
        // --- Server settings ---
        $mail->isSMTP();
        $mail->Host       = $config['host'];
        
        // Disable auth if we use something like local MailHog that doesn't need it
        if (!empty($config['username']) && $config['username'] !== 'your_mailtrap_user') {
            $mail->SMTPAuth = true;
            $mail->Username = $config['username'];
            $mail->Password = $config['password'];
            $mail->SMTPSecure = $config['encryption'];
        } else {
            $mail->SMTPAuth = false;
            $mail->SMTPAutoTLS = false;
        }

        $mail->Port = $config['port'];
        $mail->CharSet = 'UTF-8';

        // --- Recipients ---
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($toEmail, $toName);

        // --- Content ---
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = strip_tags(str_replace(['<br>', '<p>'], "\n", $htmlBody)); // Simple plaintext fallback

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("TimeForge Mailer Error [{$toEmail}]: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Load an HTML email template and inject dynamic variables
 * E.g. renderEmailTemplate('budget_alert', ['project_name' => 'UI Redesign', 'percentage' => 75])
 */
function renderEmailTemplate(string $templateName, array $data = []): string {
    $filePath = __DIR__ . "/../../email_templates/{$templateName}.html";
    
    if (!file_exists($filePath)) {
        // Fallback if template doesn't exist
        return "<h3>{$templateName}</h3><p>" . print_r($data, true) . "</p>";
    }

    $html = file_get_contents($filePath);

    // Replace {{var}} placeholders
    foreach ($data as $key => $value) {
        $html = str_replace("{{" . $key . "}}", htmlspecialchars((string)$value), $html);
    }

    return $html;
}
