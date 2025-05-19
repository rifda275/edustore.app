<?php
ob_start(); // Mulai buffer output agar tulisan dari config tidak langsung muncul

include '../koneksi/config.php';

ob_clean(); // Hapus semua output awal (seperti "Koneksi berhasil!")

session_start();

$nama_kasir = $_SESSION['nama'] ?? 'Kasir';

if (!isset($_GET['id'])) {
    die("ID transaksi tidak ditemukan.");
}

$id_transaksi = intval($_GET['id']);

$query = "SELECT t.id_transaksi, p.nama AS pelanggan, b.nama_barang, dt.jumlah, 
                 b.harga, (dt.jumlah * b.harga) AS total_harga, 
                 t.tanggal_transaksi, t.bayar, t.metode_pembayaran
          FROM transaksi t
          JOIN detailtransaksi dt ON t.id_transaksi = dt.id_transaksi
          JOIN barang b ON dt.id_barang = b.id_barang
          JOIN pengguna p ON t.id_pengguna = p.id_pengguna
          WHERE t.id_transaksi = '$id_transaksi'";

$result = $conn->query($query);

if ($result->num_rows == 0) {
    die("Data transaksi tidak ditemukan.");
}

$data_semua = [];
$total_semua = 0;
$total_item = 0;
$bayar = 0;
$metode = "Tunai";

while ($row = $result->fetch_assoc()) {
    $data_semua[] = $row;
    $total_semua += $row['total_harga'];
    $total_item += $row['jumlah'];
    $bayar = isset($_GET['bayar']) ? intval($_GET['bayar']) : $row['bayar'];
    $metode = $row['metode_pembayaran'];
}

$tanggal_transaksi = date("d/m/Y H:i", strtotime($data_semua[0]['tanggal_transaksi']));
$nama_pelanggan = $data_semua[0]['pelanggan'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Cetak Struk</title>
  <style>
    body {
      font-family: 'Courier New', monospace;
      width: 300px;
      margin: auto;
      padding: 10px;
      font-size: 20px;
    }
    .header, .footer {
      text-align: center;
    }
    .header img {
      width: 100px;
      margin-bottom: 5px;
    }
    .line {
      border-top: 1px dashed #000;
      margin: 12px 0;
    }
    table {
      width: 100%;
      font-size: 20px;
    }
    td.right {
      text-align: right;
    }
    td.bold {
      font-weight: bold;
    }
    .thanks {
      margin-top: 18px;
      font-weight: bold;
      font-size: 20px;
    }
  </style>
</head>
<body onload="window.print()">

<div class="header">
  <h3>EDUSTORE</h3>
  <img src="../icons/edustore.png" alt="Logo">
  <p>
    JL. Bong Mereng, Tunjung, Burneh, Bangkalan<br>
    Email: edustore225@gmail.com
  </p>
</div>

<div class="line"></div>

<p>
  ID Transaksi : <?= $id_transaksi ?><br>
  Kasir        : <?= $nama_kasir ?><br>
  Waktu        : <?= $tanggal_transaksi ?>
</p>

<table>
  <?php foreach ($data_semua as $row): ?>
    <tr>
      <td><?= $row['nama_barang'] ?> <?= $row['jumlah'] ?>x</td>
      <td class="right">Rp<?= number_format($row['total_harga'], 0, ',', '.') ?></td>
    </tr>
  <?php endforeach; ?>
</table>

<div class="line"></div>

<table>
  <tr>
    <td>Metode Pembayaran</td>
    <td class="right"><?= $metode ?></td>
  </tr>
  <tr>
    <td>Total Barang</td>
    <td class="right"><?= $total_item ?> Item</td>
  </tr>
  <tr>
    <td>Total Harga</td>
    <td class="right">Rp<?= number_format($total_semua, 0, ',', '.') ?></td>
  </tr>
  <tr>
    <td>Bayar</td>
    <td class="right">Rp<?= number_format($bayar, 0, ',', '.') ?></td>
  </tr>
  <tr>
    <td class="bold">Kembalian</td>
    <td class="right bold">Rp<?= number_format($bayar - $total_semua, 0, ',', '.') ?></td>
  </tr>
</table>

<div class="line"></div>

<div class="footer">
  <p class="thanks">Terima kasih atas kunjungannya<br>Jumpa & Sehat Selalu!</p>
</div>

</body>
</html>
