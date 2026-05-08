<?php
/**
 * includes/notify.php
 * Lightweight notification helpers.
 *
 * Usage:
 *   require_once __DIR__ . '/notify.php';
 *   notify($pdo, $user_id, 'time_approved', 'Your entry was approved.', '/TimeForge_Capstone/project_details.php?id=5');
 *
 * Always safe to call — silently ignores DB errors so it never breaks the main flow.
 */

/**
 * Create a notification row for $user_id.
 */
function notify(PDO $pdo, int $user_id, string $type, string $message, ?string $link = null): void
{
    try {
        $s = $pdo->prepare("
            INSERT INTO notifications (user_id, type, message, link)
            VALUES (:uid, :type, :msg, :link)
        ");
        $s->execute([
            ':uid'  => $user_id,
            ':type' => $type,
            ':msg'  => mb_substr($message, 0, 500),
            ':link' => $link,
        ]);
    } catch (Throwable $e) {
        error_log('notify() error: ' . $e->getMessage());
    }
}

/**
 * Return the unread count for the logged-in user (0 if not logged in or table missing).
 */
function unreadNotificationCount(PDO $pdo, int $user_id): int
{
    try {
        $s = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :uid AND is_read = 0");
        $s->execute([':uid' => $user_id]);
        return (int) $s->fetchColumn();
    } catch (Throwable $e) {
        return 0;
    }
}
