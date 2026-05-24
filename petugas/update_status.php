<?php
session_start();

if (!isset($_SESSION['petugas_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require __DIR__ . '/../database/conection.php';

$id_laporan  = (int) $_POST['id_laporan'];
$status_baru = trim($_POST['status_baru']);
$allowed     = ['Diproses', 'Selesai', 'Ditolak'];

if (!in_array($status_baru, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
    exit();
}

$stmt = $pdo->prepare("UPDATE laporan SET status = :status WHERE id = :id");
$success = $stmt->execute([':status' => $status_baru, ':id' => $id_laporan]);

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Status berhasil diperbarui']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal mengupdate status']);
}
?>