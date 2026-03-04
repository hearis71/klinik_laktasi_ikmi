<?php
/**
 * Setup/Update Database Script
 * Klinik Laktasi - Add missing columns to existing database
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
    
    echo "<hr>";
    echo "<h3 style='color: green;'>✓ Database sudah siap!</h3>";
    echo "<p><a href='index.php'>Klik disini untuk ke Dashboard</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
