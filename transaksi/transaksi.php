<?php
session_start();
include "../koneksi/config.php";

// Pastikan user sudah login
if (!isset($_SESSION['id_pengguna'])) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location.href='../login.php';</script>";
    exit;
}
$id_pengguna = $_SESSION['id_pengguna'];

// Proses simpan transaksi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['items'], $_POST['metode'], $_POST['bayar'])) {
    $items = json_decode($_POST['items'], true);
    $metode = $_POST['metode'];
    $bayar  = floatval($_POST['bayar']);
    $total  = 0;
    foreach ($items as $item) {
        $total += $item['subtotal'];
    }
    $stmt = $conn->prepare("
        INSERT INTO transaksi 
            (tanggal_transaksi, total_harga, metode_pembayaran, id_pengguna, bayar) 
        VALUES (NOW(), ?, ?, ?, ?)
    ");
    $stmt->bind_param("dsid", $total, $metode, $id_pengguna, $bayar);
    if ($stmt->execute()) {
        $id_transaksi = $stmt->insert_id;
        $stmt_detail = $conn->prepare("
            INSERT INTO detailtransaksi 
                (id_transaksi, id_barang, jumlah, subtotal) 
            VALUES (?, ?, ?, ?)
        ");
        foreach ($items as $item) {
            $stmt_detail->bind_param(
                "iiid", 
                $id_transaksi, 
                $item['id_barang'], 
                $item['jumlah'], 
                $item['subtotal']
            );
            $stmt_detail->execute();
            // Kurangi stok barang setelah detail transaksi berhasil disimpan
            $conn->query("UPDATE barang SET stok = stok - {$item['jumlah']} WHERE id_barang = {$item['id_barang']}");
        }
        // Menyimpan data ke tabel laporan
          $tanggal = date('Y-m-d');
          $total_pendapatan = $total; 
          $jumlah_barang_terjual = count($items); 

          $stmt_laporan = $conn->prepare("
        INSERT INTO laporan 
            (id_transaksi, tanggal, total_pendapatan, jumlah_barang_terjual) 
        VALUES (?, ?, ?, ?)
    ");
          $stmt_laporan->bind_param("isdi", $id_transaksi, $tanggal, $total_pendapatan, $jumlah_barang_terjual);
          $stmt_laporan->execute();

      echo "<script>
        window.location.href='transaksi.php?sukses=1&id_transaksi={$id_transaksi}';
      </script>";
      exit;
   } else {
    echo "Gagal menyimpan transaksi: " . $stmt->error;
    exit;
    }
}

// Ambil daftar barang untuk tampilan dan JS
$barangResult = $conn->query("
    SELECT b.*, k.nama_kategori 
    FROM barang b 
    JOIN kategoribarang k USING(id_kategori)
");
$barangArr = $barangResult->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi</title>
    <link rel="stylesheet" href="../CSS/styletransaksi.css">
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body>
<?php 
$currentPage = 'transaksi';
include '../sidebar/sidebar.php'; 
?>

<div class="main-content" id="mainContent">
    <h2 style="margin-left: 300px;color: #D2B48C;">Transaksi</h2>
    <p style="margin-left: 300px;">
      <a href="detailtransaksi.php" class="btn-orange">Lihat Data Transaksi</a>
    </p>

    <form method="POST" id="formTransaksi" style="margin-top: -20px;">
        <div class="wrapper">
            <div class="barang">
                <h3>Daftar Barang</h3>
                
                <input type="text" id="searchBarang" placeholder="Cari barang..."
                       style="margin-bottom:10px; width:calc(100% - 22px); padding:10px;">

                <input type="text" id="scanInput" placeholder="Scan barcode di sini..." autofocus
                       style="margin-bottom: 10px; width: calc(100% - 22px); padding: 10px;">
                <audio id="bip-sound" src="../audio/bip.mp3" preload="auto"></audio>
                <div id="qr-reader" style="width:300px; margin:10px 0;"></div>

                <div class="barang-list" id="barangList">
                    <?php foreach ($barangArr as $b): ?>
                        <div class="item"
                             onclick="tambahKeKeranjang(<?= $b['id_barang'] ?>,
                                                        '<?= addslashes($b['nama_barang']) ?>',
                                                        <?= $b['harga'] ?>)">
                            <img src="../uploads/<?= $b['gambar'] ?>" width="60" alt="">
                            <br><?= htmlspecialchars($b['nama_barang']) ?>
                            <br>Rp<?= number_format($b['harga'],0,',','.') ?>
                            <br><small>Stok: <?= $b['stok'] ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="keranjang">
                <h3>Pesanan</h3>
                <div id="keranjangList"></div>
                <div class="total">Total: <span id="totalHarga">Rp0</span></div>
                <input type="hidden" name="items" id="itemsInput">
                <input type="hidden" name="metode" id="metodeInput">
                <input type="hidden" name="bayar" id="bayarInput">
                <button type="button" class="btn-order" onclick="showPopup()">Order</button>
                <button type="button" class="btn-batal" onclick="batalTransaksi()">Batal</button>
            </div>
        </div>
    </form>

    <!-- Popup Metode -->
    <div class="popup" id="popupMetode">
      <div class="popup-content">
        <span class="close-popup" onclick="closePopup()">×</span>
        <h3>Pilih Metode Pembayaran</h3>
        <button class="btn-metode" onclick="pilihMetode('Tunai')">Tunai</button>
        <div class="warning">Maaf, untuk saat ini hanya bisa melayani cash...</div>
      </div>
    </div>

    <!-- Popup Nominal -->
    <div class="popup" id="popupNominal">
      <div class="popup-content">
        <span class="close-popup" onclick="closeNominalPopup()">×</span>
        <h3>Masukkan Nominal Uang Tunai</h3>
        <input type="number" id="inputNominal" placeholder="Contoh: 10000"
               style="width:100%; padding:10px; margin-top:10px; font-size:16px;">
        <div id="errorNominal" class="warning" style="display:none;">
          Nominal tidak boleh kurang dari total.
        </div>
        <button class="btn-metode" onclick="submitNominal()">Bayar</button>
      </div>
    </div>

    <!-- Popup Sukses -->
    <!-- Popup Sukses -->
  <div class="popup" id="popupSukses" style="display:none;">
    <div class="popup-content" style="width:500px; padding:30px; text-align:center;">
    <!-- Tanda X untuk menutup popup -->
    <span class="close-popup" onclick="closeSuksesPopup()">×</span>
    <img src="../berhasil.png" width="80" height="80" />
    <h2 style="margin-top: 50px; white-space: nowrap; margin-left: 150px; text-align: left;">Pembayaran Berhasil</h2>

    <div class="rincian-pembayaran" style="margin-top:20px; font-size:18px;">
      <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
        <span>Total</span><span id="suksesTotal">-</span>
      </div>
      <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
        <span>Jumlah Uang</span><span id="suksesNominal">-</span>
      </div>
      <div style="display:flex; justify-content:space-between; font-weight:bold;">
        <span>Kembalian</span><span id="suksesKembalian">-</span>
      </div>
    </div>
    <a href="<?= $_SESSION['role'] == 'Admin' ? '../dashboard_Admin.php' : '../dashboard_Kasir.php' ?>">
      <img src="../icons/home.png" width="30" style="cursor:pointer;">
    </a>
    <?php if (isset($_GET['id_transaksi'])): ?>
      <a href="cetak_transaksi.php?id=<?= $_GET['id_transaksi'] ?>">
        <img src="../icons/printer.png" width="30">
      </a>
    <?php endif; ?>
    </div>
  </div>

</div>

<script>
// Data barang untuk JS
const dataBarang = <?= json_encode($barangArr) ?>;
let keranjang = [];

// *** Perbaikan: format tanpa desimal ***
function formatRupiah(num) {
  const n = typeof num === "number" ? num : parseFloat(num) || 0;
  return "Rp" + n.toLocaleString("id-ID", {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  });
}

function tambahKeKeranjang(id, nama, harga) {
    const brgInfo = dataBarang.find(b => Number(b.id_barang) === Number(id));

    if (!brgInfo) {
        alert('Barang tidak ditemukan.');
        return;
    }

    if (brgInfo.stok <= 0) {
        alert('Stok habis.');
        return;
    }

    const existingItem = keranjang.find(item => Number(item.id_barang) === Number(id));

    if (existingItem) {
        updateQty(keranjang.indexOf(existingItem), 1);
    } else {
        keranjang.push({ 
            id_barang: id, 
            nama_barang: nama, 
            harga: harga, 
            jumlah: 1, // GANTI dari qty → jumlah
            subtotal: harga * 1
        });
    }

    renderKeranjang();
}

function updateQty(i, delta) { 
    const brgInfo = dataBarang.find(b => Number(b.id_barang) === Number(keranjang[i].id_barang));
    const newQty = keranjang[i].jumlah + delta;

    if (delta > 0) {
        if (!brgInfo || newQty > brgInfo.stok) {
            return alert("Jumlah melebihi stok yang tersedia.");
        }
    }

    keranjang[i].jumlah = newQty;
    
    if (keranjang[i].jumlah <= 0) {
        keranjang.splice(i, 1);
    } else {
        keranjang[i].subtotal = keranjang[i].jumlah * keranjang[i].harga;
    }
    
    renderKeranjang();
}



// *** Perbaikan: pastikan subtotal di-render sebagai angka, format konsisten ***
function renderKeranjang(){
  const list = document.getElementById("keranjangList");
  let total = 0;
  list.innerHTML = "";

  keranjang.forEach((it, i) => {
    const sub = Number(it.subtotal);
    total += sub;
    list.innerHTML += `
      <div class="keranjang-item">
        <div>${it.nama_barang} x ${it.jumlah}</div>
        <div>
          <button type="button" onclick="updateQty(${i},-1)">-</button>
          <button type="button" onclick="updateQty(${i},+1)">+</button>
        </div>
        <div>${formatRupiah(sub)}</div>
      </div>`;
  });

  document.getElementById("totalHarga").innerText = formatRupiah(total);
  document.getElementById("itemsInput").value = JSON.stringify(keranjang);
}

function showPopup(){
  if(!keranjang.length) return alert("Keranjang masih kosong!");
  document.getElementById("popupMetode").style.display = "flex";
}
function closePopup(){ document.getElementById("popupMetode").style.display = "none"; }
function pilihMetode(m){ closePopup(); document.getElementById("popupNominal").style.display = "flex"; }
function closeNominalPopup(){ document.getElementById("popupNominal").style.display = "none"; }

function submitNominal(){
  const nominal = parseFloat(document.getElementById("inputNominal").value);
  const total = keranjang.reduce((s,i) => s + i.subtotal, 0);
  if(isNaN(nominal) || nominal < total){
    return document.getElementById("errorNominal").style.display = "block";
  }
  document.getElementById("metodeInput").value = "Tunai";
  document.getElementById("bayarInput").value = nominal;
  localStorage.setItem("lastTotal", total);
  localStorage.setItem("lastNominal", nominal);
  document.getElementById("formTransaksi").submit();
}

document.getElementById("scanInput")
  .addEventListener("keydown", e => {
    if(e.key === "Enter"){
      const kode = e.target.value.trim(), sound = document.getElementById("bip-sound");
      const brg = dataBarang.find(b => b.id_barang == kode || b.kode_barang == kode);
      if(brg){
        e.target.value = "";
        tambahKeKeranjang(brg.id_barang, brg.nama_barang, brg.harga);
        sound.play();
      } else alert("Barang tidak ditemukan.");
    }
});

function onScanSuccess(decodedText){
  const kode = decodedText.trim(), sound = document.getElementById("bip-sound");
  const brg = dataBarang.find(b => b.id_barang == kode || b.kode_barang == kode);
  if(brg){
    tambahKeKeranjang(brg.id_barang, brg.nama_barang, brg.harga);
    sound.play();
  }
}
const html5QrcodeScanner = new Html5QrcodeScanner(
  "qr-reader", {fps:10, qrbox:250}, false
);
html5QrcodeScanner.render(onScanSuccess);

<?php if(isset($_GET['sukses']) && $_GET['sukses']==1): ?>
window.addEventListener("DOMContentLoaded", () => {
  const total = localStorage.getItem("lastTotal"),
        nominal = localStorage.getItem("lastNominal"),
        kembali = nominal - total;
  document.getElementById("suksesTotal").innerText = formatRupiah(+total);
  document.getElementById("suksesNominal").innerText = formatRupiah(+nominal);
  document.getElementById("suksesKembalian").innerText = formatRupiah(+kembali);
  document.getElementById("popupSukses").style.display = "flex";
  localStorage.removeItem("lastTotal");
  localStorage.removeItem("lastNominal");
});
<?php endif; ?>

function closeSuksesPopup() {
    document.getElementById("popupSukses").style.display = "none";
}


document.getElementById("searchBarang")
 .addEventListener("input", function(){
   const kw = this.value.toLowerCase();
   document.querySelectorAll("#barangList .item")
     .forEach(it => it.style.display =
       it.textContent.toLowerCase().includes(kw) ? "block" : "none"
     );
});

document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("toggleSidebar");
    const sidebar = document.querySelector(".sidebar");

    toggleBtn.addEventListener("click", function () {
      sidebar.classList.toggle("collapsed");
      toggleBtn.classList.toggle("moved");
    });
});

// Kosongkan keranjang dan render ulang
function batalTransaksi() {
  if (keranjang.length === 0) return;
  if (confirm("Yakin ingin membatalkan semua pesanan?")) {
    keranjang = [];
    renderKeranjang();
  }
}
</script>
</body>
</html>
