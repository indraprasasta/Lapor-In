<?php
$host       = "localhost";
$user       = "root";
$password   = "";
$db_name    = "laporin";

// Buat koneksi PDO (Baru)
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi PDO gagal: " . $e->getMessage());
}
?>