<?php

include "../koneksi/config.php"; 



$id = $_GET['id'];

$query = "DELETE FROM pengguna WHERE id_pengguna = '$id'";



if ($conn->query($query)) {

    echo "<script>alert('Barang berhasil dihapus!'); window.location='pengguna.php';</script>";

}

?>