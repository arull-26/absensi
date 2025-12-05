<?php
// Pastikan file ini ada di root /absensi/
require 'auth_check.php';
checkRole(['admin']);
require 'config.php';

$message = '';
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ubah_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $message = "Konfirmasi password baru tidak cocok.";
    } else {
        // 1. Cek Password Lama
        $stmt_check = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt_check->bind_param("i", $user_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $user = $result->fetch_assoc();

        if (password_verify($old_password, $user['password'])) {
            // 2. Update Password Baru
            $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt_update->bind_param("si", $new_password_hashed, $user_id);
            
            if ($stmt_update->execute()) {
                $message = "Password berhasil diubah!";
            } else {
                $message = "Gagal mengubah password. Silakan coba lagi.";
            }
        } else {
            $message = "Password lama salah.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'admin_sidebar.php'; // Anggap ada file sidebar ?>

    <div class="content">
        <div class="navbar"><h2>Profil Admin</h2></div>
        
        <h1>Manajemen Profil & Password</h1>
        
        <div class="card" style="max-width: 500px;">
            <h3>Ubah Password</h3>
            
            <?php if (!empty($message)): ?>
                <p style="color: <?= (strpos($message, 'berhasil') !== false) ? 'green' : 'red' ?>; margin-bottom: 15px;"><?= $message ?></p>
            <?php endif; ?>
            
            <form method="POST">
                <div style="margin-bottom: 15px;">
                    <label>Password Lama:</label>
                    <input type="password" name="old_password" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label>Password Baru:</label>
                    <input type="password" name="new_password" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label>Konfirmasi Password Baru:</label>
                    <input type="password" name="confirm_password" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                </div>
                <button type="submit" name="ubah_password" class="btn btn-primary">Ubah Password</button>
            </form>
        </div>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>