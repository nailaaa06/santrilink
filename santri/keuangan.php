<?php
session_start();
if(!isset($_SESSION['nis'])){
    header("Location: ../index.php");
    exit;
}
include '../config/koneksi.php';

$nis = $_SESSION['nis'];
$pesan = "";

// Gunakan nama tabel yang berbeda untuk menghindari konflik kolom 'nis'
$q_table = "CREATE TABLE IF NOT EXISTS `tb_pembayaran_santri` (
  `id_bayar` int(11) NOT NULL AUTO_INCREMENT,
  `nis` varchar(20) NOT NULL,
  `tgl_bayar` datetime DEFAULT CURRENT_TIMESTAMP,
  `nominal` int(11) NOT NULL,
  `metode` varchar(50) NOT NULL,
  `bukti_foto` varchar(255) DEFAULT NULL,
  `keterangan` text,
  `status` varchar(20) DEFAULT 'pending',
  PRIMARY KEY (`id_bayar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
mysqli_query($conn, $q_table);

// Handle Confirmation Form
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['konfirmasi'])){
    $nominal = $_POST['total_bayar'] ?? 650000; 
    $metode = mysqli_real_escape_string($conn, $_POST['metode']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    
    $bukti = "default_bukti.jpg";
    if(isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0){
        $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
        $new_name = "bukti_" . $nis . "_" . time() . "." . $ext;
        if(!is_dir("../assets/uploads")) mkdir("../assets/uploads");
        if(move_uploaded_file($_FILES['bukti']['tmp_name'], "../assets/uploads/" . $new_name)){
            $bukti = $new_name;
        }
    }
    
    $q_insert = "INSERT INTO tb_pembayaran_santri (nis, nominal, metode, bukti_foto, keterangan, status) VALUES ('$nis', '$nominal', '$metode', '$bukti', '$keterangan', 'pending')";
    if(mysqli_query($conn, $q_insert)){
        $pesan = "<div style='background:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px; border: 1px solid #c3e6cb;'>✅ Konfirmasi Pembayaran Berhasil Dikirim! Silakan tunggu verifikasi Admin.</div>";
    }
}

include 'header.php';
?>

<div class="breadcrumb" style="margin-bottom: 1rem;">
    <a href="beranda.php">Beranda</a> / <span>Keuangan</span>
</div>

<div class="card" style="margin-bottom: 2rem;">
    <h2 class="mb-6">Sistem Pembayaran & Keuangan</h2>

    <?= $pesan ?>

    <div style="display: flex; gap: 2rem; flex-wrap: wrap; align-items: flex-start;">
        
        <!-- BAGIAN KIRI: TAGIHAN & FORM -->
        <div style="flex: 2; min-width: 400px;">
            <div class="card" style="border: 1px solid #e0e0e0; margin-bottom: 1.5rem; background: white;">
                <h3 class="mb-4" style="font-size: 1.1rem;">Daftar Tagihan Santri</h3>
                <p style="font-size: 0.85rem; color: #666; margin-bottom: 1.5rem;">Pilih tagihan yang ingin dikonfirmasi pembayarannya:</p>
                
                <?php
                $q_tagihan_list = mysqli_query($conn, "SELECT * FROM tb_tagihan WHERE nis='$nis' AND status_bayar='Belum Bayar'");
                if(mysqli_num_rows($q_tagihan_list) == 0){
                    echo "<p style='color: #2e7d32; font-weight: 600; text-align: center; padding: 2rem;'>✅ Semua tagihan Anda sudah lunas!</p>";
                }
                while($tl = mysqli_fetch_assoc($q_tagihan_list)){
                ?>
                <div style="background: #f9f9f9; padding: 1.2rem; border-radius: 10px; margin-bottom: 1rem; border: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <input type="checkbox" checked style="width: 20px; height: 20px; accent-color: var(--primary-color);">
                        <div>
                            <div style="font-weight: bold; color: #333;"><?= $tl['nama_tagihan'] ?></div>
                            <div style="font-size: 0.8rem; color: #d32f2f;">Jatuh Tempo: <?= date('d M Y', strtotime($tl['jatuh_tempo'])) ?></div>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: 800; color: var(--primary-color); font-size: 1.1rem;">Rp. <?= number_format($tl['jumlah_tagihan'], 0, ',', '.') ?></div>
                        <div style="font-size: 0.7rem; color: #999;">Belum Bayar</div>
                    </div>
                </div>
                <?php } ?>
            </div>

            <!-- Pilih Metode -->
            <div class="card" style="border: 1px solid #e0e0e0; margin-bottom: 1.5rem;">
                <h4 class="mb-4">1. Pilih Metode Pembayaran</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                    <div onclick="selectMethod('BCA')" id="btnBCA" style="cursor:pointer; border: 2px solid #eee; padding: 1rem; border-radius: 8px; text-align: center;">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" style="height: 20px; margin-bottom: 0.5rem;">
                        <div style="font-size: 0.8rem; font-weight: 600;">Bank BCA</div>
                    </div>
                    <div onclick="selectMethod('BRI')" id="btnBRI" style="cursor:pointer; border: 2px solid #eee; padding: 1rem; border-radius: 8px; text-align: center;">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/2e/BRI_2020.svg" style="height: 20px; margin-bottom: 0.5rem;">
                        <div style="font-size: 0.8rem; font-weight: 600;">Bank BRI</div>
                    </div>
                    <div onclick="selectMethod('QRIS')" id="btnQRIS" style="cursor:pointer; border: 2px solid #eee; padding: 1rem; border-radius: 8px; text-align: center;">
                        <div style="font-weight: 900; color: #d32f2f; margin-bottom: 0.5rem;">QRIS</div>
                        <div style="font-size: 0.8rem; font-weight: 600;">Semua E-Wallet</div>
                    </div>
                </div>

                <!-- Info Rekening Dinamis -->
                <div id="infoRekening" style="margin-top: 1.5rem; display: none; background: #f0f7f4; padding: 1.5rem; border-radius: 8px; border: 1px solid #d1e7dd;">
                    <div id="rekeningText">
                        <div style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem;">Nomor Rekening Tujuan:</div>
                        <div id="noRek" style="font-size: 1.5rem; font-weight: 800; letter-spacing: 2px; color: var(--primary-color);">0000 0000 00</div>
                        <div id="atasNama" style="font-weight: 600; margin-top: 0.5rem;">a.n Pesantren Bahrul Ulum</div>
                    </div>
                    <div id="qrisImage" style="display: none; text-align: center;">
                        <div style="font-size: 0.9rem; margin-bottom: 1rem;">Silakan Scan QRIS Berikut:</div>
                        <div style="background: white; padding: 15px; display: inline-block; border-radius: 12px; border: 2px solid #ddd;">
                             <img src="https://upload.wikimedia.org/wikipedia/commons/d/d0/QR_code_for_mobile_English_Wikipedia.svg" style="width: 150px; height: 150px;">
                        </div>
                        <div style="margin-top: 0.5rem; font-weight: bold; color: #d32f2f;">QRIS YAYASAN BAHRUL ULUM</div>
                    </div>
                </div>
            </div>

            <!-- Form Konfirmasi -->
            <div class="card" style="border: 1px solid #e0e0e0;">
                <h4 class="mb-4">2. Unggah Bukti Pembayaran</h4>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="metode" id="inputMetode" required>
                    <input type="hidden" name="total_bayar" value="450000">
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.9rem; margin-bottom: 0.4rem; color: #555;">Keterangan Pembayaran</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Pembayaran SPP Bulan April" required>
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 0.9rem; margin-bottom: 0.4rem; color: #555;">Pilih File Bukti (JPG/PNG)</label>
                        <input type="file" name="bukti" class="form-control" accept="image/*" required>
                    </div>
                    <button type="submit" name="konfirmasi" class="btn btn-primary" style="width: 100%; padding: 1rem; font-weight: bold; font-size: 1rem;">KONFIRMASI PEMBAYARAN</button>
                </form>
            </div>
        </div>

        <!-- BAGIAN KANAN: RIWAYAT / URAIAN -->
        <div style="flex: 1.2; min-width: 300px;">
            <h3 class="mb-4">Riwayat Pembayaran</h3>
            <div style="background: white; border-radius: 12px; border: 1px solid #eee; overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                    <thead style="background: #f8f9fa;">
                        <tr>
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #eee;">Uraian</th>
                            <th style="padding: 1rem; text-align: right; border-bottom: 2px solid #eee;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q = mysqli_query($conn, "SELECT * FROM tb_pembayaran_santri WHERE nis='$nis' ORDER BY id_bayar DESC");
                        if(mysqli_num_rows($q) == 0){
                            echo "<tr><td colspan='2' style='padding: 2rem; text-align: center; color: #999;'>Belum ada riwayat.</td></tr>";
                        }
                        while($d = mysqli_fetch_assoc($q)){
                            $st = $d['status'];
                            $color = ($st == 'pending') ? '#f57c00' : '#2e7d32';
                            $bg = ($st == 'pending') ? '#fff3e0' : '#e8f5e9';
                        ?>
                        <tr style="border-bottom: 1px solid #f5f5f5;">
                            <td style="padding: 1rem;">
                                <div style="font-weight: 600;"><?= $d['keterangan'] ?></div>
                                <div style="font-size: 0.75rem; color: #888;"><?= date('d/m/Y', strtotime($d['tgl_bayar'])) ?> | <?= $d['metode'] ?></div>
                                <div style="font-weight: bold; color: var(--primary-color); margin-top: 0.3rem;">Rp. <?= number_format($d['nominal'], 0, ',', '.') ?></div>
                            </td>
                            <td style="padding: 1rem; text-align: right; vertical-align: top;">
                                <span style="font-size: 0.7rem; padding: 0.2rem 0.5rem; border-radius: 4px; background: <?= $bg ?>; color: <?= $color ?>; font-weight: bold;"><?= strtoupper($st) ?></span>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
function selectMethod(method) {
    // Reset all
    document.getElementById('btnBCA').style.borderColor = '#eee';
    document.getElementById('btnBRI').style.borderColor = '#eee';
    document.getElementById('btnQRIS').style.borderColor = '#eee';
    document.getElementById('btnBCA').style.background = 'white';
    document.getElementById('btnBRI').style.background = 'white';
    document.getElementById('btnQRIS').style.background = 'white';
    
    // Highlight selected
    const selectedBtn = document.getElementById('btn' + method);
    selectedBtn.style.borderColor = 'var(--primary-color)';
    selectedBtn.style.background = '#f0f7f4';
    
    document.getElementById('inputMetode').value = method;
    document.getElementById('infoRekening').style.display = 'block';
    
    if(method === 'BCA') {
        document.getElementById('rekeningText').style.display = 'block';
        document.getElementById('qrisImage').style.display = 'none';
        document.getElementById('noRek').textContent = '1234 5678 90';
        document.getElementById('atasNama').textContent = 'a.n Pesantren Bahrul Ulum';
    } else if(method === 'BRI') {
        document.getElementById('rekeningText').style.display = 'block';
        document.getElementById('qrisImage').style.display = 'none';
        document.getElementById('noRek').textContent = '0021 0100 2345 501';
        document.getElementById('atasNama').textContent = 'a.n Yayasan Bahrul Ulum';
    } else if(method === 'QRIS') {
        document.getElementById('rekeningText').style.display = 'none';
        document.getElementById('qrisImage').style.display = 'block';
    }
}
</script>

<?php include 'footer.php'; ?>
