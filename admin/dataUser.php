<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require __DIR__ . '/../database/conection.php';

$admin_nama = $_SESSION['admin_nama'];

// Proses hapus user
if (isset($_GET['hapus'])) {
    $id_hapus = (int) $_GET['hapus'];

    // Hapus foto profil jika ada
    $q = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT foto FROM users WHERE id = '$id_hapus'"));
    if (!empty($q['foto'])) {
        $path = __DIR__ . '/../uploads/foto_profil/' . $q['foto'];
        if (file_exists($path)) unlink($path);
    }

    // Hapus laporan milik user terlebih dahulu (foreign key)
    mysqli_query($koneksi, "DELETE FROM laporan WHERE user_id = '$id_hapus'");

    // Hapus user
    mysqli_query($koneksi, "DELETE FROM users WHERE id = '$id_hapus'");

    header("Location: datauser.php?deleted=1");
    exit();
}

// Cari user
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$where  = $search != '' ? "WHERE nama LIKE '%$search%' OR username LIKE '%$search%'" : '';

// Ambil data user beserta total laporannya
$query_users = mysqli_query($koneksi, "
    SELECT users.*, COUNT(laporan.id) as total_laporan
    FROM users
    LEFT JOIN laporan ON users.id = laporan.user_id
    $where
    GROUP BY users.id
    ORDER BY users.id DESC
");
$total_users = mysqli_num_rows($query_users);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data User - Admin LaporIn</title>
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
                        accent:  { DEFAULT: '#A3B18A', dark: '#8b9a70' },
                        danger:  '#DC2626',
                        dark:    '#1E293B',
                        light:   '#F8FAFC',
                        muted:   '#94A3B8',
                        info:    '#0284C7',
                    }
                }
            }
        }
    </script>
    <style>
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #F8FAFC; }
        ::-webkit-scrollbar-thumb { background: #A3B18A; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #3A5A40; }
    </style>
</head>

<body class="bg-light text-dark font-sans h-screen flex overflow-hidden">

    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-dark/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 bg-white w-64 border-r border-slate-200 z-50 transform -translate-x-full lg:translate-x-0 lg:static lg:flex lg:flex-col transition-transform duration-300 ease-in-out">
        <div class="h-16 flex items-center px-6 border-b border-slate-200">
            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white mr-3">
                <i data-lucide="leaf" class="w-5 h-5"></i>
            </div>
            <span class="text-primary font-extrabold text-2xl tracking-tight">Lapor<span class="text-accent">In</span></span>
            <span class="ml-2 text-[10px] font-bold text-white bg-primary px-2 py-0.5 rounded-full uppercase">Admin</span>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <div class="px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">Dashboard</div>
            <a href="beranda.php" class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="pie-chart" class="w-5 h-5 mr-3 group-hover:text-primary"></i> Beranda Admin
            </a>
            <div class="px-3 mt-6 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">Manajemen Data</div>
            <a href="dataLaporan.php" class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="file-text" class="w-5 h-5 mr-3 group-hover:text-primary"></i> Data Laporan
            </a>
            <a href="buatBerita.php" class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="plus-circle" class="w-5 h-5 mr-3 group-hover:text-primary"></i> Buat Berita
            </a>
            <!-- Dropdown Manajemen Pengguna -->
            <div>
                <button onclick="toggleDropdownUser()" id="dropdownUserBtn"
                    class="w-full flex items-center justify-between px-3 py-2.5 text-primary bg-primary/5 rounded-lg font-medium transition-colors">
                    <div class="flex items-center">
                        <i data-lucide="users" class="w-5 h-5 mr-3"></i>
                        Manajemen Pengguna
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" id="dropdownUserIcon" style="transform: rotate(180deg)"></i>
                </button>
                <div id="dropdownUserMenu" class="ml-4 mt-1 space-y-1 border-l-2 border-slate-100 pl-3">
                    <a href="datapetugas.php" class="flex items-center gap-2 px-3 py-2 text-sm text-muted hover:text-dark hover:bg-slate-50 rounded-lg transition-colors">
                        <span class="w-2 h-2 rounded-full bg-primary inline-block"></span> Data Petugas
                    </a>
                    <a href="datauser.php" class="flex items-center gap-2 px-3 py-2 text-sm text-primary bg-primary/10 rounded-lg font-medium">
                        <span class="w-2 h-2 rounded-full bg-info inline-block"></span> Data User
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
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_nama); ?>&background=A3B18A&color=ffffff" alt="Avatar" class="w-full h-full object-cover">
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
        <header class="h-16 bg-accent border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-30 shrink-0">
            <button class="lg:hidden text-dark p-2 -ml-2 rounded-lg" onclick="toggleSidebar()">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <h1 class="text-lg font-bold text-dark hidden sm:block">Data User Terdaftar</h1>
        </header>

        <main class="flex-1 overflow-y-auto bg-light p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto space-y-6">

                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-dark">Data User</h2>
                        <p class="text-muted text-sm mt-1">Total <span class="font-semibold text-dark"><?php echo $total_users; ?></span> user terdaftar</p>
                    </div>
                </div>

                <!-- Notifikasi -->
                <?php if(isset($_GET['deleted'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-sm">
                    ✅ User berhasil dihapus beserta seluruh laporannya.
                </div>
                <?php endif; ?>

                <!-- Search -->
                <form method="GET" class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex gap-3">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                        </div>
                        <input type="text" name="search" value="<?php echo $search; ?>"
                            placeholder="Cari nama atau username..."
                            class="w-full pl-9 pr-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 outline-none text-sm">
                    </div>
                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-primary-dark transition-colors">
                        Cari
                    </button>
                    <?php if($search): ?>
                    <a href="datauser.php" class="bg-slate-100 text-dark px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-200 transition-colors">
                        Reset
                    </a>
                    <?php endif; ?>
                </form>

                <!-- Tabel -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-xs text-slate-500 uppercase tracking-wider">
                                    <th class="py-3 px-4 font-semibold">User</th>
                                    <th class="py-3 px-4 font-semibold">NIK</th>
                                    <th class="py-3 px-4 font-semibold">Username</th>
                                    <th class="py-3 px-4 font-semibold">Gender</th>
                                    <th class="py-3 px-4 font-semibold">Alamat</th>
                                    <th class="py-3 px-4 font-semibold text-center">Total Laporan</th>
                                    <th class="py-3 px-4 text-center font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-slate-100">
                                <?php if($total_users > 0): ?>
                                    <?php while($user = mysqli_fetch_assoc($query_users)): ?>
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <!-- Foto & Nama -->
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full overflow-hidden border border-slate-200 shrink-0">
                                                    <?php if(!empty($user['foto'])): ?>
                                                    <img src="../uploads/foto_profil/<?php echo $user['foto']; ?>" class="w-full h-full object-cover">
                                                    <?php else: ?>
                                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['nama']); ?>&background=A3B18A&color=ffffff" class="w-full h-full object-cover">
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-dark"><?php echo $user['nama']; ?></p>
                                                    <p class="text-xs text-muted">ID: <?php echo $user['id']; ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 font-mono text-xs text-slate-600"><?php echo $user['nik']; ?></td>
                                        <td class="py-4 px-4 text-muted">@<?php echo $user['username']; ?></td>
                                        <td class="py-4 px-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold <?php echo $user['gender'] == 'Laki-laki' ? 'bg-info/10 text-info' : 'bg-pink-50 text-pink-500'; ?>">
                                                <?php echo $user['gender']; ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-muted text-xs max-w-[180px]">
                                            <p class="line-clamp-2"><?php echo $user['alamat']; ?></p>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold
                                                <?php echo $user['total_laporan'] > 0 ? 'bg-primary/10 text-primary' : 'bg-slate-100 text-muted'; ?>">
                                                <?php echo $user['total_laporan']; ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <button onclick="konfirmasiHapus(<?php echo $user['id']; ?>, '<?php echo addslashes($user['nama']); ?>')"
                                                class="p-1.5 text-danger hover:bg-danger/10 rounded-lg transition-colors">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="py-12 text-center text-muted">
                                            <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                                            <p class="font-medium">Tidak ada user ditemukan</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function toggleDropdownUser() {
            const menu = document.getElementById('dropdownUserMenu');
            const icon = document.getElementById('dropdownUserIcon');
            menu.classList.toggle('hidden');
            icon.style.transform = menu.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        }

        function konfirmasiHapus(id, nama) {
            if (confirm('Yakin ingin menghapus user "' + nama + '"?\n\nSemua laporan milik user ini juga akan dihapus!')) {
                window.location.href = 'datauser.php?hapus=' + id;
            }
        }
    </script>
</body>
</html>