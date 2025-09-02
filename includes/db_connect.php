<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agroinnovate";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}

// Set the character set to utf8mb4
if (!$conn->set_charset("utf8mb4")) {
    error_log("Error loading character set utf8mb4: " . $conn->error);
}

// Enable error reporting for mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Ensure required tables exist for registration/reset flows
try {
    // Table used by register/resend (singular)
    $conn->query("CREATE TABLE IF NOT EXISTS `email_verification` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) NOT NULL,
        `token` VARCHAR(64) NOT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        INDEX `idx_user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Table used by forgot_password.php and reset_password.php
    $conn->query("CREATE TABLE IF NOT EXISTS `password_resets` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `email` VARCHAR(100) NOT NULL,
        `token` VARCHAR(64) NOT NULL,
        `expiry` DATETIME NOT NULL,
        `used` TINYINT(1) NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`),
        INDEX `idx_email` (`email`),
        INDEX `idx_token` (`token`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
} catch (mysqli_sql_exception $e) {
    error_log("Table creation error: " . $e->getMessage());
}

// Function to safely execute SQL queries
function executeQuery($sql, $params = []) {
    global $conn;
    
    if (!$conn) {
        return false;
    }
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch(mysqli_sql_exception $e) {
        error_log("Database Query Error: " . $e->getMessage());
        return false;
    }
}

// Function to get a single row
function dbFetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt ? $stmt->fetch() : false;
}

// Function to get multiple rows
function dbFetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt ? $stmt->fetchAll() : [];
}

// Function to insert data
function dbInsertData($table, $data) {
    global $conn;
    
    if (!$conn) {
        return false;
    }
    
    try {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute(array_values($data));
        
        return $conn->insert_id;
    } catch(mysqli_sql_exception $e) {
        error_log("Database Insert Error: " . $e->getMessage());
        return false;
    }
}

// Function to update data
function dbUpdateData($table, $data, $where, $whereParams = []) {
    global $conn;
    
    if (!$conn) {
        return false;
    }
    
    try {
        $setClauses = [];
        $params = [];
        
        foreach ($data as $column => $value) {
            $setClauses[] = "$column = ?";
            $params[] = $value;
        }
        
        $setClause = implode(', ', $setClauses);
        
        $sql = "UPDATE $table SET $setClause WHERE $where";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute(array_merge($params, $whereParams));
        
        return $stmt->affected_rows;
    } catch(mysqli_sql_exception $e) {
        error_log("Database Update Error: " . $e->getMessage());
        return false;
    }
}

// Function to delete data
function dbDeleteData($table, $where, $params = []) {
    global $conn;
    
    if (!$conn) {
        return false;
    }
    
    try {
        $sql = "DELETE FROM $table WHERE $where";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->affected_rows;
    } catch(mysqli_sql_exception $e) {
        error_log("Database Delete Error: " . $e->getMessage());
        return false;
    }
}
?>
