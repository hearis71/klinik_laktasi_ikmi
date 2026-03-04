<?php
/**
 * Header Component
 * Klinik Laktasi - Dashboard Header
 */

if (!defined('KLINIK_LAKTASI')) {
    die('Direct access not allowed');
}

// Load config if not already loaded
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/config.php';
}

$currentDate = getCurrentDateIndonesian();
$user = getCurrentUser();
?>
<header class="header">
    <div class="header-left">
        <div class="breadcrumb">
            <span class="breadcrumb-item">BERANDA</span>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-item active"><?php echo $breadcrumbTitle ?? 'DASHBOARD'; ?></span>
        </div>
        <h2 class="page-title"><?php echo $pageTitle ?? 'Dashboard Klinik Laktasi'; ?></h2>
    </div>

    <div class="header-right">
        <div class="current-date">
            <strong>Hari ini, <?php echo $currentDate; ?></strong>
        </div>

        <button class="notification-btn">
            <span>🔔</span>
            <span class="notification-badge">3</span>
        </button>

        <div class="user-menu">
            <span style="font-size: 32px;">👤</span>
            <span class="user-name"><?php echo htmlspecialchars($user['nama'] ?? 'User'); ?></span>
            <a href="<?php echo baseUrl('logout.php'); ?>" class="logout-btn" title="Logout">
                <span>🚪</span>
            </a>
            <span style="font-size: 12px;">▼</span>
        </div>
    </div>
</header>
