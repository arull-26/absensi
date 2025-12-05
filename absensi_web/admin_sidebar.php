<?php 
// File: admin_sidebar.php 
// Pastikan file ini ada di root /absensi_web/
?>
<style>
    /* ------------------------------------- */
    /* CSS untuk Sidebar */
    /* ------------------------------------- */
    :root {
        --primary-color: #6c5ce7;
        --secondary-color: #0984e3;
        --sidebar-width: 250px;
        --text-dark: #333;
        --text-light: #f4f4f4;
    }

    .sidebar {
        width: var(--sidebar-width);
        background: linear-gradient(180deg, #4b3e8c 0%, #6c5ce7 100%); /* Gradien */
        color: var(--text-light);
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        padding: 20px 0;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease-in-out; /* Animasi geser */
        z-index: 1000;
    }

    /* Ketika sidebar disembunyikan */
    .sidebar.closed {
        transform: translateX(calc( -1 * var(--sidebar-width) + 50px)); /* Sisakan sedikit untuk tombol toggle */
    }

    .sidebar-header {
        text-align: center;
        padding: 10px 0 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        margin: 0 20px;
    }

    .sidebar-header h3 {
        margin: 0;
        color: white;
    }

    .sidebar ul {
        list-style: none;
        padding: 20px 0;
        margin: 0;
    }

    .sidebar ul li a {
        display: block;
        padding: 15px 20px;
        color: var(--text-light);
        text-decoration: none;
        transition: background-color 0.2s, padding-left 0.2s;
    }

    /* Animasi Hover yang Menarik */
    .sidebar ul li a:hover {
        background-color: rgba(255, 255, 255, 0.15); /* Background sedikit transparan */
        padding-left: 25px; /* Efek geser ke kanan */
        border-left: 5px solid var(--secondary-color); /* Garis warna sekunder */
    }

    .sidebar .logout-btn {
        display: block;
        padding: 10px;
        margin: 20px 20px;
        background-color: #ff7675; /* Merah untuk logout */
        color: white;
        text-align: center;
        border-radius: 8px;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .sidebar .logout-btn:hover {
        background-color: #e84343;
    }
    
    /* Tombol Toggle (Hamburger) */
    .sidebar-toggle {
        position: fixed;
        left: var(--sidebar-width);
        top: 15px;
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 10px 15px;
        cursor: pointer;
        z-index: 1001;
        border-radius: 0 5px 5px 0;
        transition: left 0.3s ease-in-out;
    }

    .sidebar.closed + .sidebar-toggle {
        left: 0px; 
    }

    .content {
        margin-left: var(--sidebar-width);
        padding: 20px;
        transition: margin-left 0.3s ease-in-out;
    }

    .sidebar.closed ~ .content {
        margin-left: 50px;
    }
</style>

<button class="sidebar-toggle" id="sidebarToggle">☰</button>

<div class="sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <h3>Admin Panel</h3>
        <small style="color: #bbb;">Hi, <?= $_SESSION['nama'] ?? 'Admin' ?></small>
    </div>
    
    <ul>
        <li><a href="admin_dashboard.php">🏠 Dashboard</a></li>
        <li><a href="admin_karyawan.php">👥 Manajemen Karyawan</a></li>
        <li><a href="admin_lokasi.php">📍 Lokasi Kantor</a></li>
        <li><a href="admin_approval_izin.php">✅ Approval Izin</a></li>
        <li><a href="admin_rekap.php">📊 Rekap Absensi</a></li>
        <li><a href="admin_profile.php">👤 Profil Admin</a></li>
    </ul>
    
    <a href="logout.php" class="logout-btn">Keluar (Logout)</a>
</div>