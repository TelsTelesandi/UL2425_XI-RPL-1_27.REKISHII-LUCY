<?php
$pageTitle = "Pengembalian Barang";
$activePage = "return";
require_once '../config/database.php';
require_once '../config/functions.php';
requireLogin();

if (isAdmin()) {
    header("Location: ../admin/dashboard.php");
    exit();
}

if (isset($_POST['return']) && isset($_POST['id'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $tanggal_kembali = date('Y-m-d');

    $query = "UPDATE peminjaman SET STATUS = 'dikembalikan_konfirmasi', TANGGAL_KEMBALI = '$tanggal_kembali' WHERE PEMINJAMAN_ID = '$id'";
    mysqli_query($conn, $query);
    
    header("Location: return.php?success=1");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT p.*, s.NAMA_SARANA 
          FROM peminjaman p 
          JOIN sarana s ON p.SARANA_ID = s.SARANA_ID 
          WHERE p.USER_ID = '$user_id' AND p.STATUS = 'disetujui' 
          ORDER BY p.PEMINJAMAN_ID DESC";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <p>Permintaan pengembalian berhasil diajukan. Silakan tunggu persetujuan dari admin.</p>
        <button type="button" class="close">&times;</button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Barang yang Sedang Dipinjam</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Barang</th>
                        <th>Tanggal Pinjam</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['PEMINJAMAN_ID']; ?></td>
                                <td><?php echo $row['NAMA_SARANA']; ?></td>
                                <td><?php echo $row['TANGGAL_PINJAM']; ?></td>
                                <td><?php echo $row['JUMLAH_PINJAM']; ?></td>
                                <td><?php echo getStatusLabel($row['STATUS']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#returnModal<?php echo $row['PEMINJAMAN_ID']; ?>">
                                        <i class="fas fa-undo"></i> Kembalikan
                                    </button>
                                    
                                    <!-- Return Modal -->
                                    <div class="modal" id="returnModal<?php echo $row['PEMINJAMAN_ID']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Kembalikan Barang</h5>
                                                    <button type="button" class="modal-close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form method="post" action="">
                                                    <div class="modal-body">
                                                        <p>Apakah Anda yakin ingin mengembalikan barang "<?php echo $row['NAMA_SARANA']; ?>"?</p>
                                                        <input type="hidden" name="id" value="<?php echo $row['PEMINJAMAN_ID']; ?>">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" name="return" class="btn btn-primary">Kembalikan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada barang yang sedang dipinjam</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
