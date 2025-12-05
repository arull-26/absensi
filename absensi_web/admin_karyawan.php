<?php
// File: admin_karyawan.php
require 'auth_check.php';
checkRole(['admin']);
require 'config.php';

$message = '';

// --- LOGIC CRUD ---

// 1. TAMBAH Karyawan (Simpel: role pegawai, password default)
if (isset($_POST['tambah'])) {
    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    
    if (empty($nama) || empty($username)) {
         $message = "Nama dan Username harus diisi!";
    } else {
        // Default password "123456"
        $password_hashed = password_hash("123456", PASSWORD_DEFAULT); 
        
        $stmt = $conn->prepare("INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, 'pegawai')");
        $stmt->bind_param("sss", $nama, $username, $password_hashed);
        
        if ($stmt->execute()) {
            $message = "Karyawan $nama berhasil ditambahkan! Password default: 123456.";
        } else {
            // Error 1062 adalah Duplicate entry (Username sudah ada)
            if ($conn->errno == 1062) {
                 $message = "Gagal menambah karyawan. Username sudah digunakan.";
            } else {
                 $message = "Gagal menambah karyawan. Terjadi kesalahan database.";
            }
        }
    }
    
    header("Location: admin_karyawan.php?msg=" . urlencode($message));
    exit();
}

// 2. HAPUS Karyawan
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'pegawai'");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Karyawan berhasil dihapus.";
    } else {
        $message = "Gagal menghapus karyawan.";
    }
    header("Location: admin_karyawan.php?msg=" . urlencode($message));
    exit();
}

// 3. RESET Password Karyawan (Setel ulang ke default "123456")
if (isset($_GET['action']) && $_GET['action'] == 'reset' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $password_hashed = password_hash("123456", PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ? AND role = 'pegawai'");
    $stmt->bind_param("si", $password_hashed, $id);
    if ($stmt->execute()) {
        $message = "Password berhasil direset ke default '123456'.";
    } else {
        $message = "Gagal mereset password.";
    }
    header("Location: admin_karyawan.php?msg=" . urlencode($message));
    exit();
}

// AMBIL DATA KARYAWAN (Hanya role pegawai yang ditampilkan di tabel ini)
$data_karyawan = $conn->query("SELECT id, nama, username, role FROM users WHERE role = 'pegawai' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Karyawan</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style> 
        .action-btn { margin-right: 5px; text-decoration: none; padding: 5px 10px; border-radius: 5px; } 
        .table-container { overflow-x: auto; } 
        .table th, .table td { padding: 12px 15px; text-align: left; }
    </style>
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>

    <div class="content">
        <div class="navbar"><h2>Manajemen Karyawan</h2></div>
        
        <h1>Data Karyawan</h1>
        
        <?php if (isset($_GET['msg'])): ?>
            <div class="card" style="background-color: #d1ecf1; color: #0c5460; padding: 10px; margin-bottom: 20px;">
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h3>Tambah Karyawan Baru</h3>
            <form method="POST">
                <input type="text" name="nama" placeholder="Nama Lengkap" required style="padding: 8px; margin-right: 10px; border: 1px solid #ccc; border-radius: 5px;">
                <input type="text" name="username" placeholder="Username (Unique)" required style="padding: 8px; margin-right: 10px; border: 1px solid #ccc; border-radius: 5px;">
                <button type="submit" name="tambah" class="btn btn-primary">Tambah</button>
            </form>
        </div>

        <div class="card">
            <h3>Daftar Karyawan (Pegawai)</h3>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $data_karyawan->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= $row['role'] ?></td>
                            <td>
                                <a href="admin_karyawan_edit.php?id=<?= $row['id'] ?>" class="action-btn" style="background-color: #ffc107; color: #333;">Edit</a>
                                <a href="?action=reset&id=<?= $row['id'] ?>" onclick="return confirm('Reset password <?= htmlspecialchars($row['username']) ?> ke 123456?')" class="action-btn" style="background-color: #007bff; color: white;">Reset PW</a>
                                <a href="?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus karyawan <?= htmlspecialchars($row['username']) ?>?')" class="action-btn" style="background-color: #dc3545; color: white;">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>