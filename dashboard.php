<?php
session_start();
$role = $_SESSION['role'] ?? '';
$dashboard_url = "#";

if ($role == "admin") {
    $dashboard_url = "dashboard_admin.php";
} elseif ($role == "manager") {
    $dashboard_url = "dashboard_manager.php";
} elseif ($role == "kasir") {
    $dashboard_url = "dashboard_kasir.php";
}
?>
