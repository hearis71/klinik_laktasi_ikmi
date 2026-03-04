<?php
/**
 * Rekam Medis Page
 * Klinik Laktasi - Medical Records
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireAuth();

$currentPage = 'rekam';
$breadcrumbTitle = 'REKAM MEDIS';
$pageTitle = 'Rekam Medis';

$pdo = getDbConnection();

// Fetch all medical records (asesmen)
$stmt = $pdo->query("
    SELECT a.*, r.no_registrasi, p.no_rm, p.nama as nama_pasien
    FROM asesmen a
    JOIN registrasi r ON a.no_registrasi = r.no_registrasi
    LEFT JOIN pasien p ON r.pasien_id = p.id
    ORDER BY a.created_at DESC
");
$rekamMedisList = $stmt->fetchAll();

ob_start();
?>

<div class="page-container">
    <h1>Rekam Medis</h1>
    <p>Halaman pengelolaan rekam medis pasien</p>

    <div class="table-card">
        <div class="table-header">
            <h3>Data Rekam Medis</h3>
            <div class="table-controls">
                <input
                    type="text"
                    placeholder="Cari rekam medis..."
                    class="search-input"
                />
            </div>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Registrasi</th>
                        <th>No RM</th>
                        <th>Nama Ibu</th>
                        <th>Nama Bayi</th>
                        <th>Diagnosis</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rekamMedisList)): ?>
                    <tr>
                        <td colspan="8" class="text-center">Belum ada data rekam medis</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($rekamMedisList as $index => $rm): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($rm['no_registrasi']); ?></td>
                            <td><?php echo htmlspecialchars($rm['no_rm'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($rm['nama_ibu']); ?></td>
                            <td><?php echo htmlspecialchars($rm['nama_bayi'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars(substr($rm['diagnosis'], 0, 50)) . (strlen($rm['diagnosis']) > 50 ? '...' : ''); ?></td>
                            <td><?php echo formatDateIndonesian($rm['created_at']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a
                                        href="/pages/asesmen.php?no_registrasi=<?php echo urlencode($rm['no_registrasi']); ?>"
                                        class="btn btn-sm btn-info"
                                        title="Lihat Detail"
                                    >
                                        👁️
                                    </a>
                                </div>
                            </td>
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
