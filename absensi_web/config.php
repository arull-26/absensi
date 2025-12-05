<?php
// Koneksi Database
$host = 'localhost';
$user = 'root';
$pass = ''; 
$db = 'db_absensi'; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Pengaturan Global
// Jam masuk standar (Untuk penentuan status Hadir/Terlambat)
$JAM_MASUK_STANDAR = '08:00:00'; 

// Koordinat Kantor (Titik Pusat untuk validasi GPS)
// Ganti dengan koordinat kantor Anda! Contoh:
$LAT_KANTOR = -6.2088; 
$LNG_KANTOR = 106.8456; 
$RADIUS_TOLERANSI_METER = 100; // Toleransi radius 100 meter

// Helper function untuk hitung jarak (Haversine Formula)
function hitungJarak($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371000; // Meter
    
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
         
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $distance = $earthRadius * $c; // Jarak dalam meter
    
    return $distance;
}
?>