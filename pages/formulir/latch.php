<?php
/**
 * LATCH Page
 * Klinik Laktasi - LATCH Scoring Assessment Form
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAuth();

$currentPage = 'antrian';
$breadcrumbTitle = 'ANTRIAN';
$pageTitle = 'Penilaian LATCH';

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
$stmt = $pdo->prepare("SELECT * FROM latch WHERE no_registrasi = ?");
$stmt->execute([$no_registrasi]);
$existingData = $stmt->fetch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'latch_score' => sanitize($_POST['latch_score'] ?? ''),
        'audible_swallowing_score' => sanitize($_POST['audible_swallowing_score'] ?? ''),
        'type_of_nipple_score' => sanitize($_POST['type_of_nipple_score'] ?? ''),
        'comfort_score' => sanitize($_POST['comfort_score'] ?? ''),
        'hold_score' => sanitize($_POST['hold_score'] ?? ''),
        'total_score' => sanitize($_POST['total_score'] ?? 0),
        'interpretasi' => sanitize($_POST['interpretasi'] ?? ''),
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

            $sql = "UPDATE latch SET " . implode(', ', $columns) . " WHERE no_registrasi = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);

            redirect('pages/antrian.php', 'Data LATCH berhasil diupdate', 'success');
        } else {
            // Insert
            $data['no_registrasi'] = $no_registrasi;
            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));

            $stmt = $pdo->prepare("
                INSERT INTO latch ($columns)
                VALUES ($placeholders)
            ");
            $stmt->execute(array_values($data));

            redirect('pages/antrian.php', 'Data LATCH berhasil disimpan', 'success');
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
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="form-grid" id="latchForm">
        <div class="form-section">
            <h3 class="section-title">Penilaian LATCH</h3>

            <div class="alert alert-info">
                <strong>Petunjuk:</strong> Pilih nilai untuk setiap aspek penilaian. Total skor akan dihitung otomatis.
            </div>

            <div class="form-group">
                <label>1. Latch (Perlekatan)</label>
                <select name="latch_score" class="form-control-rounded skor" data-field="latch">
                    <option value="">Pilih</option>
                    <option value="0" <?php echo ($f['latch_score'] ?? '') === '0' ? 'selected' : ''; ?>>0 - Perlekatan buruk, daya isap lemah</option>
                    <option value="1" <?php echo ($f['latch_score'] ?? '') === '1' ? 'selected' : ''; ?>>1 - Perlu stimulasi untuk perlekatan</option>
                    <option value="2" <?php echo ($f['latch_score'] ?? '') === '2' ? 'selected' : ''; ?>>2 - Perlekatan baik, daya isap kuat dan ritmis</option>
                </select>
            </div>

            <div class="form-group">
                <label>2. Audible Swallowing (Bunyi Menelan)</label>
                <select name="audible_swallowing_score" class="form-control-rounded skor" data-field="audible_swallowing">
                    <option value="">Pilih</option>
                    <option value="0" <?php echo ($f['audible_swallowing_score'] ?? '') === '0' ? 'selected' : ''; ?>>0 - Tidak terdengar</option>
                    <option value="1" <?php echo ($f['audible_swallowing_score'] ?? '') === '1' ? 'selected' : ''; ?>>1 - Jarang terdengar</option>
                    <option value="2" <?php echo ($f['audible_swallowing_score'] ?? '') === '2' ? 'selected' : ''; ?>>2 - Terdengar sering dan teratur</option>
                </select>
            </div>

            <div class="form-group">
                <label>3. Type of Nipple (Tipe/Bentuk Puting)</label>
                <select name="type_of_nipple_score" class="form-control-rounded skor" data-field="type_of_nipple">
                    <option value="">Pilih</option>
                    <option value="0" <?php echo ($f['type_of_nipple_score'] ?? '') === '0' ? 'selected' : ''; ?>>0 - Terbenam</option>
                    <option value="1" <?php echo ($f['type_of_nipple_score'] ?? '') === '1' ? 'selected' : ''; ?>>1 - Datar</option>
                    <option value="2" <?php echo ($f['type_of_nipple_score'] ?? '') === '2' ? 'selected' : ''; ?>>2 - Normal</option>
                </select>
            </div>

            <div class="form-group">
                <label>4. Comfort (Tingkat Kenyamanan)</label>
                <select name="comfort_score" class="form-control-rounded skor" data-field="comfort">
                    <option value="">Pilih</option>
                    <option value="0" <?php echo ($f['comfort_score'] ?? '') === '0' ? 'selected' : ''; ?>>0 - Nyeri, puting retak, payudara bengkak</option>
                    <option value="1" <?php echo ($f['comfort_score'] ?? '') === '1' ? 'selected' : ''; ?>>1 - Payudara penuh, puting lecet, kemerahan</option>
                    <option value="2" <?php echo ($f['comfort_score'] ?? '') === '2' ? 'selected' : ''; ?>>2 - Tidak ada keluhan</option>
                </select>
            </div>

            <div class="form-group">
                <label>5. Hold (Kemampuan ibu menggendong)</label>
                <select name="hold_score" class="form-control-rounded skor" data-field="hold">
                    <option value="">Pilih</option>
                    <option value="0" <?php echo ($f['hold_score'] ?? '') === '0' ? 'selected' : ''; ?>>0 - Perlu dibantu sepenuhnya</option>
                    <option value="1" <?php echo ($f['hold_score'] ?? '') === '1' ? 'selected' : ''; ?>>1 - Perlu dibantu sebagian</option>
                    <option value="2" <?php echo ($f['hold_score'] ?? '') === '2' ? 'selected' : ''; ?>>2 - Tidak perlu dibantu</option>
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
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Simpan Data LATCH
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

            if (total <= 3) {
                interpretasi = "BURUK";
                warna = "text-danger";
            } else if (total <= 7) {
                interpretasi = "MODERAT";
                warna = "text-warning";
            } else {
                interpretasi = "BAIK";
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
