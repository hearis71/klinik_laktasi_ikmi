<?php
/**
 * Dashboard - Main Page
 * Klinik Laktasi - Dashboard Overview
 */

define('KLINIK_LAKTASI', true);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

requireAuth();

$currentPage = 'dashboard';
$breadcrumbTitle = 'DASHBOARD';
$pageTitle = 'Dashboard Klinik Laktasi';

// Get statistics from database
$pdo = getDbConnection();

// Total patients today
$stmt = $pdo->query("SELECT COUNT(*) as total FROM registrasi WHERE DATE(tanggal_pengkajian) = CURDATE()");
$totalAntrianHariIni = $stmt->fetch()['total'] ?? 0;

// Total doctors/medics
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'medis'");
$totalDokter = $stmt->fetch()['total'] ?? 0;

// Get visit data for chart (current year)
$currentYear = date('Y');
$stmt = $pdo->prepare("
    SELECT 
        MONTH(tanggal_pengkajian) as month,
        COUNT(*) as visits
    FROM registrasi 
    WHERE YEAR(tanggal_pengkajian) = ?
    GROUP BY MONTH(tanggal_pengkajian)
    ORDER BY MONTH(tanggal_pengkajian)
");
$stmt->execute([$currentYear]);
$visitData = $stmt->fetchAll();

// Format visit data for chart
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$visitChartData = [];
for ($i = 1; $i <= 12; $i++) {
    $found = false;
    foreach ($visitData as $row) {
        if ($row['month'] == $i) {
            $visitChartData[] = ['month' => $months[$i - 1], 'visits' => (int)$row['visits']];
            $found = true;
            break;
        }
    }
    if (!$found) {
        $visitChartData[] = ['month' => $months[$i - 1], 'visits' => 0];
    }
}

// Queue status data
try {
    $stmt = $pdo->query("
        SELECT
            SUM(CASE WHEN status = 'menunggu' THEN 1 ELSE 0 END) as menunggu,
            SUM(CASE WHEN status = 'konsultasi' THEN 1 ELSE 0 END) as konsultasi,
            SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai
        FROM registrasi
        WHERE DATE(tanggal_pengkajian) = CURDATE()
    ");
    $queueStatus = $stmt->fetch();
} catch (PDOException $e) {
    // Fallback if status column doesn't exist yet
    $queueStatus = ['menunggu' => 0, 'konsultasi' => 0, 'selesai' => 0];
}

$queueStatusData = [
    ['name' => 'Menunggu', 'value' => (int)($queueStatus['menunggu'] ?? 0), 'color' => '#FFB800'],
    ['name' => 'Konsultasi', 'value' => (int)($queueStatus['konsultasi'] ?? 0), 'color' => '#3B82F6'],
    ['name' => 'Selesai', 'value' => (int)($queueStatus['selesai'] ?? 0), 'color' => '#A78BFA'],
];

// Top 10 Diagnosis (placeholder data - implement when diagnosis table exists)
$diagnosisData = [
    ['kode' => 'A010', 'nama' => 'Demam Tifoid', 'h0_7' => ['l' => 1, 'p' => 2], 'h8_28' => ['l' => 3, 'p' => 4], 'h29_1th' => ['l' => 5, 'p' => 6], 'th1_4' => ['l' => 7, 'p' => 8], 'th5_14' => ['l' => 9, 'p' => 10], 'jumlah' => ['l' => 81, 'p' => 90], 'total' => 171],
    ['kode' => 'A011', 'nama' => 'Demam Paratifoid A', 'h0_7' => ['l' => 1, 'p' => 2], 'h8_28' => ['l' => 3, 'p' => 4], 'h29_1th' => ['l' => 5, 'p' => 6], 'th1_4' => ['l' => 7, 'p' => 8], 'th5_14' => ['l' => 9, 'p' => 10], 'jumlah' => ['l' => 81, 'p' => 90], 'total' => 171],
];

// Top 10 Tindakan (placeholder data)
$tindakanData = [
    ['kode' => 'A010', 'nama' => 'Konseling Laktasi', 'jumlah' => 205],
    ['kode' => 'A011', 'nama' => 'Pemeriksaan Fisik', 'jumlah' => 109],
];

ob_start();
?>

<div class="dashboard-page">
    <div class="dashboard-content">
        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <div class="welcome-content">
                <h3>Hallo, <?php echo htmlspecialchars(getCurrentUserName()); ?>!</h3>
                <p>
                    Selamat datang di Aplikasi Manajemen Klinik Laktasi,
                    <br />
                    by : Ikatan Konselor Menyusui Indonesia (IKMI)
                </p>
            </div>
            <div class="welcome-illustration">
                <img src="/assets/images/love.png" alt="Medical Team" onerror="this.style.display='none'" />
            </div>
        </div>

        <!-- Profile Card -->
        <div class="profile-card">
            <h3>Profil</h3>
            <div class="profile-content">
                <span class="profile-icon">🏥</span>
                <div>
                    <p class="profile-name">Klinik Laktasi</p>
                    <p class="profile-type">Umum</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-content">
                <h3 class="stat-title">Total Antrean Pasien Hari Ini</h3>
                <p class="stat-value"><?php echo $totalAntrianHariIni; ?></p>
            </div>
            <span class="stat-icon">👨‍⚕️</span>
        </div>
        <div class="stat-card teal">
            <div class="stat-content">
                <h3 class="stat-title">Jumlah Konselor</h3>
                <p class="stat-value"><?php echo $totalDokter; ?></p>
            </div>
            <span class="stat-icon">👩‍⚕️</span>
        </div>
        <div class="stat-card purple">
            <div class="stat-content">
                <h3 class="stat-title">Menu 3</h3>
                <p class="stat-value">-</p>
            </div>
        </div>
        <div class="stat-card indigo">
            <div class="stat-content">
                <h3 class="stat-title">Menu 4</h3>
                <p class="stat-value">-</p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <!-- Visit Chart -->
        <div class="chart-card">
            <h3 class="chart-title">Total Kunjungan Pasien</h3>
            <p class="chart-subtitle">tahun <?php echo $currentYear; ?></p>
            <!-- <canvas id="visitChart" height="250"></canvas> -->
        </div>

        <!-- Queue Status -->
        <div class="chart-card">
            <h3 class="chart-title">Status Antrean Pasien</h3>
            <canvas id="queueChart" height="250"></canvas>
            <div class="pie-center">
                <p class="pie-percentage">Overall<br />
                    <?php 
                    $total = array_sum(array_column($queueStatusData, 'value'));
                    echo $total > 0 ? round(($queueStatusData[2]['value'] / $total) * 100) : 0; 
                    ?>%
                </p>
            </div>
        </div>

        <!-- Package Info -->
        <div class="package-card">
            <div class="package-header">
                <h3>Menu 6</h3>
                <p class="package-validity">(-)</p>
            </div>
            <div class="package-content">
                <div class="package-item">
                    <span class="package-label">Jenis Add On</span>
                    <div class="addon-list">
                        <div>WhatsApp <span>232</span></div>
                        <div>SMS <span>123</span></div>
                    </div>
                </div>
                <button class="btn-extend">Chat Lebih Lanjut</button>
            </div>
        </div>
    </div>

    <!-- Tables Section -->
    <div class="tables-grid">
        <!-- Top 10 Diagnosis -->
        <div class="table-card">
            <div class="table-header">
                <h3>Top 10 Diagnosis</h3>
                <div class="table-controls">
                    <select class="period-select">
                        <option>Pilih Periode: <?php echo date('F Y'); ?></option>
                    </select>
                    <button class="btn-download">Download</button>
                </div>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Penyakit</th>
                            <th colspan="2">0 - 7 Hari</th>
                            <th colspan="2">8 - 28 Hari</th>
                            <th colspan="2">29 Hari - &lt;1 Th</th>
                            <th colspan="2">1 - 4 Th</th>
                            <th colspan="2">5 - 14 Th</th>
                            <th colspan="2">Jumlah</th>
                            <th>Total</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>L</th>
                            <th>P</th>
                            <th>L</th>
                            <th>P</th>
                            <th>L</th>
                            <th>P</th>
                            <th>L</th>
                            <th>P</th>
                            <th>L</th>
                            <th>P</th>
                            <th>L</th>
                            <th>P</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($diagnosisData as $row): ?>
                        <tr>
                            <td><?php echo $row['kode']; ?></td>
                            <td><?php echo $row['nama']; ?></td>
                            <td><?php echo $row['h0_7']['l']; ?></td>
                            <td><?php echo $row['h0_7']['p']; ?></td>
                            <td><?php echo $row['h8_28']['l']; ?></td>
                            <td><?php echo $row['h8_28']['p']; ?></td>
                            <td><?php echo $row['h29_1th']['l']; ?></td>
                            <td><?php echo $row['h29_1th']['p']; ?></td>
                            <td><?php echo $row['th1_4']['l']; ?></td>
                            <td><?php echo $row['th1_4']['p']; ?></td>
                            <td><?php echo $row['th5_14']['l']; ?></td>
                            <td><?php echo $row['th5_14']['p']; ?></td>
                            <td><?php echo $row['jumlah']['l']; ?></td>
                            <td><?php echo $row['jumlah']['p']; ?></td>
                            <td><?php echo $row['total']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top 10 Tindakan -->
        <div class="table-card">
            <div class="table-header">
                <h3>Top 10 Tindakan</h3>
                <div class="table-controls">
                    <select class="period-select">
                        <option>Pilih Periode: <?php echo date('F Y'); ?></option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Tindakan</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tindakanData as $row): ?>
                        <tr>
                            <td><?php echo $row['kode']; ?></td>
                            <td><?php echo $row['nama']; ?></td>
                            <td><?php echo $row['jumlah']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js for charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Visit Chart
const visitCtx = document.getElementById('visitChart').getContext('2d');
new Chart(visitCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($visitChartData, 'month')); ?>,
        datasets: [{
            label: 'Kunjungan',
            data: <?php echo json_encode(array_column($visitChartData, 'visits')); ?>,
            backgroundColor: '#3B82F6',
            borderRadius: [4, 4, 0, 0]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Queue Status Chart
const queueCtx = document.getElementById('queueChart').getContext('2d');
new Chart(queueCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_column($queueStatusData, 'name')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($queueStatusData, 'value')); ?>,
            backgroundColor: <?php echo json_encode(array_column($queueStatusData, 'color')); ?>
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: {
            legend: {
                position: 'right'
            }
        }
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layouts/dashboard.php';
