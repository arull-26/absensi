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

    // 1. Cek Absensi Masuk dan Absensi Pulang
    $q_check = $conn->prepare("SELECT id FROM absensi WHERE user_id = ? AND tanggal = ? AND jam_pulang IS NULL");
    $q_check->bind_param("is", $user_id, $today);
    $q_check->execute();
    $result = $q_check->get_result();

    if ($result->num_rows === 0) {
        header("Location: pegawai_dashboard.php?msg=not_eligible_pulang"); exit();
    }
    
    $absensi_id = $result->fetch_assoc()['id'];

    // 2. Validasi Jarak GPS (Sama seperti masuk)
    $jarak = hitungJarak($lat, $lng, $LAT_KANTOR, $LNG_KANTOR);
    if ($jarak > $RADIUS_TOLERANSI_METER) {
        // Hapus komentar di bawah untuk mengaktifkan validasi
        // header("Location: pegawai_dashboard.php?err=out_of_range_pulang"); exit();
    }
    
    // 3. Simpan Foto Selfie
    $filename = 'pulang_' . $user_id . '_' . time() . '.png';
    $data_to_save = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $foto_data));
    file_put_contents('uploads/' . $filename, $data_to_save);

    // 4. Update Database
    $stmt = $conn->prepare("UPDATE absensi SET jam_pulang = ?, foto_pulang = ?, lat_pulang = ?, lng_pulang = ? WHERE id = ?");
    $stmt->bind_param("sdddi", $current_time, $filename, $lat, $lng, $absensi_id);
    
    if ($stmt->execute()) {
        header("Location: pegawai_dashboard.php?msg=success_out");
    } else {
        header("Location: pegawai_dashboard.php?msg=error");
    }
    exit();
} else {
    header("Location: pegawai_dashboard.php");
    exit();
}