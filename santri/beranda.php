<?php
session_start();
if(!isset($_SESSION['nis'])){
    header("Location: ../index.php");
    exit;
}
include '../config/koneksi.php';

$nis = $_SESSION['nis'];

// Fetch data santri lengkap
$query_santri = mysqli_query($conn, "SELECT * FROM tb_santri WHERE nis = '$nis'");
$data = mysqli_fetch_assoc($query_santri);

// Fetch data hasil ujian terbaru
$query_ujian = mysqli_query($conn, "SELECT * FROM tb_hasil_ujian WHERE nis = '$nis' ORDER BY id_nilai DESC LIMIT 1");
$ujian = mysqli_fetch_assoc($query_ujian);
$nilai_display = $ujian['nilai_akhir'] ?? '-';

include 'header.php';

// Set locale/timezone for Indonesian dates
date_default_timezone_set('Asia/Jakarta');
$hari_list = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
$bulan_list = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
$hari_ini = $hari_list[date('w')];
$tgl_sekarang = $hari_ini . ", " . date('d') . " " . $bulan_list[date('n')] . " " . date('Y');
?>

<div style="display: flex; gap: 2rem; align-items: flex-start;">
    
    <!-- KOLOM KIRI (UTAMA): JADWAL MENGAJI -->
    <div style="flex: 1.6;">
        <div class="flex justify-between items-center mb-1">
            <h3 style="font-size: 1.1rem; font-weight: 800; color: #2d3748; margin: 0;">Jadwal Mengaji</h3>
            <div style="font-size: 0.8rem; color: #3c6356; font-weight: 800; display: flex; align-items: center; gap: 5px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <?= $tgl_sekarang ?>
            </div>
        </div>
        
        <?php
        $q_jadwal = mysqli_query($conn, "SELECT j.*, u.nama_ustadz FROM tb_jadwal j LEFT JOIN tb_ustadz u ON j.nip_pengajar = u.nip WHERE j.hari = '$hari_ini' ORDER BY j.waktu_mulai ASC");
        $jml_jadwal = mysqli_num_rows($q_jadwal);
        ?>
        
        <p style="font-size: 0.8rem; color: #a0aec0; margin-bottom: 1.5rem;">Anda Memiliki <?= $jml_jadwal ?> Jadwal Mengaji</p>
        
        <div style="background: #eef2f7; padding: 0.8rem; border-radius: 4px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-size: 0.85rem; color: #3c6356; font-weight: 700;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <?= $tgl_sekarang ?>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <?php
            if($jml_jadwal == 0){
                echo "<div class='card' style='text-align:center; padding: 4rem; color: #999;'>🕌 Tidak ada jadwal mengaji hari ini.</div>";
            } else {
                while($j = mysqli_fetch_assoc($q_jadwal)){
            ?>
                <!-- Card Jadwal dengan Gradasi (Sesuai Screenshot) -->
                <div class="card" style="padding: 2rem; border-radius: 20px; background: linear-gradient(135deg, #e8f5e9, #4a7c6b); border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 1.2rem;">
                    <h4 style="font-size: 1.3rem; font-weight: 800; color: #2d3748; margin: 0;"><?= $j['nama_kitab'] ?></h4>
                    
                    <div style="display: flex; flex-direction: column; gap: 0.8rem; font-size: 1rem; color: #2d3748; font-weight: 600;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <?= date('H:i', strtotime($j['waktu_mulai'])) ?> - <?= date('H:i', strtotime($j['waktu_selesai'])) ?> WIB
                        </div>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <?= $j['nama_ustadz'] ?>
                        </div>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <?= $j['lokasi'] ?>
                        </div>
                    </div>
                </div>
            <?php } } ?>
        </div>
    </div>

    <!-- KOLOM KANAN: PROFIL, TAGIHAN, KALENDER -->
    <div style="flex: 1;">
        <!-- Welcoming Card -->
        <div class="card mb-6" style="border-radius: 20px; padding: 1.5rem; display: flex; align-items: center; gap: 1.5rem; background: #fff; border: 1px solid #f0f2f5; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
            <div style="background: #f8f9fc; padding: 8px; border-radius: 10px;">
                <img src="../assets/logo.png" alt="Logo" style="width: 45px; height: 45px; object-fit: contain;">
            </div>
            <div style="flex: 1;">
                <h3 style="margin: 0; font-size: 1.1rem; color: #2d3748; font-weight: 800;">Hai, <?= strtoupper($data['nama_santri']) ?></h3>
                <p style="margin: 0.3rem 0 0; color: #4a5568; font-size: 0.85rem; line-height: 1.5;">
                    Saat ini anda berada di kelas <span style="font-weight: 700;"><?= $data['kelas'] ?></span> dengan nilai ujian <a href="hasil_ujian.php" style="color: #3c6356; font-weight: 700; text-decoration: underline;">Lihat detail</a>
                </p>
            </div>
        </div>

        <!-- Total Tagihan -->
        <div class="card mb-6" style="border-radius: 20px; padding: 1.5rem; background: #fff; border: 1px solid #f0f2f5; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
            <div style="color: #718096; font-size: 0.85rem; margin-bottom: 0.3rem;">Tagihan Belum Jatuh Tempo</div>
            <?php
            $q_tag = mysqli_query($conn, "SELECT SUM(jumlah_tagihan) as total FROM tb_tagihan WHERE nis='$nis' AND status_bayar='Belum Bayar'");
            $d_tag = mysqli_fetch_assoc($q_tag);
            $total_tagihan = $d_tag['total'] ?? 0;
            ?>
            <div style="font-size: 1.8rem; font-weight: 800; color: #2d3748; margin-bottom: 1rem;">Rp. <?= number_format($total_tagihan, 0, ',', '.') ?></div>
            <button class="btn btn-primary" style="width: 100%; border-radius: 12px; padding: 0.75rem; font-weight: 700; background: #3c6356; border: none; font-size: 0.85rem;" onclick="window.location.href='keuangan.php'">Bayar Sekarang</button>
        </div>

        <!-- Kalender Akademik -->
        <div class="card" style="border-radius: 20px; padding: 1.2rem; background: #fff; border: 1px solid #f0f2f5; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
            <div class="flex justify-between items-center mb-4">
                <div id="monthYearDisplay" style="font-weight: 800; color: #2d3748; font-size: 0.9rem;">April 2026</div>
                <div class="flex gap-2">
                    <button id="prevMonth" style="background: #f7fafc; border: 1px solid #edf2f7; cursor: pointer; padding: 3px 8px; border-radius: 6px; font-weight: bold;">&lt;</button>
                    <button id="nextMonth" style="background: #f7fafc; border: 1px solid #edf2f7; cursor: pointer; padding: 3px 8px; border-radius: 6px; font-weight: bold;">&gt;</button>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; text-align: center; font-size: 0.65rem; color: #a0aec0; font-weight: 800; text-transform: uppercase; margin-bottom: 8px;">
                <div>S</div><div>M</div><div>T</div><div>W</div><div>T</div><div>F</div><div>S</div>
            </div>
            <div id="calendarGrid" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px;">
                <!-- JS -->
            </div>
        </div>

    </div>
