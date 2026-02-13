<?php
// includes/redirect.php
// Role-Based Access Control Redirect Logic

/**
 * Redirects the user to the appropriate dashboard based on their role.
 * 
 * @param string $role The user's role (admin, freelancer, client)
 * @return void
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
            // Fallback for unknown roles - send to home or logout
            header('Location: /TimeForge_Capstone/index.php');
            break;
    }
    exit();
}

?>
