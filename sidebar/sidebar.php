<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$base = (strpos($_SERVER['PHP_SELF'], 'restokbarang') !== false 
      || strpos($_SERVER['PHP_SELF'], 'transaksi') !== false 
      || strpos($_SERVER['PHP_SELF'], 'pengguna') !== false 
      || strpos($_SERVER['PHP_SELF'], 'barang') !== false 
      || strpos($_SERVER['PHP_SELF'], 'laporan') !== false 
      || strpos($_SERVER['PHP_SELF'], 'pengaturan') !== false)
      ? '../' : '';

$isRestokPage = strpos($_SERVER['PHP_SELF'], 'restok_barang.php') !== false;

// Tangani logout langsung di sini
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['keluar'])) {
    session_destroy();
    header("Location: {$base}login.php");
    exit();
}
?>

<div class="sidebar <?php echo ($isRestokPage ? 'restok-page' : ''); ?>" id="sidebar">
    <!-- Tombol toggle -->
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

    <div class="brand">
        <img src="<?= $base ?>icons/edustore.png" alt="EduStore Logo">
        <div class="user-info">
            Halo, <?php echo $_SESSION['nama']; ?> (<?php echo $_SESSION['role']; ?>)
        </div>
    </div>

    <ul>
        <!-- Tambahkan menu Home sebagai item pertama -->
        <li>
            <a href="<?= $base ?>dashboard_<?php echo strtolower($_SESSION['role']); ?>.php">
                <img src="<?= $base ?>icons/home.png"><span>Home</span>
            </a>
        </li>

        <?php if ($_SESSION['role'] == 'Admin') : ?>
            <li><a href="<?= $base ?>transaksi/transaksi.php"><img src="<?= $base ?>icons/transaksi.png"><span>Transaksi</span></a></li>
            <li><a href="<?= $base ?>barang/barang.php"><img src="<?= $base ?>icons/Tambah barang.png"><span>Barang</span></a></li>
            <li><a href="<?= $base ?>restokbarang/restok_barang.php"><img src="<?= $base ?>icons/restok barang.png"><span>Restok barang</span></a></li>
            <li><a href="<?= $base ?>pengguna/pengguna.php"><img src="<?= $base ?>icons/pengguna.png"><span>pengguna</span></a></li>
            <li><a href="<?= $base ?>laporan/laporan.php"><img src="<?= $base ?>icons/laporan.png"><span>Laporan</span></a></li>
            <li><a href="<?= $base ?>pengaturan/pengaturan.php"><img src="<?= $base ?>icons/pengaturan.png"><span>Pengaturan</span></a></li>

        <?php elseif ($_SESSION['role'] == 'Manager') : ?>
            <li><a href="<?= $base ?>barang/barang.php"><img src="<?= $base ?>icons/Tambah barang.png"><span>Barang</span></a></li>
            <li><a href="<?= $base ?>restokbarang/restok_barang.php"><img src="<?= $base ?>icons/restok barang.png"><span>Restok barang</span></a></li>
            <li><a href="<?= $base ?>laporan/laporan.php"><img src="<?= $base ?>icons/laporan.png"><span>Laporan</span></a></li>
            <li><a href="<?= $base ?>pengaturan/pengaturan.php"><img src="<?= $base ?>icons/pengaturan.png"><span>Pengaturan</span></a></li>

        <?php elseif ($_SESSION['role'] == 'Kasir') : ?>
            <li><a href="<?= $base ?>barang/barang.php"><img src="<?= $base ?>icons/Tambah barang.png"><span>Barang</span></a></li>
            <li><a href="<?= $base ?>transaksi/transaksi.php"><img src="<?= $base ?>icons/transaksi.png"><span>Transaksi</span></a></li>
            <li><a href="<?= $base ?>laporan/laporan.php"><img src="<?= $base ?>icons/laporan.png"><span>Laporan</span></a></li>
            <li><a href="<?= $base ?>pengaturan/pengaturan.php"><img src="<?= $base ?>icons/pengaturan.png"><span>Pengaturan</span></a></li>
        <?php endif; ?>

        <!-- Tombol logout trigger -->
        <li>
            <a href="#" onclick="tampilkanPopupLogout()">
                <img src="<?= $base ?>icons/logout.png"><span>Logout</span>
            </a>
        </li>
    </ul>

    <!-- Popup logout -->
    <div class="popup-overlay" id="popupLogout" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.4); justify-content:center; align-items:center; z-index:9999;">
        <form method="post">
            <div class="popup-box" style="background-color:#4d4d4d; color:white; padding:25px; border-radius:12px; width:300px; text-align:center; box-shadow:0 0 10px rgba(0,0,0,0.5);">
                <p style="margin-bottom:20px; font-size:14px;">Anda akan keluar dari akun ini. Lanjutkan?</p>
                <div style="display:flex; justify-content:space-between;">
                    <button type="button" onclick="sembunyikanPopupLogout()" style="padding:8px 16px; border:none; border-radius:6px; background-color:#b0b0b0; color:black;">Tidak</button>
                    <button type="submit" name="keluar" style="padding:8px 16px; border:none; border-radius:6px; background-color:#87c35e; color:white;">Iya</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("collapsed");
}
function tampilkanPopupLogout() {
    document.getElementById("popupLogout").style.display = "flex";
}
function sembunyikanPopupLogout() {
    document.getElementById("popupLogout").style.display = "none";
}
</script>
