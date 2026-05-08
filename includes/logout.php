<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/auth.php';

$_SESSION = [];
destroySession();
header('Location: ' . APP_BASE . '/login.php');
exit;
