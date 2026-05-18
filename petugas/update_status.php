<?php
session_start();

if (!isset($_SESSION['petugas_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require __DIR__ . '/../database/conection.php';

$id_laporan  = (int) $_POST['id_laporan'];
$status_baru = mysqli_real_escape_string($koneksi, $_POST['status_baru']);
$allowed     = ['Diproses', 'Selesai', 'Ditolak'];

if (!in_array($status_baru, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
    exit();
}

$query = mysqli_query($koneksi, "UPDATE laporan SET status = '$status_baru' WHERE id = '$id_laporan'");

if ($query) {
    echo json_encode(['success' => true, 'message' => 'Status berhasil diperbarui']);
} else {
    echo json_encode(['success' => false, 'message' => mysqli_error($koneksi)]);
}
?>