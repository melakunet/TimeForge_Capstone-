<?php
// Backward-compat wrapper — real logic in src/Controllers/ProjectController.php
$_GET["action"] = "delete";
require_once __DIR__ . '/src/Controllers/ProjectController.php';
