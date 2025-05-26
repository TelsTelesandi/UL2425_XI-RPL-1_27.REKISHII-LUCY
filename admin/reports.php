<?php
$pageTitle = "Laporan";
$activePage = "reports";
require_once '../config/database.php';
require_once '../config/functions.php';
requireAdmin();

// Filter parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Build query
$query = "SELECT p.*, u.NAMA_LENGKAP, s.NAMA_SARANA 
          FROM peminjaman p 
          JOIN users u ON p.USER_ID = u.USER_ID 
          JOIN sarana s ON p.SARANA_ID = s.SARANA_ID 
          WHERE p.STATUS = 'selesai'";

if (!empty($start_date)) {
    $query .= " AND p.TANGGAL_PINJAM >= '$start_date'";
}

if (!empty($end_date)) {
    $query .= " AND p.TANGGAL_PINJAM <= '$end_date'";
}

if (!empty($status)) {
    $query .= " AND p.STATUS = 'selesai'";
}

$query .= " ORDER BY p.PEMINJAMAN_ID DESC";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<div class="card mb-4">
    <div class="card-header">
        <h2 class="card-title">Filter Laporan</h2>
    </div>
    <div class="card-body">
        <form method="get" action="" class="form-row">
            <div class="form-col">
                <div class="form-group">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                </div>
            </div>
            <div class="form-col">
                <div class="form-group">
                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                </div>
            </div>
            <div class="form-col">
                <div class="form-group" style="margin-top: 32px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="reports.php" class="btn btn-secondary">
                        <i class="fas fa-refresh"></i> Reset
                    </a>
                    <button type="button" class="btn btn-success" onclick="printReport('<?php echo $start_date; ?>', '<?php echo $end_date; ?>')">
                        <i class="fas fa-print"></i> Cetak PDF
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Laporan Peminjaman Barang</h2>
        <p class="text-muted">Periode: <?php echo date('d-m-Y', strtotime($start_date)); ?> s/d <?php echo date('d-m-Y', strtotime($end_date)); ?></p>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
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
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)): 
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $row['PEMINJAMAN_ID']; ?></td>
                                <td><?php echo $row['NAMA_LENGKAP']; ?></td>
                                <td><?php echo $row['NAMA_SARANA']; ?></td>
                                <td><?php echo date('d-m-Y', strtotime($row['TANGGAL_PINJAM'])); ?></td>
                                <td><?php echo $row['TANGGAL_KEMBALI'] ? date('d-m-Y', strtotime($row['TANGGAL_KEMBALI'])) : '-'; ?></td>
                                <td><?php echo $row['JUMLAH_PINJAM']; ?></td>
                                <td><?php echo getStatusLabel($row['STATUS']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data untuk periode yang dipilih</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function printReport(startDate, endDate) {
    window.open('print-report.php?start_date=' + startDate + '&end_date=' + endDate, '_blank');
}
</script>

<?php include '../includes/footer.php'; ?>
