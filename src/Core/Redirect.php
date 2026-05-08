<?php
/**
 * src/Core/Redirect.php — Role-based redirect helper
 * Required via includes/redirect.php
 */

function redirectBasedOnRole($role) {
    switch ($role) {
        case 'admin':
            header('Location: ' . APP_BASE . '/admin/dashboard.php');
            break;
        case 'freelancer':
            header('Location: ' . APP_BASE . '/freelancer/dashboard.php');
            break;
        case 'client':
            header('Location: ' . APP_BASE . '/client/dashboard.php');
            break;
        default:
            header('Location: ' . APP_BASE . '/index.php');
            break;
    }
    exit;
}
