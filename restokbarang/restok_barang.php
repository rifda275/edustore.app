<?php 
include '../koneksi/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit();
}

$role = $_SESSION['role'];

// Jika role adalah kasir, redirect ke dashboard
if ($role === 'kasir') {
    header("Location: ../dashboard/kasir_dashboard.php");
    exit();
}

// Handle export request
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    // Get search and filter parameters
    $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
    $sort_field = isset($_GET['sort']) ? $_GET['sort'] : 'tanggal_restok';
    $sort_order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

    // Validate sort parameters
    $allowed_sort_fields = ['id_restok', 'kode_barang', 'nama_barang', 'stok', 'jumlah', 'tanggal_restok', 'harga_beli', 'harga_jual', 'laba_rugi'];
    if (!in_array($sort_field, $allowed_sort_fields)) {
        $sort_field = 'tanggal_restok';
    }

    $allowed_sort_orders = ['ASC', 'DESC'];
    if (!in_array($sort_order, $allowed_sort_orders)) {
        $sort_order = 'DESC';
    }

    // Build the query with search and filter
    $query = "
        SELECT 
            r.id_restok, 
            b.nama_barang, 
            b.kode_barang, 
            b.stok,
            r.jumlah, 
            r.tanggal_restok,
            r.harga_beli,
            b.harga AS harga_jual,
            (b.harga - r.harga_beli) AS laba_rugi
        FROM restokbarang r
        JOIN barang b ON r.id_barang = b.id_barang
        WHERE 1=1
    ";

    // Apply search filter
    if (!empty($search)) {
        $query .= " AND (b.nama_barang LIKE '%$search%' OR b.kode_barang LIKE '%$search%' OR r.id_restok LIKE '%$search%')";
    }

    // Apply date filter
    if (!empty($start_date) && !empty($end_date)) {
        $query .= " AND (DATE(r.tanggal_restok) BETWEEN '$start_date' AND '$end_date')";
    } elseif (!empty($start_date)) {
        $query .= " AND DATE(r.tanggal_restok) >= '$start_date'";
    } elseif (!empty($end_date)) {
        $query .= " AND DATE(r.tanggal_restok) <= '$end_date'";
    }

    // Add sorting
    $query .= " ORDER BY $sort_field $sort_order";

    $result = $conn->query($query);

    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="data_restok_' . date('Y-m-d') . '.xls"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Create Excel file
    echo "<!DOCTYPE html>";
    echo "<html lang='id'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<title>Data Restok Barang</title>";
    echo "</head>";
    echo "<body>";

    echo "<table border='1'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th colspan='9' style='font-size:16pt;'>Data Restok Barang</th>";
    echo "</tr>";
    echo "<tr>";
    echo "<th colspan='9' style='font-size:10pt;'>Tanggal Export: " . date('d-m-Y H:i:s') . "</th>";
    echo "</tr>";
    echo "<tr>";
    echo "<th style='background-color:#f2f2f2;'>ID</th>";
    echo "<th style='background-color:#f2f2f2;'>Kode Barang</th>";
    echo "<th style='background-color:#f2f2f2;'>Nama Barang</th>";
    echo "<th style='background-color:#f2f2f2;'>Stok Saat Ini</th>";
    echo "<th style='background-color:#f2f2f2;'>Jumlah Restok</th>";
    echo "<th style='background-color:#f2f2f2;'>Harga Beli</th>";
    echo "<th style='background-color:#f2f2f2;'>Harga Jual</th>";
    echo "<th style='background-color:#f2f2f2;'>Laba/Rugi</th>";
    echo "<th style='background-color:#f2f2f2;'>Tanggal Restok</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $laba_rugi = $row['harga_jual'] - $row['harga_beli'];
            $laba_rugi_class = $laba_rugi >= 0 ? 'profit' : 'loss';
            
            echo "<tr>";
            echo "<td>" . $row['id_restok'] . "</td>";
            echo "<td>" . $row['kode_barang'] . "</td>";
            echo "<td>" . $row['nama_barang'] . "</td>";
            echo "<td>" . $row['stok'] . "</td>";
            echo "<td>" . $row['jumlah'] . "</td>";
            echo "<td>Rp " . number_format($row['harga_beli'], 0, ',', '.') . "</td>";
            echo "<td>Rp " . number_format($row['harga_jual'], 0, ',', '.') . "</td>";
            echo "<td style='color:" . ($laba_rugi >= 0 ? 'green' : 'red') . ";'>Rp " . number_format($laba_rugi, 0, ',', '.') . "</td>";
            echo "<td>" . date("d-m-Y", strtotime($row['tanggal_restok'])) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='9' style='text-align:center;'>Tidak ada data yang ditemukan</td></tr>";
    }

    echo "</tbody>";
    echo "</table>";

    echo "</body>";
    echo "</html>";

    exit();
}

