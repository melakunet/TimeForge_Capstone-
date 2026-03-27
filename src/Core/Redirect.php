<?php
/**
 * src/Core/Redirect.php — Role-based redirect helper
 *
 * Loaded by includes/redirect.php (backward-compat wrapper).
 * Do not require this file directly from pages — use the wrapper.
 */

function redirectBasedOnRole($role) {
    switch ($role) {
        case 'admin':
            header('Location: /TimeForge_Capstone/admin/dashboard.php');
            break;
        case 'freelancer':
            header('Location: /TimeForge_Capstone/freelancer/dashboard.php');
            break;
        case 'client':
            header('Location: /TimeForge_Capstone/client/dashboard.php');
            break;
        default:
            header('Location: /TimeForge_Capstone/index.php');
            break;
    }
    exit;
}
