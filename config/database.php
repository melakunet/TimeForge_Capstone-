<?php
/**
 * config/database.php — Single source of truth for DB connection
 *
 * All files must require this (directly or via db.php wrapper).
 * Never duplicate PDO connection logic elsewhere.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host   = 'localhost';
$dbname = 'TimeForge_Capstone';
$dbuser = 'root';
$dbpass = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $dbuser,
        $dbpass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    $_SESSION['database_error'] = $e->getMessage();
    header('Location: /TimeForge_Capstone/database_error.php');
    exit;
}
