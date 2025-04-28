<?php
include '../koneksi/config.php';
session_start();

$id_pengguna = $_SESSION['id_pengguna']; // Pastikan session diset saat login

$query = mysqli_query($conn, "SELECT * FROM pengguna WHERE id_pengguna = '$id_pengguna'");
$data = mysqli_fetch_assoc($query);
$role = $data['role']; // Tambahan ini penting!
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pengaturan Akun</title>
    <link rel="stylesheet" href="../CSS/stylepengaturan.css">
</head>
<body>
<?php 
    $isRestokPage = true; 
    $currentPage = 'restok';
    include '../sidebar/sidebar.php'; 
?>

<div class="container">

    <div class="data-box">
    <h2>Pengaturan Akun</h2> <!-- Pindahkan h2 ke sini -->

        <label>Id</label>
        <input type="text" value="<?= $data['id_pengguna'] ?>" readonly>

        <label>Nama</label>
        <input type="text" value="<?= htmlspecialchars($data['nama']) ?>" readonly>

        <label>Username</label>
        <input type="text" value="<?= htmlspecialchars($data['username']) ?>" readonly>

        <label>Password</label>
        <input type="password" value="xxxxxxxxxx" readonly>

        <label>Role</label>
        <input type="text" value="<?= htmlspecialchars($data['role']) ?>" readonly>

        <label>Tanggal Terdaftar</label>
        <input type="text" value="<?= date('d/m/Y', strtotime($data['tanggal_ditambahkan'])) ?>" readonly>
        <button class="btn-edit" onclick="window.location.href='edit_pengaturan.php'">Edit</button>

    </div>
</div>
</body>
</html>
