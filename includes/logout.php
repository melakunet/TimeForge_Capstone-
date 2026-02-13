<?php
// Logout Script

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/auth.php';

// Clear all session data (Instructor Style)
$_SESSION = []; 

// Destroy session
destroySession(); // This calls session_destroy() internally in auth.php

// Redirect to login
header('Location: /TimeForge_Capstone/login.php');
exit();
?>