// Pagination setup
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Search parameters
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Filter by date
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Sort parameters
$sort_field = isset($_GET['sort']) ? $_GET['sort'] : 'tanggal_restok';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Validate sort parameters to prevent SQL injection
$allowed_sort_fields = ['id_restok', 'kode_barang', 'nama_barang', 'stok', 'jumlah', 'tanggal_restok', 'harga_beli', 'harga_jual', 'laba_rugi'];
if (!in_array($sort_field, $allowed_sort_fields)) {
    $sort_field = 'tanggal_restok';
}

$allowed_sort_orders = ['ASC', 'DESC'];
if (!in_array($sort_order, $allowed_sort_orders)) {
    $sort_order = 'DESC';
}

// Build the query with search, filter, and sorting
$query = "
    SELECT 
        r.id_restok, 
        b.nama_barang, 
        b.kode_barang, 
        b.stok,
        r.jumlah, 
        r.tanggal_restok,
        r.harga_beli,
        b.harga AS harga_jual,
        (b.harga - r.harga_beli) AS laba_rugi
    FROM restokbarang r
    JOIN barang b ON r.id_barang = b.id_barang
    WHERE 1=1
";

// Apply search filter
if (!empty($search)) {
    $query .= " AND (b.nama_barang LIKE '%$search%' OR b.kode_barang LIKE '%$search%' OR r.id_restok LIKE '%$search%')";
}

// Apply date filter
if (!empty($start_date) && !empty($end_date)) {
    $query .= " AND (DATE(r.tanggal_restok) BETWEEN '$start_date' AND '$end_date')";
} elseif (!empty($start_date)) {
    $query .= " AND DATE(r.tanggal_restok) >= '$start_date'";
} elseif (!empty($end_date)) {
    $query .= " AND DATE(r.tanggal_restok) <= '$end_date'";
}

// Count total records for pagination
$count_query = $query;
$count_result = $conn->query($count_query);
$total_records = $count_result->num_rows;
$total_pages = ceil($total_records / $records_per_page);

// Add sorting and pagination
$query .= " ORDER BY $sort_field $sort_order LIMIT $offset, $records_per_page";

$result = $conn->query($query);

