<?php
/**
 * Database Configuration
 * Klinik Laktasi - MySQL Connection
 */

// Detect if running on localhost
$isLocalhost = ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1');

if ($isLocalhost) {
    // Local development (Laragon)
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'klinik_laktasi');
} else {
    // Production (InfinityFree / Hosting)
    // You can also use getenv() here if you set these in your hosting panel
    define('DB_HOST', getenv('DB_HOST') ?: 'sql210.infinityfree.com');
    define('DB_USER', getenv('DB_USER') ?: 'if0_41339630');
    define('DB_PASS', getenv('DB_PASS') ?: 'Laktasi99ok');
    define('DB_NAME', getenv('DB_NAME') ?: 'if0_41339630_klinik_laktasi');
}

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
