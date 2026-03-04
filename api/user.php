<?php
/**
 * User API Endpoint
 * Klinik Laktasi - User Management API
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !isAdmin()) {
    jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
}

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDbConnection();

// Handle GET requests
if ($method === 'GET') {
    $stmt = $pdo->query("SELECT id, nama, email, role, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
    jsonResponse(['success' => true, 'data' => $users]);
}

// Handle POST requests
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'create':
            handleCreate($pdo, $input);
            break;
        case 'delete':
            handleDelete($pdo, $input);
            break;
    }
}

function handleCreate($pdo, $data) {
    $nama = sanitize($data['nama']);
    $email = sanitize($data['email']);
    $password = $data['password'];
    $role = sanitize($data['role'] ?? 'medis');
    
    if (empty($nama) || empty($email) || empty($password)) {
        jsonResponse(['success' => false, 'error' => 'Semua field wajib diisi'], 400);
    }
    
    if (strlen($password) < 6) {
        jsonResponse(['success' => false, 'error' => 'Password minimal 6 karakter'], 400);
    }
    
    try {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            jsonResponse(['success' => false, 'error' => 'Email sudah terdaftar'], 400);
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO users (nama, email, password, role)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$nama, $email, $hashedPassword, $role]);
        
        jsonResponse([
            'success' => true,
            'message' => 'User berhasil ditambahkan',
            'id' => dbLastInsertId()
        ]);
    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Gagal menambahkan user'], 500);
    }
}

function handleDelete($pdo, $data) {
    $id = $data['id'] ?? null;
    
    if (empty($id)) {
        jsonResponse(['success' => false, 'error' => 'ID tidak valid'], 400);
    }
    
    // Prevent deleting yourself
    if ($id == getCurrentUserId()) {
        jsonResponse(['success' => false, 'error' => 'Tidak dapat menghapus user yang sedang login'], 400);
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        jsonResponse(['success' => true, 'message' => 'User berhasil dihapus']);
    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Gagal menghapus user'], 500);
    }
}
