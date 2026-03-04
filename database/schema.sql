-- Klinik Laktasi Database Schema
-- MySQL Database for Lactation Clinic Management System

CREATE DATABASE IF NOT EXISTS klinik_laktasi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE klinik_laktasi;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('ADMIN', 'medis') DEFAULT 'medis',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Patients Table
CREATE TABLE IF NOT EXISTS pasien (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_rm VARCHAR(20) UNIQUE NOT NULL,
    nik VARCHAR(16) DEFAULT NULL,
    nama VARCHAR(255) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    alamat TEXT DEFAULT NULL,
    no_hp VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_no_rm (no_rm),
    INDEX idx_nama (nama)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Registrasi Table
CREATE TABLE IF NOT EXISTS registrasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_registrasi VARCHAR(50) UNIQUE NOT NULL,
    pasien_id INT NOT NULL,
    tanggal_pengkajian DATE NOT NULL,
    waktu_pengkajian TIME NOT NULL,
    nama_ibu VARCHAR(255) NOT NULL,
    tanggal_lahir_ibu DATE NOT NULL,
    usia_ibu VARCHAR(50) DEFAULT NULL,
    nama_bayi VARCHAR(255) DEFAULT NULL,
    tanggal_lahir_bayi DATE DEFAULT NULL,
    usia_bayi VARCHAR(50) DEFAULT NULL,
    status ENUM('menunggu', 'konsultasi', 'selesai') DEFAULT 'menunggu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pasien_id) REFERENCES pasien(id) ON DELETE CASCADE,
    INDEX idx_no_registrasi (no_registrasi),
    INDEX idx_pasien_id (pasien_id),
    INDEX idx_tanggal_pengkajian (tanggal_pengkajian),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kajian Riwayat Menyusui Table
CREATE TABLE IF NOT EXISTS kajian_riwayat_menyusui (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_registrasi VARCHAR(50) UNIQUE NOT NULL,
    
    -- Pemberian Makan Bayi Sekarang
    pemberian_makan_asi TINYINT(1) DEFAULT 0,
    pemberian_makan_susu_lain TINYINT(1) DEFAULT 0,
    keterangan_susu_lain TEXT DEFAULT NULL,
    frekuensi_menyusui VARCHAR(50) DEFAULT NULL,
    keterangan_frekuensi_menyusui TEXT DEFAULT NULL,
    lama_menyusui VARCHAR(50) DEFAULT NULL,
    menyusui VARCHAR(50) DEFAULT NULL,
    menyusui_malam_hari VARCHAR(10) DEFAULT NULL,
    jumlah_frekuensi_susu_lain VARCHAR(100) DEFAULT NULL,
    
    -- Cairan Tambahan
    cairan_tambahan_kapan_dimulai VARCHAR(100) DEFAULT NULL,
    cairan_tambahan_apa_yang_diberikan VARCHAR(255) DEFAULT NULL,
    cairan_tambahan_frekuensi_pemberian VARCHAR(100) DEFAULT NULL,
    cairan_tambahan_berapa_banyak VARCHAR(100) DEFAULT NULL,
    
    -- Makanan Tambahan
    makanan_tambahan_kapan_dimulai VARCHAR(100) DEFAULT NULL,
    makanan_tambahan_apa_yang_diberikan VARCHAR(255) DEFAULT NULL,
    makanan_tambahan_frekuensi_pemberian VARCHAR(100) DEFAULT NULL,
    makanan_tambahan_berapa_banyak VARCHAR(100) DEFAULT NULL,
    
    -- Botol
    menggunakan_botol VARCHAR(10) DEFAULT NULL,
    keterangan_botol TEXT DEFAULT NULL,
    
    -- Kesehatan & Perilaku Bayi
    bb_saat_lahir INT DEFAULT NULL,
    bb_saat_ini INT DEFAULT NULL,
    perubahan_bb VARCHAR(20) DEFAULT NULL,
    jenis_kelahiran_prematur TINYINT(1) DEFAULT 0,
    jenis_kelahiran_kembar TINYINT(1) DEFAULT 0,
    frekuensi_bak VARCHAR(50) DEFAULT NULL,
    warna_bak VARCHAR(50) DEFAULT NULL,
    frekuensi_bab VARCHAR(50) DEFAULT NULL,
    kosistensi_bab VARCHAR(50) DEFAULT NULL,
    perilaku_makan TEXT DEFAULT NULL,
    perilaku_tidur TEXT DEFAULT NULL,
    perilaku_menangis TEXT DEFAULT NULL,
    riwayat_sakit TEXT DEFAULT NULL,
    kelainan_bawaan TEXT DEFAULT NULL,
    
    -- Kehamilan, Kelahiran dan Pemberian Makan Bayi
    tempat_anc_rs TINYINT(1) DEFAULT 0,
    tempat_anc_tpmb TINYINT(1) DEFAULT 0,
    tempat_anc_puskesmas TINYINT(1) DEFAULT 0,
    tempat_anc_polindes TINYINT(1) DEFAULT 0,
    tempat_anc_lainnya TINYINT(1) DEFAULT 0,
    tempat_anc_keterangan_lainnya VARCHAR(255) DEFAULT NULL,
    
    yang_melakukan_anc_dokter_kandungan TINYINT(1) DEFAULT 0,
    yang_melakukan_anc_bidan TINYINT(1) DEFAULT 0,
    yang_melakukan_anc_dokter_umum TINYINT(1) DEFAULT 0,
    yang_melakukan_anc_lainnya TINYINT(1) DEFAULT 0,
    yang_melakukan_anc_keterangan_lainnya VARCHAR(255) DEFAULT NULL,
    
    diskusi_pemberian_makan TEXT DEFAULT NULL,
    jenis_persalinan VARCHAR(50) DEFAULT NULL,
    riwayat_imd VARCHAR(10) DEFAULT NULL,
    menyusui_1_jam VARCHAR(10) DEFAULT NULL,
    rawat_gabung VARCHAR(10) DEFAULT NULL,
    pemberian_prelaktal VARCHAR(10) DEFAULT NULL,
    bantuan_menyusui VARCHAR(10) DEFAULT NULL,
    menerima_sample_formula VARCHAR(10) DEFAULT NULL,
    
    -- Kondisi Ibu & KB
    riwayat_kesehatan_ibu TEXT DEFAULT NULL,
    obat_dikonsumsi TEXT DEFAULT NULL,
    gizi_ibu VARCHAR(50) DEFAULT NULL,
    kebiasaan_kopi VARCHAR(10) DEFAULT NULL,
    minuman_alkohol VARCHAR(10) DEFAULT NULL,
    kebiasaan_merokok VARCHAR(10) DEFAULT NULL,
    narkoba VARCHAR(10) DEFAULT NULL,
    kondisi_payudara TEXT DEFAULT NULL,
    penggunaan_kb VARCHAR(10) DEFAULT NULL,
    keterangan_kb VARCHAR(255) DEFAULT NULL,
    motivasi_menyusui TEXT DEFAULT NULL,
    
    -- Pengalaman Menyusui Sebelumnya
    jumlah_anak_sebelumnya INT DEFAULT NULL,
    jumlah_anak_disusui INT DEFAULT NULL,
    riwayat_asi_eksklusif VARCHAR(10) DEFAULT NULL,
    
    cairan_tambahan_sebelumnya_kapan_dimulai VARCHAR(100) DEFAULT NULL,
    cairan_tambahan_sebelumnya_apa_yang_diberikan VARCHAR(255) DEFAULT NULL,
    cairan_tambahan_sebelumnya_frekuensi_pemberian VARCHAR(100) DEFAULT NULL,
    cairan_tambahan_sebelumnya_berapa_banyak VARCHAR(100) DEFAULT NULL,
    
    makanan_tambahan_sebelumnya_kapan_dimulai VARCHAR(100) DEFAULT NULL,
    makanan_tambahan_sebelumnya_apa_yang_diberikan VARCHAR(255) DEFAULT NULL,
    makanan_tambahan_sebelumnya_frekuensi_pemberian VARCHAR(100) DEFAULT NULL,
    makanan_tambahan_sebelumnya_berapa_banyak VARCHAR(100) DEFAULT NULL,
    
    menggunakan_botol_sebelumnya VARCHAR(10) DEFAULT NULL,
    alasan_botol TEXT DEFAULT NULL,
    
    -- Situasi Keluarga & Sosial
    pekerjaan_orang_tua VARCHAR(255) DEFAULT NULL,
    keadaan_ekonomi VARCHAR(50) DEFAULT NULL,
    pendidikan_orang_tua VARCHAR(100) DEFAULT NULL,
    sikap_keluarga TEXT DEFAULT NULL,
    bantuan_perawatan_anak TEXT DEFAULT NULL,
    
    -- Interpretasi KMS
    pertumbuhan_sesuai_kurva TEXT DEFAULT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (no_registrasi) REFERENCES registrasi(no_registrasi) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Asesmen Table
CREATE TABLE IF NOT EXISTS asesmen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_registrasi VARCHAR(50) UNIQUE NOT NULL,
    
    -- Data Ibu
    nama_ibu VARCHAR(255) NOT NULL,
    usia_ibu VARCHAR(50) NOT NULL,
    no_hp VARCHAR(20) DEFAULT NULL,
    alamat TEXT DEFAULT NULL,
    
    -- Data Bayi
    nama_bayi VARCHAR(255) NOT NULL,
    tanggal_lahir_bayi DATE NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    berat_badan_lahir INT DEFAULT NULL,
    panjang_badan INT DEFAULT NULL,
    usia_kehamilan INT DEFAULT NULL,
    
    -- Riwayat Menyusui
    riwayat_menyusui TEXT DEFAULT NULL,
    hambatan_menyusui TEXT DEFAULT NULL,
    frekuensi_menyusui INT DEFAULT NULL,
    durasi_menyusui INT DEFAULT NULL,
    
    -- Pemeriksaan Fisik
    kondisi_payudara TEXT DEFAULT NULL,
    putting_susuk VARCHAR(50) DEFAULT NULL,
    refleks_let_down VARCHAR(50) DEFAULT NULL,
    
    -- Asesmen
    diagnosis TEXT NOT NULL,
    rencana_tindak_lanjut TEXT NOT NULL,
    edukasi TEXT DEFAULT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (no_registrasi) REFERENCES registrasi(no_registrasi) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kunjungan Table
CREATE TABLE IF NOT EXISTS kunjungan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pasien_id INT NOT NULL,
    registrasi_id INT DEFAULT NULL,
    tanggal_kunjungan DATE NOT NULL,
    waktu_kunjungan TIME NOT NULL,
    tujuan_kunjungan VARCHAR(255) DEFAULT NULL,
    keterangan TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pasien_id) REFERENCES pasien(id) ON DELETE CASCADE,
    FOREIGN KEY (registrasi_id) REFERENCES registrasi(id) ON DELETE SET NULL,
    INDEX idx_pasien_id (pasien_id),
    INDEX idx_tanggal_kunjungan (tanggal_kunjungan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user
INSERT INTO users (nama, email, password, role) VALUES
('Administrator', 'admin@kliniklaktasi.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN');
-- Default password: password
