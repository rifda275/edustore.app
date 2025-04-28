<?php
session_start();
include '../koneksi/config.php';

$role = $_SESSION['role']; // ✅ Tambahan sesuai permintaan

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM pengguna");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Pengguna</title>
    <link rel="stylesheet" href="../CSS/stylepengguna.css">
</head>
<body>
<?php 
    $currentPage = 'barang';
    include '../sidebar/sidebar.php'; 
?>
    <div class="container"> <!-- ✅ Wrapper untuk konten utama -->
        <h2>Data Pengguna</h2>
            <!-- Tombol kembali ke dashboard -->
    
        <a href="tambahpengguna.php" class="btn btn-primary">+Tambah Pengguna</a>
        <div class="table-wrapper">
            <table border="1" cellpadding="10" cellspacing="0">
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Nama</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
                <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($query)) :
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars(ucfirst($row['role'])) ?></td>
                    <td>
                        <a href="editpengguna.php?id=<?= $row['id_pengguna'] ?>" class="btn btn-warning">Edit</a>
                        <?php if ($role == 'Admin'): ?>
                            <a href="hapuspengguna.php?id=<?= $row['id_pengguna'] ?>" onclick="return confirm('Yakin ingin menghapus?')" class="btn btn-danger">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
