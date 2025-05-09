<?php
class Database {
    private $pdo;
    
    public function __construct() {
        try {
            // Retrieve environment variables for the database connection
            $dbHost = getenv('DB_HOST') ?: 'localhost';
            $dbName = getenv('DB_NAME') ?: 'postify';
            $dbUser = getenv('DB_USER') ?: 'root';
            $dbPass = getenv('DB_PASS') ?: '';
            
            // Define DSN for MySQL connection
            $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
            
            // Initialize PDO instance
            $this->pdo = new PDO($dsn, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            // Catch connection errors and log them
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection failed.");
        }
    }

    // Return the PDO instance
    public function getConnection() {
        return $this->pdo;
    }
}
?>
