<?php
/**
 * src/Core/Flash.php — Session flash message helpers
 *
 * Loaded by includes/flash.php (backward-compat wrapper).
 * Do not require this file directly from pages — use the wrapper.
 */

function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message,
    ];
}

function getFlash(): ?array {
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}