// Get items with low stock for notification
$low_stock_query = "SELECT id_barang, nama_barang, stok FROM barang WHERE stok <= 10 ORDER BY stok ASC";
$low_stock_result = $conn->query($low_stock_query);
$low_stock_count = $low_stock_result->num_rows;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Restok Barang</title>
    <link rel="stylesheet" href="../CSS/stylerestok.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .filter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        .search-container, .date-container {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .action-buttons {
            margin-left: auto;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin-top: 20px;
        }
        
        .pagination li {
            margin: 0 5px;
        }
        
        .pagination a {
            padding: 8px 12px;
            border: 1px solid #ddd;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
        }
        
        .pagination a.active {
            background-color: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }
        
        .pagination a:hover:not(.active) {
            background-color: #f1f1f1;
        }
        
        .notification-badge {
            position: relative;
        }
        
        .badge {
            position: absolute;
            top: -10px;
            right: -10px;
            padding: 5px 8px;
            border-radius: 50%;
            background-color: red;
            color: white;
            font-size: 12px;
        }
        
        .sort-indicator {
            margin-left: 5px;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            position: relative;
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 5px;
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .close {
            position: absolute;
            right: 10px;
            top: 5px;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: black;
        }
        
        .low-stock-item {
            border-bottom: 1px solid #eee;
            padding: 8px 0;
        }
        
        .low-stock-item:last-child {
            border-bottom: none;
        }
        
        .profit {
            color: green;
            font-weight: bold;
        }
        
        .loss {
            color: red;
            font-weight: bold;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .filter-container {
                flex-direction: column;
            }
            
            .action-buttons {
                margin-left: 0;
                margin-top: 10px;
            }
            
            .modal-content {
                width: 90%;
            }
            
            .table-wrapper {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>

<?php 
    $isRestokPage = true; 
    $currentPage = 'restok';
    include '../sidebar/sidebar.php'; 
?>

<div class="container">
    <div class="header-restok">
        <h2>Data Restok Barang</h2>

        <div class="notification-badge">
            <button id="showLowStockBtn" class="btn btn-warning">
                <i class="fas fa-exclamation-triangle"></i> Stok Rendah
                <?php if ($low_stock_count > 0): ?>
                <span class="badge"><?= $low_stock_count ?></span>
                <?php endif; ?>
            </button>
        </div>
    </div>

    <div class="filter-container">
        <form method="GET" action="" id="filterForm">
            <div class="search-container">
                <input type="text" name="search" placeholder="Cari kode/nama barang..." value="<?= htmlspecialchars($search) ?>" class="form-control">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </div>
            
            <div class="date-container">
                <label>Dari:</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" class="form-control">
                <label>Sampai:</label>
                <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" class="form-control">
            </div>
            
            <!-- Hidden inputs for sort and pagination -->
            <input type="hidden" name="sort" id="sort_field" value="<?= htmlspecialchars($sort_field) ?>">
            <input type="hidden" name="order" id="sort_order" value="<?= htmlspecialchars($sort_order) ?>">
            <input type="hidden" name="page" id="current_page" value="<?= $page ?>">
            
            <div class="action-buttons">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                <button type="button" class="btn btn-secondary" onclick="resetFilters()"><i class="fas fa-sync-alt"></i> Reset</button>
                <button type="button" class="btn btn-success" onclick="exportToExcel()"><i class="fas fa-file-excel"></i> Export Excel</button>
                
                <?php if ($role === 'Admin' || $role === 'Manager'): ?>
                    <a href="tambah_restok.php" class="btn btn-success"><i class="fas fa-plus"></i> Tambah Restok</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="table-wrapper">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>
                        <a href="#" onclick="sortTable('id_restok')">ID
                            <?php if ($sort_field === 'id_restok'): ?>
                                <i class="fas fa-sort-<?= $sort_order === 'ASC' ? 'up' : 'down' ?> sort-indicator"></i>
                            <?php else: ?>
                                <i class="fas fa-sort sort-indicator"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th>
                        <a href="#" onclick="sortTable('kode_barang')">Kode Barang
                            <?php if ($sort_field === 'kode_barang'): ?>
                                <i class="fas fa-sort-<?= $sort_order === 'ASC' ? 'up' : 'down' ?> sort-indicator"></i>
                            <?php else: ?>
                                <i class="fas fa-sort sort-indicator"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th>
                        <a href="#" onclick="sortTable('nama_barang')">Nama Barang
                            <?php if ($sort_field === 'nama_barang'): ?>
                                <i class="fas fa-sort-<?= $sort_order === 'ASC' ? 'up' : 'down' ?> sort-indicator"></i>
                            <?php else: ?>
                                <i class="fas fa-sort sort-indicator"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th>
                        <a href="#" onclick="sortTable('stok')">Stok Saat Ini
                            <?php if ($sort_field === 'stok'): ?>
                                <i class="fas fa-sort-<?= $sort_order === 'ASC' ? 'up' : 'down' ?> sort-indicator"></i>
                            <?php else: ?>
                                <i class="fas fa-sort sort-indicator"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th>
                        <a href="#" onclick="sortTable('jumlah')">Jumlah Restok
                            <?php if ($sort_field === 'jumlah'): ?>
                                <i class="fas fa-sort-<?= $sort_order === 'ASC' ? 'up' : 'down' ?> sort-indicator"></i>
                            <?php else: ?>
                                <i class="fas fa-sort sort-indicator"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th>
                        <a href="#" onclick="sortTable('harga_beli')">Harga Beli
                            <?php if ($sort_field === 'harga_beli'): ?>
                                <i class="fas fa-sort-<?= $sort_order === 'ASC' ? 'up' : 'down' ?> sort-indicator"></i>
                            <?php else: ?>
                                <i class="fas fa-sort sort-indicator"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th>
                        <a href="#" onclick="sortTable('harga_jual')">Harga Jual
                            <?php if ($sort_field === 'harga_jual'): ?>
                                <i class="fas fa-sort-<?= $sort_order === 'ASC' ? 'up' : 'down' ?> sort-indicator"></i>
                            <?php else: ?>
                                <i class="fas fa-sort sort-indicator"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th>
                        <a href="#" onclick="sortTable('laba_rugi')">Laba/Rugi
                            <?php if ($sort_field === 'laba_rugi'): ?>
                                <i class="fas fa-sort-<?= $sort_order === 'ASC' ? 'up' : 'down' ?> sort-indicator"></i>
                            <?php else: ?>
                                <i class="fas fa-sort sort-indicator"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th>
                        <a href="#" onclick="sortTable('tanggal_restok')">Tanggal Restok
                            <?php if ($sort_field === 'tanggal_restok'): ?>
                                <i class="fas fa-sort-<?= $sort_order === 'ASC' ? 'up' : 'down' ?> sort-indicator"></i>
                            <?php else: ?>
                                <i class="fas fa-sort sort-indicator"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id_restok'] ?></td>
                            <td><?= $row['kode_barang'] ?></td>
                            <td><?= $row['nama_barang'] ?></td>
                            <td class="<?= $row['stok'] <= 10 ? 'text-danger' : '' ?>">
                                <?= $row['stok'] ?>
                                <?php if ($row['stok'] <= 10): ?>
                                    <i class="fas fa-exclamation-circle text-danger" title="Stok rendah!"></i>
                                <?php endif; ?>
                            </td>
                            <td><?= $row['jumlah'] ?></td>
                            <td>Rp <?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                            <td class="<?= $row['laba_rugi'] >= 0 ? 'profit' : 'loss' ?>">
                                Rp <?= number_format($row['laba_rugi'], 0, ',', '.') ?>
                            </td>
                            <td><?= date("d-m-Y", strtotime($row['tanggal_restok'])) ?></td>
                            <td>
                                <?php if ($role === 'Admin' || $role === 'Manager'): ?>
                                    <form action="edit_restok.php" method="get" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $row['id_restok'] ?>">
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-edit"></i></button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($role === 'Admin'): ?>
                                    <form action="hapus_restok.php" method="post" style="display:inline;" 
                                        onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                        <input type="hidden" name="id" value="<?= $row['id_restok']; ?>">
                                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">Tidak ada data yang ditemukan</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li><a href="#" onclick="navigatePage(1)">&laquo;</a></li>
                <li><a href="#" onclick="navigatePage(<?= $page - 1 ?>)">&lsaquo;</a></li>
            <?php endif; ?>

            <?php
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $page + 2);
            
            for ($i = $start_page; $i <= $end_page; $i++): ?>
                <li><a href="#" class="<?= $i == $page ? 'active' : '' ?>" onclick="navigatePage(<?= $i ?>)"><?= $i ?></a></li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li><a href="#" onclick="navigatePage(<?= $page + 1 ?>)">&rsaquo;</a></li>
                <li><a href="#" onclick="navigatePage(<?= $total_pages ?>)">&raquo;</a></li>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
</div>

<!-- Low Stock Modal -->
<div id="lowStockModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Barang dengan Stok Rendah (<= 10)</h3>
        <div id="lowStockItems">
            <?php if ($low_stock_count > 0): ?>
                <?php while ($low_stock = $low_stock_result->fetch_assoc()): ?>
                    <div class="low-stock-item">
                        <strong><?= $low_stock['nama_barang'] ?></strong> - 
                        Stok: <span class="text-danger"><?= $low_stock['stok'] ?></span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Tidak ada barang dengan stok rendah.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Sorting function
    function sortTable(field) {
        const currentField = document.getElementById('sort_field').value;
        const currentOrder = document.getElementById('sort_order').value;
        
        let newOrder = 'ASC';
        if (field === currentField && currentOrder === 'ASC') {
            newOrder = 'DESC';
        }
        
        document.getElementById('sort_field').value = field;
        document.getElementById('sort_order').value = newOrder;
        document.getElementById('filterForm').submit();
    }
    
    // Reset filters
    function resetFilters() {
        document.querySelector('input[name="search"]').value = '';
        document.querySelector('input[name="start_date"]').value = '';
        document.querySelector('input[name="end_date"]').value = '';
        document.getElementById('sort_field').value = 'tanggal_restok';
        document.getElementById('sort_order').value = 'DESC';
        document.getElementById('current_page').value = '1';
        document.getElementById('filterForm').submit();
    }
    
    // Pagination navigation
    function navigatePage(page) {
        document.getElementById('current_page').value = page;
        document.getElementById('filterForm').submit();
        return false;
    }
    
    // Modal for low stock items
    const modal = document.getElementById("lowStockModal");
    const btn = document.getElementById("showLowStockBtn");
    const span = document.getElementsByClassName("close")[0];
    
    btn.onclick = function() {
        modal.style.display = "block";
    }
    
    span.onclick = function() {
        modal.style.display = "none";
    }
    
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    
    // Export to Excel function
    function exportToExcel() {
        // Use current form data to maintain filters in the export
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        formData.append('export', 'excel'); // Add export parameter
        
        // Create URL with parameters
        const params = new URLSearchParams(formData);
        window.location.href = window.location.pathname + '?' + params.toString();
    }
</script>

</body>
</html>