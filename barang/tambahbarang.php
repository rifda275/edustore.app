<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include "../koneksi/config.php";

// Ambil data kategori untuk dropdown/datalist
$kategoriQuery  = "SELECT * FROM kategoribarang";
$kategoriResult = $conn->query($kategoriQuery);
if (!$kategoriResult) {
    die("Query kategori gagal: " . $conn->error);
}

// Proses form jika disubmit
if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $kode_barang = trim($_POST['kode_barang']);
    $nama_barang = trim($_POST['nama_barang']);
    $harga       = $_POST['harga'];
    $stok        = $_POST['stok'];
    $gambar      = '';

    // --- PENANGANAN KATEGORI DINAMIS ---
    $kategori_input = trim($_POST['kategori']);
    $id_kategori    = null;

    // Cek apakah format "id|nama" (pilihan lama)
    if (strpos($kategori_input, '|') !== false) {
        list($possible_id, $possible_name) = explode('|', $kategori_input, 2);
        if (ctype_digit($possible_id)) {
            $cek = $conn->prepare("SELECT nama_kategori FROM kategoribarang WHERE id_kategori = ?");
            $cek->bind_param("i", $possible_id);
            $cek->execute();
            $cek->bind_result($nama_db);
            if ($cek->fetch() && $nama_db === $possible_name) {
                $id_kategori = (int)$possible_id;
            }
            $cek->close();
        }
    }

    // Jika belum dapat ID (kategori baru atau input bebas)
    if (!$id_kategori) {
        $ins = $conn->prepare("INSERT INTO kategoribarang (nama_kategori) VALUES (?)");
        $ins->bind_param("s", $kategori_input);
        if (!$ins->execute()) {
            die("Gagal menambah kategori baru: " . $ins->error);
        }
        $id_kategori = $ins->insert_id;
        $ins->close();
    }
    // --- END PENANGANAN KATEGORI ---

    // Upload gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $target_dir    = "../uploads/";
        $file_name     = basename($_FILES["gambar"]["name"]);
        $target_file   = $target_dir . time() . "_" . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed       = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed)) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar = basename($target_file);
            } else {
                die("Gagal mengupload gambar.");
            }
        } else {
            die("Hanya file JPG, PNG, dan GIF yang diperbolehkan.");
        }
    } else {
        die("Tidak ada gambar yang diupload.");
    }

    // Simpan ke database barang
    $query = "INSERT INTO barang 
              (kode_barang, nama_barang, id_kategori, harga, stok, tanggal_ditambahkan, gambar) 
              VALUES (?, ?, ?, ?, ?, NOW(), ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Query Prepare gagal: " . $conn->error);
    }
    $stmt->bind_param("ssiiss", $kode_barang, $nama_barang, $id_kategori, $harga, $stok, $gambar);

    if ($stmt->execute()) {
        echo "<script>
                alert('Barang berhasil ditambahkan!');
                window.location = 'barang.php';
              </script>";
        exit;
    } else {
        die("Error saat menyimpan barang: " . $stmt->error);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah Barang</title>
  <link rel="stylesheet" href="../CSS/styletambahbarang.css">
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
        <label for="kode_barang">Kode Barang</label>
        <input type="text" name="kode_barang" id="kode_barang" required placeholder="Scan atau ketik kode barang">

        <label for="nama_barang">Nama Barang</label>
        <input type="text" name="nama_barang" id="nama_barang" required>

        <label for="kategori">Kategori</label>
        <input list="kategoriList" name="kategori" id="kategori" required
               placeholder="Pilih atau ketik kategori baru" />
        <datalist id="kategoriList">
          <?php
            // Reset pointer & tampilkan ulang opsi
            $kategoriResult->data_seek(0);
            while ($kat = $kategoriResult->fetch_assoc()):
          ?>
            <option value="<?= $kat['id_kategori'] ?>|<?= htmlspecialchars($kat['nama_kategori']) ?>">
              <?= htmlspecialchars($kat['nama_kategori']) ?>
            </option>
          <?php endwhile; ?>
        </datalist>

        <label for="harga">Harga</label>
        <input type="number" name="harga" id="harga" required>

        <label for="stok">Stok</label>
        <input type="number" name="stok" id="stok" required>

        <label for="gambar">Upload Gambar</label>
        <input type="file" name="gambar" id="gambar" accept="image/*" required>

        <button type="submit" class="save-btn">Simpan</button>
        <button type="button" class="cancel-btn" onclick="window.location.href='barang.php'">Batal</button>
      </form>

      <div id="reader"></div>
    </div>
  </div>

  <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
  <script>
    function onScanSuccess(decodedText) {
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
      const sidebar   = document.querySelector(".sidebar");

      toggleBtn.addEventListener("click", function () {
        sidebar.classList.toggle("collapsed");
        toggleBtn.classList.toggle("moved");
      });
    });
  </script>
</body>
</html>
