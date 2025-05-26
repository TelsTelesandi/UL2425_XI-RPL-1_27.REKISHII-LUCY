<?php
$pageTitle = "Approval Pengembalian";
$activePage = "return";
require_once '../config/database.php';
require_once '../config/functions.php';
requireAdmin();

// Process approval/rejection of returns
if (isset($_POST['action']) && isset($_POST['id'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $action = mysqli_real_escape_string($conn, $_POST['action']);
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan'] ?? '');
    $tanggal_kembali = mysqli_real_escape_string($conn, $_POST['tanggal_kembali'] ?? date('Y-m-d'));
    
    if ($action == 'approve_return') {
        $status = 'selesai'; 
        
        // Get peminjaman data
        $query = "SELECT * FROM peminjaman WHERE PEMINJAMAN_ID = '$id'";
        $result = mysqli_query($conn, $query);
        $peminjaman = mysqli_fetch_assoc($result);
        
        // Update item stock
        $sarana_id = $peminjaman['SARANA_ID'];
        $jumlah_pinjam = $peminjaman['JUMLAH_PINJAM'];
        
        $query = "UPDATE sarana SET JUMLAH_TERSEDIA = JUMLAH_TERSEDIA + $jumlah_pinjam WHERE SARANA_ID = '$sarana_id'";
        mysqli_query($conn, $query);
        
        // Update peminjaman with return date and status
        $query = "UPDATE peminjaman SET STATUS = '$status', TANGGAL_KEMBALI = '$tanggal_kembali', 
                  CATATAN_ADMIN = CONCAT(IFNULL(CATATAN_ADMIN, ''), '\n', '$catatan') 
                  WHERE PEMINJAMAN_ID = '$id'";
        mysqli_query($conn, $query);
        
    } elseif ($action == 'reject_return') {
        $status = 'dipinjam'; 
        $query = "UPDATE peminjaman SET STATUS = '$status', 
                  CATATAN_ADMIN = CONCAT(IFNULL(CATATAN_ADMIN, ''), '\n', '$catatan'), TANGGAL_KEMBALI = NULL 
                  WHERE PEMINJAMAN_ID = '$id'";
        mysqli_query($conn, $query);
    }
    
    header("Location: return.php?success=1");
    exit();
}

$query = "SELECT p.*, u.NAMA_LENGKAP, s.NAMA_SARANA 
          FROM peminjaman p 
          JOIN users u ON p.USER_ID = u.USER_ID 
          JOIN sarana s ON p.SARANA_ID = s.SARANA_ID 
          WHERE p.STATUS = 'dikembalikan_konfirmasi' 
          ORDER BY p.PEMINJAMAN_ID DESC";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <p>Permintaan pengembalian berhasil diproses.</p>
        <button type="button" class="close">&times;</button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Permintaan Pengembalian Menunggu Persetujuan</h2>
        <p class="text-muted">Daftar permintaan pengembalian barang yang perlu disetujui</p>
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
                                <td><?php echo $row['NAMA_LENGKAP']; ?></td>
                                <td><?php echo $row['NAMA_SARANA']; ?></td>
                                <td><?php echo $row['TANGGAL_PINJAM']; ?></td>
                                <td><?php echo $row['JUMLAH_PINJAM']; ?></td>
                                <td><?php echo getStatusLabel($row['STATUS']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#approveReturnModal<?php echo $row['PEMINJAMAN_ID']; ?>">
                                        <i class="fas fa-check-circle"></i> Terima
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectReturnModal<?php echo $row['PEMINJAMAN_ID']; ?>">
                                        <i class="fas fa-times-circle"></i> Tolak
                                    </button>
                                    
                                    <!-- Approve Return Modal -->
                                    <div class="modal" id="approveReturnModal<?php echo $row['PEMINJAMAN_ID']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Setujui Pengembalian</h5>
                                                    <button type="button" class="modal-close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form method="post" action="">
                                                    <div class="modal-body">
                                                        <p>Anda akan menyetujui pengembalian barang:</p>
                                                        <ul>
                                                            <li><strong>Barang:</strong> <?php echo $row['NAMA_SARANA']; ?></li>
                                                            <li><strong>Peminjam:</strong> <?php echo $row['NAMA_LENGKAP']; ?></li>
                                                            <li><strong>Jumlah:</strong> <?php echo $row['JUMLAH_PINJAM']; ?></li>
                                                        </ul>
                                                        <div class="form-group">
                                                            <label for="tanggal_kembali" class="form-label">Tanggal Pengembalian</label>
                                                            <input type="date" class="form-control" id="tanggal_kembali" name="tanggal_kembali" value="<?php echo date('Y-m-d'); ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="catatan" class="form-label">Catatan Admin</label>
                                                            <textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
                                                        </div>
                                                        <input type="hidden" name="id" value="<?php echo $row['PEMINJAMAN_ID']; ?>">
                                                        <input type="hidden" name="action" value="approve_return">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-success">Setujui Pengembalian</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Reject Return Modal -->
                                    <div class="modal" id="rejectReturnModal<?php echo $row['PEMINJAMAN_ID']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Tolak Pengembalian</h5>
                                                    <button type="button" class="modal-close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form method="post" action="">
                                                    <div class="modal-body">
                                                        <p>Anda akan menolak pengembalian barang:</p>
                                                        <ul>
                                                            <li><strong>Barang:</strong> <?php echo $row['NAMA_SARANA']; ?></li>
                                                            <li><strong>Peminjam:</strong> <?php echo $row['NAMA_LENGKAP']; ?></li>
                                                        </ul>
                                                        <div class="form-group">
                                                            <label for="catatan" class="form-label">Alasan Penolakan</label>
                                                            <textarea class="form-control" id="catatan" name="catatan" rows="3" required placeholder="Berikan alasan penolakan pengembalian..."></textarea>
                                                        </div>
                                                        <input type="hidden" name="id" value="<?php echo $row['PEMINJAMAN_ID']; ?>">
                                                        <input type="hidden" name="action" value="reject_return">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger">Tolak Pengembalian</button>
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
                            <td colspan="7" class="text-center">Tidak ada permintaan pengembalian yang menunggu persetujuan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>