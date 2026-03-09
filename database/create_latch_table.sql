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
