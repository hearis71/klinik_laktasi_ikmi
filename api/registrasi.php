<?php
/**
 * Registrasi API Endpoint
 * Klinik Laktasi - Registration Management API
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
}

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDbConnection();

// Handle GET requests
if ($method === 'GET') {
    $id = $_GET['id'] ?? null;
    $no_registrasi = $_GET['no_registrasi'] ?? null;
    
    if ($id) {
        // Get by ID
        $stmt = $pdo->prepare("
            SELECT r.*, p.no_rm, p.nama as nama_pasien
            FROM registrasi r
            LEFT JOIN pasien p ON r.pasien_id = p.id
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        
        if ($data) {
            jsonResponse(['success' => true, 'data' => $data]);
        } else {
            jsonResponse(['success' => false, 'error' => 'Data tidak ditemukan'], 404);
        }
    } elseif ($no_registrasi) {
        // Get by no_registrasi
        $stmt = $pdo->prepare("
            SELECT r.*, p.no_rm, p.nama as nama_pasien
            FROM registrasi r
            LEFT JOIN pasien p ON r.pasien_id = p.id
            WHERE r.no_registrasi = ?
        ");
        $stmt->execute([$no_registrasi]);
        $data = $stmt->fetch();
        
        if ($data) {
            jsonResponse(['success' => true, 'data' => $data]);
        } else {
            jsonResponse(['success' => false, 'error' => 'Data tidak ditemukan'], 404);
        }
    } else {
        // Get all
        $stmt = $pdo->query("
            SELECT r.*, p.no_rm, p.nama as nama_pasien
            FROM registrasi r
            LEFT JOIN pasien p ON r.pasien_id = p.id
            ORDER BY r.tanggal_pengkajian DESC, r.waktu_pengkajian DESC
        ");
        $data = $stmt->fetchAll();
        jsonResponse(['success' => true, 'data' => $data]);
    }
}

// Handle POST requests
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'delete':
            handleDelete($pdo, $input);
            break;
        default:
            handleCreateOrUpdate($pdo, $input);
    }
}

function handleCreateOrUpdate($pdo, $data) {
    $id = $data['id'] ?? null;
    $pasien_id = $data['pasien_id'] ?? null;
    $no_rm = $data['no_rm'] ?? null;
    $tanggal_pengkajian = sanitize($data['tanggal_pengkajian']);
    $waktu_pengkajian = sanitize($data['waktu_pengkajian']);
    $nama_ibu = sanitize($data['nama_ibu']);
    $tanggal_lahir_ibu = sanitize($data['tanggal_lahir_ibu']);
    $usia_ibu = sanitize($data['usia_ibu'] ?? null);
    $nama_bayi = sanitize($data['nama_bayi'] ?? null);
    $tanggal_lahir_bayi = sanitize($data['tanggal_lahir_bayi'] ?? null);
    $usia_bayi = sanitize($data['usia_bayi'] ?? null);
    
    if (empty($nama_ibu) || empty($tanggal_lahir_ibu)) {
        jsonResponse(['success' => false, 'error' => 'Data ibu wajib diisi'], 400);
    }
    
    try {
        // Get pasien_id from no_rm if not provided
        if (!$pasien_id && $no_rm) {
            $stmt = $pdo->prepare("SELECT id FROM pasien WHERE no_rm = ?");
            $stmt->execute([$no_rm]);
            $pasien = $stmt->fetch();
            if ($pasien) {
                $pasien_id = $pasien['id'];
            }
        }
        
        if ($id) {
            // Update
            $stmt = $pdo->prepare("
                UPDATE registrasi 
                SET tanggal_pengkajian = ?, waktu_pengkajian = ?,
                    nama_ibu = ?, tanggal_lahir_ibu = ?, usia_ibu = ?,
                    nama_bayi = ?, tanggal_lahir_bayi = ?, usia_bayi = ?,
                    pasien_id = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $tanggal_pengkajian, $waktu_pengkajian,
                $nama_ibu, $tanggal_lahir_ibu, $usia_ibu,
                $nama_bayi, $tanggal_lahir_bayi, $usia_bayi,
                $pasien_id, $id
            ]);
            jsonResponse(['success' => true, 'message' => 'Registrasi berhasil diupdate']);
        } else {
            // Create
            $no_registrasi = generateRegistrationNumber();
            $stmt = $pdo->prepare("
                INSERT INTO registrasi (no_registrasi, pasien_id, tanggal_pengkajian, waktu_pengkajian,
                    nama_ibu, tanggal_lahir_ibu, usia_ibu, nama_bayi, tanggal_lahir_bayi, usia_bayi)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $no_registrasi, $pasien_id,
                $tanggal_pengkajian, $waktu_pengkajian,
                $nama_ibu, $tanggal_lahir_ibu, $usia_ibu,
                $nama_bayi, $tanggal_lahir_bayi, $usia_bayi
            ]);
            jsonResponse([
                'success' => true,
                'message' => 'Registrasi berhasil disimpan',
                'no_registrasi' => $no_registrasi,
                'id' => dbLastInsertId()
            ]);
        }
    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Gagal menyimpan data'], 500);
    }
}

function handleDelete($pdo, $data) {
    $id = $data['id'] ?? null;
    
    if (empty($id)) {
        jsonResponse(['success' => false, 'error' => 'ID tidak valid'], 400);
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM registrasi WHERE id = ?");
        $stmt->execute([$id]);
        jsonResponse(['success' => true, 'message' => 'Registrasi berhasil dihapus']);
    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Gagal menghapus registrasi'], 500);
    }
}
