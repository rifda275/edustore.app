<?php

session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {

    header("Location: login.php");

    exit();

}

?>



<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin - EduStore</title>

    <link rel="stylesheet" href="CSS/styles.css">

</head>

<body>



<?php include('sidebar/sidebar.php'); ?>



<div class="main-content">

    <div class="header">

        <h2>Dashboard Admin</h2>

    </div>



    <div class="dashboard-menu">

    <div class="card"><a href="pengguna/pengguna.php"><img src="https://edustore.markaz.my.id/icons/Pengguna.png"><p>Kelola Pengguna</p></a></div>

    <div class="card"><a href="transaksi/transaksi.php"><img src="https://edustore.markaz.my.id/icons/Transaksi.png"><p>Transaksi</p></a></div>

        <div class="card"><a href="barang/barang.php"><img src="icons/Tambah barang.png"><p>Kelola Barang</p></a></div>

        <div class="card"><a href="restokbarang/restok_barang.php"><img src="icons/restok barang.png"><p>Restok Barang</p></a></div>

        <div class="card"><a href="laporan/laporan.php"><img src="https://edustore.markaz.my.id/icons/Laporan.png"><p>Laporan</p></a></div>

        <div class="card"><a href="pengaturan/pengaturan.php"><img src="https://edustore.markaz.my.id/icons/pengaturan.png"><p>Pengaturan</p></a></div>

    </div>

</div>





<script>

function toggleSidebar() {

    const sidebar = document.getElementById('sidebar');

    sidebar.classList.toggle('collapsed');

}

</script>



</body>

</html>

