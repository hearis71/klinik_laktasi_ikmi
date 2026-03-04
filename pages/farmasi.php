<?php
/**
 * Farmasi Page
 * Klinik Laktasi - Pharmacy Management
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireAuth();

$currentPage = 'farmasi';
$breadcrumbTitle = 'FARMASI';
$pageTitle = 'Farmasi';

ob_start();
?>

<div class="page-container">
    <h1>Farmasi</h1>
    <p>Halaman manajemen farmasi akan ditampilkan di sini.</p>

    <div class="table-card">
        <div class="table-header">
            <h3>Stok Obat & BHP</h3>
            <button class="btn btn-primary">
                <span class="btn-icon">➕</span>
                Tambah Obat
            </button>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Obat</th>
                        <th>Nama Obat</th>
                        <th>Stok</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data obat</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
