<?php
/**
 * Authentication Helper Functions
 * Klinik Laktasi - Session & Auth Management
 */

// Load configuration
if (file_exists(__DIR__ . '/../config/config.php')) {
    require_once __DIR__ . '/../config/config.php';
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user']);
}

/**
 * Get current logged in user
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return $_SESSION['user'];
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user name
 */
function getCurrentUserName() {
    $user = getCurrentUser();
    return $user['nama'] ?? 'User';
}

/**
 * Get current user role
 */
function getCurrentUserRole() {
    $user = getCurrentUser();
    return $user['role'] ?? null;
}

/**
 * Login user
 */
function loginUser($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user'] = [
        'id' => $user['id'],
        'nama' => $user['nama'],
        'email' => $user['email'],
        'role' => $user['role']
    ];
    $_SESSION['login_time'] = time();
}

/**
 * Logout user
 */
function logoutUser() {
    session_unset();
    session_destroy();
    session_start(); // Start new session
}

/**
 * Check if user has specific role
 */
function hasRole($role) {
    return getCurrentUserRole() === $role;
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return hasRole('ADMIN');
}

/**
 * Redirect to login if not logged in
 */
function requireAuth() {
    if (!isLoggedIn()) {
        $loginUrl = function_exists('baseUrl') ? baseUrl('login.php') : '/login.php';
        header('Location: ' . $loginUrl);
        exit;
    }
}

/**
 * Redirect to dashboard if already logged in
 */
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        $dashboardUrl = function_exists('baseUrl') ? baseUrl('index.php') : '/index.php';
        header('Location: ' . $dashboardUrl);
        exit;
    }
}

/**
 * Generate CSRF token
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = binrandom(32);
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Set flash message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Get current date in Indonesian format
 */
function getCurrentDateIndonesian() {
    $months = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $days = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    
    $date = new DateTime();
    $day = $days[$date->format('l')];
    $month = $months[(int)$date->format('n')];
    $year = $date->format('Y');
    
    return "$day, " . $date->format('j') . " $month $year";
}

/**
 * Format date to Indonesian format
 */
function formatDateIndonesian($date, $format = 'long') {
    $months = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $timestamp = strtotime($date);
    $day = date('j', $timestamp);
    $month = $months[(int)date('n', $timestamp)];
    $year = date('Y', $timestamp);
    
    if ($format === 'short') {
        return "$day/$month/$year";
    }
    
    return "$day $month $year";
}

/**
 * Format time (HH:MM)
 */
function formatTime($time) {
    return date('H:i', strtotime($time));
}

/**
 * Calculate age from birth date
 */
function calculateAge($birthDate) {
    $today = new DateTime();
    $birth = new DateTime($birthDate);
    $diff = $today->diff($birth);
    
    return $diff->y;
}

/**
 * Calculate baby age in months and days
 */
function calculateBabyAge($birthDate) {
    $today = new DateTime();
    $birth = new DateTime($birthDate);
    $diff = $today->diff($birth);
    
    $months = $diff->m + ($diff->y * 12);
    $days = $diff->d;
    
    if ($months > 0) {
        return "$months bulan $days hari";
    }
    return "$days hari";
}

/**
 * Generate registration number
 */
function generateRegistrationNumber($date = null) {
    $date = $date ?? new DateTime();
    $prefix = 'REG-' . $date->format('Ymd');
    
    // Get last registration number for today
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT no_registrasi FROM registrasi WHERE no_registrasi LIKE ? ORDER BY no_registrasi DESC LIMIT 1");
    $stmt->execute([$prefix . '%']);
    $last = $stmt->fetch();
    
    if ($last) {
        $lastNum = (int)substr($last['no_registrasi'], -4);
        $newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $newNum = '0001';
    }
    
    return $prefix . '-' . $newNum;
}

/**
 * Generate medical record number (No. RM)
 */
function generateMedicalRecordNumber() {
    $pdo = getDbConnection();
    $stmt = $pdo->query("SELECT no_rm FROM pasien ORDER BY no_rm DESC LIMIT 1");
    $last = $stmt->fetch();
    
    if ($last) {
        $lastNum = (int)substr($last['no_rm'], -6);
        $newNum = str_pad($lastNum + 1, 6, '0', STR_PAD_LEFT);
    } else {
        $newNum = '000001';
    }
    
    return 'RM-' . $newNum;
}

/**
 * Sanitize input
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect with message
 */
function redirect($url, $message = null, $type = 'success') {
    if ($message) {
        setFlashMessage($type, $message);
    }
    $redirectUrl = function_exists('baseUrl') ? baseUrl($url) : $url;
    header('Location: ' . $redirectUrl);
    exit;
}

/**
 * JSON response helper
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
