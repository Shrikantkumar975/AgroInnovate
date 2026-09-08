<?php
// Database configuration
// Helper to get env var with fallback
function get_env_var($key, $default = null) {
    if (getenv($key) !== false && getenv($key) !== '') return getenv($key);
    if (isset($_ENV[$key]) && $_ENV[$key] !== '') return $_ENV[$key];
    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') return $_SERVER[$key];
    return $default;
}

// Database configuration with better detection
$db_host = get_env_var('DB_HOST', 'localhost');
$db_name = get_env_var('DB_NAME', 'agroinnovate');
$db_user = get_env_var('DB_USER', 'root');
$db_pass = get_env_var('DB_PASS', '');
$db_port = get_env_var('DB_PORT', '3306');

try {
    // First try to connect without database to check if it exists
    $temp_dsn = "mysql:host=$db_host;port=$db_port";
    $temp_pdo = new PDO($temp_dsn, $db_user, $db_pass);
    $temp_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if database exists
    $stmt = $temp_pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db_name'");
    if (!$stmt->fetch()) {
        // Create database if it doesn't exist
        $temp_pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        error_log("Created database: $db_name");
    }
    
    // Now connect to the database
    $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass);
    
    // Set PDO to throw exceptions on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Verify/create required tables
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL UNIQUE,
        `password` varchar(255) NOT NULL,
        `phone` varchar(20) NOT NULL,
        `is_verified` tinyint(1) NOT NULL DEFAULT 0,
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `email_verification` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `token` varchar(64) NOT NULL,
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    error_log("Database connection and tables verified successfully");
    
} catch (PDOException $e) {
    // Log detailed error
    error_log("Database Error: " . $e->getMessage());
    error_log("Connection Params - Host: $db_host, Port: $db_port, User: $db_user, DB: $db_name"); 
    
    // In production, show a user-friendly message
    $debug_msg = "Error: " . $e->getMessage() . " (Host: $db_host)";
    die("Sorry, there was a problem connecting to the database. Please try again later. " . $debug_msg);
}

// Make the connection available globally
global $pdo; 