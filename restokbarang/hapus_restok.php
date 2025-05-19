<?php

include '../koneksi/config.php';

error_reporting(E_ALL);

ini_set('display_errors', 1);



$id = $_GET['id'] ?? $_POST['id'] ?? null;



if ($id !== null) {

    $query = "DELETE FROM restokbarang WHERE id_restok = '$id'";

    $result = mysqli_query($conn, $query);



    if ($result) {

        header("Location: restok_barang.php");

        exit;

    } else {

        echo "Gagal menghapus data: " . mysqli_error($conn);

    }

} else {

    echo "ID tidak ditemukan.";

}