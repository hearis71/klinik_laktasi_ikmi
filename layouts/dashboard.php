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
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        <?php include __DIR__ . '/../components/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include __DIR__ . '/../components/header.php'; ?>
            
            <main class="page-content">
                <?php echo $content ?? ''; ?>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    if (window.innerWidth > 768) {
                        // Desktop: Collapse/Expand
                        sidebar.classList.toggle('toggled');
                    } else {
                        // Mobile: Show/Hide
                        sidebar.classList.add('active');
                        sidebarOverlay.classList.add('active');
                        document.body.style.overflow = 'hidden';
                    }
                });
            }

            if (sidebarClose) {
                sidebarClose.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                    document.body.style.overflow = 'auto';
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                    document.body.style.overflow = 'auto';
                });
            }

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });
        });
    </script>
</body>
</html>
