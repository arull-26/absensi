<?php
// Pastikan file ini di-include setelah auth_check.php dan config.php
$riwayat_q = $conn->prepare("SELECT tanggal, jam_masuk, jam_pulang, status FROM absensi WHERE user_id = ? ORDER BY tanggal DESC LIMIT 10");
$riwayat_q->bind_param("i", $user_id);
$riwayat_q->execute();
$riwayat_result = $riwayat_q->get_result();
?>

<div class="card">
    <h3>Riwayat Absensi Pribadi (10 Hari Terakhir)</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Masuk</th>
                <th>Pulang</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $riwayat_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['tanggal'] ?></td>
                    <td><?= $row['jam_masuk'] ?? '-' ?></td>
                    <td><?= $row['jam_pulang'] ?? '-' ?></td>
                    <td><span class="status-badge" style="padding: 5px 10px; border-radius: 5px; color: white; background-color: 
                        <?php 
                            if ($row['status'] == 'Hadir') echo '#28a745'; 
                            elseif ($row['status'] == 'Terlambat') echo '#ffc107'; 
                            else echo '#6c757d'; 
                        ?>">
                        <?= $row['status'] ?>
                    </span></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>