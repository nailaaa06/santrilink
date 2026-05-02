<?php
session_start();
include 'config/koneksi.php';

// If already logged in, redirect to correct beranda
if(isset($_SESSION['role'])){
    if($_SESSION['role'] == 'santri') header("Location: santri/beranda.php");
    else if($_SESSION['role'] == 'ustadz') header("Location: ustadz/beranda.php");
    else if($_SESSION['role'] == 'admin') header("Location: admin/beranda.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SantriLink</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="login-container">
    <div class="login-left">
        <img src="assets/poster.jpeg" alt="SantriLink Poster">
    </div>
    <div class="login-right">
        <div class="login-form-box">
            <img src="assets/logo.png" alt="Logo Bahrul Ulum" class="logo">
            <h2>Masuk dan Verifikasi</h2>
            <p>Informasi Pondok Pesantren<br>Bahrul Ulum KH. Busthomi</p>
            
            <?php if(isset($_GET['error'])): ?>
                <div style="color: red; margin-bottom: 15px; font-size: 14px;">
                    <?php 
                        if($_GET['error'] == 'password') echo "Password salah!";
                        elseif($_GET['error'] == 'akun') echo "Akun tidak ditemukan!";
                        else echo "Terjadi kesalahan.";
                    ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group" style="text-align: left;">
                    <label class="form-label">NIS</label>
                    <input type="text" name="nis" class="form-control" placeholder="Masukkan NIS Anda" required>
                </div>
                <div class="form-group" style="text-align: left; position: relative;">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan Password Anda" required>
                    <!-- Simple toggle visibility could be added here -->
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Login</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>