<?php
/**
 * Setting Page
 * Klinik Laktasi - Clinic Settings
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireAuth();

$currentPage = 'setting';
$breadcrumbTitle = 'SETTING';
$pageTitle = 'Pengaturan Klinik';

ob_start();
?>

<div class="page-container">
    <h1>Setting</h1>
    <p>Halaman pengaturan klinik</p>

    <div class="table-card">
        <div class="table-header">
            <h3>Pengaturan Umum</h3>
        </div>

        <form method="POST" action="" class="modal-bootstrap-body">
            <div class="form-group">
                <label for="nama_klinik">Nama Klinik</label>
                <input
                    type="text"
                    id="nama_klinik"
                    name="nama_klinik"
                    value="Klinik Laktasi IKMI"
                    class="form-control-rounded"
                />
            </div>

            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea
                    id="alamat"
                    name="alamat"
                    rows="3"
                    class="form-control-rounded"
                ></textarea>
            </div>

            <div class="form-group">
                <label for="telepon">No. Telepon</label>
                <input
                    type="text"
                    id="telepon"
                    name="telepon"
                    class="form-control-rounded"
                />
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control-rounded"
                />
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary-rounded">
                    <span class="btn-icon">💾</span>
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
