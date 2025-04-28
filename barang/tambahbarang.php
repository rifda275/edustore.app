<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include "../koneksi/config.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $kode_barang = trim($_POST['kode_barang']);
    $nama_barang = $_POST['nama_barang'];
    $nama_kategori = trim($_POST['kategori']);
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $gambar = '';

    // Upload gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_name = basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowed)) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar = basename($target_file);
            } else {
                echo "Gagal mengupload gambar.";
                exit;
            }
        } else {
            echo "Hanya file JPG, PNG, dan GIF yang diperbolehkan.";
            exit;
        }
    }

    // Cek kategori
    $query_check = "SELECT id_kategori FROM kategoribarang WHERE nama_kategori = ?";
    $stmt_check = $conn->prepare($query_check);
    $stmt_check->bind_param("s", $nama_kategori);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $row = $result_check->fetch_assoc();
        $id_kategori = $row['id_kategori'];
    } else {
        $query_insert_kategori = "INSERT INTO kategoribarang (nama_kategori) VALUES (?)";
        $stmt_insert_kategori = $conn->prepare($query_insert_kategori);
        $stmt_insert_kategori->bind_param("s", $nama_kategori);
        $stmt_insert_kategori->execute();
        $id_kategori = $stmt_insert_kategori->insert_id;
    }

    // Simpan ke database
    $query = "INSERT INTO barang (kode_barang, nama_barang, id_kategori, harga, stok, tanggal_ditambahkan, gambar) 
              VALUES (?, ?, ?, ?, ?, NOW(), ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssiids", $kode_barang, $nama_barang, $id_kategori, $harga, $stok, $gambar);

    if ($stmt->execute()) {
        echo "<script>alert('Barang berhasil ditambahkan!'); window.location='barang.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tambah Barang</title>
  <link rel="stylesheet" href="../CSS/styletambahbarang.css"> 
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
    <h3>Edu Store</h3>
    <a href="barang.php">Barang</a>

  <div class="container">
    <div class="card-form">
      <h2>Form Input Barang</h2>
      <form method="POST" enctype="multipart/form-data">
        <label>Kode Barang</label>
        <input type="text" name="kode_barang" id="kode_barang" required placeholder="Scan atau ketik kode barang">

        <label>Nama Barang</label>
        <input type="text" name="nama_barang" required>

        <label>Kategori</label>
        <input type="text" name="kategori" required>

        <label>Harga</label>
        <input type="number" name="harga" required>

        <label>Stok</label>
        <input type="number" name="stok" required>

        <label for="tanggal_ditambahkan">Tanggal Ditambahkan</label>
        <input type="date" name="tanggal_ditambahkan" id="tanggal_ditambahkan"
               value="<?php echo date('Y-m-d'); ?>" readonly>

        <label>Upload Gambar</label>
        <input type="file" name="gambar" accept="image/*">

        <button type="submit" class="save-btn">Simpan</button>
        <button type="button" class="cancel-btn" onclick="window.location.href='barang.php'">Batal</button>
      </form>

      <!-- Scanner Kamera (Optional) -->
      <div id="reader"></div>
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
