<?php
include "../koneksi/config.php";

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : "";

$sql = "SELECT 
            t.id_transaksi, 
            t.tanggal_transaksi, 
            t.total_harga,
            t.metode_pembayaran,
            u.nama AS nama_kasir,
            GROUP_CONCAT(CONCAT(b.nama_barang, ' (', d.jumlah, ') - Rp', FORMAT(d.subtotal, 0)) SEPARATOR ', ') AS detail_barang
        FROM transaksi t
        JOIN detailtransaksi d ON t.id_transaksi = d.id_transaksi
        JOIN barang b ON d.id_barang = b.id_barang
        JOIN pengguna u ON t.id_pengguna = u.id_pengguna";

if (!empty($keyword)) {
    $sql .= " WHERE t.id_transaksi LIKE '%$keyword%' OR b.nama_barang LIKE '%$keyword%'";
}

$sql .= " GROUP BY t.id_transaksi ORDER BY t.id_transaksi DESC";

$data = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Transaksi</title>
    <link rel="stylesheet" href="../CSS/styledetailtransaksi.css">
</head>
<body>
<?php 
$currentPage = 'transaksi'; // atau sesuai penamaan di sidebar
include '../sidebar/sidebar.php'; 
?>
<div class="content">
    <div class="header">
        <!-- ✅ Bungkus dengan .data-transaksi-container -->
        <div class="data-transaksi-container">
            <h2>Data Transaksi</h2>
        </div>

        <div class="search-box">
            <form method="GET">
                <input type="text" name="keyword" placeholder="Cari ID Transaksi / Nama Barang" value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit">Cari</button>
            </form>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID Transaksi</th>
                    <th>Tanggal</th>
                    <th>Detail Barang</th>
                    <th>Total Transaksi</th>
                    <th>Metode Pembayaran</th>
                    <th>Nama Kasir</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($data->num_rows > 0): ?>
                    <?php while($row = $data->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id_transaksi'] ?></td>
                            <td><?= $row['tanggal_transaksi'] ?></td>
                            <td><?= $row['detail_barang'] ?></td>
                            <td>Rp<?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                            <td><?= $row['metode_pembayaran'] ?></td>
                            <td><?= $row['nama_kasir'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">Data tidak ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("toggleSidebar");
    const sidebar = document.querySelector(".sidebar");
    const container = document.querySelector(".container");

    toggleBtn.addEventListener("click", function () {
      sidebar.classList.toggle("collapsed");
      toggleBtn.classList.toggle("moved");
    });
    });
</script>
</body>
</html>