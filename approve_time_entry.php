<?php
// Backward-compat wrapper — real logic in src/Controllers/TimeController.php
// Note: approve_time_entry.php passes action=approve|reject via POST "action" field.
$_GET["action"] = "approve";
require_once __DIR__ . '/src/Controllers/TimeController.php';
