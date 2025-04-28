<?php
include '../koneksi/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan.";
    exit;
}

$id = $_GET['id'];

// Proses update saat form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_barang = $_POST['id_barang'];
    $jumlah    = $_POST['jumlah'];
    $tanggal   = $_POST['tanggal_restok'];

    // ambil data lama
    $oldRes   = $conn->query("SELECT id_barang, jumlah FROM restokbarang WHERE id_restok = '$id'");
    $oldRow   = $oldRes->fetch_assoc();
    $old_id   = $oldRow['id_barang'];
    $old_jml  = $oldRow['jumlah'];

    // update restok
    $update = $conn->query("
        UPDATE restokbarang SET 
            id_barang    = '$id_barang', 
            jumlah       = '$jumlah', 
            tanggal_restok = '$tanggal' 
        WHERE id_restok = '$id'
    ");

    if ($update) {
        // sesuaikan stok di tabel barang
        if ($old_id == $id_barang) {
            // barang sama, adjust selisih
            $delta = $jumlah - $old_jml;
            if ($delta != 0) {
                $conn->query("
                    UPDATE barang 
                    SET stok = stok + $delta 
                    WHERE id_barang = $id_barang
                ");
            }
        } else {
            // barang diganti: kembalikan stok lama, baru tambahkan stok baru
            $conn->query("
                UPDATE barang 
                SET stok = stok - $old_jml 
                WHERE id_barang = $old_id
            ");
            $conn->query("
                UPDATE barang 
                SET stok = stok + $jumlah 
                WHERE id_barang = $id_barang
            ");
        }

        header("Location: restok_barang.php");
        exit();
    } else {
        echo "Gagal mengupdate data: " . $conn->error;
    }
}


// Ambil data restok berdasarkan id
$query = "SELECT * FROM restokbarang WHERE id_restok = '$id'";
$result = $conn->query($query);

if (!$result || $result->num_rows == 0) {
    echo "Data tidak ditemukan.";
    exit;
}

$data = $result->fetch_assoc();

// Ambil data barang untuk dropdown dan JS array
$barangQuery = $conn->query("SELECT id_barang, nama_barang, kode_barang FROM barang");

$jsBarangArray = [];
while ($barang = $barangQuery->fetch_assoc()) {
    $jsBarangArray[] = $barang;
}
$barangQuery = $conn->query("SELECT id_barang, nama_barang, kode_barang FROM barang"); // untuk HTML
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Restok Barang</title>
    <link rel="stylesheet" href="../CSS/styleeditrestok.css">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>
<body>
<?php 
    $isRestokPage = true; 
    $currentPage = 'restok';
    include '../sidebar/sidebar.php'; 
?>

<div class="container mt-5">
    <div class="card shadow" style="max-width: 500px; margin: auto;">
        <div class="kotak-form">
            <h2 class="card-title text-center mb-4">Edit Restok Barang</h2>
            <form method="post">
                <!-- Input kode barang -->
                <div class="mb-3">
                    <label for="kode_barang" class="form-label">Kode Barang</label>
                    <input type="text" class="form-control" name="kode_barang" id="kode_barang" placeholder="Scan atau ketik kode barang">
                </div>

               <div class="mb-3">
                    <label for="id_barang" class="form-label">Nama Barang</label>
                    <select class="form-select" name="id_barang" id="id_barang" required>
                        <?php while ($barang = $barangQuery->fetch_assoc()): ?>
                            <option value="<?= $barang['id_barang'] ?>" data-kode="<?= $barang['kode_barang'] ?>"
                                <?= $barang['id_barang'] == $data['id_barang'] ? 'selected' : '' ?>>
                                <?= $barang['nama_barang'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div> 

                <div class="mb-3">
                    <label for="jumlah" class="form-label">Jumlah</label>
                    <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?= $data['jumlah'] ?>" required>
                </div>

                <div class="mb-3">
                    <label for="tanggal_restok" class="form-label">Tanggal Restok</label>
                    <input type="date" class="form-control" name="tanggal_restok" value="<?= $data['tanggal_restok'] ?>" readonly>
                </div>
                
                <!-- Reader untuk QR/Barcode -->
                <div id="reader" style="width: 100%; margin-bottom: 15px;"></div>

                

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    <a href="restok_barang.php" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const barangList = <?= json_encode($jsBarangArray) ?>;

    function setBarangFromKode(kode) {
        const select = document.getElementById("id_barang");
        const option = select.querySelector(`option[data-kode='${kode}']`);
        if (option) {
            option.selected = true;
        } else {
            alert("Kode barang tidak ditemukan di database.");
        }
    }

    document.getElementById("kode_barang").addEventListener("change", function () {
        const kode = this.value.trim();
        if (kode !== "") {
            setBarangFromKode(kode);
        }
    });

    function onScanSuccess(decodedText, decodedResult) {
        document.getElementById("kode_barang").value = decodedText;
        setBarangFromKode(decodedText);
        html5QrcodeScanner.clear();
        alert("Kode barang berhasil dipindai: " + decodedText);
    }

    const html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", { fps: 10, qrbox: 250 }, false
    );
    html5QrcodeScanner.render(onScanSuccess);
    document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("toggleSidebar");
    const sidebar = document.querySelector(".sidebar");
    const container = document.querySelector(".container");

    toggleBtn.addEventListener("click", function () {
      sidebar.classList.toggle("collapsed");
      toggleBtn.classList.toggle("moved");
    });
  });

</script>

</body>
</html>
