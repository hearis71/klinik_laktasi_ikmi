<?php
/**
 * Pembayaran Page
 * Klinik Laktasi - Payment Management
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireAuth();

$currentPage = 'pembayaran';
$breadcrumbTitle = 'PEMBAYARAN';
$pageTitle = 'Pembayaran';

ob_start();
?>

<div class="page-container">
    <h1>Pembayaran</h1>
    <p>Halaman manajemen pembayaran akan ditampilkan di sini.</p>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
