<?php
session_start();
include 'config/koneksi.php';

// cegah akses langsung
if (!isset($_POST['nis']) || !isset($_POST['password'])) {
    header("Location: index.php");
    exit;
}

$nis = $_POST['nis'];
$password = $_POST['password'];

// ambil data dari tb_akun
$query = mysqli_query($conn, "SELECT * FROM tb_akun WHERE username='$nis'");

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}

$data = mysqli_fetch_assoc($query);

if ($data) {
    if ($password == $data['password']) {
        // Simpan sesi berdasarkan role
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];
        
        // Khusus santri, simpan NIS
        if($data['role'] == 'santri') {
            $_SESSION['nis'] = $data['username'];
            header("Location: santri/beranda.php");
        } 
        // Khusus ustadz
        else if ($data['role'] == 'ustadz') {
            $_SESSION['nip'] = $data['username'];
            header("Location: ustadz/beranda.php");
        }
        // Khusus admin
        else if ($data['role'] == 'admin') {
            header("Location: admin/beranda.php");
        }
        exit;
    } else {
        header("Location: index.php?error=password");
    }
} else {
    header("Location: index.php?error=akun");
}
?>