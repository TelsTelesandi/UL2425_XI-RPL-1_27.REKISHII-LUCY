<?php
require_once '../config/database.php';
require_once '../config/functions.php';
requireAdmin();

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

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

$query .= " ORDER BY p.PEMINJAMAN_ID DESC";
$result = mysqli_query($conn, $query);
$total_data = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman Barang</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
        }

        .print-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 12px;
            margin-bottom: 5px;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 11px;
        }

        .info-left,
        .info-right {
            flex: 1;
        }

        .info-right {
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px 4px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }

        .signature {
            text-align: center;
            margin-top: 50px;
        }

        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin: 0 auto;
            margin-top: 60px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .print-container {
                padding: 10px;
            }

            .no-print {
                display: none !important;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }

        .print-button:hover {
            background: #0056b3;
        }

        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .status-selesai {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .custom-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 11px;
            text-align: center;
            padding: 5px 20px;
            border-top: 1px solid #333;
        }

    </style>
</head>

<body>


    <div class="print-container">
        <div class="header">
            <h1>Aplikasi Peminjaman Barang</h1>
            <p>Periode: <?php echo date('d F Y', strtotime($start_date)); ?> s/d <?php echo date('d F Y', strtotime($end_date)); ?></p>
        </div>
        <h3 class="text-center">LAPORAN REKAPITULASI PEMINJAMAN BARANG</h3>
        <br><br>
        <div class="info-section">
            <div class="info-left">
                <p><strong>Total Data:</strong> <?php echo $total_data; ?></p>
            </div>
            <div class="info-right">
                <p><strong>Tanggal Cetak:</strong> <?php echo date('d F Y H:i:s'); ?></p>
                <p><strong>Dicetak oleh:</strong> <?php echo $_SESSION['nama_lengkap']; ?></p>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 8%;">ID</th>
                    <th style="width: 20%;">Nama Peminjam</th>
                    <th style="width: 25%;">Nama Barang</th>
                    <th style="width: 12%;">Tgl Pinjam</th>
                    <th style="width: 12%;">Tgl Kembali</th>
                    <th style="width: 8%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total_data > 0): ?>
                    <?php
                    $no = 1;
                    mysqli_data_seek($result, 0);
                    while ($row = mysqli_fetch_assoc($result)):
                    ?>
                        <tr>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td class="text-center"><?php echo $row['PEMINJAMAN_ID']; ?></td>
                            <td class="text-center"><?php echo $row['NAMA_LENGKAP']; ?></td>
                            <td class="text-center"><?php echo $row['NAMA_SARANA']; ?></td>
                            <td class="text-center"><?php echo date('d/m/Y', strtotime($row['TANGGAL_PINJAM'])); ?></td>
                            <td class="text-center"><?php echo $row['TANGGAL_KEMBALI'] ? date('d/m/Y', strtotime($row['TANGGAL_KEMBALI'])) : '-'; ?></td>
                            <td class="text-center"><?php echo $row['JUMLAH_PINJAM']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 20px;">
                            Tidak ada data untuk periode yang dipilih
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="footer">
            <div>
            </div>
            <div class="signature">
                <p>Mengetahui,</p>
                <div class="signature-line"></div>
                <p><strong><?php echo $_SESSION['nama_lengkap']; ?></strong></p>
            </div>
        </div>
    </div>
    <div class="custom-footer">
        <hr>
        <br>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>

</html>