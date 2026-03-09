<?php
/**
 * Pemeriksaan Fisik Page
 * Klinik Laktasi - Physical Examination Form
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAuth();

$currentPage = 'antrian';
$breadcrumbTitle = 'ANTRIAN';
$pageTitle = 'Pemeriksaan Fisik Ibu dan Bayi';

$pdo = getDbConnection();
$no_registrasi = $_GET['no_registrasi'] ?? null;

if (!$no_registrasi) {
    redirect('pages/antrian.php', 'No. Registrasi tidak ditemukan', 'error');
}

$stmt = $pdo->prepare("SELECT * FROM registrasi WHERE no_registrasi = ?");
$stmt->execute([$no_registrasi]);
$registrasi = $stmt->fetch();

if (!$registrasi) {
    redirect('pages/antrian.php', 'Data registrasi tidak ditemukan', 'error');
}

$f = $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = 'Formulir pemeriksaan fisik berhasil diisi.';
}

ob_start();
?>

    <?php include __DIR__ . '/formulir_header.php'; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="form-grid">
        <div class="form-section">
            <h3 class="section-title">Formulir Pemeriksaan Fisik Ibu</h3>

            <div class="form-group">
                <label>1. Keadaan Umum</label>
                <input type="text" name="ibu_keadaan_umum" class="form-control-rounded" value="<?php echo htmlspecialchars($f['ibu_keadaan_umum'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>2. Sistolik / Diastolik</label>
                <input type="text" name="ibu_sistolik_diastolik" class="form-control-rounded" value="<?php echo htmlspecialchars($f['ibu_sistolik_diastolik'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>3. Nadi / Pernapasan</label>
                <input type="text" name="ibu_nadi_pernapasan" class="form-control-rounded" value="<?php echo htmlspecialchars($f['ibu_nadi_pernapasan'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>4. Suhu</label>
                <input type="text" name="ibu_suhu" class="form-control-rounded" value="<?php echo htmlspecialchars($f['ibu_suhu'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>5. Skrining Nyeri</label>
                <input type="text" name="ibu_skrining_nyeri" class="form-control-rounded" value="<?php echo htmlspecialchars($f['ibu_skrining_nyeri'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>6. Tinggi Badan</label>
                <input type="text" name="ibu_tinggi_badan" class="form-control-rounded" value="<?php echo htmlspecialchars($f['ibu_tinggi_badan'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>7. Berat Badan</label>
                <input type="text" name="ibu_berat_badan" class="form-control-rounded" value="<?php echo htmlspecialchars($f['ibu_berat_badan'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>8. Index Masa Tubuh</label>
                <input type="text" name="ibu_index_masa_tubuh" class="form-control-rounded" value="<?php echo htmlspecialchars($f['ibu_index_masa_tubuh'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>9. Lingkar Kepala</label>
                <input type="text" name="ibu_lingkar_kepala" class="form-control-rounded" value="<?php echo htmlspecialchars($f['ibu_lingkar_kepala'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>10. Pemeriksaan Fisik</label>
                <textarea name="ibu_pemeriksaan_fisik" class="form-control-rounded" rows="2"><?php echo htmlspecialchars($f['ibu_pemeriksaan_fisik'] ?? ''); ?></textarea>
            </div>

            <h4 class="section-title">Kondisi Payudara</h4>

            <div class="form-group">
                <label>Putting</label>
                <input type="text" name="ibu_putting" class="form-control-rounded" value="<?php echo htmlspecialchars($f['ibu_putting'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>Areola</label>
                <input type="text" name="ibu_areola" class="form-control-rounded" value="<?php echo htmlspecialchars($f['ibu_areola'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>Corpus Payudara</label>
                <input type="text" name="ibu_corpus_payudara" class="form-control-rounded" value="<?php echo htmlspecialchars($f['ibu_corpus_payudara'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>Lain-lain</label>
                <textarea name="ibu_payudara_lainnya" class="form-control-rounded" rows="2"><?php echo htmlspecialchars($f['ibu_payudara_lainnya'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">Formulir Pemeriksaan Fisik Bayi</h3>

            <div class="form-group">
                <label>1. Keadaan Umum</label>
                <input type="text" name="bayi_keadaan_umum" class="form-control-rounded" value="<?php echo htmlspecialchars($f['bayi_keadaan_umum'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>2. Suhu</label>
                <input type="text" name="bayi_suhu" class="form-control-rounded" value="<?php echo htmlspecialchars($f['bayi_suhu'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>3. Nadi</label>
                <input type="text" name="bayi_nadi" class="form-control-rounded" value="<?php echo htmlspecialchars($f['bayi_nadi'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>4. Pernapasan</label>
                <input type="text" name="bayi_pernapasan" class="form-control-rounded" value="<?php echo htmlspecialchars($f['bayi_pernapasan'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>5. Tonus otot</label>
                <input type="text" name="bayi_tonus_otot" class="form-control-rounded" value="<?php echo htmlspecialchars($f['bayi_tonus_otot'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>6. Kondisi bibir</label>
                <input type="text" name="bayi_kondisi_bibir" class="form-control-rounded" value="<?php echo htmlspecialchars($f['bayi_kondisi_bibir'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>7. Kondisi rongga mulut</label>
                <input type="text" name="bayi_kondisi_rongga_mulut" class="form-control-rounded" value="<?php echo htmlspecialchars($f['bayi_kondisi_rongga_mulut'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>8. Kondisi lidah</label>
                <input type="text" name="bayi_kondisi_lidah" class="form-control-rounded" value="<?php echo htmlspecialchars($f['bayi_kondisi_lidah'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>9. Reflek Rooting</label>
                <input type="text" name="bayi_reflek_rooting" class="form-control-rounded" value="<?php echo htmlspecialchars($f['bayi_reflek_rooting'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>10. Reflek Sucking</label>
                <input type="text" name="bayi_reflek_sucking" class="form-control-rounded" value="<?php echo htmlspecialchars($f['bayi_reflek_sucking'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>11. Reflek Suckling</label>
                <input type="text" name="bayi_reflek_suckling" class="form-control-rounded" value="<?php echo htmlspecialchars($f['bayi_reflek_suckling'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>12. Reflek Swallowing</label>
                <input type="text" name="bayi_reflek_swallowing" class="form-control-rounded" value="<?php echo htmlspecialchars($f['bayi_reflek_swallowing'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>13. Lain-Lain</label>
                <textarea name="bayi_lain_lain" class="form-control-rounded" rows="2"><?php echo htmlspecialchars($f['bayi_lain_lain'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?php echo baseUrl('pages/antrian.php'); ?>" class="btn btn-secondary-rounded">Batal</a>
            <button type="submit" class="btn btn-primary-rounded">
                <span class="btn-icon">S</span>
                Simpan Data Pemeriksaan Fisik
            </button>
        </div>
    </form>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/formulir_layout.php';
