<?php
// Logout Script

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/auth.php';

// Destroy session
destroySession();

// Redirect to login
header('Location: /TimeForge_Capstone/login.php?logout=1');
exit();
?>
