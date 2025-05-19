<?php

include '../koneksi/config.php';



if (session_status() == PHP_SESSION_NONE) {

    session_start();

}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_pengguna = $_POST['id_pengguna'];

    $nama = $_POST['nama'];

    $username = $_POST['username'];

    $role = $_POST['role'];



    if (!empty($_POST['password'])) {

        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $query = "UPDATE pengguna SET nama='$nama', username='$username', password='$password', role='$role' WHERE id_pengguna='$id_pengguna'";

    } else {

        $query = "UPDATE pengguna SET nama='$nama', username='$username', role='$role' WHERE id_pengguna='$id_pengguna'";

    }



    $result = mysqli_query($conn, $query);



    if ($result) {

        echo "<script>alert('Data berhasil diupdate'); window.location.href='pengguna.php';</script>";

    } else {

        echo "Gagal mengupdate data: " . mysqli_error($conn);

    }



} else {

    $id_pengguna = $_GET['id'];

    $query = mysqli_query($conn, "SELECT * FROM pengguna WHERE id_pengguna='$id_pengguna'");

    $data = mysqli_fetch_assoc($query);

}

?>



<!DOCTYPE html>

<html>

<head>

    <title>Edit Pengguna - EduStore</title>

    <link rel="stylesheet" href="../CSS/styleeditpengguna.css">

</head>

<body>

<?php 

    $currentPage = 'barang';

    include '../sidebar/sidebar.php';

?>

<div class="form-container">

    <div id="main-content">

        <div class="form-card">

            <div class="card shadow">

                <div class="card-body">

                    <h3 class="text-center mb-4">Edit Pengguna</h3>

                    <form method="POST" action="">

                        <input type="hidden" name="id_pengguna" value="<?= $data['id_pengguna'] ?>">



                        <div class="mb-3">

                            <label for="nama" class="form-label">Nama</label>

                            <input type="text" id="nama" name="nama" class="form-control" value="<?= $data['nama'] ?>" required>

                        </div>



                        <div class="mb-3">

                            <label for="username" class="form-label">Username</label>

                            <input type="text" id="username" name="username" class="form-control" value="<?= $data['username'] ?>" required>

                        </div>



                        <div class="mb-3">

                            <label for="password" class="form-label">Password (Kosongkan jika tidak diubah)</label>

                            <input type="password" id="password" name="password" class="form-control">

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

                            <input type="text" id="tanggal_ditambahkan" class="form-control" value="<?= $data['tanggal_ditambahkan'] ?>" readonly>

                        </div>



                        <div class="d-flex justify-content-between">

                            <button type="submit" class="btn btn-primary">Update</button>

                            <a href="pengguna.php" class="btn btn-secondary">Batal</a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>



<script>

function toggleSidebar() {

    const sidebar = document.querySelector('.sidebar');

    const container = document.querySelector('.container');



    sidebar.classList.toggle('collapsed');

    document.body.classList.toggle('sidebar-collapsed');

    toggleBtn.classList.toggle("moved");

}

</script>

</body>

</html>

