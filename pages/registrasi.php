<?php
/**
 * Registrasi Page
 * Klinik Laktasi - Patient Registration
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireAuth();

$currentPage = 'antrian';
$expandedMenu = 'antrian';
$breadcrumbTitle = 'ANTRIAN';
$pageTitle = 'Registrasi Pasien';

$pdo = getDbConnection();

// Check if edit mode
$id = $_GET['id'] ?? null;
$isEditMode = $id !== null;
$no_rm = $_GET['no_rm'] ?? null;

$registrasiData = null;
$pasienData = null;

// Fetch existing data
if ($isEditMode) {
    // Edit mode - fetch registrasi by ID
    $stmt = $pdo->prepare("SELECT * FROM registrasi WHERE id = ?");
    $stmt->execute([$id]);
    $registrasiData = $stmt->fetch();
    
    if (!$registrasiData) {
        redirect('/pages/antrian.php', 'Data registrasi tidak ditemukan', 'error');
    }
    
    // Fetch pasien data
    $stmt = $pdo->prepare("SELECT * FROM pasien WHERE id = ?");
    $stmt->execute([$registrasiData['pasien_id']]);
    $pasienData = $stmt->fetch();
} elseif ($no_rm) {
    // New registration with patient
    $stmt = $pdo->prepare("SELECT * FROM pasien WHERE no_rm = ?");
    $stmt->execute([$no_rm]);
    $pasienData = $stmt->fetch();
    
    if (!$pasienData) {
        redirect('/pages/antrian.php', 'Data pasien tidak ditemukan', 'error');
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_pengkajian = sanitize($_POST['tanggal_pengkajian']);
    $waktu_pengkajian = sanitize($_POST['waktu_pengkajian']);
    $nama_ibu = sanitize($_POST['nama_ibu']);
    $tanggal_lahir_ibu = sanitize($_POST['tanggal_lahir_ibu']);
    $nama_bayi = sanitize($_POST['nama_bayi'] ?? null);
    $tanggal_lahir_bayi = sanitize($_POST['tanggal_lahir_bayi'] ?? null);
    $pasien_id = sanitize($_POST['pasien_id'] ?? null);
    
    // Calculate ages
    $usia_ibu = calculateAgeDetailed($tanggal_lahir_ibu);
    $usia_bayi = $tanggal_lahir_bayi ? calculateBabyAge($tanggal_lahir_bayi) : null;
    
    if (empty($nama_ibu) || empty($tanggal_lahir_ibu) || empty($tanggal_pengkajian) || empty($waktu_pengkajian)) {
        $error = 'Mohon lengkapi data ibu (Nama, Tanggal Lahir, Tanggal & Waktu Pengkajian)';
    } else {
        try {
            if ($isEditMode) {
                // Update existing registrasi
                $stmt = $pdo->prepare("
                    UPDATE registrasi 
                    SET tanggal_pengkajian = ?, waktu_pengkajian = ?, 
                        nama_ibu = ?, tanggal_lahir_ibu = ?, usia_ibu = ?,
                        nama_bayi = ?, tanggal_lahir_bayi = ?, usia_bayi = ?,
                        pasien_id = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $tanggal_pengkajian, $waktu_pengkajian,
                    $nama_ibu, $tanggal_lahir_ibu, $usia_ibu,
                    $nama_bayi, $tanggal_lahir_bayi, $usia_bayi,
                    $pasien_id,
                    $id
                ]);
                redirect('/pages/antrian.php', 'Registrasi berhasil diperbarui', 'success');
            } else {
                // Create new registrasi
                $no_registrasi = generateRegistrationNumber();
                
                $stmt = $pdo->prepare("
                    INSERT INTO registrasi (no_registrasi, pasien_id, tanggal_pengkajian, waktu_pengkajian,
                        nama_ibu, tanggal_lahir_ibu, usia_ibu, nama_bayi, tanggal_lahir_bayi, usia_bayi)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $no_registrasi, $pasien_id,
                    $tanggal_pengkajian, $waktu_pengkajian,
                    $nama_ibu, $tanggal_lahir_ibu, $usia_ibu,
                    $nama_bayi, $tanggal_lahir_bayi, $usia_bayi
                ]);
                redirect('/pages/antrian.php', 'Registrasi berhasil disimpan', 'success');
            }
        } catch (PDOException $e) {
            $error = 'Gagal menyimpan data: ' . $e->getMessage();
        }
    }
}

function calculateAgeDetailed($birthDate) {
    $today = new DateTime();
    $birth = new DateTime($birthDate);
    $diff = $today->diff($birth);
    
    $years = $diff->y;
    $months = $diff->m;
    $days = $diff->d;
    
    $parts = [];
    if ($years > 0) $parts[] = "$years Tahun";
    if ($months > 0) $parts[] = "$months Bulan";
    if ($days > 0) $parts[] = "$days Hari";
    
    return implode(' ', $parts) ?: '0 Hari';
}

// Get current user for petugas pengkaji
$user = getCurrentUser();

ob_start();
?>

<div class="page-container">
    <h1>
        <?php if ($isEditMode): ?>
            Ubah Registrasi : <?php echo htmlspecialchars($registrasiData['no_registrasi']); ?>
        <?php else: ?>
            Registrasi Pasien
        <?php endif; ?>
    </h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="form-grid">
        <div class="form-row-double">
            <div class="form-group">
                <label for="noRM">No RM</label>
                <input
                    type="text"
                    id="noRM"
                    value="<?php echo htmlspecialchars($pasienData['no_rm'] ?? ''); ?>"
                    readonly
                    class="form-control-rounded"
                />
            </div>

            <div class="form-group">
                <label for="tanggal_pengkajian">Tanggal Pengkajian</label>
                <input
                    type="date"
                    id="tanggal_pengkajian"
                    name="tanggal_pengkajian"
                    value="<?php echo htmlspecialchars($_POST['tanggal_pengkajian'] ?? ($registrasiData['tanggal_pengkajian'] ?? date('Y-m-d'))); ?>"
                    class="form-control-rounded"
                    required
                />
            </div>
        </div>

        <div class="form-row-double">
            <div class="form-group">
                <label for="waktu_pengkajian">Waktu Pengkajian (Jam & Menit)</label>
                <input
                    type="time"
                    id="waktu_pengkajian"
                    name="waktu_pengkajian"
                    value="<?php echo htmlspecialchars($_POST['waktu_pengkajian'] ?? ($registrasiData['waktu_pengkajian'] ?? date('H:i'))); ?>"
                    class="form-control-rounded"
                    required
                />
            </div>

            <div class="form-group">
                <label for="petugasPengkaji">Petugas Pengkaji</label>
                <input
                    type="text"
                    id="petugasPengkaji"
                    value="<?php echo htmlspecialchars($user['nama']); ?>"
                    readonly
                    class="form-control-rounded"
                />
            </div>
        </div>

        <div class="form-row-full">
            <div class="form-group">
                <label for="nama_ibu">Nama Ibu</label>
                <input
                    type="text"
                    id="nama_ibu"
                    name="nama_ibu"
                    value="<?php echo htmlspecialchars($_POST['nama_ibu'] ?? ($registrasiData['nama_ibu'] ?? $pasienData['nama'] ?? '')); ?>"
                    placeholder="Masukkan nama ibu"
                    class="form-control-rounded"
                    <?php echo $pasienData ? 'readonly' : ''; ?>
                    required
                />
                <?php if ($pasienData): ?>
                    <span class="form-hint">Data otomatis dari pasien</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-row-double">
            <div class="form-group">
                <label for="tanggal_lahir_ibu">Tanggal Lahir Ibu</label>
                <input
                    type="date"
                    id="tanggal_lahir_ibu"
                    name="tanggal_lahir_ibu"
                    value="<?php echo htmlspecialchars($_POST['tanggal_lahir_ibu'] ?? ($registrasiData['tanggal_lahir_ibu'] ?? $pasienData['tanggal_lahir'] ?? '')); ?>"
                    class="form-control-rounded"
                    <?php echo $pasienData ? 'readonly' : ''; ?>
                    required
                />
            </div>

            <div class="form-group">
                <label for="usia_ibu">Usia Ibu</label>
                <input
                    type="text"
                    id="usia_ibu"
                    value="<?php 
                        $tgl_lahir = $_POST['tanggal_lahir_ibu'] ?? ($registrasiData['tanggal_lahir_ibu'] ?? $pasienData['tanggal_lahir'] ?? '');
                        echo $tgl_lahir ? calculateAgeDetailed($tgl_lahir) : ''; 
                    ?>"
                    readonly
                    class="form-control-rounded"
                />
                <span class="form-hint">Format: Tahun, Bulan, Hari</span>
            </div>
        </div>

        <div class="form-row-full">
            <div class="form-group">
                <label for="nama_bayi">Nama Bayi</label>
                <input
                    type="text"
                    id="nama_bayi"
                    name="nama_bayi"
                    value="<?php echo htmlspecialchars($_POST['nama_bayi'] ?? $registrasiData['nama_bayi'] ?? ''); ?>"
                    placeholder="Masukkan nama bayi"
                    class="form-control-rounded"
                />
            </div>
        </div>

        <div class="form-row-double">
            <div class="form-group">
                <label for="tanggal_lahir_bayi">Tanggal Lahir Bayi</label>
                <input
                    type="date"
                    id="tanggal_lahir_bayi"
                    name="tanggal_lahir_bayi"
                    value="<?php echo htmlspecialchars($_POST['tanggal_lahir_bayi'] ?? ($registrasiData['tanggal_lahir_bayi'] ?? '')); ?>"
                    class="form-control-rounded"
                />
            </div>

            <div class="form-group">
                <label for="usia_bayi">Usia Bayi</label>
                <input
                    type="text"
                    id="usia_bayi"
                    value="<?php 
                        $tgl_lahir_bayi = $_POST['tanggal_lahir_bayi'] ?? $registrasiData['tanggal_lahir_bayi'] ?? '';
                        echo $tgl_lahir_bayi ? calculateBabyAge($tgl_lahir_bayi) : ''; 
                    ?>"
                    readonly
                    class="form-control-rounded"
                />
                <span class="form-hint">Format: bulan dan hari</span>
            </div>
        </div>

        <input type="hidden" name="pasien_id" value="<?php echo $pasienData['id'] ?? ''; ?>">

        <div class="form-actions">
            <a href="/pages/antrian.php" class="btn btn-secondary-rounded">
                Kembali
            </a>
            <button type="submit" class="btn btn-primary-rounded">
                <?php if ($isEditMode): ?>
                    Perbarui
                <?php else: ?>
                    Simpan
                <?php endif; ?>
            </button>
        </div>
    </form>
</div>

<script>
// Auto-calculate baby age
document.getElementById('tanggal_lahir_bayi').addEventListener('change', function() {
    const birthDate = new Date(this.value);
    const today = new Date();
    
    let months = (today.getFullYear() - birthDate.getFullYear()) * 12;
    months -= birthDate.getMonth();
    months += today.getMonth();
    
    let days = today.getDate() - birthDate.getDate();
    if (days < 0) {
        months--;
        const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
        days += prevMonth.getDate();
    }
    
    if (months < 0) {
        months = 0;
    }
    
    document.getElementById('usia_bayi').value = months + ' bulan ' + days + ' hari';
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
