<?php
/**
 * IBFAT Page
 * Klinik Laktasi - Infant Breastfeeding Assessment Tool Form
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAuth();

$currentPage = 'antrian';
$breadcrumbTitle = 'ANTRIAN';
$pageTitle = 'Penilaian IBFAT';

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

// Check if data already exists
$stmt = $pdo->prepare("SELECT * FROM ibfat WHERE no_registrasi = ?");
$stmt->execute([$no_registrasi]);
$existingData = $stmt->fetch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'posture_score' => sanitize($_POST['posture_score'] ?? ''),
        'attachment_score' => sanitize($_POST['attachment_score'] ?? ''),
        'swallowing_score' => sanitize($_POST['swallowing_score'] ?? ''),
        'positioning_score' => sanitize($_POST['positioning_score'] ?? ''),
        'total_score' => sanitize($_POST['total_score'] ?? 0),
        'interpretasi' => sanitize($_POST['interpretasi'] ?? ''),
        'catatan' => sanitize($_POST['catatan'] ?? ''),
    ];

    try {
        if ($existingData) {
            // Update
            $columns = [];
            $values = [];
            foreach ($data as $key => $value) {
                $columns[] = "$key = ?";
                $values[] = $value;
            }
            $values[] = $no_registrasi;

            $sql = "UPDATE ibfat SET " . implode(', ', $columns) . " WHERE no_registrasi = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);

            redirect('pages/antrian.php', 'Data IBFAT berhasil diupdate', 'success');
        } else {
            // Insert
            $data['no_registrasi'] = $no_registrasi;
            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));

            $stmt = $pdo->prepare("
                INSERT INTO ibfat ($columns)
                VALUES ($placeholders)
            ");
            $stmt->execute(array_values($data));

            redirect('pages/antrian.php', 'Data IBFAT berhasil disimpan', 'success');
        }
    } catch (PDOException $e) {
        $error = 'Gagal menyimpan data: ' . $e->getMessage();
    }
}

// Pre-fill form with existing data
$f = $existingData ?: [];

ob_start();
?>

    <!-- Header Data Ibu & Bayi -->
    <?php include __DIR__ . '/formulir_header.php'; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="form-grid" id="ibfatForm">
        <div class="form-section">
            <h3 class="section-title">Infant Breastfeeding Assessment Tool (IBFAT)</h3>

            <div class="alert alert-info">
                <strong>Petunjuk:</strong> Pilih nilai untuk setiap aspek penilaian. Total skor akan dihitung otomatis.
                <br/>Skor maksimal: 16 (4 aspek x 4 poin)
            </div>

            <div class="form-group">
                <label>1. Posture (Postur Tubuh Bayi)</label>
                <select name="posture_score" class="form-control-rounded skor" data-field="posture">
                    <option value="">Pilih</option>
                    <option value="1" <?php echo ($f['posture_score'] ?? '') === '1' ? 'selected' : ''; ?>>1 - Tubuh bayi tegang/tidak rileks</option>
                    <option value="2" <?php echo ($f['posture_score'] ?? '') === '2' ? 'selected' : ''; ?>>2 - Tubuh bayi cukup rileks</option>
                    <option value="3" <?php echo ($f['posture_score'] ?? '') === '3' ? 'selected' : ''; ?>>3 - Tubuh bayi rileks</option>
                    <option value="4" <?php echo ($f['posture_score'] ?? '') === '4' ? 'selected' : ''; ?>>4 - Tubuh bayi sangat rileks, lengan dan tangan terbuka</option>
                </select>
            </div>

            <div class="form-group">
                <label>2. Attachment (Perlekatan)</label>
                <select name="attachment_score" class="form-control-rounded skor" data-field="attachment">
                    <option value="">Pilih</option>
                    <option value="1" <?php echo ($f['attachment_score'] ?? '') === '1' ? 'selected' : ''; ?>>1 - Dagu tidak menempel pada payudara</option>
                    <option value="2" <?php echo ($f['attachment_score'] ?? '') === '2' ? 'selected' : ''; ?>>2 - Dagu menempel sebagian</option>
                    <option value="3" <?php echo ($f['attachment_score'] ?? '') === '3' ? 'selected' : ''; ?>>3 - Dagu menempel pada payudara</option>
                    <option value="4" <?php echo ($f['attachment_score'] ?? '') === '4' ? 'selected' : ''; ?>>4 - Dagu menempel, mulut terbuka lebar, areola terlihat lebih banyak di atas</option>
                </select>
            </div>

            <div class="form-group">
                <label>3. Swallowing (Menelan)</label>
                <select name="swallowing_score" class="form-control-rounded skor" data-field="swallowing">
                    <option value="">Pilih</option>
                    <option value="1" <?php echo ($f['swallowing_score'] ?? '') === '1' ? 'selected' : ''; ?>>1 - Tidak ada gerakan menelan</option>
                    <option value="2" <?php echo ($f['swallowing_score'] ?? '') === '2' ? 'selected' : ''; ?>>2 - Gerakan menelan jarang</option>
                    <option value="3" <?php echo ($f['swallowing_score'] ?? '') === '3' ? 'selected' : ''; ?>>3 - Gerakan menelan teratur</option>
                    <option value="4" <?php echo ($f['swallowing_score'] ?? '') === '4' ? 'selected' : ''; ?>>4 - Gerakan menelan sering dan terdengar</option>
                </select>
            </div>

            <div class="form-group">
                <label>4. Positioning (Posisi)</label>
                <select name="positioning_score" class="form-control-rounded skor" data-field="positioning">
                    <option value="">Pilih</option>
                    <option value="1" <?php echo ($f['positioning_score'] ?? '') === '1' ? 'selected' : ''; ?>>1 - Posisi bayi tidak tepat, perlu bantuan penuh</option>
                    <option value="2" <?php echo ($f['positioning_score'] ?? '') === '2' ? 'selected' : ''; ?>>2 - Posisi bayi cukup tepat, perlu bantuan sebagian</option>
                    <option value="3" <?php echo ($f['positioning_score'] ?? '') === '3' ? 'selected' : ''; ?>>3 - Posisi bayi tepat</option>
                    <option value="4" <?php echo ($f['positioning_score'] ?? '') === '4' ? 'selected' : ''; ?>>4 - Posisi bayi sangat tepat, perut bayi menempel pada perut ibu</option>
                </select>
            </div>

            <div class="form-group">
                <label>Total Skor</label>
                <div class="score-display">
                    <h3 id="total_skor" class="mb-0">0</h3>
                </div>
                <input type="hidden" name="total_score" id="total_score_input" value="<?php echo htmlspecialchars($f['total_score'] ?? 0); ?>" />
            </div>

            <div class="form-group">
                <label>Interpretasi</label>
                <div class="score-display">
                    <h4 id="interpretasi" class="mb-0">-</h4>
                </div>
                <input type="hidden" name="interpretasi" id="interpretasi_input" value="<?php echo htmlspecialchars($f['interpretasi'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>Catatan Tambahan</label>
                <textarea name="catatan" class="form-control-rounded" rows="3" placeholder="Catatan hasil penilaian..."><?php echo htmlspecialchars($f['catatan'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Simpan Data IBFAT
            </button>
            <a href="<?php echo baseUrl('pages/antrian.php'); ?>" class="btn btn-secondary">
                Batal
            </a>
        </div>
    </form>

    <style>
        .score-display {
            background: #f8f9fa;
            padding: 15px 20px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            margin-top: 8px;
        }

        #total_skor {
            color: #3B82F6;
            font-size: 2rem;
            font-weight: 700;
        }

        #interpretasi {
            font-size: 1.25rem;
            font-weight: 600;
        }

        #interpretasi.text-danger {
            color: #DC3545;
        }

        #interpretasi.text-warning {
            color: #FFC107;
        }

        #interpretasi.text-success {
            color: #28A745;
        }

        .alert-info {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            color: #0056b3;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
    </style>

    <script>
        function hitungSkor() {
            let total = 0;

            document.querySelectorAll(".skor").forEach(function(el) {
                if (el.value !== "") {
                    total += parseInt(el.value);
                }
            });

            let interpretasi = "";
            let warna = "";

            // IBFAT interpretation: Score 14-16 = Baik, 10-13 = Cukup, <10 = Kurang
            if (total < 10) {
                interpretasi = "KURANG - Perlu perbaikan teknik menyusui";
                warna = "text-danger";
            } else if (total < 14) {
                interpretasi = "CUKUP - Teknik menyusui cukup baik";
                warna = "text-warning";
            } else {
                interpretasi = "BAIK - Teknik menyusui sudah optimal";
                warna = "text-success";
            }

            document.getElementById("total_skor").innerText = total;
            document.getElementById("total_score_input").value = total;

            let hasil = document.getElementById("interpretasi");
            hasil.innerText = interpretasi;
            hasil.className = warna;
            document.getElementById("interpretasi_input").value = interpretasi;
        }

        document.querySelectorAll(".skor").forEach(function(el) {
            el.addEventListener("change", hitungSkor);
        });

        // Initialize on page load if values exist
        document.addEventListener('DOMContentLoaded', function() {
            hitungSkor();
        });
    </script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/formulir_layout.php';
