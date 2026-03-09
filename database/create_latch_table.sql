-- Create latch table for LATCH scoring assessment
-- Run this SQL in phpMyAdmin or your database manager

CREATE TABLE IF NOT EXISTS `latch` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `no_registrasi` VARCHAR(50) NOT NULL,
  `latch_score` TINYINT DEFAULT NULL,
  `audible_swallowing_score` TINYINT DEFAULT NULL,
  `type_of_nipple_score` TINYINT DEFAULT NULL,
  `comfort_score` TINYINT DEFAULT NULL,
  `hold_score` TINYINT DEFAULT NULL,
  `total_score` TINYINT DEFAULT 0,
  `interpretasi` VARCHAR(50) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_no_registrasi` (`no_registrasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create pemeriksaan_fisik table for physical examination records
CREATE TABLE IF NOT EXISTS `pemeriksaan_fisik` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `no_registrasi` VARCHAR(50) NOT NULL,
  
  -- Ibu fields
  `ibu_keadaan_umum` VARCHAR(255) DEFAULT NULL,
  `ibu_sistolik_diastolik` VARCHAR(50) DEFAULT NULL,
  `ibu_nadi_pernapasan` VARCHAR(50) DEFAULT NULL,
  `ibu_suhu` VARCHAR(20) DEFAULT NULL,
  `ibu_skrining_nyeri` VARCHAR(50) DEFAULT NULL,
  `ibu_tinggi_badan` VARCHAR(20) DEFAULT NULL,
  `ibu_berat_badan` VARCHAR(20) DEFAULT NULL,
  `ibu_index_masa_tubuh` VARCHAR(20) DEFAULT NULL,
  `ibu_lingkar_kepala` VARCHAR(20) DEFAULT NULL,
  `ibu_pemeriksaan_fisik` TEXT DEFAULT NULL,
  `ibu_putting` VARCHAR(255) DEFAULT NULL,
  `ibu_areola` VARCHAR(255) DEFAULT NULL,
  `ibu_corpus_payudara` VARCHAR(255) DEFAULT NULL,
  `ibu_payudara_lainnya` TEXT DEFAULT NULL,
  
  -- Bayi fields
  `bayi_keadaan_umum` VARCHAR(255) DEFAULT NULL,
  `bayi_suhu` VARCHAR(20) DEFAULT NULL,
  `bayi_nadi` VARCHAR(50) DEFAULT NULL,
  `bayi_pernapasan` VARCHAR(50) DEFAULT NULL,
  `bayi_tonus_otot` VARCHAR(255) DEFAULT NULL,
  `bayi_kondisi_bibir` VARCHAR(255) DEFAULT NULL,
  `bayi_kondisi_rongga_mulut` VARCHAR(255) DEFAULT NULL,
  `bayi_kondisi_lidah` VARCHAR(255) DEFAULT NULL,
  `bayi_reflek_rooting` VARCHAR(255) DEFAULT NULL,
  `bayi_reflek_sucking` VARCHAR(255) DEFAULT NULL,
  `bayi_reflek_suckling` VARCHAR(255) DEFAULT NULL,
  `bayi_reflek_swallowing` VARCHAR(255) DEFAULT NULL,
  `bayi_lain_lain` TEXT DEFAULT NULL,
  
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_no_registrasi` (`no_registrasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create ibfat table for Infant Breastfeeding Assessment Tool
CREATE TABLE IF NOT EXISTS `ibfat` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `no_registrasi` VARCHAR(50) NOT NULL,
  `posture_score` TINYINT DEFAULT NULL,
  `attachment_score` TINYINT DEFAULT NULL,
  `swallowing_score` TINYINT DEFAULT NULL,
  `positioning_score` TINYINT DEFAULT NULL,
  `total_score` TINYINT DEFAULT 0,
  `interpretasi` VARCHAR(100) DEFAULT NULL,
  `catatan` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_no_registrasi` (`no_registrasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create pibbs table for Preterm Infant Breastfeeding Behaviour Scale
CREATE TABLE IF NOT EXISTS `pibbs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `no_registrasi` VARCHAR(50) NOT NULL,
  `mencari_puting_score` TINYINT DEFAULT NULL,
  `cakupan_areola_score` TINYINT DEFAULT NULL,
  `menempel_melekat_score` TINYINT DEFAULT NULL,
  `menghisap_score` TINYINT DEFAULT NULL,
  `menghisap_terpanjang_score` TINYINT DEFAULT NULL,
  `menelan_score` TINYINT DEFAULT NULL,
  `catatan` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_no_registrasi` (`no_registrasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create bsessf table for Breastfeeding Self-Efficacy Scale - Short Form
CREATE TABLE IF NOT EXISTS `bsessf` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `no_registrasi` VARCHAR(50) NOT NULL,
  `pertanyaan_1` TINYINT DEFAULT NULL,
  `pertanyaan_2` TINYINT DEFAULT NULL,
  `pertanyaan_3` TINYINT DEFAULT NULL,
  `pertanyaan_4` TINYINT DEFAULT NULL,
  `pertanyaan_5` TINYINT DEFAULT NULL,
  `pertanyaan_6` TINYINT DEFAULT NULL,
  `pertanyaan_7` TINYINT DEFAULT NULL,
  `pertanyaan_8` TINYINT DEFAULT NULL,
  `pertanyaan_9` TINYINT DEFAULT NULL,
  `pertanyaan_10` TINYINT DEFAULT NULL,
  `pertanyaan_11` TINYINT DEFAULT NULL,
  `pertanyaan_12` TINYINT DEFAULT NULL,
  `total_score` TINYINT DEFAULT 0,
  `interpretasi` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_no_registrasi` (`no_registrasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create hatlff table for Hazelbaker Assessment Tool for Lingual Frenulum Function
CREATE TABLE IF NOT EXISTS `hatlff` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `no_registrasi` VARCHAR(50) NOT NULL,
  
  -- Fungsi scores
  `gerak_samping` TINYINT DEFAULT NULL,
  `gerak_atas` TINYINT DEFAULT NULL,
  `gerak_memanjang` TINYINT DEFAULT NULL,
  `pelebaran_ujung` TINYINT DEFAULT NULL,
  `bentuk_mangkok` TINYINT DEFAULT NULL,
  `gerak_berirama` TINYINT DEFAULT NULL,
  `berdecak` TINYINT DEFAULT NULL,
  
  -- Penampilan scores
  `bentuk_lidah` TINYINT DEFAULT NULL,
  `elastisitas` TINYINT DEFAULT NULL,
  `panjang_frenulum` TINYINT DEFAULT NULL,
  `perlekatan_lidah` TINYINT DEFAULT NULL,
  `perlekatan_dasar` TINYINT DEFAULT NULL,
  
  `skor_fungsi` TINYINT DEFAULT 0,
  `skor_penampilan` TINYINT DEFAULT 0,
  `skor_total` TINYINT DEFAULT 0,
  `interpretasi` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_no_registrasi` (`no_registrasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
