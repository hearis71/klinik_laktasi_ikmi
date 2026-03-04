<?php
/**
 * Application Configuration
 * Klinik Laktasi - Base Configuration
 */

// Define base URL - adjust according to your setup
// Uses environment variable if provided (e.g. on hosting platforms)
$envBase = getenv('BASE_URL');
if ($envBase) {
    define('BASE_URL', rtrim($envBase, '/'));
} else {
    // Option 1: If using virtual host (http://klinik-laktasi.test)
    // define('BASE_URL', 'http://klinik-laktasi.test');
    
    // Option 2: If using localhost subfolder (http://localhost/klinik-laktasi2)
    define('BASE_URL', 'http://localhost/klinik-laktasi2');
}

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Site settings
define('SITE_NAME', 'Klinik Laktasi IKMI');
define('SITE_VERSION', '1.0.0');

/**
 * Get base URL
 */
function baseUrl($path = '') {
    return BASE_URL . ($path ? '/' . ltrim($path, '/') : '');
}

/**
 * Redirect to a URL
 */
function redirectUrl($path, $message = null, $type = 'success') {
    if ($message) {
        setFlashMessage($type, $message);
    }
    $url = baseUrl($path);
    header('Location: ' . $url);
    exit;
}
