<?php

session_start();

if (!isset($_SESSION['username'])) {

    header("Location: login.php");

    exit();

}

include 'config.php';



// Hapus data jika ada request

if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    $conn->query("DELETE FROM restokbarang WHERE id_restok=$id");

    header("Location: restok_list.php");

    exit();

}



// Ambil data dari database

$result = $conn->query("SELECT r.id_restok, b.nama_barang, r.jumlah, r.tanggal_restok FROM restokbarang r JOIN barang b ON r.id_barang = b.id_barang");

?>



<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Restok Barang</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

</head>

<body>

    <div class="container mt-4">

        <h2>Data Restok Barang</h2>

        <a href="tambah_restok.php" class="btn btn-primary mb-3">Tambah Restok</a>

        <table class="table table-bordered">

            <thead class="table-dark">

                <tr>

                    <th>ID</th>

                    <th>Barang</th>

                    <th>Jumlah</th>

                    <th>Tanggal Restok</th>

                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

                <?php while ($row = $result->fetch_assoc()): ?>

                    <tr>

                        <td><?php echo $row['id_restok']; ?></td>

                        <td><?php echo $row['nama_barang']; ?></td>

                        <td><?php echo $row['jumlah']; ?></td>

                        <td><?php echo $row['tanggal_restok']; ?></td>

                        <td>

                            <a href="edit_restok.php?id=<?php echo $row['id_restok']; ?>" class="btn btn-warning">Edit</a>

                            <a href="restok_list.php?hapus=<?php echo $row['id_restok']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>

                        </td>

                    </tr>

                <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</body>

</html>

