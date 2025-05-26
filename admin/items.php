<?php
$pageTitle = "Kelola Barang";
$activePage = "items";
require_once '../config/database.php';
require_once '../config/functions.php';
requireAdmin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        // Add new item
        if ($_POST['action'] == 'add') {
            $nama = mysqli_real_escape_string($conn, $_POST['nama']);
            $jumlah = mysqli_real_escape_string($conn, $_POST['jumlah']);
            $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
            $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
            
            $query = "INSERT INTO sarana (NAMA_SARANA, JUMLAH_TERSEDIA, LOKASI, KETERANGAN) 
                      VALUES ('$nama', '$jumlah', '$lokasi', '$keterangan')";
            mysqli_query($conn, $query);
            
            header("Location: items.php?success=add");
            exit();
        }
        
        if ($_POST['action'] == 'edit') {
            $id = mysqli_real_escape_string($conn, $_POST['id']);
            $nama = mysqli_real_escape_string($conn, $_POST['nama']);
            $jumlah = mysqli_real_escape_string($conn, $_POST['jumlah']);
            $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
            $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
            
            $query = "UPDATE sarana SET 
                      NAMA_SARANA = '$nama', 
                      JUMLAH_TERSEDIA = '$jumlah', 
                      LOKASI = '$lokasi', 
                      KETERANGAN = '$keterangan' 
                      WHERE SARANA_ID = '$id'";
            mysqli_query($conn, $query);
            
            header("Location: items.php?success=edit");
            exit();
        }
        
        if ($_POST['action'] == 'delete') {
            $id = mysqli_real_escape_string($conn, $_POST['id']);
            
            $query = "SELECT COUNT(*) as count FROM peminjaman WHERE SARANA_ID = '$id' AND STATUS IN ('menunggu', 'disetujui', 'dipinjam', 'dikembalikan_konfirmasi')";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_assoc($result);
            
            if ($row['count'] > 0) {
                header("Location: items.php?error=in_use");
                exit();
            }
            
            $query = "UPDATE sarana SET enabled = 1 WHERE SARANA_ID = '$id'";
            mysqli_query($conn, $query);
            
            header("Location: items.php?success=delete");
            exit();
        }
    }
}

$query = "SELECT * FROM sarana WHERE enabled = 0 ORDER BY SARANA_ID DESC";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <?php if ($_GET['success'] == 'add'): ?>
            <p>Barang berhasil ditambahkan.</p>
        <?php elseif ($_GET['success'] == 'edit'): ?>
            <p>Barang berhasil diperbarui.</p>
        <?php elseif ($_GET['success'] == 'delete'): ?>
            <p>Barang berhasil dihapus.</p>
        <?php endif; ?>
        <button type="button" class="close">&times;</button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] == 'in_use'): ?>
    <div class="alert alert-danger">
        <p>Barang tidak dapat dihapus karena sedang dipinjam.</p>
        <button type="button" class="close">&times;</button>
    </div>
<?php endif; ?>

<div class="mb-4">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">
        <i class="fas fa-plus"></i> Tambah Barang
    </button>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Daftar Barang</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Barang</th>
                        <th>Jumlah Tersedia</th>
                        <th>Lokasi</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['SARANA_ID']; ?></td>
                                <td><?php echo $row['NAMA_SARANA']; ?></td>
                                <td><?php echo $row['JUMLAH_TERSEDIA']; ?></td>
                                <td><?php echo $row['LOKASI']; ?></td>
                                <td><?php echo $row['KETERANGAN']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal<?php echo $row['SARANA_ID']; ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal<?php echo $row['SARANA_ID']; ?>">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                    
                                    <div class="modal" id="editModal<?php echo $row['SARANA_ID']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Barang</h5>
                                                    <button type="button" class="modal-close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form method="post" action="">
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="nama" class="form-label">Nama Barang</label>
                                                            <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $row['NAMA_SARANA']; ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="jumlah" class="form-label">Jumlah Tersedia</label>
                                                            <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?php echo $row['JUMLAH_TERSEDIA']; ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="lokasi" class="form-label">Lokasi</label>
                                                            <input type="text" class="form-control" id="lokasi" name="lokasi" value="<?php echo $row['LOKASI']; ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="keterangan" class="form-label">Keterangan</label>
                                                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?php echo $row['KETERANGAN']; ?></textarea>
                                                        </div>
                                                        <input type="hidden" name="id" value="<?php echo $row['SARANA_ID']; ?>">
                                                        <input type="hidden" name="action" value="edit">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="modal" id="deleteModal<?php echo $row['SARANA_ID']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Hapus Barang</h5>
                                                    <button type="button" class="modal-close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form method="post" action="">
                                                    <div class="modal-body">
                                                        <p>Apakah Anda yakin ingin menghapus barang "<?php echo $row['NAMA_SARANA']; ?>"?</p>
                                                        <input type="hidden" name="id" value="<?php echo $row['SARANA_ID']; ?>">
                                                        <input type="hidden" name="action" value="delete">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger">Hapus</button>
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
                            <td colspan="6" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal" id="addModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Barang</h5>
                <button type="button" class="modal-close" data-dismiss="modal">&times;</button>
            </div>
            <form method="post" action="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="jumlah" class="form-label">Jumlah Tersedia</label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah" required>
                    </div>
                    <div class="form-group">
                        <label for="lokasi" class="form-label">Lokasi</label>
                        <input type="text" class="form-control" id="lokasi" name="lokasi" required>
                    </div>
                    <div class="form-group">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                    </div>
                    <input type="hidden" name="action" value="add">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>