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
$laporan_id = (int) $_POST['id'];

// Verify that the report belongs to the current user
$stmt_check = $pdo->prepare("SELECT * FROM laporan WHERE id = :id AND user_id = :user_id");
$stmt_check->execute([':id' => $laporan_id, ':user_id' => $user_id]);

if ($stmt_check->rowCount() === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Laporan tidak ditemukan atau Anda tidak memiliki izin untuk menghapus'
    ]);
    exit();
}

$laporan = $stmt_check->fetch();

// Delete associated files
$upload_dir = __DIR__ . '/../uploads/foto_laporan/';
if (!empty($laporan['foto'])) {
    $file_path = $upload_dir . $laporan['foto'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

// Delete the report from database
$stmt_delete = $pdo->prepare("DELETE FROM laporan WHERE id = :id AND user_id = :user_id");
$success = $stmt_delete->execute([':id' => $laporan_id, ':user_id' => $user_id]);

if ($success) {
    echo json_encode([
        'success' => true,
        'message' => 'Laporan berhasil dihapus'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menghapus laporan'
    ]);
}
?>
