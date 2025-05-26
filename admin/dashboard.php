<?php
$pageTitle = "Dashboard Admin";
$activePage = "dashboard";
require_once '../config/database.php';
require_once '../config/functions.php';
requireAdmin();

$query = "SELECT p.*, u.NAMA_LENGKAP, s.NAMA_SARANA 
          FROM peminjaman p 
          JOIN users u ON p.USER_ID = u.USER_ID 
          JOIN sarana s ON p.SARANA_ID = s.SARANA_ID  
          ORDER BY p.PEMINJAMAN_ID DESC";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<div class="stats-grid">
    <div class="stats-card primary">
        <h3 class="stats-card-title">Total Barang</h3>
        <?php
        $query = "SELECT COUNT(*) as total FROM sarana";
        $result_count = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result_count);
        ?>
        <p class="stats-card-value"><?php echo $row['total']; ?></p>
        <i class="stats-card-icon"></i>
    </div>
    
    <div class="stats-card warning">
        <h3 class="stats-card-title">Peminjaman Menunggu</h3>
        <?php
        $query = "SELECT COUNT(*) as total FROM peminjaman WHERE STATUS = 'menunggu'";
        $result_count = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result_count);
        ?>
        <p class="stats-card-value"><?php echo $row['total']; ?></p>
        <i class="stats-card-icon"></i>
    </div>
    
    <div class="stats-card info">
        <h3 class="stats-card-title">Sedang Dipinjam</h3>
        <?php
        $query = "SELECT COUNT(*) as total FROM peminjaman WHERE STATUS = 'dipinjam'";
        $result_count = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result_count);
        ?>
        <p class="stats-card-value"><?php echo $row['total']; ?></p>
        <i class="stats-card-icon"></i>
    </div>
    
    <div class="stats-card success">
        <h3 class="stats-card-title">Peminjaman Selesai</h3>
        <?php
        $query = "SELECT COUNT(*) as total FROM peminjaman WHERE STATUS = 'selesai'";
        $result_count = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result_count);
        ?>
        <p class="stats-card-value"><?php echo $row['total']; ?></p>
        <i class="stats-card-icon"></i>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Rekap Peminjaman dan Pengembalian</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Peminjam</th>
                        <th>Barang</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['PEMINJAMAN_ID']; ?></td>
                                <td><?php echo $row['NAMA_LENGKAP']; ?></td>
                                <td><?php echo $row['NAMA_SARANA']; ?></td>
                                <td><?php echo $row['TANGGAL_PINJAM']; ?></td>
                                <td><?php echo $row['TANGGAL_KEMBALI']; ?></td>
                                <td><?php echo $row['JUMLAH_PINJAM']; ?></td>
                                <td><?php echo getStatusLabel($row['STATUS']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
