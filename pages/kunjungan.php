<?php
/**
 * Kunjungan (Visit) Page
 * Klinik Laktasi - Patient Visit History
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireAuth();

$currentPage = 'kunjungan';
$breadcrumbTitle = 'KUNJUNGAN';
$pageTitle = 'Riwayat Kunjungan';

$pdo = getDbConnection();

// Fetch kunjungan data with patient info
$stmt = $pdo->query("
    SELECT k.*, p.no_rm, p.nama as nama_pasien
    FROM kunjungan k
    LEFT JOIN pasien p ON k.pasien_id = p.id
    ORDER BY k.tanggal_kunjungan DESC, k.waktu_kunjungan DESC
");
$kunjunganList = $stmt->fetchAll();

ob_start();
?>

<div class="page-container">
    <h1>Kunjungan Pasien</h1>
    <p>Halaman data kunjungan pasien</p>

    <div class="table-card">
        <div class="table-header">
            <h3>Data Kunjungan</h3>
            <div class="table-controls">
                <button class="btn btn-primary">
                    <span class="btn-icon">➕</span>
                    Tambah Kunjungan
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No RM</th>
                        <th>Nama Pasien</th>
                        <th>Tanggal Kunjungan</th>
                        <th>Waktu Kunjungan</th>
                        <th>Tujuan Kunjungan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kunjunganList)): ?>
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data kunjungan</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($kunjunganList as $index => $k): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($k['no_rm'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($k['nama_pasien'] ?? '-'); ?></td>
                            <td><?php echo formatDateIndonesian($k['tanggal_kunjungan']); ?></td>
                            <td><?php echo formatTime($k['waktu_kunjungan']); ?></td>
                            <td><?php echo htmlspecialchars($k['tujuan_kunjungan'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($k['keterangan'] ?? '-'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
