<?php
header('Content-Type: application/json');
include_once '../config/koneksi.php';

// Auto-create table if not exists
$createTableQuery = "CREATE TABLE IF NOT EXISTS `tb_kalender` (
  `id_acara` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `nama_acara` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  PRIMARY KEY (`id_acara`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
mysqli_query($conn, $createTableQuery);

// Cek method request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Ambil data bulan dan tahun jika ada
    $query = mysqli_query($conn, "SELECT * FROM tb_kalender ORDER BY tanggal ASC");
    $events = [];
    while($row = mysqli_fetch_assoc($query)) {
        $events[] = $row;
    }
    echo json_encode($events);
} 
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $tanggal = mysqli_real_escape_string($conn, $input['tanggal']);
    $nama_acara = mysqli_real_escape_string($conn, $input['nama_acara']);
    $deskripsi = mysqli_real_escape_string($conn, isset($input['deskripsi']) ? $input['deskripsi'] : '');

    if(!empty($tanggal) && !empty($nama_acara)) {
        $q = "INSERT INTO tb_kalender (tanggal, nama_acara, deskripsi) VALUES ('$tanggal', '$nama_acara', '$deskripsi')";
        if(mysqli_query($conn, $q)) {
            echo json_encode(['status' => 'success', 'message' => 'Acara berhasil ditambahkan']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    }
}
?>
