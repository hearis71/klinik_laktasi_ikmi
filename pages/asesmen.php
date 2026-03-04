<?php
/**
 * Asesmen Page
 * Klinik Laktasi - Assessment Form
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireAuth();

$currentPage = 'antrian';
$breadcrumbTitle = 'ANTRIAN';
$pageTitle = 'Asesmen Konseling Laktasi';

$pdo = getDbConnection();
$no_registrasi = $_GET['no_registrasi'] ?? null;

if (!$no_registrasi) {
    redirect('pages/antrian.php', 'No. Registrasi tidak ditemukan', 'error');
}

// Fetch registrasi data
$stmt = $pdo->prepare("SELECT * FROM registrasi WHERE no_registrasi = ?");
$stmt->execute([$no_registrasi]);
$registrasi = $stmt->fetch();

if (!$registrasi) {
    redirect('pages/antrian.php', 'Data registrasi tidak ditemukan', 'error');
}

// Check if assessment already exists
$stmt = $pdo->prepare("SELECT * FROM asesmen WHERE no_registrasi = ?");
$stmt->execute([$no_registrasi]);
$asesmenData = $stmt->fetch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama_ibu' => sanitize($_POST['nama_ibu']),
        'usia_ibu' => sanitize($_POST['usia_ibu']),
        'no_hp' => sanitize($_POST['no_hp'] ?? null),
        'alamat' => sanitize($_POST['alamat'] ?? null),
        'nama_bayi' => sanitize($_POST['nama_bayi']),
        'tanggal_lahir_bayi' => sanitize($_POST['tanggal_lahir_bayi']),
        'jenis_kelamin' => sanitize($_POST['jenis_kelamin']),
        'berat_badan_lahir' => sanitize($_POST['berat_badan_lahir'] ?? null),
        'panjang_badan' => sanitize($_POST['panjang_badan'] ?? null),
        'usia_kehamilan' => sanitize($_POST['usia_kehamilan'] ?? null),
        'riwayat_menyusui' => sanitize($_POST['riwayat_menyusui'] ?? null),
        'hambatan_menyusui' => sanitize($_POST['hambatan_menyusui'] ?? null),
        'frekuensi_menyusui' => sanitize($_POST['frekuensi_menyusui'] ?? null),
        'durasi_menyusui' => sanitize($_POST['durasi_menyusui'] ?? null),
        'kondisi_payudara' => sanitize($_POST['kondisi_payudara'] ?? null),
        'putting_susuk' => sanitize($_POST['putting_susuk'] ?? null),
        'refleks_let_down' => sanitize($_POST['refleks_let_down'] ?? null),
        'diagnosis' => sanitize($_POST['diagnosis']),
        'rencana_tindak_lanjut' => sanitize($_POST['rencana_tindak_lanjut']),
        'edukasi' => sanitize($_POST['edukasi'] ?? null),
    ];
    
    try {
        if ($asesmenData) {
            // Update
            $stmt = $pdo->prepare("
                UPDATE asesmen SET
                    nama_ibu = ?, usia_ibu = ?, no_hp = ?, alamat = ?,
                    nama_bayi = ?, tanggal_lahir_bayi = ?, jenis_kelamin = ?,
                    berat_badan_lahir = ?, panjang_badan = ?, usia_kehamilan = ?,
                    riwayat_menyusui = ?, hambatan_menyusui = ?, frekuensi_menyusui = ?,
                    durasi_menyusui = ?, kondisi_payudara = ?, putting_susuk = ?,
                    refleks_let_down = ?, diagnosis = ?, rencana_tindak_lanjut = ?,
                    edukasi = ?
                WHERE no_registrasi = ?
            ");
            $stmt->execute([
                $data['nama_ibu'], $data['usia_ibu'], $data['no_hp'], $data['alamat'],
                $data['nama_bayi'], $data['tanggal_lahir_bayi'], $data['jenis_kelamin'],
                $data['berat_badan_lahir'], $data['panjang_badan'], $data['usia_kehamilan'],
                $data['riwayat_menyusui'], $data['hambatan_menyusui'], $data['frekuensi_menyusui'],
                $data['durasi_menyusui'], $data['kondisi_payudara'], $data['putting_susuk'],
                $data['refleks_let_down'], $data['diagnosis'], $data['rencana_tindak_lanjut'],
                $data['edukasi'],
                $no_registrasi
            ]);
            redirect('pages/antrian.php', 'Asesmen berhasil diupdate', 'success');
        } else {
            // Insert
            $stmt = $pdo->prepare("
                INSERT INTO asesmen (no_registrasi, nama_ibu, usia_ibu, no_hp, alamat,
                    nama_bayi, tanggal_lahir_bayi, jenis_kelamin, berat_badan_lahir,
                    panjang_badan, usia_kehamilan, riwayat_menyusui, hambatan_menyusui,
                    frekuensi_menyusui, durasi_menyusui, kondisi_payudara, putting_susuk,
                    refleks_let_down, diagnosis, rencana_tindak_lanjut, edukasi)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $no_registrasi,
                $data['nama_ibu'], $data['usia_ibu'], $data['no_hp'], $data['alamat'],
                $data['nama_bayi'], $data['tanggal_lahir_bayi'], $data['jenis_kelamin'],
                $data['berat_badan_lahir'], $data['panjang_badan'], $data['usia_kehamilan'],
                $data['riwayat_menyusui'], $data['hambatan_menyusui'], $data['frekuensi_menyusui'],
                $data['durasi_menyusui'], $data['kondisi_payudara'], $data['putting_susuk'],
                $data['refleks_let_down'], $data['diagnosis'], $data['rencana_tindak_lanjut'],
                $data['edukasi']
            ]);
            redirect('pages/antrian.php', 'Asesmen berhasil disimpan', 'success');
        }
    } catch (PDOException $e) {
        $error = 'Gagal menyimpan data: ' . $e->getMessage();
    }
}

// Pre-fill with existing data or registrasi data
$formData = $asesmenData ?: [
    'nama_ibu' => $registrasi['nama_ibu'],
    'usia_ibu' => $registrasi['usia_ibu'],
    'nama_bayi' => $registrasi['nama_bayi'] ?? '',
    'tanggal_lahir_bayi' => $registrasi['tanggal_lahir_bayi'] ?? '',
];

ob_start();
?>

<div class="page-container">
    <div class="page-header">
        <h1>Asesmen Konseling Laktasi</h1>
        <p>No. Registrasi: <?php echo htmlspecialchars($no_registrasi); ?></p>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <!-- Data Ibu -->
        <div class="form-section">
            <h3 class="section-title">Data Ibu</h3>
            <div class="form-row-double">
                <div class="form-group">
                    <label>Nama Ibu</label>
                    <input
                        type="text"
                        name="nama_ibu"
                        class="form-control-rounded"
                        value="<?php echo htmlspecialchars($formData['nama_ibu']); ?>"
                        required
                    />
                </div>
                <div class="form-group">
                    <label>Usia</label>
                    <input
                        type="text"
                        name="usia_ibu"
                        class="form-control-rounded"
                        value="<?php echo htmlspecialchars($formData['usia_ibu']); ?>"
                        required
                    />
                </div>
            </div>
            <div class="form-row-double">
                <div class="form-group">
                    <label>No. HP</label>
                    <input
                        type="text"
                        name="no_hp"
                        class="form-control-rounded"
                        value="<?php echo htmlspecialchars($formData['no_hp'] ?? ''); ?>"
                    />
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <input
                        type="text"
                        name="alamat"
                        class="form-control-rounded"
                        value="<?php echo htmlspecialchars($formData['alamat'] ?? ''); ?>"
                    />
                </div>
            </div>
        </div>

        <!-- Data Bayi -->
        <div class="form-section">
            <h3 class="section-title">Data Bayi</h3>
            <div class="form-row-double">
                <div class="form-group">
                    <label>Nama Bayi</label>
                    <input
                        type="text"
                        name="nama_bayi"
                        class="form-control-rounded"
                        value="<?php echo htmlspecialchars($formData['nama_bayi']); ?>"
                        required
                    />
                </div>
                <div class="form-group">
                    <label>Tanggal Lahir</label>
                    <input
                        type="date"
                        name="tanggal_lahir_bayi"
                        class="form-control-rounded"
                        value="<?php echo htmlspecialchars($formData['tanggal_lahir_bayi'] ?? ''); ?>"
                        required
                    />
                </div>
            </div>
            <div class="form-row-double">
                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-control-rounded" required>
                        <option value="">Pilih</option>
                        <option value="L" <?php echo ($formData['jenis_kelamin'] ?? '') === 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="P" <?php echo ($formData['jenis_kelamin'] ?? '') === 'P' ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Berat Badan Lahir (gram)</label>
                    <input
                        type="number"
                        name="berat_badan_lahir"
                        class="form-control-rounded"
                        value="<?php echo htmlspecialchars($formData['berat_badan_lahir'] ?? ''); ?>"
                    />
                </div>
            </div>
            <div class="form-row-double">
                <div class="form-group">
                    <label>Panjang Badan (cm)</label>
                    <input
                        type="number"
                        name="panjang_badan"
                        class="form-control-rounded"
                        value="<?php echo htmlspecialchars($formData['panjang_badan'] ?? ''); ?>"
                    />
                </div>
                <div class="form-group">
                    <label>Usia Kehamilan (minggu)</label>
                    <input
                        type="number"
                        name="usia_kehamilan"
                        class="form-control-rounded"
                        value="<?php echo htmlspecialchars($formData['usia_kehamilan'] ?? ''); ?>"
                    />
                </div>
            </div>
        </div>

        <!-- Riwayat Menyusui -->
        <div class="form-section">
            <h3 class="section-title">Riwayat Menyusui</h3>
            <div class="form-group">
                <label>Riwayat Menyusui Sebelumnya</label>
                <textarea name="riwayat_menyusui" class="form-control-rounded" rows="3"><?php echo htmlspecialchars($formData['riwayat_menyusui'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label>Hambatan/Keluhan Menyusui</label>
                <textarea name="hambatan_menyusui" class="form-control-rounded" rows="3"><?php echo htmlspecialchars($formData['hambatan_menyusui'] ?? ''); ?></textarea>
            </div>
            <div class="form-row-double">
                <div class="form-group">
                    <label>Frekuensi Menyusui (kali/hari)</label>
                    <input
                        type="number"
                        name="frekuensi_menyusui"
                        class="form-control-rounded"
                        value="<?php echo htmlspecialchars($formData['frekuensi_menyusui'] ?? ''); ?>"
                    />
                </div>
                <div class="form-group">
                    <label>Durasi Menyusui (menit)</label>
                    <input
                        type="number"
                        name="durasi_menyusui"
                        class="form-control-rounded"
                        value="<?php echo htmlspecialchars($formData['durasi_menyusui'] ?? ''); ?>"
                    />
                </div>
            </div>
        </div>

        <!-- Pemeriksaan Fisik -->
        <div class="form-section">
            <h3 class="section-title">Pemeriksaan Fisik</h3>
            <div class="form-group">
                <label>Kondisi Payudara</label>
                <textarea name="kondisi_payudara" class="form-control-rounded" rows="2"><?php echo htmlspecialchars($formData['kondisi_payudara'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label>Putting Susuk</label>
                <select name="putting_susuk" class="form-control-rounded">
                    <option value="">Pilih</option>
                    <option value="Normal" <?php echo ($formData['putting_susuk'] ?? '') === 'Normal' ? 'selected' : ''; ?>>Normal</option>
                    <option value="Datar" <?php echo ($formData['putting_susuk'] ?? '') === 'Datar' ? 'selected' : ''; ?>>Datar</option>
                    <option value="Terdorong" <?php echo ($formData['putting_susuk'] ?? '') === 'Terdorong' ? 'selected' : ''; ?>>Terdorong</option>
                    <option value="Lecet" <?php echo ($formData['putting_susuk'] ?? '') === 'Lecet' ? 'selected' : ''; ?>>Lecet</option>
                </select>
            </div>
            <div class="form-group">
                <label>Refleks Let-Down</label>
                <select name="refleks_let_down" class="form-control-rounded">
                    <option value="">Pilih</option>
                    <option value="Baik" <?php echo ($formData['refleks_let_down'] ?? '') === 'Baik' ? 'selected' : ''; ?>>Baik</option>
                    <option value="Cukup" <?php echo ($formData['refleks_let_down'] ?? '') === 'Cukup' ? 'selected' : ''; ?>>Cukup</option>
                    <option value="Kurang" <?php echo ($formData['refleks_let_down'] ?? '') === 'Kurang' ? 'selected' : ''; ?>>Kurang</option>
                </select>
            </div>
        </div>

        <!-- Asesmen dan Rencana -->
        <div class="form-section">
            <h3 class="section-title">Asesmen dan Rencana Tindak Lanjut</h3>
            <div class="form-group">
                <label>Diagnosis/Masalah</label>
                <textarea name="diagnosis" class="form-control-rounded" rows="3" required><?php echo htmlspecialchars($formData['diagnosis'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label>Rencana Tindak Lanjut</label>
                <textarea name="rencana_tindak_lanjut" class="form-control-rounded" rows="3" required><?php echo htmlspecialchars($formData['rencana_tindak_lanjut'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label>Edukasi yang Diberikan</label>
                <textarea name="edukasi" class="form-control-rounded" rows="3"><?php echo htmlspecialchars($formData['edukasi'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?php echo baseUrl('pages/antrian.php'); ?>" class="btn btn-secondary-rounded">Batal</a>
            <button type="submit" class="btn btn-primary-rounded">Simpan Asesmen</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
