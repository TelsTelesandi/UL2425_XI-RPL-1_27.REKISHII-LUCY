<?php
$pageTitle = "Dashboard User";
$activePage = "dashboard";
require_once '../config/database.php';
require_once '../config/functions.php';
requireLogin();

if (isAdmin()) {
    header("Location: ../admin/dashboard.php");
    exit();
}

// Get user's borrowing history
$user_id = $_SESSION['user_id'];
$query = "SELECT p.*, s.NAMA_SARANA 
          FROM peminjaman p 
          JOIN sarana s ON p.SARANA_ID = s.SARANA_ID 
          WHERE p.USER_ID = '$user_id' 
          ORDER BY p.PEMINJAMAN_ID DESC";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<div class="stats-grid">
    <div class="stats-card primary">
        <h3 class="stats-card-title">Total Peminjaman</h3>
        <?php
        $query = "SELECT COUNT(*) as total FROM peminjaman WHERE USER_ID = '$user_id'";
        $result_count = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result_count);
        ?>
        <p class="stats-card-value"><?php echo $row['total']; ?></p>
        <i class="fas fa-box stats-card-icon"></i>
    </div>

    <div class="stats-card warning">
        <h3 class="stats-card-title">Menunggu Persetujuan</h3>
        <?php
        $query = "SELECT COUNT(*) as total FROM peminjaman WHERE USER_ID = '$user_id' AND STATUS = 'menunggu'";
        $result_count = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result_count);
        ?>
        <p class="stats-card-value"><?php echo $row['total']; ?></p>
        <i class="fas fa-clock stats-card-icon"></i>
    </div>

    <div class="stats-card info">
        <h3 class="stats-card-title">Sedang Dipinjam</h3>
        <?php
        $query = "SELECT COUNT(*) as total FROM peminjaman WHERE USER_ID = '$user_id' AND STATUS = 'dipinjam'";
        $result_count = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result_count);
        ?>
        <p class="stats-card-value"><?php echo $row['total']; ?></p>
        <i class="fas fa-hand-holding stats-card-icon"></i>
    </div>

    <div class="stats-card success">
        <h3 class="stats-card-title">Peminjaman Selesai</h3>
        <?php
        $query = "SELECT COUNT(*) as total FROM peminjaman WHERE USER_ID = '$user_id' AND STATUS = 'selesai'";
        $result_count = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result_count);
        ?>
        <p class="stats-card-value"><?php echo $row['total']; ?></p>
        <i class="fas fa-check-circle stats-card-icon"></i>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Riwayat Peminjaman Barang</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Barang</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Catatan Admin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['PEMINJAMAN_ID']; ?></td>
                                <td><?php echo $row['NAMA_SARANA']; ?></td>
                                <td><?php echo $row['TANGGAL_PINJAM']; ?></td>
                                <td><?php echo $row['TANGGAL_KEMBALI'] ?? '-'; ?></td>
                                <td><?php echo $row['JUMLAH_PINJAM']; ?></td>
                                <td><?php echo getStatusLabel($row['STATUS']); ?></td>
                                <td><?php echo $row['CATATAN_ADMIN'] ?? '-'; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada riwayat peminjaman</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>