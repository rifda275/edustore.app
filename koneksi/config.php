<?php
$host = "localhost";  // Server database
$user = "root";       // Username XAMPP (default "root")
$pass = "";           // Password (kosong jika default XAMPP)
$db   = "edustore";   // Nama database

$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi berhasil atau tidak
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>
