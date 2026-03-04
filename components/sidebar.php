<?php
/**
 * Sidebar Component
 * Klinik Laktasi - Dashboard Sidebar Navigation
 */

if (!defined('KLINIK_LAKTASI')) {
    die('Direct access not allowed');
}

// Load config if not already loaded
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/config.php';
}

$currentPage = $currentPage ?? 'dashboard';
$expandedMenu = $expandedMenu ?? null;

$menuItems = [
    ['id' => 'dashboard', 'icon' => '🏠', 'label' => 'Dashboard', 'path' => baseUrl('index.php')],
    // ['id' => 'tarif', 'icon' => '🏷️', 'label' => 'Management Tarif', 'path' => baseUrl('pages/tarif.php')],
    // ['id' => 'obat', 'icon' => '💊', 'label' => 'Management Obat & BHP', 'path' => baseUrl('pages/obat.php')],
    ['id' => 'pasien', 'icon' => '👥', 'label' => 'Daftar Pasien', 'path' => baseUrl('pages/pasien.php')],
    // ['id' => 'registrasi', 'icon' => '📋', 'label' => 'Registrasi', 'path' => baseUrl('pages/registrasi.php')],
    ['id' => 'antrian', 'icon' => '⏰', 'label' => 'Antrean Perawatan', 'path' => baseUrl('pages/antrian.php')],

    [
        'id' => 'formulir',
        'icon' => '📝',
        'label' => 'Formulir',
        'hasSubmenu' => true,
        'submenu' => [
            ['id' => 'alasan-konsultasi', 'label' => 'Alasan Konsultasi', 'path' => baseUrl('pages/formulir/alasan-konsultasi.php')],
            ['id' => 'kajian-riwayat', 'label' => 'Kajian Riwayat Menyusui', 'path' => baseUrl('pages/formulir/kajian-riwayat-menyusui.php')],
            ['id' => 'pemeriksaan-fisik', 'label' => 'Pemeriksaan Fisik Bayi', 'path' => baseUrl('pages/formulir/pemeriksaan-fisik-bayi.php')],
            ['id' => 'latch', 'label' => 'LATCH', 'path' => baseUrl('pages/formulir/latch.php')],
            ['id' => 'ibfat', 'label' => 'IBFAT', 'path' => baseUrl('pages/formulir/ibfat.php')],
            ['id' => 'pibbs', 'label' => 'PIBBS', 'path' => baseUrl('pages/formulir/pibbs.php')],
            ['id' => 'bses-sf', 'label' => 'BSES-SF', 'path' => baseUrl('pages/formulir/bses-sf.php')],
            ['id' => 'hatlff', 'label' => 'HATLFF', 'path' => baseUrl('pages/formulir/hatlff.php')],
            ['id' => 'identifikasi-masalah', 'label' => 'Identifikasi Masalah', 'path' => baseUrl('pages/formulir/identifikasi-masalah.php')],
            ['id' => 'assesment', 'label' => 'Assesment', 'path' => baseUrl('pages/formulir/assesment.php')],
            ['id' => 'intervensi-edukasi', 'label' => 'Intervensi & Edukasi', 'path' => baseUrl('pages/formulir/intervensi-edukasi.php')],
            ['id' => 'rencana-followup', 'label' => 'Rencana Follow Up/ Rujukan', 'path' => baseUrl('pages/formulir/rencana-followup.php')],
        ]
    ],


    ['id' => 'rekam', 'icon' => '📄', 'label' => 'Rekam Medis', 'path' => baseUrl('pages/rekam.php')],
    // ['id' => 'farmasi', 'icon' => '🧪', 'label' => 'Farmasi', 'path' => baseUrl('pages/farmasi.php')],
    // ['id' => 'pembayaran', 'icon' => '💰', 'label' => 'Pembayaran', 'path' => baseUrl('pages/pembayaran.php')],
    ['id' => 'kunjungan', 'icon' => '📊', 'label' => 'Riwayat Kunjungan', 'path' => baseUrl('pages/kunjungan.php')],
    // ['id' => 'komisi', 'icon' => '📈', 'label' => 'Komisi', 'path' => baseUrl('pages/komisi.php')],
    
    [
        'id' => 'account',
        'icon' => '⚙️',
        'label' => 'Account Setting',
        'hasSubmenu' => true,
        'submenu' => [
            ['id' => 'profile', 'label' => 'Profil', 'path' => '#'],
            ['id' => 'security', 'label' => 'Keamanan', 'path' => '#'],
        ]
    ],

    [
        'id' => 'settings',
        'icon' => '🔧',
        'label' => 'Setting Management',
        'hasSubmenu' => true,
        'submenu' => [
            ['id' => 'clinic', 'label' => 'Pengaturan Klinik', 'path' => baseUrl('pages/setting.php')],
            ['id' => 'user', 'label' => 'Manajemen User', 'path' => baseUrl('pages/manajemen-user.php')],
        ]
    ],
    ['id' => 'setting', 'icon' => '🔧', 'label' => 'Setting', 'path' => baseUrl('pages/setting.php')],
];
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <h1 class="logo">IKMI care+</h1>
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($menuItems as $item): ?>
            <div class="menu-item-wrapper">
                <?php if (isset($item['hasSubmenu']) && $item['hasSubmenu']): ?>
                    <button
                        class="menu-item <?php echo $currentPage === $item['id'] ? 'active' : ''; ?>"
                        onclick="toggleSubmenu('<?php echo $item['id']; ?>')"
                    >
                        <span class="menu-icon"><?php echo $item['icon']; ?></span>
                        <span class="menu-label"><?php echo $item['label']; ?></span>
                        <span class="submenu-arrow <?php echo $expandedMenu === $item['id'] ? 'rotated' : ''; ?>" id="arrow-<?php echo $item['id']; ?>">
                            ▶
                        </span>
                    </button>

                    <div class="submenu <?php echo $expandedMenu === $item['id'] ? 'open' : ''; ?>" id="submenu-<?php echo $item['id']; ?>">
                        <div class="submenu-inner">
                        <?php foreach ($item['submenu'] as $subItem): ?>
                            <a href="<?php echo $subItem['path']; ?>" class="submenu-item">
                                <?php echo $subItem['label']; ?>
                            </a>
                        <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo $item['path']; ?>" class="menu-item <?php echo $currentPage === $item['id'] ? 'active' : ''; ?>">
                        <span class="menu-icon"><?php echo $item['icon']; ?></span>
                        <span class="menu-label"><?php echo $item['label']; ?></span>
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </nav>
</aside>

<script>
function toggleSubmenu(menuId) {
    const submenu = document.getElementById('submenu-' + menuId);
    const arrow = document.getElementById('arrow-' + menuId);
    
    if (submenu.classList.contains('open')) {
        submenu.classList.remove('open');
        arrow.classList.remove('rotated');
    } else {
        submenu.classList.add('open');
        arrow.classList.add('rotated');
    }
}
</script>
