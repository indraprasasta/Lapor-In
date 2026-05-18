<?php
session_start();
header('Content-Type: application/json');

// cek user harus login terlebih dahulu
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Anda harus login terlebih dahulu'
    ]);
    exit();
}

// cek jika metode request bukan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit();
}

// cek jika id laporan tidak diberikan atau kosong
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID laporan tidak ditemukan'
    ]);
    exit();
}

require __DIR__ . '/../database/conection.php';

$user_id = $_SESSION['user_id'];
$laporan_id = mysqli_real_escape_string($koneksi, $_POST['id']);

// Verify that the report belongs to the current user
$query_check = mysqli_query($koneksi, "SELECT * FROM laporan WHERE id = '$laporan_id' AND user_id = '$user_id'");

if (mysqli_num_rows($query_check) === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Laporan tidak ditemukan atau Anda tidak memiliki izin untuk menghapus'
    ]);
    exit();
}

$laporan = mysqli_fetch_assoc($query_check);

// Delete associated files
$upload_dir = __DIR__ . '/../uploads/foto_laporan/';
if (!empty($laporan['foto'])) {
    $file_path = $upload_dir . $laporan['foto'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

// Delete the report from database
$query_delete = mysqli_query($koneksi, "DELETE FROM laporan WHERE id = '$laporan_id' AND user_id = '$user_id'");

if ($query_delete) {
    echo json_encode([
        'success' => true,
        'message' => 'Laporan berhasil dihapus'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menghapus laporan: ' . mysqli_error($koneksi)
    ]);
}

mysqli_close($koneksi);
?>
