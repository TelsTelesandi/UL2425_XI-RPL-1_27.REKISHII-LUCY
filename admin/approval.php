<?php
$pageTitle = "Approval Peminjaman";
$activePage = "approval";
require_once '../config/database.php';
require_once '../config/functions.php';
requireAdmin();

if (isset($_POST['action']) && isset($_POST['id'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $action = mysqli_real_escape_string($conn, $_POST['action']);
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan'] ?? '');
    
    if ($action == 'approve') {
        $status = 'disetujui';
        
        $query = "SELECT * FROM peminjaman WHERE PEMINJAMAN_ID = '$id'";
        $result = mysqli_query($conn, $query);
        $peminjaman = mysqli_fetch_assoc($result);
        
        $sarana_id = $peminjaman['SARANA_ID'];
        $jumlah_pinjam = $peminjaman['JUMLAH_PINJAM'];
        
        $query = "UPDATE sarana SET JUMLAH_TERSEDIA = JUMLAH_TERSEDIA - $jumlah_pinjam WHERE SARANA_ID = '$sarana_id'";
        mysqli_query($conn, $query);
    } else {
        $status = 'ditolak';
    }
    
    $query = "UPDATE peminjaman SET STATUS = '$status', CATATAN_ADMIN = '$catatan' WHERE PEMINJAMAN_ID = '$id'";
    mysqli_query($conn, $query);
    
    header("Location: approval.php?success=1");
    exit();
}

$query = "SELECT p.*, u.NAMA_LENGKAP, s.NAMA_SARANA, s.JUMLAH_TERSEDIA 
          FROM peminjaman p 
          JOIN users u ON p.USER_ID = u.USER_ID 
          JOIN sarana s ON p.SARANA_ID = s.SARANA_ID 
          WHERE p.STATUS = 'menunggu' 
          ORDER BY p.PEMINJAMAN_ID DESC";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <p>Permintaan berhasil diproses.</p>
        <button type="button" class="close">&times;</button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Permintaan Peminjaman Menunggu Persetujuan</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Peminjam</th>
                        <th>Barang</th>
                        <th>Stok Tersedia</th>
                        <th>Tanggal Pinjam</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['PEMINJAMAN_ID']; ?></td>
                                <td><?php echo $row['NAMA_LENGKAP']; ?></td>
                                <td><?php echo $row['NAMA_SARANA']; ?></td>
                                <td><?php echo $row['JUMLAH_TERSEDIA']; ?></td>
                                <td><?php echo $row['TANGGAL_PINJAM']; ?></td>
                                <td><?php echo $row['JUMLAH_PINJAM']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#approveModal<?php echo $row['PEMINJAMAN_ID']; ?>">
                                        <i class="fas fa-check"></i> Setujui
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal<?php echo $row['PEMINJAMAN_ID']; ?>">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                    
                                    <div class="modal" id="approveModal<?php echo $row['PEMINJAMAN_ID']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Setujui Peminjaman</h5>
                                                    <button type="button" class="modal-close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form method="post" action="">
                                                    <div class="modal-body">
                                                        <p>Apakah Anda yakin ingin menyetujui peminjaman ini?</p>
                                                        <div class="form-group">
                                                            <label for="catatan" class="form-label">Catatan (opsional)</label>
                                                            <textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
                                                        </div>
                                                        <input type="hidden" name="id" value="<?php echo $row['PEMINJAMAN_ID']; ?>">
                                                        <input type="hidden" name="action" value="approve">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-success">Setujui</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="modal" id="rejectModal<?php echo $row['PEMINJAMAN_ID']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Tolak Peminjaman</h5>
                                                    <button type="button" class="modal-close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form method="post" action="">
                                                    <div class="modal-body">
                                                        <p>Apakah Anda yakin ingin menolak peminjaman ini?</p>
                                                        <div class="form-group">
                                                            <label for="catatan" class="form-label">Alasan Penolakan</label>
                                                            <textarea class="form-control" id="catatan" name="catatan" rows="3" required></textarea>
                                                        </div>
                                                        <input type="hidden" name="id" value="<?php echo $row['PEMINJAMAN_ID']; ?>">
                                                        <input type="hidden" name="action" value="reject">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger">Tolak</button>
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
                            <td colspan="7" class="text-center">Tidak ada permintaan peminjaman yang menunggu persetujuan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
