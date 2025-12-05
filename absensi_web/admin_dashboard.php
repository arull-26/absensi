<?php
require 'auth_check.php';
checkRole(['admin']);
require 'config.php';
// Logic untuk mengambil data statistik, misal:
$total_karyawan = $conn->query("SELECT COUNT(id) FROM users WHERE role='pegawai'")->fetch_row()[0];
// Ambil data untuk Chart.js (perlu query kompleks, disederhanakan di sini)
$data_chart = [
    'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum'],
    'hadir' => [20, 22, 18, 25, 23],
    'terlambat' => [2, 1, 4, 0, 2]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="sidebar">
        <h3>Admin Panel</h3>
        <hr>
        <ul style="list-style: none; padding: 0;">
            <li style="margin-bottom: 10px;"><a href="admin_dashboard.php" style="color: var(--primary-color); text-decoration: none;">🏠 Dashboard</a></li>
            <li style="margin-bottom: 10px;"><a href="admin_karyawan.php" style="color: var(--text-dark); text-decoration: none;">👥 Manajemen Karyawan</a></li>
            <li style="margin-bottom: 10px;"><a href="admin_rekap.php" style="color: var(--text-dark); text-decoration: none;">📊 Rekap Absensi</a></li>
            <li style="margin-bottom: 10px;"><a href="admin_profile.php" style="color: var(--text-dark); text-decoration: none;">👤 Profil Admin</a></li>
        </ul>
        <a href="logout.php" class="btn btn-primary" style="margin-top: 20px; display: block; text-align: center;">Logout</a>
    </div>

    <div class="content">
        <div class="navbar">
            <h2>Selamat Datang, Admin!</h2>
            <button onclick="toggleSidebar()" class="btn btn-primary" style="display: none;">Menu</button>
        </div>
        
        <h1>Dashboard Administrasi</h1>
        
        <div style="display: flex; gap: 20px; margin-bottom: 30px;">
            <div class="card" style="flex: 1; text-align: center;">
                <h4>Total Karyawan</h4>
                <p style="font-size: 2em; color: var(--secondary-color); font-weight: 700;"><?= $total_karyawan ?></p>
            </div>
            </div>

        <div class="card">
            <h3>Grafik Kehadiran Mingguan</h3>
            <canvas id="attendanceChart" height="100"></canvas>
        </div>

    </div>

    <script src="assets/js/script.js"></script>
    <script>
        // Chart.js implementation
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($data_chart['labels']) ?>,
                datasets: [
                    {
                        label: 'Hadir',
                        data: <?= json_encode($data_chart['hadir']) ?>,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        fill: true
                    },
                    {
                        label: 'Terlambat',
                        data: <?= json_encode($data_chart['terlambat']) ?>,
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
</body>
</html>