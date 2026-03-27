<?php
// Backward-compat wrapper — real logic in src/Controllers/TimeController.php
$_GET["action"] = "delete";
require_once __DIR__ . '/src/Controllers/TimeController.php';