</div>

<!-- Modal Event -->
<div id="eventModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; justify-content: center; align-items: center; backdrop-filter: blur(2px);">
    <div style="background: white; padding: 2rem; border-radius: 20px; width: 400px; max-width: 90%; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalDateTitle" style="margin: 0; font-size: 1.2rem; color: #3c6356; font-weight: 800;">Detail Acara</h3>
            <button onclick="closeModal()" style="background: none; border: none; font-size: 2rem; cursor: pointer; color: #ccc;">&times;</button>
        </div>
        <div id="eventList" style="margin-bottom: 1.5rem; max-height: 150px; overflow-y: auto;"></div>
        <div style="background: #f8f9fa; padding: 1.2rem; border-radius: 12px;">
            <h4 style="margin-bottom: 0.8rem; font-size: 0.85rem; color: #666;">Tambah Acara Baru</h4>
            <input type="text" id="eventName" placeholder="Judul Acara" class="form-control" style="margin-bottom: 0.8rem;">
            <textarea id="eventDesc" placeholder="Keterangan" class="form-control" style="margin-bottom: 1rem;"></textarea>
            <button onclick="saveEvent()" class="btn btn-primary" style="width: 100%; font-weight: 800; background: #3c6356; border: none;">Simpan Acara</button>
        </div>
    </div>
