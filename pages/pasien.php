<?php
/**
 * Pasien (Patient) Management Page
 * Klinik Laktasi - Patient List
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireAuth();

$currentPage = 'pasien';
$breadcrumbTitle = 'PASIEN';
$pageTitle = 'Daftar Pasien';

// Handle search
$searchTerm = $_GET['q'] ?? '';

// Fetch patients
$pdo = getDbConnection();
if ($searchTerm) {
    $stmt = $pdo->prepare("
        SELECT * FROM pasien 
        WHERE nama LIKE ? OR no_rm LIKE ? OR nik LIKE ?
        ORDER BY created_at DESC
    ");
    $searchParam = "%$searchTerm%";
    $stmt->execute([$searchParam, $searchParam, $searchParam]);
} else {
    $stmt = $pdo->query("SELECT * FROM pasien ORDER BY created_at DESC");
}
$pasienList = $stmt->fetchAll();

ob_start();
?>

<div class="page-container">
    <div class="page-header">
        <h1>
            <span class="page-icon">👥</span>
            Daftar Pasien
        </h1>
        <button class="btn btn-primary" onclick="openAddModal()">
            <span class="btn-icon">➕</span>
            Tambah Pasien Baru
        </button>
    </div>

    <?php 
    $flash = getFlashMessage();
    if ($flash): 
    ?>
        <div class="alert alert-<?php echo $flash['type']; ?>">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>

    <div class="table-card">
        <div class="table-header">
            <h3>Data Pasien</h3>
            <div class="table-controls">
                <form method="GET" action="" class="search-box">
                    <span class="search-icon">🔍</span>
                    <input
                        type="text"
                        name="q"
                        placeholder="Cari pasien (nama, no RM, NIK)..."
                        value="<?php echo htmlspecialchars($searchTerm); ?>"
                        class="search-input"
                    />
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No RM</th>
                        <th>Nama</th>
                        <th>Tanggal Lahir</th>
                        <th>Usia</th>
                        <th>No HP</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pasienList)): ?>
                    <tr>
                        <td colspan="8" class="text-center">
                            <?php echo $searchTerm ? 'Tidak ada pasien yang ditemukan' : 'Belum ada data pasien'; ?>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($pasienList as $index => $p): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><strong><?php echo htmlspecialchars($p['no_rm']); ?></strong></td>
                            <td><?php echo htmlspecialchars($p['nama']); ?></td>
                            <td><?php echo formatDateIndonesian($p['tanggal_lahir']); ?></td>
                            <td><?php echo calculateAge($p['tanggal_lahir']); ?> tahun</td>
                            <td><?php echo htmlspecialchars($p['no_hp'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($p['alamat'] ?? '-'); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button
                                        class="btn btn-sm btn-primary"
                                        onclick="openEditModal(<?php echo htmlspecialchars(json_encode($p)); ?>)"
                                        title="Edit Pasien"
                                    >
                                        ✏️
                                    </button>
                                    <a
                                        href="<?php echo baseUrl('pages/registrasi.php?no_rm=' . urlencode($p['no_rm'])); ?>"
                                        class="btn btn-sm btn-success"
                                        title="Registrasi"
                                    >
                                        📄
                                    </a>
                                    <button
                                        class="btn btn-sm btn-danger"
                                        onclick="confirmDelete(<?php echo $p['id']; ?>, '<?php echo htmlspecialchars($p['nama']); ?>')"
                                        title="Hapus Pasien"
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

<!-- Modal Add/Edit Patient -->
<div id="patientModal" class="modal-bootstrap-overlay" style="display: none;">
    <div class="modal-bootstrap" onclick="event.stopPropagation()">
        <div class="modal-bootstrap-header">
            <h5 class="modal-title">
                <span class="modal-icon-primary">👤</span>
                <span id="modalTitle">Tambah Pasien Baru</span>
            </h5>
            <button class="btn-close-modal" onclick="closeModal()">
                <span>&times;</span>
            </button>
        </div>

        <form id="patientForm" method="POST" action="<?php echo baseUrl('api/pasien.php'); ?>" class="modal-bootstrap-body">
            <input type="hidden" name="action" id="formAction" value="create">
            <input type="hidden" name="id" id="patientId">
            
            <div class="form-row">
                <div class="form-group-full">
                    <label for="nik" class="form-label">
                        NIK <span class="text-muted">(Opsional)</span>
                    </label>
                    <input
                        type="text"
                        id="nik"
                        name="nik"
                        placeholder="Masukkan NIK (16 digit)"
                        class="form-control-rounded"
                        maxlength="16"
                    />
                </div>
            </div>

            <div class="form-row">
                <div class="form-group-full">
                    <label for="nama" class="form-label">
                        Nama Lengkap <span class="text-required">*</span>
                    </label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        placeholder="Masukkan nama lengkap"
                        class="form-control-rounded"
                        required
                    />
                </div>
            </div>

            <div class="form-row">
                <div class="form-group-full">
                    <label for="tanggalLahir" class="form-label">
                        Tanggal Lahir <span class="text-required">*</span>
                    </label>
                    <input
                        type="date"
                        id="tanggalLahir"
                        name="tanggal_lahir"
                        class="form-control-rounded"
                        required
                    />
                </div>
            </div>

            <div class="form-row">
                <div class="form-group-full">
                    <label for="alamat" class="form-label">
                        Alamat <span class="text-muted">(Opsional)</span>
                    </label>
                    <textarea
                        id="alamat"
                        name="alamat"
                        placeholder="Masukkan alamat lengkap"
                        class="form-control-rounded textarea"
                        rows="3"
                    ></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group-full">
                    <label for="noHp" class="form-label">
                        No HP / WhatsApp <span class="text-muted">(Opsional)</span>
                    </label>
                    <input
                        type="tel"
                        id="noHp"
                        name="no_hp"
                        placeholder="08xxxxxxxxxx"
                        class="form-control-rounded"
                    />
                </div>
            </div>

            <div class="modal-bootstrap-footer">
                <button type="button" class="btn btn-secondary-rounded" onclick="closeModal()">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary-rounded">
                    <span class="btn-icon">💾</span>
                    <span id="submitText">Simpan Pasien</span>
                </button>
            </div>
        </form>
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
                Apakah Anda yakin ingin menghapus data pasien ini?
            </p>
            <div class="confirm-info-card">
                <div class="confirm-patient-name" id="deletePatientName">-</div>
                <div class="confirm-patient-rm" id="deletePatientRm">No RM: -</div>
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
            <button type="button" class="btn btn-danger-rounded" onclick="deletePatient()">
                <span class="btn-icon">🗑️</span>
                Ya, Hapus
            </button>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?php echo BASE_URL; ?>';
let deleteId = null;

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Pasien Baru';
    document.getElementById('formAction').value = 'create';
    document.getElementById('patientForm').reset();
    document.getElementById('patientId').value = '';
    document.getElementById('patientModal').style.display = 'flex';
}

function openEditModal(patient) {
    document.getElementById('modalTitle').textContent = 'Edit Data Pasien';
    document.getElementById('formAction').value = 'update';
    document.getElementById('patientId').value = patient.id;
    document.getElementById('nik').value = patient.nik || '';
    document.getElementById('nama').value = patient.nama;
    document.getElementById('tanggalLahir').value = patient.tanggal_lahir;
    document.getElementById('alamat').value = patient.alamat || '';
    document.getElementById('noHp').value = patient.no_hp || '';
    document.getElementById('patientModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('patientModal').style.display = 'none';
}

function confirmDelete(id, name) {
    deleteId = id;
    document.getElementById('deletePatientName').textContent = name;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    deleteId = null;
}

function deletePatient() {
    if (!deleteId) return;

    fetch(BASE_URL + '/api/pasien.php', {
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

// Handle patient form submission
document.getElementById('patientForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const action = formData.get('action');
    
    fetch(BASE_URL + '/api/pasien.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: action,
            id: formData.get('id'),
            nik: formData.get('nik'),
            nama: formData.get('nama'),
            tanggal_lahir: formData.get('tanggal_lahir'),
            alamat: formData.get('alamat'),
            no_hp: formData.get('no_hp')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal();
            window.location.reload();
        } else {
            alert('Gagal menyimpan: ' + data.error);
        }
    })
    .catch(err => {
        alert('Terjadi kesalahan pada server');
    });
});

// Close modals on outside click
document.getElementById('patientModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
