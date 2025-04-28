<?php 
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Kasir') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir - EduStore</title>
    <link rel="stylesheet" href="CSS/styles.css">
</head>
<body>

<?php include 'sidebar/sidebar.php'; ?>

<div class="main-content">
    <div class="header">
        <h2>Dashboard Kasir</h2>
    </div>
    <div class="dashboard-menu">
    <div class="card"><a href="transaksi/transaksi.php"><img src="icons/Transaksi.png"><p>Transaksi</p></a></div>
        <div class="card"><a href="barang/barang.php"><img src="icons/Tambah barang.png"><p>Barang</p></a></div>
        <div class="card"><a href="laporan/laporan.php"><img src="icons/Laporan.png"><p>Laporan</p></a></div>
        <div class="card"><a href="pengaturan/pengaturan.php"><img src="icons/Pengaturan.png"><p>Pengaturan</p></a></div>
    </div>
</div>


<script>
    function toggleSidebar() {
        const sidebar = document.getElementById("sidebar");
        sidebar.classList.toggle("collapsed");
    }
</script>

</body>
</html>
