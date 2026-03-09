<?php
/**
 * PIBBS Page
 * Klinik Laktasi - Preterm Infant Breastfeeding Behaviour Scale
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAuth();

$currentPage = 'antrian';
$breadcrumbTitle = 'ANTRIAN';
$pageTitle = 'Penilaian PIBBS';

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
$stmt = $pdo->prepare("SELECT * FROM pibbs WHERE no_registrasi = ?");
$stmt->execute([$no_registrasi]);
$existingData = $stmt->fetch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'mencari_puting_score' => sanitize($_POST['mencari_puting_score'] ?? ''),
        'cakupan_areola_score' => sanitize($_POST['cakupan_areola_score'] ?? ''),
        'menempel_melekat_score' => sanitize($_POST['menempel_melekat_score'] ?? ''),
        'menghisap_score' => sanitize($_POST['menghisap_score'] ?? ''),
        'menghisap_terpanjang_score' => sanitize($_POST['menghisap_terpanjang_score'] ?? ''),
        'menelan_score' => sanitize($_POST['menelan_score'] ?? ''),
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

            $sql = "UPDATE pibbs SET " . implode(', ', $columns) . " WHERE no_registrasi = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);

            redirect('pages/antrian.php', 'Data PIBBS berhasil diupdate', 'success');
        } else {
            // Insert
            $data['no_registrasi'] = $no_registrasi;
            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));

            $stmt = $pdo->prepare("
                INSERT INTO pibbs ($columns)
                VALUES ($placeholders)
            ");
            $stmt->execute(array_values($data));

            redirect('pages/antrian.php', 'Data PIBBS berhasil disimpan', 'success');
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

    <form method="POST" action="" class="form-grid" id="pibbsForm">
        <div class="form-section">
            <h3 class="section-title">Preterm Infant Breastfeeding Behaviour Scale (PIBBS)</h3>

            <div class="alert alert-info">
                <strong>Petunjuk Penilaian:</strong>
                <ul class="mb-0" style="margin-top: 8px;">
                    <li>Skor <b>tidak dijumlahkan</b>.</li>
                    <li>Penilaian dilakukan <b>pada setiap item secara individual</b>.</li>
                    <li><b>Semakin tinggi nilai</b> pada suatu item menunjukkan kemampuan menyusu bayi yang <b>semakin baik</b>.</li>
                    <li>Perkembangan menyusu bayi dinilai dari <b>perubahan skor tiap item pada kunjungan berikutnya</b>.</li>
                </ul>
            </div>

            <div class="table-responsive">
                <table class="form-table">
                    <thead>
                        <tr>
                            <th width="25%">Perilaku</th>
                            <th width="55%">Tahapan Perkembangan</th>
                            <th width="20%">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><b>Mencari puting susu</b></td>
                            <td>
                                <select name="mencari_puting_score" class="form-control-rounded pibbs" data-field="mencari_puting">
                                    <option value="">Pilih</option>
                                    <option value="0" <?php echo ($f['mencari_puting_score'] ?? '') === '0' ? 'selected' : ''; ?>>Tidak mencari puting susu</option>
                                    <option value="1" <?php echo ($f['mencari_puting_score'] ?? '') === '1' ? 'selected' : ''; ?>>Menunjukkan beberapa tindakan mencari puting</option>
                                    <option value="2" <?php echo ($f['mencari_puting_score'] ?? '') === '2' ? 'selected' : ''; ?>>Menunjukkan tindakan nyata mencari puting</option>
                                </select>
                            </td>
                            <td class="text-center"><span class="badge-score" id="badge_mencari_puting">-</span></td>
                        </tr>

                        <tr>
                            <td><b>Cakupan areola</b></td>
                            <td>
                                <select name="cakupan_areola_score" class="form-control-rounded pibbs" data-field="cakupan_areola">
                                    <option value="">Pilih</option>
                                    <option value="0" <?php echo ($f['cakupan_areola_score'] ?? '') === '0' ? 'selected' : ''; ?>>Mulut bayi hanya menyentuh puting</option>
                                    <option value="1" <?php echo ($f['cakupan_areola_score'] ?? '') === '1' ? 'selected' : ''; ?>>Sebagian dari puting</option>
                                    <option value="2" <?php echo ($f['cakupan_areola_score'] ?? '') === '2' ? 'selected' : ''; ?>>Seluruh puting tanpa areola</option>
                                    <option value="3" <?php echo ($f['cakupan_areola_score'] ?? '') === '3' ? 'selected' : ''; ?>>Puting dan sebagian areola</option>
                                </select>
                            </td>
                            <td class="text-center"><span class="badge-score" id="badge_cakupan_areola">-</span></td>
                        </tr>

                        <tr>
                            <td><b>Menempel dan melekat</b></td>
                            <td>
                                <select name="menempel_melekat_score" class="form-control-rounded pibbs" data-field="menempel_melekat">
                                    <option value="">Pilih</option>
                                    <option value="1" <?php echo ($f['menempel_melekat_score'] ?? '') === '1' ? 'selected' : ''; ?>>Tidak melekat</option>
                                    <option value="2" <?php echo ($f['menempel_melekat_score'] ?? '') === '2' ? 'selected' : ''; ?>>Melekat ≤ 5 menit</option>
                                    <option value="3" <?php echo ($f['menempel_melekat_score'] ?? '') === '3' ? 'selected' : ''; ?>>Melekat 6-10 menit</option>
                                    <option value="4" <?php echo ($f['menempel_melekat_score'] ?? '') === '4' ? 'selected' : ''; ?>>Melekat ≥ 11-15 menit</option>
                                </select>
                            </td>
                            <td class="text-center"><span class="badge-score" id="badge_menempel_melekat">-</span></td>
                        </tr>

                        <tr>
                            <td><b>Menghisap</b></td>
                            <td>
                                <select name="menghisap_score" class="form-control-rounded pibbs" data-field="menghisap">
                                    <option value="">Pilih</option>
                                    <option value="0" <?php echo ($f['menghisap_score'] ?? '') === '0' ? 'selected' : ''; ?>>Tidak menghisap</option>
                                    <option value="1" <?php echo ($f['menghisap_score'] ?? '') === '1' ? 'selected' : ''; ?>>Menjilat tanpa menghisap</option>
                                    <option value="2" <?php echo ($f['menghisap_score'] ?? '') === '2' ? 'selected' : ''; ?>>Hisapan pendek 2-9 kali</option>
                                    <option value="3" <?php echo ($f['menghisap_score'] ?? '') === '3' ? 'selected' : ''; ?>>Hisapan pendek berulang</option>
                                    <option value="4" <?php echo ($f['menghisap_score'] ?? '') === '4' ? 'selected' : ''; ?>>Hisapan panjang berulang</option>
                                </select>
                            </td>
                            <td class="text-center"><span class="badge-score" id="badge_menghisap">-</span></td>
                        </tr>

                        <tr>
                            <td><b>Menghisap terpanjang</b></td>
                            <td>
                                <select name="menghisap_terpanjang_score" class="form-control-rounded pibbs" data-field="menghisap_terpanjang">
                                    <option value="">Pilih</option>
                                    <option value="1" <?php echo ($f['menghisap_terpanjang_score'] ?? '') === '1' ? 'selected' : ''; ?>>1-5 hisapan</option>
                                    <option value="2" <?php echo ($f['menghisap_terpanjang_score'] ?? '') === '2' ? 'selected' : ''; ?>>6-10 hisapan</option>
                                    <option value="3" <?php echo ($f['menghisap_terpanjang_score'] ?? '') === '3' ? 'selected' : ''; ?>>11-15 hisapan</option>
                                    <option value="4" <?php echo ($f['menghisap_terpanjang_score'] ?? '') === '4' ? 'selected' : ''; ?>>16-20 hisapan</option>
                                    <option value="5" <?php echo ($f['menghisap_terpanjang_score'] ?? '') === '5' ? 'selected' : ''; ?>>21-25 hisapan</option>
                                    <option value="6" <?php echo ($f['menghisap_terpanjang_score'] ?? '') === '6' ? 'selected' : ''; ?>>≥ 26 hisapan</option>
                                </select>
                            </td>
                            <td class="text-center"><span class="badge-score" id="badge_menghisap_terpanjang">-</span></td>
                        </tr>

                        <tr>
                            <td><b>Menelan</b></td>
                            <td>
                                <select name="menelan_score" class="form-control-rounded pibbs" data-field="menelan">
                                    <option value="">Pilih</option>
                                    <option value="0" <?php echo ($f['menelan_score'] ?? '') === '0' ? 'selected' : ''; ?>>Menelan tidak diketahui</option>
                                    <option value="1" <?php echo ($f['menelan_score'] ?? '') === '1' ? 'selected' : ''; ?>>Menelan sesekali</option>
                                    <option value="2" <?php echo ($f['menelan_score'] ?? '') === '2' ? 'selected' : ''; ?>>Menelan berkali-kali</option>
                                </select>
                            </td>
                            <td class="text-center"><span class="badge-score" id="badge_menelan">-</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="form-group" style="margin-top: 20px;">
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
                Simpan Data PIBBS
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

        .badge-score {
            display: inline-block;
            background: #3B82F6;
            color: #fff;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            min-width: 24px;
            text-align: center;
        }

        .alert-info {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            color: #0056b3;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert-info ul {
            padding-left: 20px;
        }

        .text-center {
            text-align: center;
        }
    </style>

    <script>
        document.querySelectorAll(".pibbs").forEach(function(select) {
            select.addEventListener("change", function() {
                let field = this.getAttribute('data-field');
                let badgeCell = document.getElementById('badge_' + field);

                if (this.value === "") {
                    badgeCell.innerText = "-";
                } else {
                    badgeCell.innerText = this.value;
                }
            });
        });

        // Initialize badges on page load if values exist
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll(".pibbs").forEach(function(select) {
                if (select.value !== "") {
                    let field = select.getAttribute('data-field');
                    let badgeCell = document.getElementById('badge_' + field);
                    badgeCell.innerText = select.value;
                }
            });
        });
    </script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/formulir_layout.php';
