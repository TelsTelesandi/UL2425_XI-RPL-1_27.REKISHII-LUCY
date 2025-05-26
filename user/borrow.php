<?php
$pageTitle = "Peminjaman Barang";
$activePage = "borrow";
require_once '../config/database.php';
require_once '../config/functions.php';
requireLogin();

if (isAdmin()) {
    header("Location: ../admin/dashboard.php");
    exit();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sarana_id = mysqli_real_escape_string($conn, $_POST['sarana_id']);
    $jumlah = mysqli_real_escape_string($conn, $_POST['jumlah']);
    $tanggal_pinjam = mysqli_real_escape_string($conn, $_POST['tanggal_pinjam']);
    $user_id = $_SESSION['user_id'];

    // Check if item is available
    $query = "SELECT * FROM sarana WHERE SARANA_ID = '$sarana_id'";
    $result = mysqli_query($conn, $query);
    $item = mysqli_fetch_assoc($result);

    if ($item['JUMLAH_TERSEDIA'] < $jumlah) {
        $error = "Jumlah barang yang tersedia tidak mencukupi.";
    } else {
        // Insert into peminjaman with status 'menunggu'
        $query = "INSERT INTO peminjaman (USER_ID, SARANA_ID, TANGGAL_PINJAM, JUMLAH_PINJAM, STATUS) 
                  VALUES ('$user_id', '$sarana_id', '$tanggal_pinjam', '$jumlah', 'menunggu')";
        mysqli_query($conn, $query);

        header("Location: borrow.php?success=1");
        exit();
    }
}

// Get available items
$query = "SELECT * FROM sarana WHERE JUMLAH_TERSEDIA > 0 AND enabled = 0 ORDER BY NAMA_SARANA";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <p>Permintaan peminjaman berhasil diajukan. Silakan tunggu persetujuan dari admin.</p>
        <button type="button" class="close">&times;</button>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <p><?php echo $error; ?></p>
        <button type="button" class="close">&times;</button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Form Peminjaman Barang</h2>
    </div>
    <div class="card-body">
        <form method="post" action="">
            <div class="form-group">
                <label for="sarana_id" class="form-label">Pilih Barang</label>
                <select class="form-select" id="sarana_id" name="sarana_id" required>
                    <option value="">-- Pilih Barang --</option>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <option value="<?php echo $row['SARANA_ID']; ?>" data-stock="<?php echo $row['JUMLAH_TERSEDIA']; ?>">
                            <?php echo $row['NAMA_SARANA']; ?> (Tersedia: <?php echo $row['JUMLAH_TERSEDIA']; ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="jumlah" class="form-label">Jumlah</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" required>
                <div id="stockWarning" class="form-text text-danger" style="display: none;">
                    Jumlah melebihi stok yang tersedia.
                </div>
            </div>
            <div class="form-group">
                <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
                <input type="date" class="form-control" id="tanggal_pinjam" name="tanggal_pinjam" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary" id="submitBtn">Ajukan Peminjaman</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>