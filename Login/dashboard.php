<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['nis'])) {
    header("Location: index.php");
    exit;
}

$nis = $_SESSION['nis'];

// ambil data user
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user WHERE nis='$nis'"));

// ambil jadwal
$jadwal = mysqli_query($conn, "SELECT * FROM jadwal");

// ambil keuangan
$uang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM keuangan WHERE nis='$nis'"));
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="header">
  <div class="logo">
    <img src="logo.png">
    <div>
      <strong>PONDOK PESANTREN</strong><br>
      <small>BAHRUL ULUM</small>
    </div>
  </div>

  <div class="menu">
    <div><i class="fa fa-home"></i> Beranda</div>
    <div><i class="fa fa-wallet"></i> Keuangan</div>
  </div>

  <div class="right">
    <i class="fa fa-bell notif"></i>
    <img src="<?php echo $user['foto']; ?>" class="top-profile">
  </div>
</div>

<div class="wrapper">
<div class="container">

<!-- LEFT -->
<div class="card">

  <div class="top">
    <div>
      <h3>Jadwal Mengaji</h3>
    </div>
  </div>

  <?php while($j = mysqli_fetch_assoc($jadwal)): ?>
  <div class="schedule">
    <strong><?php echo $j['kitab']; ?></strong>
    <p><i class="fa fa-clock"></i> <?php echo $j['jam']; ?></p>
    <p><i class="fa fa-user"></i> <?php echo $j['ustadz']; ?></p>
    <p><i class="fa fa-location-dot"></i> <?php echo $j['tempat']; ?></p>
  </div>
  <?php endwhile; ?>

</div>

<!-- RIGHT -->
<div>

  <div class="card profile">
    <img src="<?php echo $user['foto']; ?>">
    <div>
      <strong>Hai, <?php echo $user['nama']; ?></strong>
      <p>Kelas: <?php echo $user['kelas']; ?></p>
    </div>
  </div>

  <div class="card">
    <h4>Total Tagihan</h4>
    <p><strong>Rp. <?php echo number_format($uang['total']); ?></strong></p>
    <button class="btn">Bayar Sekarang</button>
  </div>

  <a href="logout.php">Logout</a>

</div>

</div>
</div>

</body>
</html>