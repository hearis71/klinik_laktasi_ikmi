<?php
/**
 * Tarif Page
 * Klinik Laktasi - Tariff Management
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireAuth();

$currentPage = 'tarif';
$breadcrumbTitle = 'TARIF';
$pageTitle = 'Management Tarif';

ob_start();
?>

<div class="page-container">
    <h1>Management Tarif</h1>
    <p>Halaman pengelolaan tarif layanan klinik akan ditampilkan di sini.</p>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
