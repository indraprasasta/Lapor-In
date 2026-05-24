<?php
require 'database/conection.php';
$stmt = $pdo->query("SELECT id, password FROM petugas");
$petugas = $stmt->fetchAll();
$updated = 0;
foreach($petugas as $p) {
    if (strpos($p['password'], '$2y$') !== 0) {
        $hashed = password_hash($p['password'], PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE petugas SET password = ? WHERE id = ?")->execute([$hashed, $p['id']]);
        $updated++;
    }
}
echo "Updated $updated unhashed passwords in database.";
