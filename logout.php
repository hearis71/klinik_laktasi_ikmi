<?php
/**
 * Logout Handler
 * Klinik Laktasi - User Logout
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

logoutUser();

$redirectUrl = function_exists('baseUrl') ? baseUrl('login.php') : '/login.php';
header('Location: ' . $redirectUrl);
exit;
