<?php

ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);

session_start();

include "koneksi/config.php";



$error = ""; // Untuk pesan error login

$pesan = ""; // Untuk notifikasi logout



// Ambil pesan logout dari session (jika ada)

if (isset($_SESSION['pesan'])) {

    $pesan = $_SESSION['pesan'];

    unset($_SESSION['pesan']); // hapus agar tidak tampil terus

}



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];

    $password = $_POST['password'];



    // Cek username di database

    $query = "SELECT * FROM pengguna WHERE username = ?";

    $stmt = $conn->prepare($query);

    $stmt->bind_param("s", $username);

    $stmt->execute();

    $result = $stmt->get_result();



    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();



        if (password_verify($password, $user['password'])) {

            $_SESSION['username'] = $user['username'];

            $_SESSION['id_pengguna'] = $user['id_pengguna'];

            $_SESSION['nama'] = $user['nama'];

            $_SESSION['role'] = $user['role'];

            

    

            // Arahkan ke dashboard sesuai role

            if ($user['role'] == 'Admin') {

                header("Location: dashboard_Admin.php");

            } elseif ($user['role'] == 'Manager') {

                header("Location: dashboard_Manager.php");

            } elseif ($user['role'] == 'Kasir') {

                header("Location: dashboard_Kasir.php");

            } else {

                $error = "Role tidak dikenali!";

            }

    

            exit(); // penting agar tidak lanjut render HTML

        } else {

            $error = "Password salah!";

        }

    } else {

        $error = "Username tidak ditemukan!";

    }

}

    

?>



<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login EduStore</title>

    <link rel="stylesheet" href="CSS/styleslogin.css"> 

</head>

<body>



<div class="login-container">

    <div class="login-box">

        <img src="icons/edustore.png" alt="EduStore Logo" class="logo">

        <h2>Login</h2>



        <?php if (!empty($pesan)): ?>

            <div class="success-message"><?php echo $pesan; ?></div>

        <?php endif; ?>



        <?php if (!empty($error)): ?>

            <div class="error-message"><?php echo $error; ?></div>

        <?php endif; ?>



        <form action="login.php" method="POST">

            <label>Username</label>

            <input type="text" name="username" required>



            <label>Password</label>

            <input type="password" name="password" required>



            <button type="submit" class="login-button">Login</button>

        </form>

    </div>

</div>



</body>

</html>