<?php
/**
 * 404 Error Page
 * Klinik Laktasi - Page Not Found
 */

define('KLINIK_LAKTASI', true);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <link rel="stylesheet" href="/assets/css/style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            text-align: center;
            color: white;
        }
        .error-content h1 {
            font-size: 120px;
            font-weight: 700;
            margin: 0;
            line-height: 1;
        }
        .error-content h2 {
            font-size: 32px;
            margin: 16px 0;
        }
        .error-content p {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 24px;
        }
        .btn-home {
            display: inline-block;
            padding: 12px 32px;
            background: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-content">
            <h1>404</h1>
            <h2>Halaman Tidak Ditemukan</h2>
            <p>Maaf, halaman yang Anda cari tidak dapat ditemukan.</p>
            <a href="/index.php" class="btn-home">Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>
