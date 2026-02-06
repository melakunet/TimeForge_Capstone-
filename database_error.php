<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Read and clear the stored error message to avoid stale data
$errorMessage = isset($_SESSION["database_error"]) ? $_SESSION["database_error"] : "Unknown error";
unset($_SESSION["database_error"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TimeForge - Database Error</title>
    <link rel="stylesheet" type="text/css" href="/TimeForge_Capstone/css/style.css" />
</head>
<body>
    <main class="container page-error">
        <h2>Database Error</h2>
        <p>There was an error connecting to the database.</p>
        <p>Please make sure the database is installed and MySQL is running.</p>
        <div class="details">Error Message: <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
        <div class="actions">
            <a class="btn btn-primary" href="/TimeForge_Capstone/index.php">Return to Home</a>
            <a class="btn btn-secondary" href="/TimeForge_Capstone/database_error.php">Retry</a>
        </div>
    </main>
</body>
</html>
