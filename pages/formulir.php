<?php
/**
 * Formulir Page
 * Klinik Laktasi - Assessment Form
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireAuth();

$currentPage = 'antrian';
$breadcrumbTitle = 'ANTRIAN';
$pageTitle = 'Formulir Pengkajian';

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

ob_start();
?>

    <!-- Formulir Content -->
    <div class="form-section">
        <h3 class="section-title">PengisianFormulir</h3>
        
        <div class="formulir-grid">
            <a href="<?php echo baseUrl('pages/formulir/kajian-riwayat-menyusui.php?no_registrasi=' . urlencode($no_registrasi)); ?>" class="formulir-card">
                <span class="formulir-icon">📋</span>
                <h4>Kajian Riwayat Menyusui</h4>
                <p>Pengkajian riwayat menyusui ibu</p>
            </a>
            <a href="<?php echo baseUrl('pages/formulir/pemeriksaan_fisik.php?no_registrasi=' . urlencode($no_registrasi)); ?>" class="formulir-card">
                <span class="formulir-icon">👶</span>
                <h4>Pemeriksaan Fisik Ibu dan Bayi</h4>
                <p>Pemeriksaan fisik ibu dan bayi</p>
            </a>
            <a href="<?php echo baseUrl('pages/formulir/latch.php?no_registrasi=' . urlencode($no_registrasi)); ?>" class="formulir-card">
                <span class="formulir-icon">🤱</span>
                <h4>LATCH</h4>
                <p>Penilaian LATCH</p>
            </a>
            <a href="<?php echo baseUrl('pages/formulir/ibfat.php?no_registrasi=' . urlencode($no_registrasi)); ?>" class="formulir-card">
                <span class="formulir-icon">📊</span>
                <h4>IBFAT</h4>
                <p>Infant Breastfeeding Assessment Tool</p>
            </a>
            <a href="<?php echo baseUrl('pages/formulir/pibbs.php?no_registrasi=' . urlencode($no_registrasi)); ?>" class="formulir-card">
                <span class="formulir-icon">📈</span>
                <h4>PIBBS</h4>
                <p>Preterm Infant Breastfeeding Behaviour Scale</p>
            </a>
            <a href="<?php echo baseUrl('pages/formulir/bsessf.php?no_registrasi=' . urlencode($no_registrasi)); ?>" class="formulir-card">
                <span class="formulir-icon">💪</span>
                <h4>BSES-SF</h4>
                <p>Breastfeeding Self-Efficacy Scale</p>
            </a>
            <a href="<?php echo baseUrl('pages/formulir/hatlff.php?no_registrasi=' . urlencode($no_registrasi)); ?>" class="formulir-card">
                <span class="formulir-icon">👧🏽</span>
                <h4>HATLFF</h4>
                <p>Hazelbaker Assesment Tools for Lingual Frenulum Function</p>
            </a>
        </div>
    </div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/formulir_layout.php';
