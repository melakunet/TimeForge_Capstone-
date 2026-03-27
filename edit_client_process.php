<?php
// Backward-compat wrapper — real logic in src/Controllers/ClientController.php
$_GET["action"] = "edit";
require_once __DIR__ . '/src/Controllers/ClientController.php';
