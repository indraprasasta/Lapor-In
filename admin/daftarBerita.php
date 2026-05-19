<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require __DIR__ . '/../database/conection.php';

$admin_nama = $_SESSION['admin_nama'];

// Proses hapus berita
if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    
    // Ambil foto dulu sebelum hapus
    $q = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT foto FROM berita WHERE id = '$id_hapus'"));
    if (!empty($q['foto'])) {
        $path = __DIR__ . '/../uploads/foto_berita/' . $q['foto'];
        if (file_exists($path)) unlink($path);
    }
    
    mysqli_query($koneksi, "DELETE FROM berita WHERE id = '$id_hapus'");
    header("Location: daftarBerita.php");
    exit();
}

// Ambil semua berita
$query_berita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal DESC");
$total = mysqli_num_rows($query_berita);
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
    <aside class="hidden lg:flex flex-col w-64 bg-white border-r border-slate-200">
        <div class="h-16 flex items-center px-6 border-b border-slate-200">
            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white mr-3">
                <i data-lucide="leaf" class="w-5 h-5"></i>
            </div>
            <span class="text-primary font-extrabold text-2xl tracking-tight">Lapor<span class="text-accent">In</span></span>
            <span class="ml-2 text-[10px] font-bold text-white bg-primary px-2 py-0.5 rounded-full uppercase">Admin</span>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1">
            <div
            class="px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider"
            >
            Dashboard
            </div>
            <a href="beranda.php" class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="pie-chart" class="w-5 h-5 mr-3 group-hover:text-primary"></i> Beranda Admin
            </a>
            <div
            class="px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider"
            >
            Manajemen data
            </div>
            <a
            href="dataLaporan.php"
            class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group"
            >
            <i
            data-lucide="file-text"
            class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"
            ></i>
            Data Laporan
            </a>
            <a href="buatBerita.php" class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="plus-circle" class="w-5 h-5 mr-3 group-hover:text-primary"></i> Buat Berita
            </a>
            <a href="daftarBerita.php" class="flex items-center px-3 py-2.5 bg-primary/10 text-primary rounded-lg font-medium group">
                <i data-lucide="newspaper" class="w-5 h-5 mr-3"></i> Daftar Berita
            </a>
            <div>
                <button onclick="toggleDropdownUser()"
                    class="w-full flex items-center justify-between px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group"
                    id="dropdownUserBtn">
                    <div class="flex items-center">
                        <i data-lucide="users" class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"></i>
                        Manajemen Pengguna
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" id="dropdownUserIcon"></i>
                </button>
                <div id="dropdownUserMenu" class="hidden ml-4 mt-1 space-y-1 border-l-2 border-slate-100 pl-3">
                    <a href="dataPetugas.php" class="flex items-center gap-2 px-3 py-2 text-sm text-muted hover:text-dark hover:bg-slate-50 rounded-lg transition-colors">
                        <span class="w-2 h-2 rounded-full bg-primary inline-block"></span>
                        Data Petugas
                    </a>
                    <a href="dataUser.php" class="flex items-center gap-2 px-3 py-2 text-sm text-muted hover:text-dark hover:bg-slate-50 rounded-lg transition-colors">
                        <span class="w-2 h-2 rounded-full bg-info inline-block"></span>
                        Data User
                    </a>
                </div>
            </div>
        <a
        href="kategoriLaporan.php"
        class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group"
        >
        <i
            data-lucide="tags"
            class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"
        ></i>
        Kategori Laporan
        </a>
        </nav>
        <div class="p-4 border-t border-slate-200">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-full bg-slate-100 overflow-hidden border border-slate-200">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_nama); ?>&background=A3B18A&color=ffffff" 
                        alt="Avatar" class="w-full h-full object-cover">
                </div>
                <div class="ml-3">
                    <p class="text-sm font-semibold text-dark"><?php echo $admin_nama; ?></p>
                    <p class="text-[10px] text-muted">Administrator</p>
                </div>
            </div>
            <a href="logout.php" class="w-full flex items-center justify-center px-3 py-2 text-sm text-danger bg-red-50 hover:bg-red-100 rounded-lg font-medium transition-colors">
                <i data-lucide="log-out" class="w-4 h-4 mr-2"></i> Keluar
            </a>
        </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 z-30">
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
                                <?php while($berita = mysqli_fetch_assoc($query_berita)): ?>
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
                                <?php endwhile; ?>
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
              // function dropdown
        function toggleDropdownUser() {
        const menu = document.getElementById('dropdownUserMenu');
        const icon = document.getElementById('dropdownUserIcon');
        const btn  = document.getElementById('dropdownUserBtn');

        menu.classList.toggle('hidden');

        if (!menu.classList.contains('hidden')) {
            icon.style.transform = 'rotate(180deg)';
            btn.classList.add('text-primary', 'bg-primary/5');
            btn.classList.remove('text-muted');
        } else {
            icon.style.transform = 'rotate(0deg)';
            btn.classList.remove('text-primary', 'bg-primary/5');
            btn.classList.add('text-muted');
        }
    }
    </script>
</body>
</html>