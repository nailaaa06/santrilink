<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../config/koneksi.php';
$nama_santri = "Santri";
$foto_santri = "profil.jpg";
$nis = $_SESSION['nis'] ?? '';

if($nis) {
    $q = mysqli_query($conn, "SELECT nama_santri, foto FROM tb_santri WHERE nis='$nis'");
    if($d = mysqli_fetch_assoc($q)) {
        $nama_santri = $d['nama_santri'];
        $foto_santri = $d['foto'];
    }
}
$path_foto_header = "../assets/" . $foto_santri;
if (!file_exists($path_foto_header) || empty($foto_santri)) {
    $path_foto_header = "../assets/profil.jpg";
}

// Logic Notifikasi Sederhana
$notif_count = 0;
$notif_items = [];

// Cek Tagihan
$q_tag = mysqli_query($conn, "SELECT COUNT(*) as jml FROM tb_tagihan WHERE nis='$nis' AND status_bayar='Belum Bayar'");
$d_tag = mysqli_fetch_assoc($q_tag);
if($d_tag['jml'] > 0) {
    $notif_count++;
    $notif_items[] = ["title" => "Tagihan Belum Bayar", "desc" => "Anda memiliki {$d_tag['jml']} tagihan aktif.", "link" => "keuangan.php"];
}

// Cek Acara Hari Ini
$today = date('Y-m-d');
$q_cal = mysqli_query($conn, "SELECT * FROM tb_kalender WHERE tanggal = '$today'");
while($row = mysqli_fetch_assoc($q_cal)) {
    $notif_count++;
    $notif_items[] = ["title" => "Acara Hari Ini", "desc" => $row['nama_acara'], "link" => "beranda.php"];
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SantriLink</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .top-nav {
            background: linear-gradient(rgba(27, 75, 57, 0.85), rgba(46, 107, 86, 0.85)), url('../assets/background.jpg') !important;
            background-size: cover !important;
            background-position: center 20% !important; /* Ambil daerah atas agar lebih estetik */
            height: 70px;
            display: flex;
            align-items: center;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .nav-link {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
        }
        .notif-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4d4d;
            color: white;
            font-size: 0.6rem;
            padding: 2px 5px;
            border-radius: 10px;
            font-weight: bold;
            border: 2px solid #2e6b56;
        }
        #notifDropdown {
            display: none;
            position: absolute;
            top: 60px;
            right: 80px;
            background: white;
            width: 280px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            z-index: 2000;
            overflow: hidden;
        }
        .notif-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: 0.3s;
        }
        .notif-item:hover { background: #f9f9f9; }
        .notif-title { font-weight: 700; font-size: 0.85rem; color: #333; margin-bottom: 2px; }
        .notif-desc { font-size: 0.75rem; color: #777; }
    </style>
</head>
<body>

<nav class="top-nav">
    <div class="top-nav-left" style="display: flex; align-items: center; gap: 1rem;">
        <img src="../assets/logo.png" alt="Logo" style="height: 40px;">
        <div style="color: white; line-height: 1.2;">
            <div style="font-size: 0.7rem; font-weight: 700; opacity: 0.9; letter-spacing: 1px;">PONDOK PESANTREN</div>
            <div style="font-size: 0.9rem; font-weight: 800; letter-spacing: 0.5px;">BAHRUL ULUM KH. BUSTHOMI</div>
        </div>
    </div>
    
    <div style="flex: 1; display: flex; justify-content: center; gap: 0.5rem;">
        <a href="beranda.php" class="nav-link <?= ($current_page == 'beranda.php') ? 'active' : '' ?>">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            Beranda
        </a>
        <a href="keuangan.php" class="nav-link <?= ($current_page == 'keuangan.php') ? 'active' : '' ?>">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Keuangan
        </a>
        <a href="hasil_ujian.php" class="nav-link <?= ($current_page == 'hasil_ujian.php') ? 'active' : '' ?>">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            Hasil Ujian
        </a>
        <a href="javascript:void(0)" class="nav-link" onclick="alert('Fitur Kegiatan Santri sedang dalam tahap pengembangan dan akan segera hadir! Terima kasih atas kesabarannya.')">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 4v4h4"></path></svg>
            Kegiatan Santri
        </a>
    </div>

    <div style="display: flex; align-items: center; gap: 1.5rem;">
        <!-- Notifikasi -->
        <div style="position: relative; cursor: pointer;" id="notifToggle">
            <svg width="24" height="24" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            <?php if($notif_count > 0): ?>
                <span class="notif-badge"><?= $notif_count ?></span>
            <?php endif; ?>
        </div>

        <!-- Profil -->
        <div class="top-nav-profile" id="profileToggle" style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
            <div style="text-align: right; color: white;">
                <div style="font-size: 0.75rem; font-weight: 700; line-height: 1.1;"><?= htmlspecialchars($nama_santri) ?></div>
                <div style="font-size: 0.6rem; opacity: 0.8;">Santri Aktif</div>
            </div>
            <img src="<?= $path_foto_header ?>" alt="Profile" style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.5);">
        </div>
    </div>

    <!-- Dropdown Notif -->
    <div id="notifDropdown">
        <div style="padding: 1rem; background: #3c6356; color: white; font-weight: bold; font-size: 0.9rem;">Notifikasi</div>
        <?php if(empty($notif_items)): ?>
            <div style="padding: 2rem; text-align: center; color: #999; font-size: 0.8rem;">Tidak ada notifikasi baru.</div>
        <?php else: ?>
            <?php foreach($notif_items as $item): ?>
                <div class="notif-item" onclick="window.location.href='<?= $item['link'] ?>'">
                    <div class="notif-title"><?= $item['title'] ?></div>
                    <div class="notif-desc"><?= $item['desc'] ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Dropdown Profil -->
    <div id="profileDropdown" style="display: none; position: absolute; top: 60px; right: 2rem; background: white; width: 180px; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 2000;">
        <a href="profil.php" style="display: block; padding: 0.8rem 1rem; color: #333; text-decoration: none; font-size: 0.85rem; border-bottom: 1px solid #eee;">Profil Saya</a>
        <a href="../logout.php" style="display: block; padding: 0.8rem 1rem; color: #ff4d4d; text-decoration: none; font-size: 0.85rem; font-weight: bold;">Logout</a>
    </div>
</nav>

<script>
document.getElementById('profileToggle').onclick = (e) => {
    e.stopPropagation();
    const d = document.getElementById('profileDropdown');
    d.style.display = d.style.display === 'block' ? 'none' : 'block';
    document.getElementById('notifDropdown').style.display = 'none';
};

document.getElementById('notifToggle').onclick = (e) => {
    e.stopPropagation();
    const d = document.getElementById('notifDropdown');
    d.style.display = d.style.display === 'block' ? 'none' : 'block';
    document.getElementById('profileDropdown').style.display = 'none';
};

window.onclick = () => {
    document.getElementById('profileDropdown').style.display = 'none';
    document.getElementById('notifDropdown').style.display = 'none';
};
</script>

<div class="content-wrapper">
