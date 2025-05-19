<?php
include '../koneksi/config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_barang'])) {
    $id_barang = $_POST['id_barang'];
    $jumlah = $_POST['jumlah'];
    $tanggal = date('Y-m-d'); // Tanggal dari server
    $kategori = $_POST['kategori']; // New kategori field
    

    if (isset($_SESSION['id_pengguna']) && !empty($id_barang)) {
        $id_pengguna = $_SESSION['id_pengguna'];
    
        // Simpan data restok dengan kategori
        $insert = $conn->query("
            INSERT INTO restokbarang 
                (id_pengguna, id_barang, jumlah, tanggal_restok, kategori) 
            VALUES 
                ('$id_pengguna', '$id_barang', '$jumlah', '$tanggal', '$kategori')
        ");
    
        if ($insert) {
            // Baru perbarui stok barang
            $conn->query("
                UPDATE barang 
                SET stok = stok + $jumlah 
                WHERE id_barang = $id_barang
            ");
    
            echo "<script>
                    alert('Data berhasil disimpan!');
                    window.location.href='restok_barang.php';
                  </script>";
            exit();
        } else {
            echo "Gagal menyimpan data: " . $conn->error;
        }
    } else {
        echo "<script>
                alert('Gagal menyimpan: Pengguna belum login atau Barang tidak valid!');
                window.history.back();
              </script>";
        exit();
    }
    
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Restok Barang</title>
    <link rel="stylesheet" href="../CSS/styletambahrestok.css">
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
        <h2>Tambah Restok Barang</h2>
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
                    $query_barang = "SELECT id_barang, nama_barang, kode_barang FROM barang";
                    $result_barang = $conn->query($query_barang);

                    $jsBarangArray = []; // untuk JS
                    while ($barang = $result_barang->fetch_assoc()) {
                        echo "<option value='{$barang['id_barang']}' data-kode='{$barang['kode_barang']}'>{$barang['nama_barang']}</option>";
                        $jsBarangArray[] = [
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
                <input type="number" name="jumlah" id="jumlah" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="tanggal_restok" class="form-label">Tanggal Restok</label>
                <input type="date" name="tanggal_restok" id="tanggal_restok" class="form-control" value="<?= date('Y-m-d') ?>" readonly>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="restok_barang.php" class="btn btn-secondary">Kembali</a>
        </form>

        <!-- Scanner Barcode -->
        <div style="margin-top:30px;">
            <h3>Scan Barcode Barang</h3>
            <div id="reader" style="width:100%; max-width:400px; margin:auto;"></div>
        </div>
    </div>
</div>

<script>
    const barangList = <?= json_encode($jsBarangArray) ?>;

    function setBarangFromKode(kode) {
        const option = document.querySelector(`#id_barang option[data-kode='${kode}']`);
        if (option) {
            option.selected = true;
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