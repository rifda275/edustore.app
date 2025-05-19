<?php

include '../koneksi/config.php';



$jenis_filter = isset($_POST['jenis_filter']) ? $_POST['jenis_filter'] : 'harian';

$tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d');



$where = '';

$label = 'Barang Terlaris Bulan Ini';



if ($jenis_filter == 'harian') {

    $where = "DATE(t.tanggal_transaksi) = '$tanggal'";

    $label = "Barang Terlaris Tanggal " . date('d/m/Y', strtotime($tanggal));

} elseif ($jenis_filter == 'bulanan') {

    $bulan = date('m', strtotime($tanggal));

    $tahun = date('Y', strtotime($tanggal));

    $where = "MONTH(t.tanggal_transaksi) = '$bulan' AND YEAR(t.tanggal_transaksi) = '$tahun'";

    $label = "Barang Terlaris Bulan " . date('F Y', strtotime($tanggal));

} elseif ($jenis_filter == 'tahunan') {

    $tahun = date('Y', strtotime($tanggal));

    $where = "YEAR(t.tanggal_transaksi) = '$tahun'";

    $label = "Barang Terlaris Tahun $tahun";

}



$dataLaporan = [];

$total_item = 0;

$total_pendapatan = 0;



$query = mysqli_query($conn, "

    SELECT 

        b.nama_barang,

        SUM(dt.jumlah) AS total_terjual,

        SUM(dt.subtotal * dt.jumlah) AS total_pendapatan

    FROM transaksi t

    JOIN detailtransaksi dt ON t.id_transaksi = dt.id_transaksi

    JOIN barang b ON dt.id_barang = b.id_barang

    WHERE $where

    GROUP BY b.id_barang

    ORDER BY total_terjual DESC

");



while ($row = mysqli_fetch_assoc($query)) {

    $dataLaporan[] = $row;

    $total_item += $row['total_terjual'];

    $total_pendapatan += $row['total_pendapatan'];

}

?>



<!DOCTYPE html>

<html>

<head>

    <title>Laporan Barang Terlaris</title>

    <link rel="stylesheet" href="../CSS/stylebarangterlaris.css">

</head>

<body>

<?php 

    $currentPage = 'barang';

    include '../sidebar/sidebar.php'; 

?>

    <div class="container print-area">



        <!-- Logo dan Judul -->

        <div class="header-logo">

            <img src="../icons/edustore.png" alt="Logo" class="logo-img">

            <h2><?= $label ?></h2>

        </div>



      <!-- Filter -->

<form method="POST" class="filter" style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">

    <label for="jenis_filter" style="color: #D2B48C;">Filter:</label>



    <select name="jenis_filter" onchange="this.form.submit()" style="padding: 6px 10px; border-radius: 4px; border: 1px solid #D2B48C; background-color: #D2B48C;">

        <option value="harian" <?= $jenis_filter == 'harian' ? 'selected' : '' ?>>Harian</option>

        <option value="bulanan" <?= $jenis_filter == 'bulanan' ? 'selected' : '' ?>>Bulanan</option>

        <option value="tahunan" <?= $jenis_filter == 'tahunan' ? 'selected' : '' ?>>Tahunan</option>

    </select>



    <input type="date" name="tanggal" value="<?= $tanggal ?>" style="padding: 6px 10px; border-radius: 4px; border: 1px solid #D2B48C; background-color: #D2B48C;">



    <button type="submit" name="filter" class="btn btn-primary">Tampilkan</button>

</form>





        <!-- Tabel -->

        <table>

            <tr>

                <th>Nama Barang</th>

                <th>Total Terjual</th>

                <th>Total Pendapatan</th>

            </tr>



            <?php if (count($dataLaporan) > 0): ?>

                <?php foreach ($dataLaporan as $row): ?>

                    <tr>

                        <td><?= $row['nama_barang'] ?></td>

                        <td><?= $row['total_terjual'] ?> Item</td>

                        <td>Rp <?= number_format($row['total_pendapatan'], 0, ',', '.') ?></td>

                    </tr>

                <?php endforeach; ?>

                <tr>

                    <td><strong>Total</strong></td>

                    <td><strong><?= $total_item ?> Item</strong></td>

                    <td><strong>Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></strong></td>

                </tr>

            <?php else: ?>

                <tr><td colspan="3">Data tidak ditemukan untuk tanggal tersebut.</td></tr>

            <?php endif; ?>

        </table>



        <!-- Tombol Aksi -->

        <div class="actions">

            <button onclick="window.print()">Cetak</button>

            <button onclick="window.location.href='laporan.php'">Kembali</button>

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