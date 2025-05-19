<?php
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit();
}

$role = $_SESSION['role'];

include "../koneksi/config.php";

// Ambil semua kategori untuk dropdown
$kategoriResult = $conn->query("SELECT * FROM kategoribarang");

// Ambil kategori yang dipilih dari dropdown (jika ada)
$kategoriFilter = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Query barang (dengan filter jika ada)
$query = "SELECT barang.id_barang, barang.nama_barang, kategoribarang.nama_kategori, 
                 barang.harga, barang.stok, barang.tanggal_ditambahkan, barang.gambar, barang.kode_barang
          FROM barang 
          JOIN kategoribarang ON barang.id_kategori = kategoribarang.id_kategori";

if (!empty($kategoriFilter)) {
    $query .= " WHERE barang.id_kategori = '" . $conn->real_escape_string($kategoriFilter) . "'";
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Barang</title>
    <link rel="stylesheet" href="../CSS/stylesbarang.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<?php 
    $currentPage = 'barang';
    include '../sidebar/sidebar.php'; 
?>

<div class="container">
    <h2>Daftar Barang</h2>

    <?php if ($role === 'Admin' || $role === 'Manager'): ?>
        <a href="tambahbarang.php" class="btn btn-success">+ Tambah Barang</a>
    <?php endif; ?>

    <!-- Dropdown Filter Kategori -->
<form method="get" action="" style="margin-top: 1em; margin-bottom: 1em;">
    <label for="kategori" style="color: #D2B48C; font-weight: bold;">Filter Kategori:</label>
    <select name="kategori" id="kategori">
        <option value="">-- Semua Kategori --</option>
        <?php while ($kat = $kategoriResult->fetch_assoc()) : ?>
            <option value="<?= $kat['id_kategori'] ?>" <?= ($kategoriFilter == $kat['id_kategori']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($kat['nama_kategori']) ?>
            </option>
        <?php endwhile; ?>
    </select>
    <button type="submit" class="btn btn-primary">Tampilkan</button>
</form>

    <table>
        <tr>
            <th>ID</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Tanggal Ditambahkan</th>
            <th>Gambar</th>
            <th>Barcode</th>
            <?php if ($role !== 'Kasir'): ?>
                <th class="aksi-col">Aksi</th>
            <?php endif; ?>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['id_barang']) ?></td>
                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                <td>Rp<?= number_format($row['harga'], 0, ',', '.') ?></td>
                <td><?= htmlspecialchars($row['stok']) ?></td>
                <td><?= htmlspecialchars($row['tanggal_ditambahkan']) ?></td>
                <td>
                    <?php if (!empty($row['gambar'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($row['gambar']) ?>" class="thumbnail" alt="Gambar Barang">
                    <?php else: ?>
                        Tidak Ada Gambar
                    <?php endif; ?>
                </td>
                <td>
                    <?= !empty($row['kode_barang']) ? htmlspecialchars($row['kode_barang']) : 'Tidak Ada Barcode' ?>
                </td>
                <?php if ($role !== 'Kasir'): ?>
                <td>
     <?php if ($role === 'Admin' || $role === 'Manager'): ?>
                        <!-- Tombol Edit -->
                        <form action="editbarang.php" method="get" style="display:inline;">
    <input type="hidden" name="id" value="<?= $row['id_barang'] ?>">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-pencil-alt"></i>
    </button>
</form>                    <?php endif; ?>

                    <?php if ($role === 'Admin'): ?>
                        <!-- Tombol Hapus -->
                        <form action="hapus_barang.php" method="get" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus?');">
    <input type="hidden" name="id" value="<?= $row['id_barang'] ?>">
    <button type="submit" class="btn btn-danger">
        <i class="fas fa-trash"></i>
    </button>
</form>                    
<?php endif; ?>
                </td>
                <?php endif; ?>            
</tr>
        <?php } ?>
    </table>
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
  window.scrollTo({ top: 0, behavior: "auto" });

</script>

</body>
</html>
