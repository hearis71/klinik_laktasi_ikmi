<?php
/**
 * Formulir Layout
 * Layout for assessment forms
 */

if (!defined('KLINIK_LAKTASI')) {
    die('Direct access not allowed');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Formulir'; ?> - Klinik Laktasi</title>
    <link rel="stylesheet" href="<?php echo baseUrl('assets/css/style.css'); ?>">
</head>
<body>
    <div class="formulir-layout-container">
        <!-- Header Formulir -->
        <div class="formulir-layout-header">
            <div class="formulir-layout-title">
                <h2><?php echo $pageTitle ?? 'Formulir Pengkajian'; ?></h2>
            </div>
            <div class="formulir-layout-actions">
                <a href="<?php echo baseUrl('pages/antrian.php'); ?>" class="btn btn-secondary btn-sm">
                    ← Kembali ke Antrian
                </a>
            </div>
        </div>

        <!-- Data Ibu & Bayi Header -->
        <?php include __DIR__ . '/../pages/formulir/formulir_header.php'; ?>

        <!-- Main Content -->
        <div class="formulir-layout-content">
            <?php echo $content ?? ''; ?>
        </div>
    </div>
</body>
</html>