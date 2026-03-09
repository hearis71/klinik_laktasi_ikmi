<?php
/**
 * BSES-SF Page
 * Klinik Laktasi - Breastfeeding Self-Efficacy Scale - Short Form
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAuth();

$currentPage = 'antrian';
$breadcrumbTitle = 'ANTRIAN';
$pageTitle = 'Kuesioner BSES-SF';

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
$stmt = $pdo->prepare("SELECT * FROM bsessf WHERE no_registrasi = ?");
$stmt->execute([$no_registrasi]);
$existingData = $stmt->fetch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'pertanyaan_1' => sanitize($_POST['pertanyaan_1'] ?? ''),
        'pertanyaan_2' => sanitize($_POST['pertanyaan_2'] ?? ''),
        'pertanyaan_3' => sanitize($_POST['pertanyaan_3'] ?? ''),
        'pertanyaan_4' => sanitize($_POST['pertanyaan_4'] ?? ''),
        'pertanyaan_5' => sanitize($_POST['pertanyaan_5'] ?? ''),
        'pertanyaan_6' => sanitize($_POST['pertanyaan_6'] ?? ''),
        'pertanyaan_7' => sanitize($_POST['pertanyaan_7'] ?? ''),
        'pertanyaan_8' => sanitize($_POST['pertanyaan_8'] ?? ''),
        'pertanyaan_9' => sanitize($_POST['pertanyaan_9'] ?? ''),
        'pertanyaan_10' => sanitize($_POST['pertanyaan_10'] ?? ''),
        'pertanyaan_11' => sanitize($_POST['pertanyaan_11'] ?? ''),
        'pertanyaan_12' => sanitize($_POST['pertanyaan_12'] ?? ''),
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

            $sql = "UPDATE bsessf SET " . implode(', ', $columns) . " WHERE no_registrasi = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);

            redirect('pages/antrian.php', 'Data BSES-SF berhasil diupdate', 'success');
        } else {
            // Insert
            $data['no_registrasi'] = $no_registrasi;
            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));

            $stmt = $pdo->prepare("
                INSERT INTO bsessf ($columns)
                VALUES ($placeholders)
            ");
            $stmt->execute(array_values($data));

            redirect('pages/antrian.php', 'Data BSES-SF berhasil disimpan', 'success');
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

    <form method="POST" action="" class="form-grid" id="bsessfForm">
        <div class="form-section">
            <h3 class="section-title">Breastfeeding Self-Efficacy Scale - Short Form (BSES-SF)</h3>

            <div class="alert alert-info">
                <strong>Petunjuk:</strong> Pilih jawaban yang paling sesuai dengan perasaan Anda.
                <br/>Skor total akan dihitung otomatis setelah semua pertanyaan terjawab.
            </div>

            <div class="legend-box">
                <strong>Skala Penilaian:</strong>
                <div class="legend-items">
                    <span class="legend-item">STY: Sangat Tidak Yakin (1)</span>
                    <span class="legend-item">TY: Tidak Yakin (2)</span>
                    <span class="legend-item">KY: Kurang Yakin (3)</span>
                    <span class="legend-item">Y: Yakin (4)</span>
                    <span class="legend-item">SY: Sangat Yakin (5)</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="form-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="65%">Pernyataan</th>
                            <th width="30%">Jawaban</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $pertanyaan = [
                            "Saya merasa bahwa bayi saya mendapatkan cukup ASI.",
                            "Saya tetap dapat menyusui bayi saya walaupun banyak hal yang saya lakukan.",
                            "Saya memberikan ASI kepada bayi saya tanpa tambahan susu formula.",
                            "Saya memastikan bahwa bayi saya tidak mendapatkan makanan apapun selain ASI.",
                            "Saya mampu mengelola keadaan saat menyusui untuk kenyamanan saya.",
                            "Saya akan tetap menyusui bayi saya bahkan saat bayi saya menangis.",
                            "Saya tetap nyaman dalam menyusui saat ada anggota keluarga atau orang lain disekitar saya.",
                            "Saya puas dengan pengalaman menyusui saya.",
                            "Saya memberikan ASI kepada bayi saya dengan satu payudara sampai habis lalu beralih ke payudara sebelahnya.",
                            "Saya terus menyusui bayi saya untuk memberikan makanan.",
                            "Saya mampu memenuhi keinginan menyusui bayi saya.",
                            "Saya mengetahui tanda ketika bayi saya selesai menyusu."
                        ];

                        foreach ($pertanyaan as $i => $p):
                            $fieldName = 'pertanyaan_' . ($i + 1);
                            $savedValue = $f[$fieldName] ?? '';
                        ?>
                        <tr>
                            <td><?php echo ($i + 1); ?></td>
                            <td><?php echo htmlspecialchars($p); ?></td>
                            <td>
                                <select name="<?php echo $fieldName; ?>" class="form-control-rounded skor" data-field="<?php echo $fieldName; ?>">
                                    <option value="">Pilih</option>
                                    <option value="1" <?php echo $savedValue === '1' ? 'selected' : ''; ?>>STY</option>
                                    <option value="2" <?php echo $savedValue === '2' ? 'selected' : ''; ?>>TY</option>
                                    <option value="3" <?php echo $savedValue === '3' ? 'selected' : ''; ?>>KY</option>
                                    <option value="4" <?php echo $savedValue === '4' ? 'selected' : ''; ?>>Y</option>
                                    <option value="5" <?php echo $savedValue === '5' ? 'selected' : ''; ?>>SY</option>
                                </select>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="score-summary">
                <div class="score-item">
                    <label>Skor Total</label>
                    <h3 id="totalSkor">0</h3>
                    <input type="hidden" name="total_score" id="total_score_input" value="<?php echo htmlspecialchars($f['total_score'] ?? 0); ?>" />
                </div>
                <div class="score-item">
                    <label>Interpretasi</label>
                    <div id="interpretasi" class="interpretasi-box">
                        <h4 id="interpretasi_text">-</h4>
                    </div>
                    <input type="hidden" name="interpretasi" id="interpretasi_input" value="<?php echo htmlspecialchars($f['interpretasi'] ?? ''); ?>" />
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Simpan Data BSES-SF
            </button>
            <a href="<?php echo baseUrl('pages/antrian.php'); ?>" class="btn btn-secondary">
                Batal
            </a>
        </div>
    </form>

    <style>
        .form-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
        }

        .form-table thead tr {
            background: #f8f9fa;
        }

        .form-table th,
        .form-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .form-table th {
            font-weight: 600;
            color: #495057;
            font-size: 0.875rem;
        }

        .form-table tbody tr:last-child td {
            border-bottom: none;
        }

        .form-table tbody tr:hover {
            background: #f8f9fa;
        }

        .legend-box {
            background: #f8f9fa;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }

        .legend-items {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 8px;
        }

        .legend-item {
            background: #fff;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.875rem;
            border: 1px solid #dee2e6;
        }

        .alert-info {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            color: #0056b3;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .score-summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }

        .score-item label {
            font-weight: 600;
            color: #495057;
            font-size: 0.875rem;
            display: block;
            margin-bottom: 8px;
        }

        #totalSkor {
            color: #3B82F6;
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }

        .interpretasi-box {
            background: #fff;
            padding: 12px 16px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        #interpretasi_text {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
        }

        #interpretasi_text.text-danger {
            color: #DC3545;
        }

        #interpretasi_text.text-warning {
            color: #FFC107;
        }

        #interpretasi_text.text-success {
            color: #28A745;
        }
    </style>

    <script>
        const dropdowns = document.querySelectorAll(".skor");

        dropdowns.forEach(d => {
            d.addEventListener("change", hitungSkor);
        });

        function hitungSkor() {
            let total = 0;

            dropdowns.forEach(d => {
                if (d.value !== "") {
                    total += parseInt(d.value);
                }
            });

            document.getElementById("totalSkor").innerText = total;
            document.getElementById("total_score_input").value = total;

            let interpretasi = "";
            let warna = "";

            if (total < 12) {
                interpretasi = "Belum lengkap";
                warna = "text-muted";
            } else if (total <= 35) {
                interpretasi = "Self-efficacy menyusui RENDAH";
                warna = "text-danger";
            } else if (total <= 47) {
                interpretasi = "Self-efficacy menyusui SEDANG";
                warna = "text-warning";
            } else {
                interpretasi = "Self-efficacy menyusui TINGGI";
                warna = "text-success";
            }

            let interpretasiEl = document.getElementById("interpretasi_text");
            interpretasiEl.innerText = interpretasi;
            interpretasiEl.className = warna;
            document.getElementById("interpretasi_input").value = interpretasi;
        }

        // Initialize on page load if values exist
        document.addEventListener('DOMContentLoaded', function() {
            hitungSkor();
        });
    </script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/formulir_layout.php';
