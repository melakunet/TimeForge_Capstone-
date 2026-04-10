<?php
/**
 * src/Core/Redirect.php — Role-based redirect helper
 * Required via includes/redirect.php
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
