
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../koneksi/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_restok = $_GET['id'] ?? null;
if (!$id_restok) {
    echo "<script>alert('ID Restok tidak ditemukan!'); window.location.href='restok_barang.php';</script>";
    exit();
}

$sql = "SELECT * FROM restokbarang WHERE id_restok = '$id_restok'";
$result = $conn->query($sql);
if ($result->num_rows === 0) {
    echo "<script>alert('Data restok tidak ditemukan!'); window.location.href='restok_barang.php';</script>";
    exit();
}
$data = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id_barang'], $_POST['jumlah'], $_POST['harga_beli'])) {
        $id_barang = $_POST['id_barang'];
        $jumlah_baru = $_POST['jumlah'];
        $harga_beli = $_POST['harga_beli'];
        $jumlah_lama = $data['jumlah'];
        $selisih_stok = $jumlah_baru - $jumlah_lama;

        if (isset($_SESSION['id_pengguna']) && !empty($id_barang)) {
            $id_pengguna = $_SESSION['id_pengguna'];

            $update = $conn->query("
                UPDATE restokbarang 
                SET id_barang = '$id_barang', jumlah = '$jumlah_baru', harga_beli = '$harga_beli' 
                WHERE id_restok = '$id_restok'
            ");
            if (!$update) {
                die("Gagal memperbarui data restok: " . $conn->error);
            }

            $conn->query("UPDATE barang SET stok = stok + $selisih_stok WHERE id_barang = '$id_barang'");

            $query_harga_jual = "SELECT harga FROM barang WHERE id_barang = '$id_barang'";
            $result_harga_jual = $conn->query($query_harga_jual);
            $harga_jual = ($result_harga_jual->num_rows > 0) ? $result_harga_jual->fetch_assoc()['harga'] : 0;

            $laba_rugi_rupiah = ($harga_jual - $harga_beli) * $jumlah_baru;
            $laba_rugi_persen = ($harga_beli > 0) ? ($laba_rugi_rupiah / ($harga_beli * $jumlah_baru)) * 100 : 0;

            echo "<script>
                alert('Data berhasil diperbarui!\\nLaba/Rugi: Rp " . number_format($laba_rugi_rupiah, 0, ',', '.') . " (" . number_format($laba_rugi_persen, 2, ',', '.') . "%)');
                window.location.href='restok_barang.php';
            </script>";
            exit();
        } else {
            echo "<script>alert('Pengguna belum login atau data tidak valid!'); window.history.back();</script>";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Restok Barang</title>
    <link rel="stylesheet" href="../CSS/styleeditrestok.css">
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body>
<?php
    $isRestokPage = true;
    $currentPage = 'restok';
    include '../sidebar/sidebar.php';
?>
<div class="container">
    <div class="kotak-form">
        <h2>Edit Restok Barang</h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="kode_barang" class="form-label">Scan / Masukkan Kode Barang</label>
                <input type="text" id="kode_barang" class="form-control" placeholder="Scan barcode atau ketik kode...">
            </div>

            <div class="mb-3">
                <label for="id_barang" class="form-label">Nama Barang</label>
                <select name="id_barang" id="id_barang" class="form-select" required>
                    <option value="">-- Pilih Barang --</option>
                    <?php
                    $barangArray = [];
                    $query_barang = "SELECT id_barang, nama_barang, kode_barang FROM barang";
                    $result_barang = $conn->query($query_barang);
                    while ($barang = $result_barang->fetch_assoc()) {
                        $selected = ($barang['id_barang'] == $data['id_barang']) ? "selected" : "";
                        echo "<option value='{$barang['id_barang']}' data-kode='{$barang['kode_barang']}' $selected>{$barang['nama_barang']}</option>";
                        $barangArray[] = [
                            'id' => $barang['id_barang'],
                            'kode' => $barang['kode_barang']
                        ];
                    }
                    ?>
                </select>
            </div>
 <!-- Tambahkan field Kategori -->
            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori</label>
               <select name="kategori" id="kategori" class="form-select" required>
    <option value="">-- Pilih Kategori --</option>
    <?php
    $kategoriQuery = $conn->query("SELECT nama_kategori FROM kategoribarang");
    while ($kategori = $kategoriQuery->fetch_assoc()) {
        echo "<option value='" . htmlspecialchars($kategori['nama_kategori']) . "'>" . htmlspecialchars($kategori['nama_kategori']) . "</option>";
    }
    ?>
</select>
            </div>


            <div class="mb-3">
                <label for="jumlah" class="form-label">Jumlah</label>
                <input type="number" name="jumlah" id="jumlah" class="form-control" required value="<?= $data['jumlah'] ?>">
            </div>

            <div class="mb-3">
                <label for="tanggal_restok" class="form-label">Tanggal Restok</label>
                <input type="date" name="tanggal_restok" id="tanggal_restok" class="form-control" value="<?= $data['tanggal_restok'] ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="harga_beli" class="form-label">Harga Beli</label>
                <input type="number" name="harga_beli" id="harga_beli" class="form-control" required value="<?= $data['harga_beli'] ?>">
            </div>

          <button type="submit" class="btn btn-warning" style="background-color: #527253; color: #D2B48C;">Update</button>
            <a href="restok_barang.php" class="btn btn-secondary">Kembali</a>
        </form>

        <div style="margin-top:30px;">
            <h3>Scan Barcode Barang</h3>
            <div id="reader" style="width:100%; max-width:400px; margin:auto;"></div>
        </div>
    </div>
</div>

<script>
    const barangList = <?= json_encode($barangArray) ?>;

    function setBarangFromKode(kode) {
        const option = document.querySelector(`#id_barang option[data-kode='${kode}']`);
        if (option) {
            option.selected = true;
            document.getElementById("id_barang").dispatchEvent(new Event('change'));
        } else {
            alert("Kode barang tidak ditemukan di database.");
        }
    }

    document.getElementById("kode_barang").addEventListener("change", function () {
        const kode = this.value.trim();
        setBarangFromKode(kode);
    });

    function onScanSuccess(decodedText, decodedResult) {
        document.getElementById("kode_barang").value = decodedText;
        setBarangFromKode(decodedText);
        html5QrcodeScanner.clear();
        alert("Kode barang berhasil dipindai: " + decodedText);
    }

    const html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        { fps: 10, qrbox: 250 },
        false
    );
    html5QrcodeScanner.render(onScanSuccess);
</script>
</body>
</html>
