<?php
/**
 * Dashboard Layout
 * Klinik Laktasi - Main Dashboard Layout Template
 */

if (!defined('KLINIK_LAKTASI')) {
    die('Direct access not allowed');
}

// Load config if not already loaded
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/config.php';
}

requireAuth();

$breadcrumbTitle = $breadcrumbTitle ?? 'DASHBOARD';
$pageTitle = $pageTitle ?? 'Dashboard Klinik Laktasi';
$currentPage = $currentPage ?? 'dashboard';
$expandedMenu = $expandedMenu ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Klinik Laktasi</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <?php include __DIR__ . '/../components/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include __DIR__ . '/../components/header.php'; ?>
            
            <main class="page-content">
                <?php echo $content ?? ''; ?>
            </main>
        </div>
    </div>
</body>
</html>
