<?php
/**
 * Antrian (Queue) Page
 * Klinik Laktasi - Patient Queue Management
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireAuth();

$currentPage = 'antrian';
$breadcrumbTitle = 'ANTRIAN';
$pageTitle = 'Antrean Perawatan';

// Fetch registrasi data
$pdo = getDbConnection();
$stmt = $pdo->query("
    SELECT r.*, p.no_rm, p.nama as nama_pasien, p.tanggal_lahir as tanggal_lahir_pasien
    FROM registrasi r
    LEFT JOIN pasien p ON r.pasien_id = p.id
    ORDER BY r.tanggal_pengkajian DESC, r.waktu_pengkajian DESC
");
$registrasiList = $stmt->fetchAll();

ob_start();
?>

<div class="page-container">
    <h1>Antrean</h1>
    <p>Halaman pemantauan antrean pasien</p>

    <div class="table-card">
        <div class="table-header">
            <h3>Daftar Antrean</h3>
            <a href="<?php echo baseUrl('pages/registrasi.php'); ?>" class="btn btn-primary">
                <span class="btn-icon">➕</span>
                Registrasi Baru
            </a>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No. Registrasi</th>
                        <th>No. RM</th>
                        <th>Nama Ibu</th>
                        <th>Tanggal Lahir Ibu</th>
                        <th>Usia Ibu</th>
                        <th>Nama Bayi</th>
                        <th>Tanggal Lahir Bayi</th>
                        <th>Usia Bayi</th>
                        <th>Tanggal Pengkajian</th>
                        <th>Waktu Pengkajian</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($registrasiList)): ?>
                    <tr>
                        <td colspan="11" class="text-center">Belum ada data registrasi</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($registrasiList as $reg): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reg['no_registrasi']); ?></td>
                            <td><?php echo htmlspecialchars($reg['no_rm'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($reg['nama_ibu']); ?></td>
                            <td>
                                <?php 
                                if ($reg['tanggal_lahir_ibu']) {
                                    echo formatDateIndonesian($reg['tanggal_lahir_ibu']);
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($reg['usia_ibu'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($reg['nama_bayi'] ?? '-'); ?></td>
                            <td>
                                <?php 
                                if ($reg['tanggal_lahir_bayi']) {
                                    echo formatDateIndonesian($reg['tanggal_lahir_bayi']);
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($reg['usia_bayi'] ?? '-'); ?></td>
                            <td><?php echo formatDateIndonesian($reg['tanggal_pengkajian']); ?></td>
                            <td><?php echo formatTime($reg['waktu_pengkajian']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a
                                        href="<?php echo baseUrl('pages/registrasi.php?id=' . $reg['id']); ?>"
                                        class="btn btn-sm btn-primary"
                                        title="Ubah"
                                    >
                                        ✏️
                                    </a>
                                    <!-- <a
                                        href="<?php echo baseUrl('pages/asesmen.php?no_registrasi=' . urlencode($reg['no_registrasi'])); ?>"
                                        class="btn btn-sm btn-info"
                                        title="Asesmen"
                                    >
                                        📋
                                    </a> -->
                                    <a
                                        href="<?php echo baseUrl('pages/formulir.php?no_registrasi=' . urlencode($reg['no_registrasi'])); ?>"
                                        class="btn btn-sm btn-secondary"
                                        title="Formulir"
                                    >
                                        📋
                                    </a>
                                    <button
                                        class="btn btn-sm btn-danger"
                                        onclick="confirmDelete(<?php echo $reg['id']; ?>, '<?php echo htmlspecialchars($reg['no_registrasi']); ?>')"
                                        title="Batal"
                                    >
                                        🗑️
                                    </button>
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

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-bootstrap-overlay modal-bootstrap-sm" style="display: none;">
    <div class="modal-bootstrap" onclick="event.stopPropagation()">
        <div class="modal-bootstrap-header modal-header-danger">
            <h5 class="modal-title">
                <span class="modal-icon-danger">🗑️</span>
                Konfirmasi Hapus
            </h5>
            <button class="btn-close-modal" onclick="closeDeleteModal()">
                <span>&times;</span>
            </button>
        </div>

        <div class="modal-bootstrap-body text-center">
            <div class="confirm-icon-wrapper">
                <span style="font-size: 64px;">⚠️</span>
            </div>
            <p class="confirm-message">
                Apakah Anda yakin ingin menghapus registrasi ini?
            </p>
            <div class="confirm-info-card">
                <div class="confirm-patient-name" id="deleteRegNo">-</div>
            </div>
            <p class="confirm-warning">
                <span class="warning-icon">⚠️</span>
                Data yang dihapus tidak dapat dikembalikan.
            </p>
        </div>

        <div class="modal-bootstrap-footer">
            <button type="button" class="btn btn-secondary-rounded" onclick="closeDeleteModal()">
                Batal
            </button>
            <button type="button" class="btn btn-danger-rounded" onclick="deleteRegistrasi()">
                <span class="btn-icon">🗑️</span>
                Ya, Hapus
            </button>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?php echo BASE_URL; ?>';
let deleteId = null;

function confirmDelete(id, noRegistrasi) {
    deleteId = id;
    document.getElementById('deleteRegNo').textContent = noRegistrasi;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    deleteId = null;
}

function deleteRegistrasi() {
    if (!deleteId) return;

    fetch(BASE_URL + '/api/registrasi.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'delete', id: deleteId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Gagal menghapus: ' + data.error);
        }
    })
    .catch(err => {
        alert('Terjadi kesalahan pada server');
    });
}

document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
