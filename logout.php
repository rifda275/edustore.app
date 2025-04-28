<?php
session_start();

// Ambil role dari URL
$role = isset($_GET['role']) ? $_GET['role'] : '';

// Jika tombol logout diklik
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['keluar'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Jika tombol batal diklik, redirect ke dashboard sesuai role
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['batal'])) {
    if ($role === 'kasir') {
        header("Location: dashboard_kasir.php");
    } elseif ($role === 'admin') {
        header("Location: dashboard_admin.php");
    } elseif ($role === 'manager') {
        header("Location: dashboard_manager.php");
    } else {
        header("Location: index.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Logout - EduStore</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: rgba(0,0,0,0.4);
            font-family: Arial, sans-serif;
        }

        .popup-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .popup-box {
            background-color: #4d4d4d;
            color: white;
            padding: 25px;
            border-radius: 12px;
            width: 300px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }

        .popup-box p {
            margin-bottom: 20px;
            font-size: 14px;
        }

        .popup-buttons {
            display: flex;
            justify-content: space-between;
        }

        .popup-buttons button {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-tidak {
            background-color: #b0b0b0;
            color: black;
        }

        .btn-ya {
            background-color: #527253;
            color: white;
        }
    </style>
</head>
<body>
    <form method="post">
        <div class="popup-overlay">
            <div class="popup-box">
                <p>Anda akan keluar dari akun ini. Lanjutkan?</p>
                <div class="popup-buttons">
                    <button type="submit" name="batal" class="btn-tidak">Tidak</button>
                    <button type="submit" name="keluar" class="btn-ya">Iya</button>
                </div>
            </div>
        </div>
    </form>
</body>
</html>
