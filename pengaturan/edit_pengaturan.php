<?php

include '../koneksi/config.php';

session_start();



$id_pengguna = $_SESSION['id_pengguna']; // dari session login



// Ambil data lama

$query = mysqli_query($conn, "SELECT * FROM pengguna WHERE id_pengguna = '$id_pengguna'");

$data = mysqli_fetch_assoc($query);



// Proses update jika form dikirim

if (isset($_POST['update'])) {

    $nama = $_POST['nama'];

    $username = $_POST['username'];

    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $data['password'];

    $role = $_POST['role'];



    $update = mysqli_query($conn, "UPDATE pengguna SET 

        nama = '$nama',

        username = '$username',

        password = '$password',

        role = '$role'

        WHERE id_pengguna = '$id_pengguna'

    ");



    if ($update) {

        echo "<script>alert('Data berhasil diperbarui'); window.location='pengaturan.php';</script>";

    } else {

        echo "<script>alert('Gagal memperbarui data');</script>";

    }

}

?>



<!DOCTYPE html>

<html>

<head>

    <title>Edit Akun</title>

    <link rel="stylesheet" href="../CSS/styleeditpengaturan.css">

</head>

<body>

<?php 

    $isRestokPage = true; 

    $currentPage = 'restok';

    include '../sidebar/sidebar.php'; 

?>

<div class="container">

    <div class="data-box">

    <h2>Edit Akun</h2>

        <form method="POST">

            <label>Nama</label>

            <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>



            <label>Username</label>

            <input type="text" name="username" value="<?= htmlspecialchars($data['username']) ?>" required>



            <label>Password (kosongkan jika tidak diubah)</label>

            <input type="password" name="password">


	 <label>Role</label>

	<input type="text" name="role"value="<?= htmlspecialchars($data['role']) ?>" readonly>


            <button type="submit" name="update" class="btn-submit">Simpan Perubahan</button>

            <button type="button" name="Cancel" class="btn-Cancel" onclick="window.location.href='pengaturan.php'">Kembali</button>

        </form>

    </div>

</div>





</body>

</html>

