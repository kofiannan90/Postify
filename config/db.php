<?php
// Secure PDO database connection configuration using environment variables
try {
    // Retrieve environment variables for the database connection
    $dbHost = getenv('DB_HOST') ?: 'localhost';       // Default to 'localhost' if not set
    $dbName = getenv('DB_NAME') ?: 'postify';  // Default database name
    $dbUser = getenv('DB_USER') ?: 'root';            // Default to 'root' for local environments
    $dbPass = getenv('DB_PASS') ?: '';                // Default to empty password for local dev
    
    // Define DSN (Data Source Name) for MySQL connection
    $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
    
    // PDO options
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Enable exception on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Default fetch mode
        PDO::ATTR_EMULATE_PREPARES => false,           // Use real prepared statements (avoid emulation)
    ];

    // Create PDO instance
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);

} catch (PDOException $e) {
    // Catch connection errors and log them securely
    error_log("Database connection failed: " . $e->getMessage());  // Log the error
    die("Database connection failed. Please try again later.");  // Generic error message for users
}
?>
