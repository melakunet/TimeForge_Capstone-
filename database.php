<?php
// Centralized database connection file
// Purpose: Establish PDO connection and redirect to a friendly error page on failure.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$dbname = 'TimeForge_Capstone';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // Store the error and redirect to a dedicated error page
    $_SESSION["database_error"] = $e->getMessage();
    header("Location: /TimeForge_Capstone/database_error.php");
    exit();
}
?>