<?php
include "../koneksi/config.php"; 

$id = $_GET['id'];
$query = "DELETE FROM barang WHERE id_barang = '$id'";

if ($conn->query($query)) {
    echo "<script>alert('Barang berhasil dihapus!'); window.location='barang.php';</script>";
}
?>