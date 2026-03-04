<?php
/**
 * Obat Page
 * Klinik Laktasi - Medicine Management
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireAuth();

$currentPage = 'obat';
$breadcrumbTitle = 'OBAT';
$pageTitle = 'Management Obat & BHP';

ob_start();
?>

<div class="page-container">
    <h1>Management Obat & BHP</h1>
    <p>Halaman pengelolaan data obat dan BHP akan ditampilkan di sini.</p>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
