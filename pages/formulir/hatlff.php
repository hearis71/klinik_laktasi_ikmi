<?php
/**
 * HATLFF Page
 * Klinik Laktasi - Hazelbaker Assessment Tool for Lingual Frenulum Function
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAuth();

$currentPage = 'antrian';
$breadcrumbTitle = 'ANTRIAN';
$pageTitle = 'Penilaian HATLFF';

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
$stmt = $pdo->prepare("SELECT * FROM hatlff WHERE no_registrasi = ?");
$stmt->execute([$no_registrasi]);
$existingData = $stmt->fetch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fungsi = ($_POST['gerak_samping'] ?? 0) +
              ($_POST['gerak_atas'] ?? 0) +
              ($_POST['gerak_memanjang'] ?? 0) +
              ($_POST['pelebaran_ujung'] ?? 0) +
              ($_POST['bentuk_mangkok'] ?? 0) +
              ($_POST['gerak_berirama'] ?? 0) +
              ($_POST['berdecak'] ?? 0);

    $penampilan = ($_POST['bentuk_lidah'] ?? 0) +
                  ($_POST['elastisitas'] ?? 0) +
                  ($_POST['panjang_frenulum'] ?? 0) +
                  ($_POST['perlekatan_lidah'] ?? 0) +
                  ($_POST['perlekatan_dasar'] ?? 0);

    $total = $fungsi + $penampilan;

    $data = [
        'gerak_samping' => sanitize($_POST['gerak_samping'] ?? 0),
        'gerak_atas' => sanitize($_POST['gerak_atas'] ?? 0),
        'gerak_memanjang' => sanitize($_POST['gerak_memanjang'] ?? 0),
        'pelebaran_ujung' => sanitize($_POST['pelebaran_ujung'] ?? 0),
        'bentuk_mangkok' => sanitize($_POST['bentuk_mangkok'] ?? 0),
        'gerak_berirama' => sanitize($_POST['gerak_berirama'] ?? 0),
        'berdecak' => sanitize($_POST['berdecak'] ?? 0),
        'bentuk_lidah' => sanitize($_POST['bentuk_lidah'] ?? 0),
        'elastisitas' => sanitize($_POST['elastisitas'] ?? 0),
        'panjang_frenulum' => sanitize($_POST['panjang_frenulum'] ?? 0),
        'perlekatan_lidah' => sanitize($_POST['perlekatan_lidah'] ?? 0),
        'perlekatan_dasar' => sanitize($_POST['perlekatan_dasar'] ?? 0),
        'skor_fungsi' => $fungsi,
        'skor_penampilan' => $penampilan,
        'skor_total' => $total,
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

            $sql = "UPDATE hatlff SET " . implode(', ', $columns) . " WHERE no_registrasi = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);

            redirect('pages/antrian.php', 'Data HATLFF berhasil diupdate', 'success');
        } else {
            // Insert
            $data['no_registrasi'] = $no_registrasi;
            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));

            $stmt = $pdo->prepare("
                INSERT INTO hatlff ($columns)
                VALUES ($placeholders)
            ");
            $stmt->execute(array_values($data));

            redirect('pages/antrian.php', 'Data HATLFF berhasil disimpan', 'success');
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

    <form method="POST" action="" class="form-grid" id="hatlffForm">
        <div class="form-section">
            <h3 class="section-title">Hazelbaker Assessment Tool for Lingual Frenulum Function (HATLFF)</h3>

            <div class="alert alert-info">
                <strong>Petunjuk:</strong> Pilih nilai untuk setiap aspek (0, 1, atau 2). Skor akan dihitung otomatis.
            </div>

            <h4 class="subsection-title">ASPEK FUNGSIONAL</h4>

            <?php
            $fungsi = [
                'gerak_samping' => 'Gerak lidah ke samping',
                'gerak_atas' => 'Gerak lidah ke atas',
                'gerak_memanjang' => 'Gerak lidah memanjang',
                'pelebaran_ujung' => 'Pelebaran ujung lidah',
                'bentuk_mangkok' => 'Gerak lidah membentuk mangkok',
                'gerak_berirama' => 'Gerak lidah berirama',
                'berdecak' => 'Berdecak'
            ];

            foreach ($fungsi as $name => $label):
                $savedValue = $f[$name] ?? '';
            ?>
            <div class="form-group">
                <label><?php echo htmlspecialchars($label); ?></label>
                <select name="<?php echo $name; ?>" class="form-control-rounded skor-fungsi" data-field="<?php echo $name; ?>">
                    <option value="">Pilih</option>
                    <option value="2" <?php echo $savedValue === '2' ? 'selected' : ''; ?>>2 - Normal</option>
                    <option value="1" <?php echo $savedValue === '1' ? 'selected' : ''; ?>>1 - Moderat</option>
                    <option value="0" <?php echo $savedValue === '0' ? 'selected' : ''; ?>>0 - Abnormal</option>
                </select>
            </div>
            <?php endforeach; ?>

            <div class="score-summary-inline">
                <div class="score-item-inline">
                    <label>Total Skor Fungsi</label>
                    <h4 id="total_fungsi">0</h4>
                </div>
            </div>

            <h4 class="subsection-title">ASPEK PENAMPILAN</h4>

            <?php
            $penampilan = [
                'bentuk_lidah' => 'Bentuk lidah ketika terangkat',
                'elastisitas' => 'Elastisitas frenulum',
                'panjang_frenulum' => 'Panjang frenulum',
                'perlekatan_lidah' => 'Perlekatan frenulum pada lidah',
                'perlekatan_dasar' => 'Perlekatan frenulum pada dasar mulut / gusi'
            ];

            foreach ($penampilan as $name => $label):
                $savedValue = $f[$name] ?? '';
            ?>
            <div class="form-group">
                <label><?php echo htmlspecialchars($label); ?></label>
                <select name="<?php echo $name; ?>" class="form-control-rounded skor-penampilan" data-field="<?php echo $name; ?>">
                    <option value="">Pilih</option>
                    <option value="2" <?php echo $savedValue === '2' ? 'selected' : ''; ?>>2 - Normal</option>
                    <option value="1" <?php echo $savedValue === '1' ? 'selected' : ''; ?>>1 - Moderat</option>
                    <option value="0" <?php echo $savedValue === '0' ? 'selected' : ''; ?>>0 - Abnormal</option>
                </select>
            </div>
            <?php endforeach; ?>

            <div class="score-summary-inline">
                <div class="score-item-inline">
                    <label>Total Skor Penampilan</label>
                    <h4 id="total_penampilan">0</h4>
                </div>
                <div class="score-item-inline">
                    <label>Total Skor Gabungan</label>
                    <h4 id="total_all">0</h4>
                </div>
            </div>

            <div class="form-group">
                <label>Interpretasi</label>
                <div id="interpretasi" class="interpretasi-box">
                    <p id="interpretasi_text">-</p>
                </div>
                <input type="hidden" name="interpretasi" id="interpretasi_input" value="" />
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Simpan Data HATLFF
            </button>
            <a href="<?php echo baseUrl('pages/antrian.php'); ?>" class="btn btn-secondary">
                Batal
            </a>
        </div>
    </form>

    <style>
        .subsection-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #3B82F6;
            margin: 24px 0 16px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #e9ecef;
        }

        .alert-info {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            color: #0056b3;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .score-summary-inline {
            display: flex;
            gap: 20px;
            background: #f8f9fa;
            padding: 16px 20px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            margin: 16px 0;
        }

        .score-item-inline {
            flex: 1;
        }

        .score-item-inline label {
            font-weight: 600;
            color: #495057;
            font-size: 0.875rem;
            display: block;
            margin-bottom: 8px;
        }

        .score-item-inline h4 {
            margin: 0;
            color: #3B82F6;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .interpretasi-box {
            background: #fff;
            padding: 16px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            min-height: 60px;
        }

        .interpretasi-box p {
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .interpretasi-box.text-danger {
            border-color: #DC3545;
            background: #fff5f5;
        }

        .interpretasi-box.text-danger p {
            color: #DC3545;
            font-weight: 600;
        }

        .interpretasi-box.text-warning {
            border-color: #FFC107;
            background: #fffdf5;
        }

        .interpretasi-box.text-warning p {
            color: #B78900;
            font-weight: 600;
        }

        .interpretasi-box.text-success {
            border-color: #28A745;
            background: #f5fff5;
        }

        .interpretasi-box.text-success p {
            color: #28A745;
            font-weight: 600;
        }
    </style>

    <script>
        function hitungSkor() {
            let fungsi = 0;
            let penampilan = 0;

            document.querySelectorAll('.skor-fungsi').forEach(el => {
                if (el.value !== "") {
                    fungsi += parseInt(el.value);
                }
            });

            document.querySelectorAll('.skor-penampilan').forEach(el => {
                if (el.value !== "") {
                    penampilan += parseInt(el.value);
                }
            });

            let total = fungsi + penampilan;

            document.getElementById('total_fungsi').innerText = fungsi;
            document.getElementById('total_penampilan').innerText = penampilan;
            document.getElementById('total_all').innerText = total;

            let hasil = "";
            let warna = "";

            if (fungsi === 14) {
                hasil = "Fungsi frenulum sempurna, tidak memerlukan frenotomi";
                warna = "text-success";
            } else if (fungsi >= 11 && fungsi <= 13 && penampilan >= 10) {
                hasil = "Skor masih dapat ditoleransi";
                warna = "text-warning";
            } else if (fungsi < 11) {
                hasil = "Mengindikasikan kebutuhan frenotomi apabila manajemen laktasi tidak berhasil";
                warna = "text-danger";
            } else {
                hasil = "Penilaian belum lengkap";
                warna = "";
            }

            if (penampilan < 8 && penampilan > 0) {
                hasil += "<br><strong>Catatan:</strong> Skor penampilan mendukung diagnosis ankyloglossia";
            }

            let interpretasiEl = document.getElementById('interpretasi_text');
            interpretasiEl.innerHTML = hasil;
            
            let boxEl = document.getElementById('interpretasi');
            boxEl.className = 'interpretasi-box ' + warna;
            
            document.getElementById('interpretasi_input').value = hasil.replace(/<br>/g, ' ');
        }

        document.querySelectorAll('select').forEach(el => {
            el.addEventListener('change', hitungSkor);
        });

        // Initialize on page load if values exist
        document.addEventListener('DOMContentLoaded', function() {
            hitungSkor();
        });
    </script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/formulir_layout.php';