</div>

<script>
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let eventsData = [];
let selectedDate = null;

async function fetchEvents() {
    try {
        const response = await fetch('../acara/acara.php');
        eventsData = await response.json();
        renderCalendar(currentMonth, currentYear);
    } catch (e) { renderCalendar(currentMonth, currentYear); }
}

function renderCalendar(month, year) {
    const grid = document.getElementById('calendarGrid');
    const display = document.getElementById('monthYearDisplay');
    grid.innerHTML = '';
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const names = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    display.textContent = `${names[month]} ${year}`;

    for(let i = 0; i < firstDay; i++) grid.innerHTML += '<div></div>';

    for(let i = 1; i <= daysInMonth; i++) {
        const dStr = `${year}-${String(month+1).padStart(2,'0')}-${String(i).padStart(2,'0')}`;
        const dayEvents = eventsData.filter(ev => ev.tanggal === dStr);
        const has = dayEvents.length > 0;
        
        let content = `<div style="font-weight:800; font-size: 0.85rem;">${i}</div>`;
        if(has) {
            const title = dayEvents[0].nama_acara.length > 7 ? dayEvents[0].nama_acara.substring(0, 5) + '..' : dayEvents[0].nama_acara;
            content += `<div style="font-size:0.45rem; margin-top:2px; background:rgba(255,255,255,0.3); padding:1px; border-radius:2px; white-space:nowrap; overflow:hidden;">${title}</div>`;
        }

        const style = has 
            ? "background: #3c6356; color: white; border-radius: 8px;" 
            : "background: #fff; border: 1px solid #f7fafc; border-radius: 8px; color: #4a5568;";

        grid.innerHTML += `
            <div onclick="openModal('${dStr}', ${i})" style="${style} padding: 8px 1px; cursor: pointer; text-align:center; min-height: 50px; display: flex; flex-direction: column; justify-content: center;">
                ${content}
            </div>`;
    }
}

document.getElementById('prevMonth').onclick = () => { currentMonth--; if(currentMonth<0){currentMonth=11;currentYear--;} renderCalendar(currentMonth, currentYear); };
document.getElementById('nextMonth').onclick = () => { currentMonth++; if(currentMonth>11){currentMonth=0;currentYear++;} renderCalendar(currentMonth, currentYear); };

function openModal(dStr, i) {
    selectedDate = dStr;
    const names = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    document.getElementById('modalDateTitle').textContent = `${i} ${names[currentMonth]} ${currentYear}`;
    const list = document.getElementById('eventList');
    list.innerHTML = '';
    const evs = eventsData.filter(ev => ev.tanggal === dStr);
    if(evs.length > 0) {
        evs.forEach(ev => {
            list.innerHTML += `<div style="background:#f0f7f4; border-left:4px solid #3c6356; padding:10px; border-radius:8px; margin-bottom:8px;">
                <b style="color:#1b4b39; display:block;">${ev.nama_acara}</b>
                <small style="color:#666;">${ev.deskripsi || '-'}</small>
            </div>`;
        });
    } else { list.innerHTML = '<div style="font-size:0.8rem; color:#999; text-align:center;">Belum ada acara.</div>'; }
    document.getElementById('eventModal').style.display = 'flex';
}

function closeModal() { document.getElementById('eventModal').style.display = 'none'; }

async function saveEvent() {
    const n = document.getElementById('eventName').value;
    const d = document.getElementById('eventDesc').value;
    if(!n) return alert("Isi judul acara!");
    const res = await fetch('../acara/acara.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ tanggal: selectedDate, nama_acara: n, deskripsi: d })
    });
    const result = await res.json();
    if(result.status === 'success') { closeModal(); fetchEvents(); }
}

fetchEvents();
</script>

<?php include 'footer.php'; ?>
