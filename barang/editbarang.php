<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include "../koneksi/config.php";

if (!isset($_GET['id'])) {
    die("Error: ID Barang tidak ditemukan.");
}

$id_barang = $_GET['id'];

// Ambil data barang
$query = "SELECT * FROM barang WHERE id_barang=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_barang);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Error: Data barang tidak ditemukan.");
}

// Ambil data kategori untuk dropdown
$query_kategori = "SELECT * FROM kategoribarang";
$result_kategori = $conn->query($query_kategori);

// Proses simpan update barang
if (isset($_POST['simpan'])) {
    $kode_barang = $_POST['kode_barang'];
    $nama_barang = $_POST['nama_barang'];
    $id_kategori = $_POST['id_kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    if ($_FILES['gambar']['name'] != "") {
        $gambar = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];
        $folder = "../uploads/";

        $ekstensi_diperbolehkan = ['jpg', 'jpeg', 'png', 'gif'];
        $ekstensi = strtolower(pathinfo($gambar, PATHINFO_EXTENSION));
        $ukuran = $_FILES['gambar']['size'];

        if (!in_array($ekstensi, $ekstensi_diperbolehkan)) {
            echo "<script>alert('Format gambar tidak valid.');</script>";
        } elseif ($ukuran > 2097152) {
            echo "<script>alert('Ukuran gambar terlalu besar.');</script>";
        } else {
            $nama_gambar_baru = time() . '_' . $gambar;
            if (move_uploaded_file($tmp, $folder . $nama_gambar_baru)) {
                $query_update = "UPDATE barang SET kode_barang=?, nama_barang=?, id_kategori=?, harga=?, stok=?, gambar=? WHERE id_barang=?";
                $stmt = $conn->prepare($query_update);
                $stmt->bind_param("ssiiisi", $kode_barang, $nama_barang, $id_kategori, $harga, $stok, $nama_gambar_baru, $id_barang);
            } else {
                echo "<script>alert('Gagal mengupload gambar.');</script>";
            }
        }
    } else {
        $query_update = "UPDATE barang SET kode_barang=?, nama_barang=?, id_kategori=?, harga=?, stok=? WHERE id_barang=?";
        $stmt = $conn->prepare($query_update);
        $stmt->bind_param("ssiiii", $kode_barang, $nama_barang, $id_kategori, $harga, $stok, $id_barang);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location='barang.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data barang!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Barang</title>
  <link rel="stylesheet" href="../CSS/styleeditbarang.css" />
  <style>
    #reader {
      width: 300px;
      margin: 20px auto;
    }
  </style>
</head>
<body>

<?php 
    $currentPage = 'barang';
    include '../sidebar/sidebar.php'; 
?>

<div class="container">
  <div class="card-form">
    <h2>Edit Barang</h2>
    <form method="POST" enctype="multipart/form-data">
      <div>
        <label>Kode Barang:</label>
        <input type="text" id="kode_barang" name="kode_barang" value="<?= htmlspecialchars($data['kode_barang']) ?>" required>
      </div>

      <div>
        <label>Nama Barang:</label>
        <input type="text" name="nama_barang" value="<?= htmlspecialchars($data['nama_barang']) ?>" required>
      </div>

      <div>
        <label>Kategori:</label>
        <select name="id_kategori" required>
          <option value="">Pilih Kategori</option>
          <?php while ($row = $result_kategori->fetch_assoc()) { ?>
            <option value="<?= $row['id_kategori'] ?>" <?= ($row['id_kategori'] == $data['id_kategori']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($row['nama_kategori']) ?>
            </option>
          <?php } ?>
        </select>
      </div>

      <div>
        <label>Harga:</label>
        <input type="number" name="harga" value="<?= $data['harga'] ?>" required>
      </div>

      <div>
        <label>Stok:</label>
        <input type="number" name="stok" value="<?= $data['stok'] ?>" required>
      </div>

      <div>
        <label>Tanggal Ditambahkan:</label>
        <input type="date" name="tanggal_ditambahkan" value="<?= date('Y-m-d', strtotime($data['tanggal_ditambahkan'])) ?>" readonly>
      </div>

      <div class="full-width">
        <label>Gambar Saat Ini:</label><br>
        <?php if (!empty($data['gambar'])) { ?>
          <img src="../uploads/<?= $data['gambar'] ?>" width="100"><br>
        <?php } else { ?>
          <i>Tidak ada gambar</i><br>
        <?php } ?>
      </div>

      <div class="full-width">
        <label>Gambar Baru (jika ingin diganti):</label>
        <input type="file" name="gambar" accept="image/*">
      </div>

      <div style="margin-top: 5px;">
        <h3>Atau scan barcode di bawah ini:</h3>
        <div id="reader" style="width: 100%; max-width: 500px; margin: auto;"></div>
      </div>

      <div style="text-align:center;">
        <button type="submit" name="simpan" class="save-btn">Simpan Perubahan</button>
        <button type="button" class="cancel-btn" onclick="window.location.href='barang.php'">Batal</button>
      </div>
    </form>
  </div> 
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
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