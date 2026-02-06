<?php
// Compatibility wrapper: use centralized database.php for connection.
// Purpose: Keep existing includes that reference db.php working.
require_once __DIR__ . '/database.php';
?>