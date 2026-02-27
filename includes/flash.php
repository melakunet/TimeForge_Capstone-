<?php
// Flash message helpers
// Usage:
//   setFlash('success', 'Saved!');
//   $flash = getFlash(); // ['type' => 'success', 'message' => 'Saved!']

function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type,
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
