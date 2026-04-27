<?php
$host       = "localhost";
$user       = "root";
$password   = "";
$db_name    = "laporin";

$koneksi = mysqli_connect($host, $user, $password, $db_name);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>