<?php

include '../koneksi/config.php';



if (session_status() == PHP_SESSION_NONE) {

    session_start();

}



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama = $_POST['nama'];

    $username = $_POST['username'];

    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $role = isset($_POST['role']) && !empty($_POST['role']) ? $_POST['role'] : 'User';



    $cek_user = $conn->prepare("SELECT id_pengguna FROM pengguna WHERE username = ?");

    $cek_user->bind_param("s", $username);

    $cek_user->execute();

    $cek_user->store_result();



    if ($cek_user->num_rows > 0) {

        echo "<script>alert('Username sudah digunakan!'); window.location.href='tambahpengguna.php';</script>";

        exit;

    }

    $cek_user->close();



    $sql = "INSERT INTO pengguna (nama, username, password, role) VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ssss", $nama, $username, $password, $role);



    if ($stmt->execute()) {

        echo "<script>alert('Pengguna berhasil ditambahkan!'); window.location.href='pengguna.php';</script>";

    } else {

        echo "<script>alert('Gagal menyimpan data! Coba lagi.'); window.location.href='tambahpengguna.php';</script>";

    }



    $stmt->close();

    $conn->close();

}

?>



<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Pengguna</title>

    <link rel="stylesheet" href="../CSS/styletambahpengguna.css">

</head>

<body>

<?php 

    $currentPage = 'barang';

    include '../sidebar/sidebar.php'; 

?>

<body class="sidebar-open">

<div class="container form-card">

    <div class="card shadow">

        <div class="card-body">

            <h3 class="text-center mb-4">Tambah Pengguna</h3>

            <form action="" method="POST">

                <div class="mb-3">

                    <label for="nama" class="form-label">Nama</label>

                    <input type="text" class="form-control" id="nama" name="nama" required>

                </div>

                <div class="mb-3">

                    <label for="username" class="form-label">Username</label>

                    <input type="text" class="form-control" id="username" name="username" required>

                </div>

                <div class="mb-3">

                    <label for="password" class="form-label">Password</label>

                    <input type="password" class="form-control" id="password" name="password" required>

                </div>

                <div class="mb-3">

                    <label for="role" class="form-label">Role</label>

                    <select class="form-control" id="role" name="role">

                        <option value="Admin">Admin</option>

                        <option value="Manager">Manager</option>

                        <option value="Kasir" selected>Kasir</option>

                    </select>

                </div>

                <div class="mb-3">

                    <label for="tanggal_ditambahkan" class="form-label">Tanggal Ditambahkan</label>

                    <input type="text" class="form-control" id="tanggal_ditambahkan" value="<?php echo date('Y-m-d'); ?>" readonly>

                </div>

                <div class="d-flex justify-content-between">

                    <button type="submit" name="submit" class="btn btn-primary">Simpan</button>

                    <a href="pengguna.php" class="btn btn-secondary">Batal</a>

                </div>

            </form>

        </div>

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

