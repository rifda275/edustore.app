<?php
// Mulai session jika belum
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';      // atau bisa juga '127.0.0.1'
$user = 'u1659760_edustore';
$pass = 'R3&m4SEt';
$db   = 'u1659760_edustore';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
} else {
    // Simpan pesan "Koneksi berhasil!" sebagai flash message
    // tanpa langsung menampilkannya di semua halaman
    if (!isset($_SESSION['koneksi_status'])) {
        $_SESSION['koneksi_status'] = 'Koneksi berhasil!';
    }
}
