<?php 
include '../koneksi/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit();
}

$role = $_SESSION['role'];

// Jika role adalah kasir, redirect ke dashboard
if ($role === 'kasir') {
    header("Location: ../dashboard/kasir_dashboard.php");
    exit();
}

// Ambil data restok + stok saat ini dari tabel barang
$query = "
    SELECT 
        r.id_restok, 
        b.nama_barang, 
        b.kode_barang, 
        b.stok,             /* Tambahan: stok kini */
        r.jumlah, 
        r.tanggal_restok 
    FROM restokbarang r
    JOIN barang b ON r.id_barang = b.id_barang
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Restok Barang</title>
    <link rel="stylesheet" href="../CSS/stylerestok.css">
</head>
<body>

<?php 
    $isRestokPage = true; 
    $currentPage = 'restok';
    include '../sidebar/sidebar.php'; 
?>

<div class="container">
    <div class="header-restok">
        <h2>Data Restok Barang</h2>


        <?php if ($role === 'Admin' || $role === 'Manager'): ?>
            <a href="tambah_restok.php" class="btn btn-success">+ Tambah Restok</a>
        <?php endif; ?>
    </div>

    <div class="table-wrapper">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Stok Saat Ini</th>        <!-- Tambahan header -->
                    <th>Jumlah Restok</th>
                    <th>Tanggal Restok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_restok'] ?></td>
                        <td><?= $row['kode_barang'] ?></td>
                        <td><?= $row['nama_barang'] ?></td>
                        <td><?= $row['stok'] ?></td>          <!-- Tampilkan stok -->
                        <td><?= $row['jumlah'] ?></td>
                        <td><?= date("Y-m-d", strtotime($row['tanggal_restok'])) ?></td>
                        <td>
                            <?php if ($role === 'Admin' || $role === 'Manager'): ?>
                                <form action="edit_restok.php" method="get" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $row['id_restok'] ?>">
                                    <button type="submit" class="btn btn-primary">Edit</button>
                                </form>
                            <?php endif; ?>

                            <?php if ($role === 'Admin'): ?>
                                <form action="hapus_restok.php" method="post" style="display:inline;" 
                                      onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                    <input type="hidden" name="id" value="<?= $row['id_restok']; ?>">
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
