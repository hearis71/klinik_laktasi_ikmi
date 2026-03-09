<?php
/**
 * Setup/Update Database Script
 * Klinik Laktasi - Add missing columns and tables to existing database
 */

require_once __DIR__ . '/config/database.php';

echo "<h2>Klinik Laktasi - Database Update</h2>";

try {
    $pdo = getDbConnection();

    // Check if status column exists
    $stmt = $pdo->query("
        SELECT COUNT(*) as count
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'registrasi'
        AND COLUMN_NAME = 'status'
    ");
    $result = $stmt->fetch();

    if ($result['count'] > 0) {
        echo "<p style='color: green;'>✓ Kolom 'status' sudah ada di tabel registrasi</p>";
    } else {
        // Add status column
        $pdo->exec("
            ALTER TABLE registrasi
            ADD COLUMN status ENUM('menunggu', 'konsultasi', 'selesai') DEFAULT 'menunggu'
            AFTER usia_bayi
        ");
        echo "<p style='color: green;'>✓ Kolom 'status' berhasil ditambahkan ke tabel registrasi</p>";
    }

    // Check if index exists
    $stmt = $pdo->query("
        SELECT COUNT(*) as count
        FROM INFORMATION_SCHEMA.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'registrasi'
        AND INDEX_NAME = 'idx_status'
    ");
    $result = $stmt->fetch();

    if ($result['count'] > 0) {
        echo "<p style='color: green;'>✓ Index 'idx_status' sudah ada</p>";
    } else {
        // Add index
        $pdo->exec("ALTER TABLE registrasi ADD INDEX idx_status (status)");
        echo "<p style='color: green;'>✓ Index 'idx_status' berhasil ditambahkan</p>";
    }

    // Create latch table if not exists
    $stmt = $pdo->query("
        SELECT COUNT(*) as count
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'latch'
    ");
    $result = $stmt->fetch();

    if ($result['count'] > 0) {
        echo "<p style='color: green;'>✓ Tabel 'latch' sudah ada</p>";
    } else {
        // Create latch table
        $pdo->exec("
            CREATE TABLE latch (
                id INT AUTO_INCREMENT PRIMARY KEY,
                no_registrasi VARCHAR(50) NOT NULL,
                latch_score TINYINT DEFAULT NULL,
                audible_swallowing_score TINYINT DEFAULT NULL,
                type_of_nipple_score TINYINT DEFAULT NULL,
                comfort_score TINYINT DEFAULT NULL,
                hold_score TINYINT DEFAULT NULL,
                total_score TINYINT DEFAULT 0,
                interpretasi VARCHAR(50) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_no_registrasi (no_registrasi)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "<p style='color: green;'>✓ Tabel 'latch' berhasil dibuat</p>";
    }

    // Create pemeriksaan_fisik table if not exists
    $stmt = $pdo->query("
        SELECT COUNT(*) as count
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'pemeriksaan_fisik'
    ");
    $result = $stmt->fetch();

    if ($result['count'] > 0) {
        echo "<p style='color: green;'>✓ Tabel 'pemeriksaan_fisik' sudah ada</p>";
    } else {
        // Create pemeriksaan_fisik table
        $pdo->exec("
            CREATE TABLE pemeriksaan_fisik (
                id INT AUTO_INCREMENT PRIMARY KEY,
                no_registrasi VARCHAR(50) NOT NULL,
                
                -- Ibu fields
                ibu_keadaan_umum VARCHAR(255) DEFAULT NULL,
                ibu_sistolik_diastolik VARCHAR(50) DEFAULT NULL,
                ibu_nadi_pernapasan VARCHAR(50) DEFAULT NULL,
                ibu_suhu VARCHAR(20) DEFAULT NULL,
                ibu_skrining_nyeri VARCHAR(50) DEFAULT NULL,
                ibu_tinggi_badan VARCHAR(20) DEFAULT NULL,
                ibu_berat_badan VARCHAR(20) DEFAULT NULL,
                ibu_index_masa_tubuh VARCHAR(20) DEFAULT NULL,
                ibu_lingkar_kepala VARCHAR(20) DEFAULT NULL,
                ibu_pemeriksaan_fisik TEXT DEFAULT NULL,
                ibu_putting VARCHAR(255) DEFAULT NULL,
                ibu_areola VARCHAR(255) DEFAULT NULL,
                ibu_corpus_payudara VARCHAR(255) DEFAULT NULL,
                ibu_payudara_lainnya TEXT DEFAULT NULL,
                
                -- Bayi fields
                bayi_keadaan_umum VARCHAR(255) DEFAULT NULL,
                bayi_suhu VARCHAR(20) DEFAULT NULL,
                bayi_nadi VARCHAR(50) DEFAULT NULL,
                bayi_pernapasan VARCHAR(50) DEFAULT NULL,
                bayi_tonus_otot VARCHAR(255) DEFAULT NULL,
                bayi_kondisi_bibir VARCHAR(255) DEFAULT NULL,
                bayi_kondisi_rongga_mulut VARCHAR(255) DEFAULT NULL,
                bayi_kondisi_lidah VARCHAR(255) DEFAULT NULL,
                bayi_reflek_rooting VARCHAR(255) DEFAULT NULL,
                bayi_reflek_sucking VARCHAR(255) DEFAULT NULL,
                bayi_reflek_suckling VARCHAR(255) DEFAULT NULL,
                bayi_reflek_swallowing VARCHAR(255) DEFAULT NULL,
                bayi_lain_lain TEXT DEFAULT NULL,
                
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_no_registrasi (no_registrasi)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "<p style='color: green;'>✓ Tabel 'pemeriksaan_fisik' berhasil dibuat</p>";
    }

    // Create ibfat table if not exists
    $stmt = $pdo->query("
        SELECT COUNT(*) as count
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'ibfat'
    ");
    $result = $stmt->fetch();

    if ($result['count'] > 0) {
        echo "<p style='color: green;'>✓ Tabel 'ibfat' sudah ada</p>";
    } else {
        // Create ibfat table
        $pdo->exec("
            CREATE TABLE ibfat (
                id INT AUTO_INCREMENT PRIMARY KEY,
                no_registrasi VARCHAR(50) NOT NULL,
                posture_score TINYINT DEFAULT NULL,
                attachment_score TINYINT DEFAULT NULL,
                swallowing_score TINYINT DEFAULT NULL,
                positioning_score TINYINT DEFAULT NULL,
                total_score TINYINT DEFAULT 0,
                interpretasi VARCHAR(100) DEFAULT NULL,
                catatan TEXT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_no_registrasi (no_registrasi)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "<p style='color: green;'>✓ Tabel 'ibfat' berhasil dibuat</p>";
    }

    // Create pibbs table if not exists
    $stmt = $pdo->query("
        SELECT COUNT(*) as count
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'pibbs'
    ");
    $result = $stmt->fetch();

    if ($result['count'] > 0) {
        echo "<p style='color: green;'>✓ Tabel 'pibbs' sudah ada</p>";
    } else {
        // Create pibbs table
        $pdo->exec("
            CREATE TABLE pibbs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                no_registrasi VARCHAR(50) NOT NULL,
                mencari_puting_score TINYINT DEFAULT NULL,
                cakupan_areola_score TINYINT DEFAULT NULL,
                menempel_melekat_score TINYINT DEFAULT NULL,
                menghisap_score TINYINT DEFAULT NULL,
                menghisap_terpanjang_score TINYINT DEFAULT NULL,
                menelan_score TINYINT DEFAULT NULL,
                catatan TEXT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_no_registrasi (no_registrasi)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "<p style='color: green;'>✓ Tabel 'pibbs' berhasil dibuat</p>";
    }

    // Create bsessf table if not exists
    $stmt = $pdo->query("
        SELECT COUNT(*) as count
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'bsessf'
    ");
    $result = $stmt->fetch();

    if ($result['count'] > 0) {
        echo "<p style='color: green;'>✓ Tabel 'bsessf' sudah ada</p>";
    } else {
        // Create bsessf table
        $pdo->exec("
            CREATE TABLE bsessf (
                id INT AUTO_INCREMENT PRIMARY KEY,
                no_registrasi VARCHAR(50) NOT NULL,
                pertanyaan_1 TINYINT DEFAULT NULL,
                pertanyaan_2 TINYINT DEFAULT NULL,
                pertanyaan_3 TINYINT DEFAULT NULL,
                pertanyaan_4 TINYINT DEFAULT NULL,
                pertanyaan_5 TINYINT DEFAULT NULL,
                pertanyaan_6 TINYINT DEFAULT NULL,
                pertanyaan_7 TINYINT DEFAULT NULL,
                pertanyaan_8 TINYINT DEFAULT NULL,
                pertanyaan_9 TINYINT DEFAULT NULL,
                pertanyaan_10 TINYINT DEFAULT NULL,
                pertanyaan_11 TINYINT DEFAULT NULL,
                pertanyaan_12 TINYINT DEFAULT NULL,
                total_score TINYINT DEFAULT 0,
                interpretasi VARCHAR(100) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_no_registrasi (no_registrasi)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "<p style='color: green;'>✓ Tabel 'bsessf' berhasil dibuat</p>";
    }

    // Create hatlff table if not exists
    $stmt = $pdo->query("
        SELECT COUNT(*) as count
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'hatlff'
    ");
    $result = $stmt->fetch();

    if ($result['count'] > 0) {
        echo "<p style='color: green;'>✓ Tabel 'hatlff' sudah ada</p>";
    } else {
        // Create hatlff table
        $pdo->exec("
            CREATE TABLE hatlff (
                id INT AUTO_INCREMENT PRIMARY KEY,
                no_registrasi VARCHAR(50) NOT NULL,
                
                -- Fungsi scores
                gerak_samping TINYINT DEFAULT NULL,
                gerak_atas TINYINT DEFAULT NULL,
                gerak_memanjang TINYINT DEFAULT NULL,
                pelebaran_ujung TINYINT DEFAULT NULL,
                bentuk_mangkok TINYINT DEFAULT NULL,
                gerak_berirama TINYINT DEFAULT NULL,
                berdecak TINYINT DEFAULT NULL,
                
                -- Penampilan scores
                bentuk_lidah TINYINT DEFAULT NULL,
                elastisitas TINYINT DEFAULT NULL,
                panjang_frenulum TINYINT DEFAULT NULL,
                perlekatan_lidah TINYINT DEFAULT NULL,
                perlekatan_dasar TINYINT DEFAULT NULL,
                
                skor_fungsi TINYINT DEFAULT 0,
                skor_penampilan TINYINT DEFAULT 0,
                skor_total TINYINT DEFAULT 0,
                interpretasi TEXT DEFAULT NULL,
                
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_no_registrasi (no_registrasi)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "<p style='color: green;'>✓ Tabel 'hatlff' berhasil dibuat</p>";
    }

    echo "<hr>";
    echo "<h3 style='color: green;'>✓ Database sudah siap!</h3>";
    echo "<p><a href='index.php'>Klik disini untuk ke Dashboard</a></p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
