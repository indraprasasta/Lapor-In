<?php
session_start();
require __DIR__ . '/../database/conection.php';
$folder = __DIR__ . '/../uploads/foto_profil/';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$id_user = $_SESSION['user_id'];
$nama    = trim($_POST['nama']);
$alamat  = trim($_POST['alamat']);

//pengecekan folder jika belum ada 
if (!is_dir($folder)) {
    mkdir($folder, 0755, true);
}

// Ambil foto lama dari database dulu
$stmt_lama = $pdo->prepare("SELECT foto FROM users WHERE id = :id");
$stmt_lama->execute([':id' => $id_user]);
$data_lama = $stmt_lama->fetch();
$foto_lama  = $data_lama['foto']; // simpan foto lama

// Proses upload foto
$nama_file = $foto_lama;

if (!empty($_FILES['foto']['name'])) {
    $nama_file_baru = time() . '_' . $_FILES['foto']['name'];
    $folder      = __DIR__ . '/../uploads/foto_profil/';
    $tipe_file   = $_FILES['foto']['type'];
    $ukuran_file = $_FILES['foto']['size'];

    $tipe_allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    
    if (!in_array($tipe_file, $tipe_allowed)) {
        echo "<script>alert('Format foto tidak didukung!'); history.back();</script>";
        exit();
    }

    if ($ukuran_file > 2 * 1024 * 1024) {
        echo "<script>alert('Ukuran foto maksimal 2MB!'); history.back();</script>";
        exit();
    }

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $folder . $nama_file_baru)) {
        $nama_file = $nama_file_baru;
        
        // Hapus foto lama jika ada
        if (!empty($foto_lama) && file_exists($folder . $foto_lama)) {
            unlink($folder . $foto_lama);
        }
    }
}

// Update data user
$stmt = $pdo->prepare("UPDATE users SET nama = :nama, alamat = :alamat, foto = :foto WHERE id = :id");
$success = $stmt->execute([':nama' => $nama, ':alamat' => $alamat, ':foto' => $nama_file, ':id' => $id_user]);

if ($success) {
    $_SESSION['nama'] = $nama;
    echo "<script>alert('Profil berhasil diperbarui!'); window.location.href = 'profile.php';</script>";
} else {
    echo "<script>alert('Gagal memperbarui profil!'); history.back();</script>";
}
?>