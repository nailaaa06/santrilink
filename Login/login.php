<?php
session_start();
include 'koneksi.php';

// cegah akses langsung
if (!isset($_POST['nis']) || !isset($_POST['password'])) {
    header("Location: index.php");
    exit;
}

$nis = $_POST['nis'];
$password = $_POST['password'];

// ambil data
$query = mysqli_query($conn, "SELECT * FROM login WHERE nis='$nis'");

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}

$data = mysqli_fetch_assoc($query);

if ($data) {
    if ($password == $data['password']) {
        $_SESSION['nis'] = $data['nis'];
        header("Location: dashboard.php");
    } else {
        header("Location: index.php?error=password");
    }
} else {
    header("Location: index.php?error=akun");
}
?>