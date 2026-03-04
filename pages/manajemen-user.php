<?php
/**
 * Manajemen User Page
 * Klinik Laktasi - User Management
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireAuth();

// Only admin can access
if (!isAdmin()) {
    redirect('/index.php', 'Akses ditolak. Hanya administrator yang dapat mengakses halaman ini.', 'error');
}

$currentPage = 'settings';
$expandedMenu = 'settings';
$breadcrumbTitle = 'SETTING';
$pageTitle = 'Manajemen User';

$pdo = getDbConnection();

// Fetch users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

ob_start();
?>

<div class="page-container">
    <div class="page-header">
        <h1>
            <span class="page-icon">👥</span>
            Manajemen User
        </h1>
        <button class="btn btn-primary" onclick="openAddModal()">
            <span class="btn-icon">➕</span>
            Tambah User Baru
        </button>
    </div>

    <?php 
    $flash = getFlashMessage();
    if ($flash): 
    ?>
        <div class="alert alert-<?php echo $flash['type']; ?>">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>

    <div class="table-card">
        <div class="table-header">
            <h5>Daftar User</h5>
            <div class="table-controls">
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Cari user..."
                    class="search-input"
                    onkeyup="filterTable()"
                />
            </div>
        </div>

        <div class="table-responsive">
            <table id="usersTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="text-center">Belum ada user</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($users as $index => $user): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td class="fw-medium"><?php echo htmlspecialchars($user['nama']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge <?php echo $user['role'] === 'ADMIN' ? 'bg-danger' : 'bg-primary'; ?>">
                                    <?php echo htmlspecialchars($user['role']); ?>
                                </span>
                            </td>
                            <td><?php echo formatDateIndonesian($user['created_at']); ?></td>
                            <td class="text-center">
                                <button
                                    class="btn btn-sm btn-danger"
                                    onclick="confirmDelete(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['nama']); ?>')"
                                    title="Hapus User"
                                    <?php echo $user['id'] == getCurrentUserId() ? 'disabled' : ''; ?>
                                >
                                    🗑️
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add User -->
<div id="userModal" class="modal-bootstrap-overlay" style="display: none;">
    <div class="modal-bootstrap" onclick="event.stopPropagation()">
        <div class="modal-bootstrap-header">
            <h5 class="modal-title">
                <span class="modal-icon-primary">👤</span>
                Tambah User Baru
            </h5>
            <button class="btn-close-modal" onclick="closeModal()">
                <span>&times;</span>
            </button>
        </div>

        <form id="userForm" method="POST" action="/api/user.php" class="modal-bootstrap-body">
            <input type="hidden" name="action" value="create">
            
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Lengkap</label>
                <input
                    type="text"
                    id="nama"
                    name="nama"
                    placeholder="Masukkan nama lengkap"
                    class="form-control-rounded"
                    required
                />
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="contoh@email.com"
                    class="form-control-rounded"
                    required
                />
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Minimal 6 karakter"
                    class="form-control-rounded"
                    minlength="6"
                    required
                />
            </div>

            <div class="mb-0">
                <label for="role" class="form-label">Role</label>
                <select id="role" name="role" class="form-select">
                    <option value="medis">Medis</option>
                    <option value="ADMIN">Admin</option>
                </select>
            </div>

            <div class="modal-bootstrap-footer">
                <button type="button" class="btn btn-secondary-rounded" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary-rounded">
                    <span class="btn-icon">💾</span>
                    Simpan User
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-bootstrap-overlay modal-bootstrap-sm" style="display: none;">
    <div class="modal-bootstrap" onclick="event.stopPropagation()">
        <div class="modal-bootstrap-header modal-header-danger">
            <h5 class="modal-title">
                <span class="modal-icon-danger">🗑️</span>
                Konfirmasi Hapus
            </h5>
            <button class="btn-close-modal" onclick="closeDeleteModal()">
                <span>&times;</span>
            </button>
        </div>

        <div class="modal-bootstrap-body text-center">
            <div class="confirm-icon-wrapper">
                <span style="font-size: 64px;">⚠️</span>
            </div>
            <p class="confirm-message">Apakah Anda yakin ingin menghapus user ini?</p>
            <div class="confirm-info-card">
                <div class="confirm-patient-name" id="deleteUserName">-</div>
            </div>
            <p class="confirm-warning">
                <span class="warning-icon">⚠️</span>
                Data yang dihapus tidak dapat dikembalikan.
            </p>
        </div>

        <div class="modal-bootstrap-footer">
            <button type="button" class="btn btn-secondary-rounded" onclick="closeDeleteModal()">Batal</button>
            <button type="button" class="btn btn-danger-rounded" onclick="deleteUser()">
                <span class="btn-icon">🗑️</span>
                Ya, Hapus
            </button>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?php echo BASE_URL; ?>';
let deleteId = null;

function openAddModal() {
    document.getElementById('userForm').reset();
    document.getElementById('userModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('userModal').style.display = 'none';
}

function confirmDelete(id, name) {
    deleteId = id;
    document.getElementById('deleteUserName').textContent = name;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    deleteId = null;
}

function deleteUser() {
    if (!deleteId) return;

    fetch(BASE_URL + '/api/user.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'delete', id: deleteId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Gagal menghapus: ' + data.error);
        }
    })
    .catch(err => {
        alert('Terjadi kesalahan pada server');
    });
}

function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('usersTable');
    const tr = table.getElementsByTagName('tr');
    
    for (let i = 1; i < tr.length; i++) {
        const tdName = tr[i].getElementsByTagName('td')[1];
        const tdEmail = tr[i].getElementsByTagName('td')[2];
        const tdRole = tr[i].getElementsByTagName('td')[3];
        
        if (tdName || tdEmail || tdRole) {
            const txtValue = (tdName.textContent || tdName.innerText) + ' ' +
                           (tdEmail.textContent || tdEmail.innerText) + ' ' +
                           (tdRole.textContent || tdRole.innerText);
            tr[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
        }
    }
}

document.getElementById('userModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
