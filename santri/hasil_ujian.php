<?php
session_start();
if(!isset($_SESSION['nis'])){
    header("Location: ../index.php");
    exit;
}
include '../config/koneksi.php';

$nis = $_SESSION['nis'];

// Get filter values
$selected_sem = $_GET['sem'] ?? 'Semua';
$selected_tahun = $_GET['tahun'] ?? 'Semua';

// Fetch data santri lengkap
$query_santri = mysqli_query($conn, "
    SELECT s.*, u.nama_ustadz as wali_kelas 
    FROM tb_santri s 
    LEFT JOIN tb_ustadz u ON s.nip_wali_kelas = u.nip 
    WHERE s.nis = '$nis'
");
$data = mysqli_fetch_assoc($query_santri);

// Fetch available years and semesters for filter
$q_filter = mysqli_query($conn, "SELECT DISTINCT semester, tahun_ajaran FROM tb_hasil_ujian WHERE nis='$nis'");
$filter_options = [];
while($f = mysqli_fetch_assoc($q_filter)){
    $filter_options[] = $f;
}

// Build Query with Filter
$where = "WHERE nis='$nis'";
if($selected_sem != 'Semua') $where .= " AND semester = '$selected_sem'";
if($selected_tahun != 'Semua') $where .= " AND tahun_ajaran = '$selected_tahun'";

$q_transkrip = mysqli_query($conn, "SELECT * FROM tb_hasil_ujian $where ORDER BY semester DESC, nama_kitab ASC");
$transkrip = [];
while($row = mysqli_fetch_assoc($q_transkrip)){
    $transkrip[] = $row;
}

// Group by semester for display
$grouped_transkrip = [];
foreach($transkrip as $t){
    $grouped_transkrip[$t['semester']][] = $t;
}

include 'header.php';
?>

<div class="breadcrumb">
    <a href="beranda.php">Beranda</a> / <span>Hasil Ujian & Transkrip</span>
</div>

<div class="card" style="min-height: 80vh; padding: 2.5rem; border-radius: 20px;">
    
    <div style="text-align: center; margin-bottom: 3rem;">
        <h2 style="font-size: 2rem; color: #1b4b39; letter-spacing: 1px; margin-bottom: 0.5rem;">TRANSKRIP NILAI SANTRI</h2>
        <p style="color: #666; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px;">Pondok Pesantren Bahrul Ulum KH. Busthomi</p>
    </div>

    <!-- Filter Section -->
    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 15px; margin-bottom: 2rem; display: flex; gap: 1.5rem; align-items: center;">
        <div style="font-weight: 700; color: #3c6356; font-size: 0.9rem;">Filter Transkrip:</div>
        <form method="GET" style="display: flex; gap: 1rem;">
            <select name="sem" class="form-control" style="width: 180px; padding: 0.5rem;">
                <option value="Semua">Semua Semester</option>
                <?php 
                $unique_sem = array_unique(array_column($filter_options, 'semester'));
                foreach($unique_sem as $s): 
                ?>
                    <option value="<?= $s ?>" <?= $selected_sem == $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.5rem; font-size: 0.85rem;">Terapkan</button>
            <a href="hasil_ujian.php" class="btn btn-outline" style="padding: 0.5rem 1.5rem; font-size: 0.85rem; text-decoration: none;">Reset</a>
        </form>
    </div>

    <!-- Info Santri -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; margin-bottom: 3rem; background: #fff; padding: 2rem; border-radius: 15px; border: 1px solid #f0f0f0; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
        <div style="font-size: 0.95rem;">
            <div style="display: flex; margin-bottom: 0.8rem;">
                <div style="width: 140px; color: #888;">Nama Santri</div>
                <div style="font-weight: 700;">: <?= htmlspecialchars($data['nama_santri']) ?></div>
            </div>
            <div style="display: flex; margin-bottom: 0.8rem;">
                <div style="width: 140px; color: #888;">NIS</div>
                <div style="font-weight: 700;">: <?= $data['nis'] ?></div>
            </div>
        </div>
        <div style="font-size: 0.95rem;">
            <div style="display: flex; margin-bottom: 0.8rem;">
                <div style="width: 140px; color: #888;">Kelas / Angkatan</div>
                <div style="font-weight: 700;">: <?= $data['kelas'] ?> / <?= $data['angkatan'] ?></div>
            </div>
            <div style="display: flex;">
                <div style="width: 140px; color: #888;">Wali Kelas</div>
                <div style="font-weight: 700;">: <?= $data['wali_kelas'] ?? '-' ?></div>
            </div>
        </div>
    </div>

    <!-- Hasil Nilai -->
    <?php if(empty($grouped_transkrip)): ?>
        <div style="text-align: center; padding: 5rem; color: #999;">
            <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-bottom: 1rem; opacity: 0.3;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <p>Data nilai tidak ditemukan untuk filter ini.</p>
        </div>
    <?php else: ?>
        <?php foreach($grouped_transkrip as $sem => $items): ?>
            <div style="margin-bottom: 3rem; border: 1px solid #eee; border-radius: 15px; overflow: hidden;">
                <div style="background: #1b4b39; color: white; padding: 1rem 2rem; font-weight: 700; display: flex; justify-content: space-between;">
                    <span>SEMESTER: <?= $sem ?></span>
                    <span style="font-size: 0.8rem; opacity: 0.8;">Tahun Ajaran Aktif</span>
                </div>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 1px solid #eee;">
                            <th style="padding: 1.2rem; text-align: left; font-size: 0.75rem; color: #999; text-transform: uppercase;">Mata Pelajaran (Kitab)</th>
                            <th style="padding: 1.2rem; text-align: center; font-size: 0.75rem; color: #999; text-transform: uppercase;">Nilai</th>
                            <th style="padding: 1.2rem; text-align: left; font-size: 0.75rem; color: #999; text-transform: uppercase;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $item): ?>
                            <tr style="border-bottom: 1px solid #f9f9f9;">
                                <td style="padding: 1.2rem; font-weight: 600;"><?= $item['nama_kitab'] ?></td>
                                <td style="padding: 1.2rem; text-align: center;">
                                    <span style="display: inline-block; width: 40px; height: 40px; line-height: 40px; background: #e8f5e9; color: #1b4b39; border-radius: 50%; font-weight: 800;"><?= $item['nilai_akhir'] ?></span>
                                </td>
                                <td style="padding: 1.2rem; color: #666; font-size: 0.9rem;"><?= $item['keterangan'] ?: '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div style="margin-top: 3rem; text-align: right; border-top: 1px solid #eee; padding-top: 2rem;">
        <button class="btn btn-outline" style="border-radius: 10px; padding: 0.8rem 2rem;" onclick="window.print()">
            🖨️ Cetak Transkrip Nilai
        </button>
    </div>

</div>

<?php include 'footer.php'; ?>
