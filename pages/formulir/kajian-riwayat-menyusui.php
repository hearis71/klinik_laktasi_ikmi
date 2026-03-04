<?php
/**
 * Kajian Riwayat Menyusui Page
 * Klinik Laktasi - Breastfeeding History Assessment Form
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAuth();

$currentPage = 'antrian';
$breadcrumbTitle = 'ANTRIAN';
$pageTitle = 'Kajian Riwayat Menyusui';

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
$stmt = $pdo->prepare("SELECT * FROM kajian_riwayat_menyusui WHERE no_registrasi = ?");
$stmt->execute([$no_registrasi]);
$existingData = $stmt->fetch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        // Pemberian Makan
        'pemberian_makan_asi' => isset($_POST['pemberian_makan_asi']) ? 1 : 0,
        'pemberian_makan_susu_lain' => isset($_POST['pemberian_makan_susu_lain']) ? 1 : 0,
        'keterangan_susu_lain' => sanitize($_POST['keterangan_susu_lain'] ?? ''),
        'frekuensi_menyusui' => sanitize($_POST['frekuensi_menyusui'] ?? ''),
        'keterangan_frekuensi_menyusui' => sanitize($_POST['keterangan_frekuensi_menyusui'] ?? ''),
        'lama_menyusui' => sanitize($_POST['lama_menyusui'] ?? ''),
        'menyusui' => sanitize($_POST['menyusui'] ?? ''),
        'menyusui_malam_hari' => sanitize($_POST['menyusui_malam_hari'] ?? ''),
        'jumlah_frekuensi_susu_lain' => sanitize($_POST['jumlah_frekuensi_susu_lain'] ?? ''),
        
        // Cairan Tambahan
        'cairan_tambahan_kapan_dimulai' => sanitize($_POST['cairan_tambahan_kapan_dimulai'] ?? ''),
        'cairan_tambahan_apa_yang_diberikan' => sanitize($_POST['cairan_tambahan_apa_yang_diberikan'] ?? ''),
        'cairan_tambahan_frekuensi_pemberian' => sanitize($_POST['cairan_tambahan_frekuensi_pemberian'] ?? ''),
        'cairan_tambahan_berapa_banyak' => sanitize($_POST['cairan_tambahan_berapa_banyak'] ?? ''),
        
        // Makanan Tambahan
        'makanan_tambahan_kapan_dimulai' => sanitize($_POST['makanan_tambahan_kapan_dimulai'] ?? ''),
        'makanan_tambahan_apa_yang_diberikan' => sanitize($_POST['makanan_tambahan_apa_yang_diberikan'] ?? ''),
        'makanan_tambahan_frekuensi_pemberian' => sanitize($_POST['makanan_tambahan_frekuensi_pemberian'] ?? ''),
        'makanan_tambahan_berapa_banyak' => sanitize($_POST['makanan_tambahan_berapa_banyak'] ?? ''),
        
        // Botol
        'menggunakan_botol' => sanitize($_POST['menggunakan_botol'] ?? ''),
        'keterangan_botol' => sanitize($_POST['keterangan_botol'] ?? ''),
        
        // Kesehatan Bayi
        'bb_saat_lahir' => sanitize($_POST['bb_saat_lahir'] ?? null),
        'bb_saat_ini' => sanitize($_POST['bb_saat_ini'] ?? null),
        'jenis_kelahiran_prematur' => isset($_POST['jenis_kelahiran_prematur']) ? 1 : 0,
        'jenis_kelahiran_kembar' => isset($_POST['jenis_kelahiran_kembar']) ? 1 : 0,
        'frekuensi_bak' => sanitize($_POST['frekuensi_bak'] ?? ''),
        'warna_bak' => sanitize($_POST['warna_bak'] ?? ''),
        'frekuensi_bab' => sanitize($_POST['frekuensi_bab'] ?? ''),
        'kosistensi_bab' => sanitize($_POST['kosistensi_bab'] ?? ''),
        'perilaku_makan' => sanitize($_POST['perilaku_makan'] ?? ''),
        'perilaku_tidur' => sanitize($_POST['perilaku_tidur'] ?? ''),
        'perilaku_menangis' => sanitize($_POST['perilaku_menangis'] ?? ''),
        'riwayat_sakit' => sanitize($_POST['riwayat_sakit'] ?? ''),
        'kelainan_bawaan' => sanitize($_POST['kelainan_bawaan'] ?? ''),
        
        // Kehamilan & Kelahiran
        'tempat_anc_rs' => isset($_POST['tempat_anc_rs']) ? 1 : 0,
        'tempat_anc_tpmb' => isset($_POST['tempat_anc_tpmb']) ? 1 : 0,
        'tempat_anc_puskesmas' => isset($_POST['tempat_anc_puskesmas']) ? 1 : 0,
        'tempat_anc_polindes' => isset($_POST['tempat_anc_polindes']) ? 1 : 0,
        'tempat_anc_lainnya' => isset($_POST['tempat_anc_lainnya']) ? 1 : 0,
        'tempat_anc_keterangan_lainnya' => sanitize($_POST['tempat_anc_keterangan_lainnya'] ?? ''),
        'yang_melakukan_anc_dokter_kandungan' => isset($_POST['yang_melakukan_anc_dokter_kandungan']) ? 1 : 0,
        'yang_melakukan_anc_bidan' => isset($_POST['yang_melakukan_anc_bidan']) ? 1 : 0,
        'yang_melakukan_anc_dokter_umum' => isset($_POST['yang_melakukan_anc_dokter_umum']) ? 1 : 0,
        'yang_melakukan_anc_lainnya' => isset($_POST['yang_melakukan_anc_lainnya']) ? 1 : 0,
        'yang_melakukan_anc_keterangan_lainnya' => sanitize($_POST['yang_melakukan_anc_keterangan_lainnya'] ?? ''),
        'diskusi_pemberian_makan' => sanitize($_POST['diskusi_pemberian_makan'] ?? ''),
        'jenis_persalinan' => sanitize($_POST['jenis_persalinan'] ?? ''),
        'riwayat_imd' => sanitize($_POST['riwayat_imd'] ?? ''),
        'menyusui_1_jam' => sanitize($_POST['menyusui_1_jam'] ?? ''),
        'rawat_gabung' => sanitize($_POST['rawat_gabung'] ?? ''),
        'pemberian_prelaktal' => sanitize($_POST['pemberian_prelaktal'] ?? ''),
        'bantuan_menyusui' => sanitize($_POST['bantuan_menyusui'] ?? ''),
        'menerima_sample_formula' => sanitize($_POST['menerima_sample_formula'] ?? ''),
        
        // Kondisi Ibu
        'riwayat_kesehatan_ibu' => sanitize($_POST['riwayat_kesehatan_ibu'] ?? ''),
        'obat_dikonsumsi' => sanitize($_POST['obat_dikonsumsi'] ?? ''),
        'gizi_ibu' => sanitize($_POST['gizi_ibu'] ?? ''),
        'kebiasaan_kopi' => sanitize($_POST['kebiasaan_kopi'] ?? ''),
        'minuman_alkohol' => sanitize($_POST['minuman_alkohol'] ?? ''),
        'kebiasaan_merokok' => sanitize($_POST['kebiasaan_merokok'] ?? ''),
        'narkoba' => sanitize($_POST['narkoba'] ?? ''),
        'kondisi_payudara' => sanitize($_POST['kondisi_payudara'] ?? ''),
        'penggunaan_kb' => sanitize($_POST['penggunaan_kb'] ?? ''),
        'keterangan_kb' => sanitize($_POST['keterangan_kb'] ?? ''),
        'motivasi_menyusui' => sanitize($_POST['motivasi_menyusui'] ?? ''),
        
        // Pengalaman Sebelumnya
        'jumlah_anak_sebelumnya' => sanitize($_POST['jumlah_anak_sebelumnya'] ?? null),
        'jumlah_anak_disusui' => sanitize($_POST['jumlah_anak_disusui'] ?? null),
        'riwayat_asi_eksklusif' => sanitize($_POST['riwayat_asi_eksklusif'] ?? ''),
        'menggunakan_botol_sebelumnya' => sanitize($_POST['menggunakan_botol_sebelumnya'] ?? ''),
        'alasan_botol' => sanitize($_POST['alasan_botol'] ?? ''),
        
        // Situasi Keluarga
        'pekerjaan_orang_tua' => sanitize($_POST['pekerjaan_orang_tua'] ?? ''),
        'keadaan_ekonomi' => sanitize($_POST['keadaan_ekonomi'] ?? ''),
        'pendidikan_orang_tua' => sanitize($_POST['pendidikan_orang_tua'] ?? ''),
        'sikap_keluarga' => sanitize($_POST['sikap_keluarga'] ?? ''),
        'bantuan_perawatan_anak' => sanitize($_POST['bantuan_perawatan_anak'] ?? ''),
        
        // KMS
        'pertumbuhan_sesuai_kurva' => sanitize($_POST['pertumbuhan_sesuai_kurva'] ?? ''),
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
            
            $sql = "UPDATE kajian_riwayat_menyusui SET " . implode(', ', $columns) . " WHERE no_registrasi = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);

            redirect('pages/antrian.php', 'Data kajian berhasil diupdate', 'success');
        } else {
            // Insert
            $data['no_registrasi'] = $no_registrasi;
            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            
            $stmt = $pdo->prepare("
                INSERT INTO kajian_riwayat_menyusui ($columns)
                VALUES ($placeholders)
            ");
            $stmt->execute(array_values($data));

            redirect('pages/antrian.php', 'Data kajian berhasil disimpan', 'success');
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

    <form method="POST" action="" class="form-grid">
        <!-- Pemberian Makan Bayi Sekarang -->
        <div class="form-section">
            <h3 class="section-title">Pemberian Makan Bayi Sekarang</h3>

            <div class="form-group">
                <label>1. Pemberian Makan</label>
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="pemberian_makan_asi" <?php echo ($f['pemberian_makan_asi'] ?? 0) ? 'checked' : ''; ?> />
                        ASI
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="pemberian_makan_susu_lain" <?php echo ($f['pemberian_makan_susu_lain'] ?? 0) ? 'checked' : ''; ?> />
                        Susu Lain
                    </label>
                    <input type="text" name="keterangan_susu_lain" class="form-control-inline" placeholder="Keterangan" value="<?php echo htmlspecialchars($f['keterangan_susu_lain'] ?? ''); ?>" />
                </div>
            </div>

            <div class="form-group">
                <label>2. Frekuensi Menyusui</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="frekuensi_menyusui" value="<8x/hari" <?php echo ($f['frekuensi_menyusui'] ?? '') === '<8x/hari' ? 'checked' : ''; ?> />
                        &lt;8x/hari
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="frekuensi_menyusui" value="8-12x/hari" <?php echo ($f['frekuensi_menyusui'] ?? '') === '8-12x/hari' ? 'checked' : ''; ?> />
                        8-12x/hari
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="frekuensi_menyusui" value=">12x/hari" <?php echo ($f['frekuensi_menyusui'] ?? '') === '>12x/hari' ? 'checked' : ''; ?> />
                        &gt;12x/hari
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="frekuensi_menyusui" value="lain" <?php echo ($f['frekuensi_menyusui'] ?? '') === 'lain' ? 'checked' : ''; ?> />
                        Lain
                    </label>
                    <input type="text" name="keterangan_frekuensi_menyusui" class="form-control-inline" placeholder="Keterangan" value="<?php echo htmlspecialchars($f['keterangan_frekuensi_menyusui'] ?? ''); ?>" />
                </div>
            </div>

            <div class="form-group">
                <label>3. Lama Menyusui</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="lama_menyusui" value="<10 menit" <?php echo ($f['lama_menyusui'] ?? '') === '<10 menit' ? 'checked' : ''; ?> />
                        &lt;10 menit
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="lama_menyusui" value="10-30 menit" <?php echo ($f['lama_menyusui'] ?? '') === '10-30 menit' ? 'checked' : ''; ?> />
                        10-30 menit
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="lama_menyusui" value=">30 menit" <?php echo ($f['lama_menyusui'] ?? '') === '>30 menit' ? 'checked' : ''; ?> />
                        &gt;30 menit
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>4. Menyusui</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="menyusui" value="satu payudara" <?php echo ($f['menyusui'] ?? '') === 'satu payudara' ? 'checked' : ''; ?> />
                        Di satu payudara
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="menyusui" value="kedua payudara" <?php echo ($f['menyusui'] ?? '') === 'kedua payudara' ? 'checked' : ''; ?> />
                        Di kedua payudara
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>5. Menyusui Waktu Malam Hari</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="menyusui_malam_hari" value="Ya" <?php echo ($f['menyusui_malam_hari'] ?? '') === 'Ya' ? 'checked' : ''; ?> />
                        Ya
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="menyusui_malam_hari" value="Tidak" <?php echo ($f['menyusui_malam_hari'] ?? '') === 'Tidak' ? 'checked' : ''; ?> />
                        Tidak
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>6. Jumlah dan Frekuensi Pemberian Susu Lain</label>
                <input type="text" name="jumlah_frekuensi_susu_lain" class="form-control-rounded" value="<?php echo htmlspecialchars($f['jumlah_frekuensi_susu_lain'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>7. Cairan Lain sebagai Tambahan ASI</label>
                <div class="form-subgroup">
                    <input type="text" name="cairan_tambahan_kapan_dimulai" class="form-control-rounded" placeholder="a. Kapan dimulai" value="<?php echo htmlspecialchars($f['cairan_tambahan_kapan_dimulai'] ?? ''); ?>" />
                    <input type="text" name="cairan_tambahan_apa_yang_diberikan" class="form-control-rounded" placeholder="b. Apa yang diberikan" value="<?php echo htmlspecialchars($f['cairan_tambahan_apa_yang_diberikan'] ?? ''); ?>" />
                    <input type="text" name="cairan_tambahan_frekuensi_pemberian" class="form-control-rounded" placeholder="c. Frekuensi pemberian" value="<?php echo htmlspecialchars($f['cairan_tambahan_frekuensi_pemberian'] ?? ''); ?>" />
                    <input type="text" name="cairan_tambahan_berapa_banyak" class="form-control-rounded" placeholder="d. Berapa banyak diberikan sekali minum" value="<?php echo htmlspecialchars($f['cairan_tambahan_berapa_banyak'] ?? ''); ?>" />
                </div>
            </div>

            <div class="form-group">
                <label>8. Makanan Lain sebagai Tambahan ASI</label>
                <div class="form-subgroup">
                    <input type="text" name="makanan_tambahan_kapan_dimulai" class="form-control-rounded" placeholder="a. Kapan dimulai" value="<?php echo htmlspecialchars($f['makanan_tambahan_kapan_dimulai'] ?? ''); ?>" />
                    <input type="text" name="makanan_tambahan_apa_yang_diberikan" class="form-control-rounded" placeholder="b. Apa yang diberikan" value="<?php echo htmlspecialchars($f['makanan_tambahan_apa_yang_diberikan'] ?? ''); ?>" />
                    <input type="text" name="makanan_tambahan_frekuensi_pemberian" class="form-control-rounded" placeholder="c. Frekuensi pemberian" value="<?php echo htmlspecialchars($f['makanan_tambahan_frekuensi_pemberian'] ?? ''); ?>" />
                    <input type="text" name="makanan_tambahan_berapa_banyak" class="form-control-rounded" placeholder="d. Berapa banyak diberikan sekali makan" value="<?php echo htmlspecialchars($f['makanan_tambahan_berapa_banyak'] ?? ''); ?>" />
                </div>
            </div>

            <div class="form-group">
                <label>9. Apakah Menggunakan Botol/DOT/Empeng?</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="menggunakan_botol" value="Ya" <?php echo ($f['menggunakan_botol'] ?? '') === 'Ya' ? 'checked' : ''; ?> />
                        Ya
                    </label>
                    <input type="text" name="keterangan_botol" class="form-control-inline" placeholder="Bagaimana membersihkannya?" value="<?php echo htmlspecialchars($f['keterangan_botol'] ?? ''); ?>" />
                    <label class="radio-label">
                        <input type="radio" name="menggunakan_botol" value="Tidak" <?php echo ($f['menggunakan_botol'] ?? '') === 'Tidak' || empty($f['menggunakan_botol']) ? 'checked' : ''; ?> />
                        Tidak
                    </label>
                </div>
            </div>
        </div>

        <!-- Kesehatan & Perilaku Bayi -->
        <div class="form-section">
            <h3 class="section-title">Kesehatan & Perilaku Bayi</h3>

            <div class="form-row-double">
                <div class="form-group">
                    <label>1. BB Saat Lahir (gram)</label>
                    <input type="number" name="bb_saat_lahir" class="form-control-rounded" value="<?php echo htmlspecialchars($f['bb_saat_lahir'] ?? ''); ?>" />
                </div>
                <div class="form-group">
                    <label>BB Saat Ini (gram)</label>
                    <input type="number" name="bb_saat_ini" class="form-control-rounded" value="<?php echo htmlspecialchars($f['bb_saat_ini'] ?? ''); ?>" />
                </div>
            </div>

            <div class="form-group">
                <label>2. Jenis Kelahiran</label>
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="jenis_kelahiran_prematur" <?php echo ($f['jenis_kelahiran_prematur'] ?? 0) ? 'checked' : ''; ?> />
                        Prematur
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="jenis_kelahiran_kembar" <?php echo ($f['jenis_kelahiran_kembar'] ?? 0) ? 'checked' : ''; ?> />
                        Kembar
                    </label>
                </div>
            </div>

            <div class="form-row-double">
                <div class="form-group">
                    <label>3. Frekuensi BAK</label>
                    <input type="text" name="frekuensi_bak" class="form-control-rounded" value="<?php echo htmlspecialchars($f['frekuensi_bak'] ?? ''); ?>" />
                </div>
                <div class="form-group">
                    <label>Warna BAK</label>
                    <input type="text" name="warna_bak" class="form-control-rounded" value="<?php echo htmlspecialchars($f['warna_bak'] ?? ''); ?>" />
                </div>
            </div>

            <div class="form-row-double">
                <div class="form-group">
                    <label>4. Frekuensi BAB</label>
                    <input type="text" name="frekuensi_bab" class="form-control-rounded" value="<?php echo htmlspecialchars($f['frekuensi_bab'] ?? ''); ?>" />
                </div>
                <div class="form-group">
                    <label>Konsistensi BAB</label>
                    <input type="text" name="kosistensi_bab" class="form-control-rounded" value="<?php echo htmlspecialchars($f['kosistensi_bab'] ?? ''); ?>" />
                </div>
            </div>

            <div class="form-group">
                <label>5. Perilaku Makan</label>
                <textarea name="perilaku_makan" class="form-control-rounded" rows="2"><?php echo htmlspecialchars($f['perilaku_makan'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>6. Perilaku Tidur</label>
                <textarea name="perilaku_tidur" class="form-control-rounded" rows="2"><?php echo htmlspecialchars($f['perilaku_tidur'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>7. Perilaku Menangis</label>
                <textarea name="perilaku_menangis" class="form-control-rounded" rows="2"><?php echo htmlspecialchars($f['perilaku_menangis'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>8. Riwayat Sakit</label>
                <textarea name="riwayat_sakit" class="form-control-rounded" rows="2"><?php echo htmlspecialchars($f['riwayat_sakit'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>9. Kelainan Bawaan</label>
                <textarea name="kelainan_bawaan" class="form-control-rounded" rows="2"><?php echo htmlspecialchars($f['kelainan_bawaan'] ?? ''); ?></textarea>
            </div>
        </div>

        <!-- Kehamilan, Kelahiran dan Pemberian Makan Bayi -->
        <div class="form-section">
            <h3 class="section-title">Kehamilan, Kelahiran dan Pemberian Makan Bayi</h3>

            <div class="form-group">
                <label>1. Tempat ANC</label>
                <div class="checkbox-group">
                    <label class="checkbox-label"><input type="checkbox" name="tempat_anc_rs" <?php echo ($f['tempat_anc_rs'] ?? 0) ? 'checked' : ''; ?> /> RS</label>
                    <label class="checkbox-label"><input type="checkbox" name="tempat_anc_tpmb" <?php echo ($f['tempat_anc_tpmb'] ?? 0) ? 'checked' : ''; ?> /> TPMB</label>
                    <label class="checkbox-label"><input type="checkbox" name="tempat_anc_puskesmas" <?php echo ($f['tempat_anc_puskesmas'] ?? 0) ? 'checked' : ''; ?> /> Puskesmas</label>
                    <label class="checkbox-label"><input type="checkbox" name="tempat_anc_polindes" <?php echo ($f['tempat_anc_polindes'] ?? 0) ? 'checked' : ''; ?> /> Polindes</label>
                    <label class="checkbox-label"><input type="checkbox" name="tempat_anc_lainnya" <?php echo ($f['tempat_anc_lainnya'] ?? 0) ? 'checked' : ''; ?> /> Lainnya</label>
                    <input type="text" name="tempat_anc_keterangan_lainnya" class="form-control-inline" placeholder="Keterangan" value="<?php echo htmlspecialchars($f['tempat_anc_keterangan_lainnya'] ?? ''); ?>" />
                </div>
            </div>

            <div class="form-group">
                <label>2. Yang Melakukan ANC</label>
                <div class="checkbox-group">
                    <label class="checkbox-label"><input type="checkbox" name="yang_melakukan_anc_dokter_kandungan" <?php echo ($f['yang_melakukan_anc_dokter_kandungan'] ?? 0) ? 'checked' : ''; ?> /> Dokter Kandungan</label>
                    <label class="checkbox-label"><input type="checkbox" name="yang_melakukan_anc_bidan" <?php echo ($f['yang_melakukan_anc_bidan'] ?? 0) ? 'checked' : ''; ?> /> Bidan</label>
                    <label class="checkbox-label"><input type="checkbox" name="yang_melakukan_anc_dokter_umum" <?php echo ($f['yang_melakukan_anc_dokter_umum'] ?? 0) ? 'checked' : ''; ?> /> Dokter Umum</label>
                    <label class="checkbox-label"><input type="checkbox" name="yang_melakukan_anc_lainnya" <?php echo ($f['yang_melakukan_anc_lainnya'] ?? 0) ? 'checked' : ''; ?> /> Lainnya</label>
                    <input type="text" name="yang_melakukan_anc_keterangan_lainnya" class="form-control-inline" placeholder="Keterangan" value="<?php echo htmlspecialchars($f['yang_melakukan_anc_keterangan_lainnya'] ?? ''); ?>" />
                </div>
            </div>

            <div class="form-group">
                <label>3. Diskusi Pemberian Makan</label>
                <textarea name="diskusi_pemberian_makan" class="form-control-rounded" rows="2"><?php echo htmlspecialchars($f['diskusi_pemberian_makan'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>4. Jenis Persalinan</label>
                <select name="jenis_persalinan" class="form-control-rounded">
                    <option value="">Pilih</option>
                    <option value="Normal" <?php echo ($f['jenis_persalinan'] ?? '') === 'Normal' ? 'selected' : ''; ?>>Normal</option>
                    <option value="Sesar" <?php echo ($f['jenis_persalinan'] ?? '') === 'Sesar' ? 'selected' : ''; ?>>Sesar</option>
                    <option value="Vacum" <?php echo ($f['jenis_persalinan'] ?? '') === 'Vacum' ? 'selected' : ''; ?>>Vacum</option>
                    <option value="Forsep" <?php echo ($f['jenis_persalinan'] ?? '') === 'Forsep' ? 'selected' : ''; ?>>Forsep</option>
                </select>
            </div>

            <div class="form-group">
                <label>5. Riwayat IMD</label>
                <div class="radio-group">
                    <label class="radio-label"><input type="radio" name="riwayat_imd" value="Ya" <?php echo ($f['riwayat_imd'] ?? '') === 'Ya' ? 'checked' : ''; ?> /> Ya</label>
                    <label class="radio-label"><input type="radio" name="riwayat_imd" value="Tidak" <?php echo ($f['riwayat_imd'] ?? '') === 'Tidak' || empty($f['riwayat_imd']) ? 'checked' : ''; ?> /> Tidak</label>
                </div>
            </div>

            <div class="form-group">
                <label>6. Menyusui 1 Jam Pertama</label>
                <div class="radio-group">
                    <label class="radio-label"><input type="radio" name="menyusui_1_jam" value="Ya" <?php echo ($f['menyusui_1_jam'] ?? '') === 'Ya' ? 'checked' : ''; ?> /> Ya</label>
                    <label class="radio-label"><input type="radio" name="menyusui_1_jam" value="Tidak" <?php echo ($f['menyusui_1_jam'] ?? '') === 'Tidak' || empty($f['menyusui_1_jam']) ? 'checked' : ''; ?> /> Tidak</label>
                </div>
            </div>

            <div class="form-group">
                <label>7. Rawat Gabung</label>
                <div class="radio-group">
                    <label class="radio-label"><input type="radio" name="rawat_gabung" value="Ya" <?php echo ($f['rawat_gabung'] ?? '') === 'Ya' ? 'checked' : ''; ?> /> Ya</label>
                    <label class="radio-label"><input type="radio" name="rawat_gabung" value="Tidak" <?php echo ($f['rawat_gabung'] ?? '') === 'Tidak' || empty($f['rawat_gabung']) ? 'checked' : ''; ?> /> Tidak</label>
                </div>
            </div>

            <div class="form-group">
                <label>8. Pemberian Prelaktal</label>
                <div class="radio-group">
                    <label class="radio-label"><input type="radio" name="pemberian_prelaktal" value="Ya" <?php echo ($f['pemberian_prelaktal'] ?? '') === 'Ya' ? 'checked' : ''; ?> /> Ya</label>
                    <label class="radio-label"><input type="radio" name="pemberian_prelaktal" value="Tidak" <?php echo ($f['pemberian_prelaktal'] ?? '') === 'Tidak' || empty($f['pemberian_prelaktal']) ? 'checked' : ''; ?> /> Tidak</label>
                </div>
            </div>

            <div class="form-group">
                <label>9. Bantuan Menyusui</label>
                <div class="radio-group">
                    <label class="radio-label"><input type="radio" name="bantuan_menyusui" value="Ya" <?php echo ($f['bantuan_menyusui'] ?? '') === 'Ya' ? 'checked' : ''; ?> /> Ya</label>
                    <label class="radio-label"><input type="radio" name="bantuan_menyusui" value="Tidak" <?php echo ($f['bantuan_menyusui'] ?? '') === 'Tidak' || empty($f['bantuan_menyusui']) ? 'checked' : ''; ?> /> Tidak</label>
                </div>
            </div>

            <div class="form-group">
                <label>10. Menerima Sample Formula</label>
                <div class="radio-group">
                    <label class="radio-label"><input type="radio" name="menerima_sample_formula" value="Ya" <?php echo ($f['menerima_sample_formula'] ?? '') === 'Ya' ? 'checked' : ''; ?> /> Ya</label>
                    <label class="radio-label"><input type="radio" name="menerima_sample_formula" value="Tidak" <?php echo ($f['menerima_sample_formula'] ?? '') === 'Tidak' || empty($f['menerima_sample_formula']) ? 'checked' : ''; ?> /> Tidak</label>
                </div>
            </div>
        </div>

        <!-- Kondisi Ibu & KB -->
        <div class="form-section">
            <h3 class="section-title">Kondisi Ibu & KB</h3>

            <div class="form-group">
                <label>1. Riwayat Kesehatan Ibu</label>
                <textarea name="riwayat_kesehatan_ibu" class="form-control-rounded" rows="2"><?php echo htmlspecialchars($f['riwayat_kesehatan_ibu'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>2. Obat Dikonsumsi</label>
                <textarea name="obat_dikonsumsi" class="form-control-rounded" rows="2"><?php echo htmlspecialchars($f['obat_dikonsumsi'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>3. Gizi Ibu</label>
                <select name="gizi_ibu" class="form-control-rounded">
                    <option value="">Pilih</option>
                    <option value="Baik" <?php echo ($f['gizi_ibu'] ?? '') === 'Baik' ? 'selected' : ''; ?>>Baik</option>
                    <option value="Cukup" <?php echo ($f['gizi_ibu'] ?? '') === 'Cukup' ? 'selected' : ''; ?>>Cukup</option>
                    <option value="Kurang" <?php echo ($f['gizi_ibu'] ?? '') === 'Kurang' ? 'selected' : ''; ?>>Kurang</option>
                </select>
            </div>

            <div class="form-group">
                <label>4. Kebiasaan Kopi</label>
                <div class="radio-group">
                    <label class="radio-label"><input type="radio" name="kebiasaan_kopi" value="Ya" <?php echo ($f['kebiasaan_kopi'] ?? '') === 'Ya' ? 'checked' : ''; ?> /> Ya</label>
                    <label class="radio-label"><input type="radio" name="kebiasaan_kopi" value="Tidak" <?php echo ($f['kebiasaan_kopi'] ?? '') === 'Tidak' || empty($f['kebiasaan_kopi']) ? 'checked' : ''; ?> /> Tidak</label>
                </div>
            </div>

            <div class="form-group">
                <label>5. Minuman Alkohol</label>
                <div class="radio-group">
                    <label class="radio-label"><input type="radio" name="minuman_alkohol" value="Ya" <?php echo ($f['minuman_alkohol'] ?? '') === 'Ya' ? 'checked' : ''; ?> /> Ya</label>
                    <label class="radio-label"><input type="radio" name="minuman_alkohol" value="Tidak" <?php echo ($f['minuman_alkohol'] ?? '') === 'Tidak' || empty($f['minuman_alkohol']) ? 'checked' : ''; ?> /> Tidak</label>
                </div>
            </div>

            <div class="form-group">
                <label>6. Kebiasaan Merokok</label>
                <div class="radio-group">
                    <label class="radio-label"><input type="radio" name="kebiasaan_merokok" value="Ya" <?php echo ($f['kebiasaan_merokok'] ?? '') === 'Ya' ? 'checked' : ''; ?> /> Ya</label>
                    <label class="radio-label"><input type="radio" name="kebiasaan_merokok" value="Tidak" <?php echo ($f['kebiasaan_merokok'] ?? '') === 'Tidak' || empty($f['kebiasaan_merokok']) ? 'checked' : ''; ?> /> Tidak</label>
                </div>
            </div>

            <div class="form-group">
                <label>7. Narkoba</label>
                <div class="radio-group">
                    <label class="radio-label"><input type="radio" name="narkoba" value="Ya" <?php echo ($f['narkoba'] ?? '') === 'Ya' ? 'checked' : ''; ?> /> Ya</label>
                    <label class="radio-label"><input type="radio" name="narkoba" value="Tidak" <?php echo ($f['narkoba'] ?? '') === 'Tidak' || empty($f['narkoba']) ? 'checked' : ''; ?> /> Tidak</label>
                </div>
            </div>

            <div class="form-group">
                <label>8. Kondisi Payudara</label>
                <textarea name="kondisi_payudara" class="form-control-rounded" rows="2"><?php echo htmlspecialchars($f['kondisi_payudara'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>9. Penggunaan KB</label>
                <div class="radio-group">
                    <label class="radio-label"><input type="radio" name="penggunaan_kb" value="Ya" <?php echo ($f['penggunaan_kb'] ?? '') === 'Ya' ? 'checked' : ''; ?> /> Ya</label>
                    <label class="radio-label"><input type="radio" name="penggunaan_kb" value="Tidak" <?php echo ($f['penggunaan_kb'] ?? '') === 'Tidak' || empty($f['penggunaan_kb']) ? 'checked' : ''; ?> /> Tidak</label>
                </div>
                <input type="text" name="keterangan_kb" class="form-control-rounded" placeholder="Jenis KB" value="<?php echo htmlspecialchars($f['keterangan_kb'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>10. Motivasi Menyusui</label>
                <textarea name="motivasi_menyusui" class="form-control-rounded" rows="2"><?php echo htmlspecialchars($f['motivasi_menyusui'] ?? ''); ?></textarea>
            </div>
        </div>

        <!-- Pengalaman Menyusui Sebelumnya -->
        <div class="form-section">
            <h3 class="section-title">Pengalaman Menyusui Bayi/Anak Sebelumnya</h3>

            <div class="form-row-double">
                <div class="form-group">
                    <label>1. Jumlah Anak Sebelumnya</label>
                    <input type="number" name="jumlah_anak_sebelumnya" class="form-control-rounded" value="<?php echo htmlspecialchars($f['jumlah_anak_sebelumnya'] ?? ''); ?>" />
                </div>
                <div class="form-group">
                    <label>Jumlah Anak Disusui</label>
                    <input type="number" name="jumlah_anak_disusui" class="form-control-rounded" value="<?php echo htmlspecialchars($f['jumlah_anak_disusui'] ?? ''); ?>" />
                </div>
            </div>

            <div class="form-group">
                <label>2. Riwayat ASI Eksklusif</label>
                <div class="radio-group">
                    <label class="radio-label"><input type="radio" name="riwayat_asi_eksklusif" value="Ya" <?php echo ($f['riwayat_asi_eksklusif'] ?? '') === 'Ya' ? 'checked' : ''; ?> /> Ya</label>
                    <label class="radio-label"><input type="radio" name="riwayat_asi_eksklusif" value="Tidak" <?php echo ($f['riwayat_asi_eksklusif'] ?? '') === 'Tidak' || empty($f['riwayat_asi_eksklusif']) ? 'checked' : ''; ?> /> Tidak</label>
                </div>
            </div>

            <div class="form-group">
                <label>3. Menggunakan Botol Sebelumnya</label>
                <div class="radio-group">
                    <label class="radio-label"><input type="radio" name="menggunakan_botol_sebelumnya" value="Ya" <?php echo ($f['menggunakan_botol_sebelumnya'] ?? '') === 'Ya' ? 'checked' : ''; ?> /> Ya</label>
                    <label class="radio-label"><input type="radio" name="menggunakan_botol_sebelumnya" value="Tidak" <?php echo ($f['menggunakan_botol_sebelumnya'] ?? '') === 'Tidak' || empty($f['menggunakan_botol_sebelumnya']) ? 'checked' : ''; ?> /> Tidak</label>
                </div>
                <input type="text" name="alasan_botol" class="form-control-rounded" placeholder="Alasan" value="<?php echo htmlspecialchars($f['alasan_botol'] ?? ''); ?>" />
            </div>
        </div>

        <!-- Situasi Keluarga & Sosial -->
        <div class="form-section">
            <h3 class="section-title">Situasi Keluarga & Sosial</h3>

            <div class="form-group">
                <label>1. Pekerjaan Orang Tua</label>
                <input type="text" name="pekerjaan_orang_tua" class="form-control-rounded" value="<?php echo htmlspecialchars($f['pekerjaan_orang_tua'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>2. Keadaan Ekonomi</label>
                <select name="keadaan_ekonomi" class="form-control-rounded">
                    <option value="">Pilih</option>
                    <option value="Baik" <?php echo ($f['keadaan_ekonomi'] ?? '') === 'Baik' ? 'selected' : ''; ?>>Baik</option>
                    <option value="Cukup" <?php echo ($f['keadaan_ekonomi'] ?? '') === 'Cukup' ? 'selected' : ''; ?>>Cukup</option>
                    <option value="Kurang" <?php echo ($f['keadaan_ekonomi'] ?? '') === 'Kurang' ? 'selected' : ''; ?>>Kurang</option>
                </select>
            </div>

            <div class="form-group">
                <label>3. Pendidikan Orang Tua</label>
                <input type="text" name="pendidikan_orang_tua" class="form-control-rounded" value="<?php echo htmlspecialchars($f['pendidikan_orang_tua'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label>4. Sikap Keluarga</label>
                <textarea name="sikap_keluarga" class="form-control-rounded" rows="2"><?php echo htmlspecialchars($f['sikap_keluarga'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>5. Bantuan Perawatan Anak</label>
                <textarea name="bantuan_perawatan_anak" class="form-control-rounded" rows="2"><?php echo htmlspecialchars($f['bantuan_perawatan_anak'] ?? ''); ?></textarea>
            </div>
        </div>

        <!-- Interpretasi KMS -->
        <div class="form-section">
            <h3 class="section-title">Interpretasi KMS</h3>

            <div class="form-group">
                <label>Pertumbuhan Sesuai Kurva</label>
                <textarea name="pertumbuhan_sesuai_kurva" class="form-control-rounded" rows="2"><?php echo htmlspecialchars($f['pertumbuhan_sesuai_kurva'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?php echo baseUrl('pages/antrian.php'); ?>" class="btn btn-secondary-rounded">Batal</a>
            <button type="submit" class="btn btn-primary-rounded">
                <span class="btn-icon">💾</span>
                Simpan Data Kajian
            </button>
        </div>
    </form>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/formulir_layout.php';
