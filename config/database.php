<?php
/**
 * Database Configuration
 * Klinik Laktasi - MySQL Connection
 */

// read from environment variables if available (Railway sets DATABASE_URL or individual vars)
$envHost = getenv('DB_HOST');
$envUser = getenv('DB_USER');
$envPass = getenv('DB_PASS');
$envName = getenv('DB_NAME');

// Fallback defaults for local development
define('DB_HOST', $envHost ? $envHost : 'localhost');
define('DB_USER', $envUser ? $envUser : 'root');
define('DB_PASS', $envPass ? $envPass : '');
define('DB_NAME', $envName ? $envName : 'klinik_laktasi');

/**
 * Create database connection using PDO
 */
function getDbConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

/**
 * Execute a query and return results
 */
function dbQuery($sql, $params = []) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Execute a query and return single row
 */
function dbQueryOne($sql, $params = []) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
}

/**
 * Execute insert/update/delete and return affected rows or last insert id
 */
function dbExecute($sql, $params = []) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

/**
 * Get last inserted ID
 */
function dbLastInsertId() {
    $pdo = getDbConnection();
    return $pdo->lastInsertId();
}
