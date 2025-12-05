<?php
require 'auth_check.php';
checkRole(['pegawai']);
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $today = date('Y-m-d');
    $current_time = date('H:i:s');
    
    $lat = $_POST['latitude'];
    $lng = $_POST['longitude'];
    $foto_data = $_POST['photo_data'];

    // 1. Cek Duplikasi Absensi Masuk
    $q_check = $conn->prepare("SELECT id FROM absensi WHERE user_id = ? AND tanggal = ?");
    $q_check->bind_param("is", $user_id, $today);
    $q_check->execute();
    if ($q_check->get_result()->num_rows > 0) {
        header("Location: pegawai_dashboard.php?msg=already_in"); exit();
    }
    
    // 2. Validasi Jarak GPS
    $jarak = hitungJarak($lat, $lng, $LAT_KANTOR, $LNG_KANTOR);
    if ($jarak > $RADIUS_TOLERANSI_METER) {
        // Hapus komentar di bawah untuk mengaktifkan validasi
        // header("Location: pegawai_dashboard.php?err=out_of_range"); exit();
    }

    // 3. Tentukan Status (Hadir/Terlambat)
    $status = ($current_time <= $JAM_MASUK_STANDAR) ? 'Hadir' : 'Terlambat';
    
    // 4. Simpan Foto Selfie
    $filename = 'masuk_' . $user_id . '_' . time() . '.png';
    $data_to_save = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $foto_data));
    file_put_contents('uploads/' . $filename, $data_to_save);

    // 5. Insert ke Database
    $stmt = $conn->prepare("INSERT INTO absensi (user_id, tanggal, jam_masuk, foto_masuk, lat_masuk, lng_masuk, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssdds", $user_id, $today, $current_time, $filename, $lat, $lng, $status);
    
    if ($stmt->execute()) {
        header("Location: pegawai_dashboard.php?msg=success_in");
    } else {
        header("Location: pegawai_dashboard.php?msg=error");
    }
    exit();
} else {
    header("Location: pegawai_dashboard.php");
    exit();
}