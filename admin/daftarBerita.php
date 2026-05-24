<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

require __DIR__ . '/../database/conection.php';

$admin_nama = $_SESSION['admin_nama'];

// Hapus data berita
if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    
    // Ambil data foto
    $stmt_foto = $pdo->prepare("SELECT foto FROM berita WHERE id = :id");
    $stmt_foto->execute([':id' => $id_hapus]);
    $q = $stmt_foto->fetch();
    if (!empty($q['foto'])) {
        $path = __DIR__ . '/../uploads/foto_berita/' . $q['foto'];
        if (file_exists($path)) unlink($path);
    }
    
    $stmt_hapus = $pdo->prepare("DELETE FROM berita WHERE id = :id");
    $stmt_hapus->execute([':id' => $id_hapus]);
    header("Location: daftarBerita.php");
    exit();
}

// Ambil data berita
$query_berita_stmt = $pdo->query("SELECT * FROM berita ORDER BY tanggal DESC");
$query_berita = $query_berita_stmt->fetchAll();
$total = count($query_berita);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Berita - Admin LaporIn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                    colors: {
                        primary: { DEFAULT: '#3A5A40', dark: '#2B4330' },
                        accent: { DEFAULT: '#A3B18A', dark: '#8b9a70' },
                        danger: '#DC2626',
                        info: "#0284C7", 
                        dark: '#1E293B',
                        light: '#F8FAFC',
                        muted: '#94A3B8',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-light text-dark font-sans h-screen flex overflow-hidden">

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-16 bg-accent border-b border-slate-200 flex items-center justify-between px-6 z-30">
            <h1 class="text-lg font-bold text-dark">Daftar Berita</h1>
            <a href="buatBerita.php"
                class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-primary-dark transition-colors flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i> Buat Berita
            </a>
        </header>

        <main class="flex-1 overflow-y-auto bg-light p-6">
            <div class="max-w-6xl mx-auto">

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-xs text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-4 font-semibold">Foto</th>
                                <th class="py-3 px-4 font-semibold">Judul & Kategori</th>
                                <th class="py-3 px-4 font-semibold">Tanggal</th>
                                <th class="py-3 px-4 text-center font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-100">
                            <?php if($total > 0): ?>
                                <?php foreach($query_berita as $berita): ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-4">
                                        <?php if(!empty($berita['foto'])): ?>
                                        <img src="../uploads/foto_berita/<?php echo $berita['foto']; ?>"
                                            class="w-16 h-12 object-cover rounded-lg border border-slate-200">
                                        <?php else: ?>
                                        <div class="w-16 h-12 bg-slate-100 rounded-lg flex items-center justify-center">
                                            <i data-lucide="image" class="w-5 h-5 text-slate-400"></i>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="font-bold text-dark line-clamp-1"><?php echo $berita['judul']; ?></p>
                                        <span class="text-xs text-accent font-semibold uppercase"><?php echo $berita['kategori']; ?></span>
                                    </td>
                                    <td class="py-3 px-4 text-muted">
                                        <?php echo date('d M Y', strtotime($berita['tanggal'])); ?>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="editBerita.php?id=<?php echo $berita['id']; ?>"
                                                class="p-1.5 text-primary hover:bg-primary/10 rounded-lg transition-colors">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </a>
                                            <button onclick="hapusBerita(<?php echo $berita['id']; ?>)"
                                                class="p-1.5 text-danger hover:bg-danger/10 rounded-lg transition-colors">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-muted">
                                        <i data-lucide="newspaper" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                                        <p class="font-medium">Belum ada berita</p>
                                        <a href="buatBerita.php" class="text-primary text-sm hover:underline mt-1 inline-block">Buat berita pertama</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();

        function hapusBerita(id) {
            if (confirm('Yakin ingin menghapus berita ini?')) {
                window.location.href = 'daftarBerita.php?hapus=' + id;
            }
        }
        
    </script>
</body>
</html>