<?php
/**
 * Pasien API Endpoint
 * Klinik Laktasi - Patient Management API
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

// Require authentication
if (!isLoggedIn()) {
    jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

$pdo = getDbConnection();

// Handle GET requests
if ($method === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM pasien ORDER BY created_at DESC");
        $pasien = $stmt->fetchAll();
        jsonResponse(['success' => true, 'data' => $pasien]);
    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Database error'], 500);
    }
}

// Handle POST requests
if ($method === 'POST') {
    // Check if JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if ($input) {
        $action = $input['action'] ?? '';
    } else {
        $action = $_POST['action'] ?? '';
    }
    
    switch ($action) {
        case 'create':
            handleCreate($pdo, $input ?? $_POST);
            break;
        case 'update':
            handleUpdate($pdo, $input ?? $_POST);
            break;
        case 'delete':
            handleDelete($pdo, $input ?? $_POST);
            break;
        default:
            // Handle form submission without action
            handleCreate($pdo, $input ?? $_POST);
    }
}

// Handle PUT requests (for update)
if ($method === 'PUT') {
    parse_str(file_get_contents('php://input'), $input);
    handleUpdate($pdo, $input);
}

// Handle DELETE requests
if ($method === 'DELETE') {
    parse_str(file_get_contents('php://input'), $input);
    handleDelete($pdo, $input);
}

function handleCreate($pdo, $data) {
    $nik = sanitize($data['nik'] ?? null);
    $nama = sanitize($data['nama']);
    $tanggal_lahir = sanitize($data['tanggal_lahir']);
    $alamat = sanitize($data['alamat'] ?? null);
    $no_hp = sanitize($data['no_hp'] ?? null);
    
    if (empty($nama) || empty($tanggal_lahir)) {
        jsonResponse(['success' => false, 'error' => 'Nama dan tanggal lahir wajib diisi'], 400);
    }
    
    try {
        // Generate No. RM
        $no_rm = generateMedicalRecordNumber();
        
        $stmt = $pdo->prepare("
            INSERT INTO pasien (no_rm, nik, nama, tanggal_lahir, alamat, no_hp)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([$no_rm, $nik, $nama, $tanggal_lahir, $alamat, $no_hp]);
        $id = dbLastInsertId();
        
        jsonResponse([
            'success' => true,
            'message' => 'Pasien berhasil ditambahkan',
            'no_rm' => $no_rm,
            'id' => $id
        ]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            jsonResponse(['success' => false, 'error' => 'NIK sudah terdaftar'], 400);
        }
        jsonResponse(['success' => false, 'error' => 'Gagal menambahkan pasien'], 500);
    }
}

function handleUpdate($pdo, $data) {
    $id = $data['id'] ?? null;
    $nik = sanitize($data['nik'] ?? null);
    $nama = sanitize($data['nama']);
    $tanggal_lahir = sanitize($data['tanggal_lahir']);
    $alamat = sanitize($data['alamat'] ?? null);
    $no_hp = sanitize($data['no_hp'] ?? null);
    
    if (empty($id) || empty($nama) || empty($tanggal_lahir)) {
        jsonResponse(['success' => false, 'error' => 'Data tidak lengkap'], 400);
    }
    
    try {
        $stmt = $pdo->prepare("
            UPDATE pasien 
            SET nik = ?, nama = ?, tanggal_lahir = ?, alamat = ?, no_hp = ?
            WHERE id = ?
        ");
        
        $stmt->execute([$nik, $nama, $tanggal_lahir, $alamat, $no_hp, $id]);
        
        jsonResponse(['success' => true, 'message' => 'Data pasien berhasil diupdate']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            jsonResponse(['success' => false, 'error' => 'NIK sudah terdaftar'], 400);
        }
        jsonResponse(['success' => false, 'error' => 'Gagal mengupdate data pasien'], 500);
    }
}

function handleDelete($pdo, $data) {
    $id = $data['id'] ?? null;
    
    if (empty($id)) {
        jsonResponse(['success' => false, 'error' => 'ID pasien tidak valid'], 400);
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM pasien WHERE id = ?");
        $stmt->execute([$id]);
        
        jsonResponse(['success' => true, 'message' => 'Pasien berhasil dihapus']);
    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Gagal menghapus pasien'], 500);
    }
}
