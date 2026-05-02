<?php
session_start();
if(!isset($_SESSION['nis'])){
    header("Location: ../index.php");
    exit;
}
include '../config/koneksi.php';

$nis = $_SESSION['nis'];
$pesan = "";

// Proses Update Profil
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profil'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    
    // Update foto jika ada
    if(isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0){
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['foto_profil']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)){
            $new_filename = str_replace('.', '', $nis) . '_' . time() . '.' . $ext;
            $dest = '../assets/uploads/' . $new_filename;
            
            if(move_uploaded_file($_FILES['foto_profil']['tmp_name'], $dest)){
                mysqli_query($conn, "UPDATE tb_santri SET foto='$new_filename' WHERE nis='$nis'");
            } else {
                $pesan = "<div style='color: red; margin-bottom: 1rem;'>Gagal mengupload foto.</div>";
            }
        } else {
            $pesan = "<div style='color: red; margin-bottom: 1rem;'>Format foto harus JPG atau PNG.</div>";
        }
    }
    
    // Update teks
    $q_update = "UPDATE tb_santri SET email='$email', alamat='$alamat' WHERE nis='$nis'";
    if(mysqli_query($conn, $q_update)){
        $pesan = "<div style='color: green; margin-bottom: 1rem;'>Profil berhasil diperbarui!</div>";
    } else {
        $pesan = "<div style='color: red; margin-bottom: 1rem;'>Gagal memperbarui profil.</div>";
    }
}

// Proses Hapus Foto
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['hapus_foto'])){
    mysqli_query($conn, "UPDATE tb_santri SET foto='profil.jpg' WHERE nis='$nis'");
    $pesan = "<div style='color: green; margin-bottom: 1rem;'>Foto berhasil dihapus (kembali ke default).</div>";
}

// Fetch data terbaru
$query = mysqli_query($conn, "SELECT * FROM tb_santri WHERE nis='$nis'");
$data = mysqli_fetch_assoc($query);

// Path foto
$foto_path = '../assets/profil.jpg'; // default
if(!empty($data['foto']) && $data['foto'] != 'profil.jpg'){
    $foto_path = '../assets/uploads/' . $data['foto'];
}

include 'header.php';
?>

<div class="card" style="min-height: 70vh;">
    <div style="display: flex; gap: 3rem;">
        
        <!-- Kiri: Foto Profil -->
        <div style="flex: 1; text-align: center; border-right: 1px solid var(--border-color); padding-right: 2rem;">
            <h3 class="mb-4">Foto Profil</h3>
            <div style="width: 200px; height: 250px; margin: 0 auto 1.5rem auto; border-radius: 12px; overflow: hidden; border: 4px solid #eee;">
                <img src="<?= $foto_path ?>" alt="Foto Profil" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="flex justify-center gap-4" style="flex-direction: column;">
                    <input type="file" name="foto_profil" class="form-control" accept="image/*" style="font-size: 0.8rem;">
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="submit" name="update_profil" class="btn btn-outline" style="flex: 1; font-size: 0.85rem;">Upload Foto</button>
                        <button type="submit" name="hapus_foto" class="btn btn-primary" style="flex: 1; background-color: #d32f2f; font-size: 0.85rem;" onclick="return confirm('Yakin hapus foto?')">Hapus Foto</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Kanan: Edit Profil -->
        <div style="flex: 2; padding-left: 1rem;">
            <div class="flex items-center gap-4 mb-6">
                <h3 style="margin: 0;">Edit Profil</h3>
                <svg width="20" height="20" fill="none" stroke="var(--primary-color)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            </div>

            <?= $pesan ?>

            <form method="POST">
                <div style="display: grid; grid-template-columns: 180px 1fr; gap: 1rem; align-items: center; margin-bottom: 1rem;">
                    <label style="font-weight: 500; color: var(--text-gray);">Nama Santri</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama_santri']) ?>" disabled style="background-color: #f5f5f5;">
                </div>
                
                <div style="display: grid; grid-template-columns: 180px 1fr; gap: 1rem; align-items: center; margin-bottom: 1rem;">
                    <label style="font-weight: 500; color: var(--text-gray);">Wali Santri</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['wali_santri']) ?>" disabled style="background-color: #f5f5f5;">
                </div>

                <div style="display: grid; grid-template-columns: 180px 1fr; gap: 1rem; align-items: center; margin-bottom: 1rem;">
                    <label style="font-weight: 500; color: var(--text-gray);">NIS</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['nis']) ?>" disabled style="background-color: #f5f5f5;">
                </div>

                <div style="display: grid; grid-template-columns: 180px 1fr; gap: 1rem; align-items: center; margin-bottom: 1rem;">
                    <label style="font-weight: 500; color: var(--text-gray);">Tempat Tanggal Lahir</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['tempat_lahir'] . ', ' . $data['tgl_lahir']) ?>" disabled style="background-color: #f5f5f5;">
                </div>

                <div style="display: grid; grid-template-columns: 180px 1fr; gap: 1rem; align-items: center; margin-bottom: 1rem;">
                    <label style="font-weight: 500; color: var(--text-gray);">Jenis Kelamin</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['jenis_kelamin']) ?>" disabled style="background-color: #f5f5f5;">
                </div>

                <hr style="border: 0; border-top: 1px solid #eee; margin: 1.5rem 0;">

                <div style="display: grid; grid-template-columns: 180px 1fr; gap: 1rem; align-items: center; margin-bottom: 1rem;">
                    <label style="font-weight: 500; color: var(--primary-color);">Email Terdaftar</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>" required>
                </div>

                <div style="display: grid; grid-template-columns: 180px 1fr; gap: 1rem; align-items: start; margin-bottom: 2rem;">
                    <label style="font-weight: 500; color: var(--primary-color); margin-top: 0.5rem;">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($data['alamat']) ?></textarea>
                </div>

                <button type="submit" name="update_profil" class="btn btn-primary" style="padding: 0.75rem 2.5rem; width: 100%;">Simpan Perubahan Data</button>
            </form>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>
