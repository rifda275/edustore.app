<?php  
session_start();
include '../koneksi/config.php';

$role = $_SESSION['role'];

// Ambil filter tanggal jika ada
$filterTanggal = isset($_GET['filter_tanggal']) ? $_GET['filter_tanggal'] : '';

// Ambil semua laporan dari tabel laporan
$query = "
    SELECT 
        l.id_transaksi,
        l.tanggal,
        u.nama AS kasir,
        l.total_pendapatan AS total,
        l.jumlah_barang_terjual AS jumlah_barang,
        (
            SELECT 
                SUM((b.harga - IFNULL(r.harga_beli, 0)) * dt.jumlah) 
            FROM detailtransaksi dt
            JOIN barang b ON dt.id_barang = b.id_barang
            LEFT JOIN (
                SELECT id_barang, MAX(harga_beli) AS harga_beli
                FROM restokbarang
                GROUP BY id_barang
            ) r ON b.id_barang = r.id_barang
            WHERE dt.id_transaksi = l.id_transaksi
        ) AS keuntungan
    FROM laporan l
    JOIN transaksi t ON l.id_transaksi = t.id_transaksi
    JOIN pengguna u ON t.id_pengguna = u.id_pengguna
";

if (!empty($filterTanggal)) {
    $query .= " WHERE DATE(l.tanggal) = '$filterTanggal'";
}

$query .= " ORDER BY l.id_transaksi DESC";

$laporan = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <link rel="stylesheet" href="../CSS/stylelaporan.css">
    <style>
        @media print {
            body {
                visibility: hidden;
            }
            .printable {
                visibility: visible;
                position: absolute;
                top: 0;
                left: 0;
            }
            .action-bar, .sidebar, .btn-danger, .filter-date {
                display: none;
            }
        }
    </style>
</head>
<body>

<?php 
    $currentPage = 'barang';
    include '../sidebar/sidebar.php'; 
?>

<div class="container">
    <h2>Laporan Penjualan</h2>

    <div class="action-bar" style="display: flex; align-items: center; gap: 10px;">
        <a href="barang_terlaris.php" class="btn btn-danger">Lihat Barang Terlaris</a>
        <button onclick="window.print()" class="btn btn-danger">Cetak</button>

        <form method="GET" class="filter-date" style="display: flex; align-items: center; gap: 10px; margin: 0;">
            <input type="date" name="filter_tanggal" class="btn btn-success" 
                value="<?= isset($_GET['filter_tanggal']) ? $_GET['filter_tanggal'] : '' ?>">
            <button type="submit" class="btn btn-primary">Terapkan Filter</button>
        </form>
    </div>

    <div class="printable">
        <div class="print-header only-print">
            <img src="../icons/edustore.png" alt="Logo" class="logo-img">
            <h2>Laporan Penjualan</h2>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID Transaksi</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th>Jumlah Pendapatan</th>
                    <th>Total Barang</th>
                    <th>Keuntungan</th>
                    <th>Detil Transaksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($laporan)) : ?>
                <tr>
                    <td><?= $row['id_transaksi'] ?></td>
                    <td><?= $row['tanggal'] ?></td>
                    <td><?= $row['kasir'] ?></td>
                    <td>Rp<?= number_format($row['total'], 0, ',', '.') ?></td>
                    <td><?= $row['jumlah_barang'] ?></td>
                    <td>Rp<?= number_format($row['keuntungan'] ?? 0, 0, ',', '.') ?></td>
                    <td>
                        <a href="../transaksi/cetak_transaksi.php?id=<?= $row['id_transaksi'] ?>">
                            <button class="btn btn-secondary">Lihat</button>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.querySelector(".toggle-btn");
        const sidebar = document.querySelector(".laporan-page .sidebar");
        const container = document.querySelector(".laporan-page .container");

        if (toggleBtn && sidebar && container) {
            toggleBtn.addEventListener("click", function () {
                sidebar.classList.toggle("collapsed");
                container.classList.toggle("collapsed");
                toggleBtn.classList.toggle("moved");
            });
        }
    });
    </script>
</body>
</html>
