<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require __DIR__ . '/../database/conection.php';

$admin_nama  = $_SESSION['admin_nama'];
$pesan_error = "";

// Proses tambah kategori
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama_kategori']);
    $icon      = mysqli_real_escape_string($koneksi, $_POST['icon']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    $cek = mysqli_query($koneksi, "SELECT id FROM kategori_laporan WHERE nama_kategori = '$nama'");
    if (mysqli_num_rows($cek) > 0) {
        $pesan_error = "Kategori '$nama' sudah ada!";
    } else {
        mysqli_query($koneksi, "INSERT INTO kategori_laporan (nama_kategori, icon, deskripsi) VALUES ('$nama', '$icon', '$deskripsi')");
        header("Location: kategori_laporan.php?added=1");
        exit();
    }
}

// Proses toggle aktif/nonaktif
if (isset($_GET['toggle'])) {
    $id     = (int) $_GET['toggle'];
    $aktif  = (int) $_GET['aktif'];
    $baru   = $aktif == 1 ? 0 : 1;
    mysqli_query($koneksi, "UPDATE kategori_laporan SET aktif = '$baru' WHERE id = '$id'");
    header("Location: kategori_laporan.php");
    exit();
}

// Proses hapus
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM kategori_laporan WHERE id = '$id'");
    header("Location: kategori_laporan.php?deleted=1");
    exit();
}

// Ambil semua kategori
$query_kategori = mysqli_query($koneksi, "SELECT * FROM kategori_laporan ORDER BY id ASC");
$total          = mysqli_num_rows($query_kategori);
$total_aktif    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM kategori_laporan WHERE aktif = 1"))['t'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Laporan - Admin LaporIn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                    colors: {
                        primary:   { DEFAULT: '#3A5A40', dark: '#2B4330' },
                        accent:    { DEFAULT: '#A3B18A', dark: '#8b9a70' },
                        secondary: '#588157',
                        warning:   '#D97706',
                        danger:    '#DC2626',
                        info:      '#0284C7',
                        dark:      '#1E293B',
                        light:     '#F8FAFC',
                        muted:     '#94A3B8',
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

        #modalTambah { transition: opacity 0.2s ease; }
        #modalTambah.hidden { display: none; }
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
            <a href="daftarBerita.php" class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
            <i data-lucide="file-text" class="w-5 h-5 mr-3 group-hover:text-primary"></i> Daftar Berita
            </a>
            <div>
                <button onclick="toggleDropdownUser()" id="dropdownUserBtn"
                    class="w-full flex items-center justify-between px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors">
                    <div class="flex items-center">
                        <i data-lucide="users" class="w-5 h-5 mr-3"></i> Manajemen Pengguna
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4" id="dropdownUserIcon"></i>
                </button>
                <div id="dropdownUserMenu" class="hidden ml-4 mt-1 space-y-1 border-l-2 border-slate-100 pl-3">
                    <a href="dataPetugas.php" class="flex items-center gap-2 px-3 py-2 text-sm text-muted hover:text-dark hover:bg-slate-50 rounded-lg transition-colors">
                        <span class="w-2 h-2 rounded-full bg-primary inline-block"></span> Data Petugas
                    </a>
                    <a href="dataUser.php" class="flex items-center gap-2 px-3 py-2 text-sm text-muted hover:text-dark hover:bg-slate-50 rounded-lg transition-colors">
                        <span class="w-2 h-2 rounded-full bg-info inline-block"></span> Data User
                    </a>
                </div>
            </div>
            <a href="kategori_laporan.php" class="flex items-center px-3 py-2.5 bg-primary/10 text-primary rounded-lg font-medium group">
                <i data-lucide="tags" class="w-5 h-5 mr-3"></i> Kategori Laporan
            </a>
        </nav>

        <div class="p-4 border-t border-slate-200">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-full bg-slate-100 overflow-hidden border border-slate-200">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_nama); ?>&background=A3B18A&color=ffffff" class="w-full h-full object-cover">
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
            <h1 class="text-lg font-bold text-dark hidden sm:block">Manajemen Kategori Laporan</h1>
        </header>

        <main class="flex-1 overflow-y-auto bg-light p-4 sm:p-6 lg:p-8">
            <div class="max-w-5xl mx-auto space-y-6">

                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-dark">Kategori Laporan</h2>
                        <p class="text-muted text-sm mt-1">
                            Total <span class="font-semibold text-dark"><?php echo $total; ?></span> kategori,
                            <span class="font-semibold text-secondary"><?php echo $total_aktif; ?></span> aktif
                        </p>
                    </div>
                    <button onclick="bukaModal()"
                        class="bg-primary text-white px-4 py-2.5 rounded-lg text-sm font-semibold hover:bg-primary-dark transition-colors flex items-center gap-2 shadow-sm">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kategori
                    </button>
                </div>

                <!-- Notifikasi -->
                <?php if(isset($_GET['added'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-sm">
                    ✅ Kategori berhasil ditambahkan!
                </div>
                <?php elseif(isset($_GET['deleted'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-sm">
                    ✅ Kategori berhasil dihapus!
                </div>
                <?php endif; ?>

                <?php if($pesan_error != ""): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm">
                    ❌ <?php echo $pesan_error; ?>
                </div>
                <?php endif; ?>

                <!-- Grid Kategori -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php if($total > 0): ?>
                        <?php while($kat = mysqli_fetch_assoc($query_kategori)): ?>
                        <div class="bg-white border <?php echo $kat['aktif'] ? 'border-slate-200' : 'border-slate-100 opacity-60'; ?> rounded-xl shadow-sm p-5 transition-all hover:shadow-md">
                            <div class="flex items-start justify-between mb-3">
                                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
                                    <i data-lucide="<?php echo $kat['icon']; ?>" class="w-6 h-6 text-primary"></i>
                                </div>
                                <div class="flex items-center gap-1">
                                    <!-- Toggle aktif -->
                                    <a href="kategori_laporan.php?toggle=<?php echo $kat['id']; ?>&aktif=<?php echo $kat['aktif']; ?>"
                                        title="<?php echo $kat['aktif'] ? 'Nonaktifkan' : 'Aktifkan'; ?>"
                                        class="p-1.5 rounded-lg <?php echo $kat['aktif'] ? 'text-secondary hover:bg-secondary/10' : 'text-muted hover:bg-slate-100'; ?> transition-colors">
                                        <i data-lucide="<?php echo $kat['aktif'] ? 'toggle-right' : 'toggle-left'; ?>" class="w-5 h-5"></i>
                                    </a>
                                    <!-- Hapus -->
                                    <button onclick="hapusKategori(<?php echo $kat['id']; ?>, '<?php echo addslashes($kat['nama_kategori']); ?>')"
                                        class="p-1.5 text-danger hover:bg-danger/10 rounded-lg transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>

                            <h3 class="font-bold text-dark mb-1"><?php echo $kat['nama_kategori']; ?></h3>
                            <p class="text-xs text-muted line-clamp-2 mb-3"><?php echo $kat['deskripsi'] ?: 'Tidak ada deskripsi'; ?></p>

                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold <?php echo $kat['aktif'] ? 'bg-secondary/10 text-secondary' : 'bg-slate-100 text-muted'; ?>">
                                    <?php echo $kat['aktif'] ? 'AKTIF' : 'NONAKTIF'; ?>
                                </span>
                                <span class="text-xs text-muted font-mono">icon: <?php echo $kat['icon']; ?></span>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-span-3 py-12 text-center text-muted">
                            <i data-lucide="tags" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                            <p class="font-medium">Belum ada kategori</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </main>
    </div>

    <!-- Modal Tambah Kategori -->
    <div id="modalTambah" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-dark/60">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                <h3 class="font-bold text-dark">Tambah Kategori Baru</h3>
                <button onclick="tutupModal()" class="p-1.5 rounded-lg text-muted hover:text-danger hover:bg-red-50 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" class="px-6 py-5 space-y-4">
                <input type="hidden" name="tambah" value="1">

                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="nama_kategori" required placeholder="Cth: Jalan Rusak"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">
                        Icon <span class="text-danger">*</span>
                        <a href="https://lucide.dev/icons/" target="_blank" class="text-primary text-xs font-normal hover:underline ml-1">Lihat daftar icon →</a>
                    </label>
                    <input type="text" name="icon" required placeholder="Cth: road, tree-pine, lightbulb-off"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none text-sm">
                    <p class="text-xs text-muted mt-1">Gunakan nama icon dari Lucide Icons</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" placeholder="Deskripsi singkat kategori ini..."
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none text-sm resize-none"></textarea>
                </div>

                <div class="flex gap-3 justify-end pt-2 border-t border-slate-100">
                    <button type="button" onclick="tutupModal()"
                        class="px-4 py-2 text-sm font-medium text-muted bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-lg transition-colors flex items-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah
                    </button>
                </div>
            </form>
        </div>
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

        function bukaModal() {
            document.getElementById('modalTambah').classList.remove('hidden');
        }

        function tutupModal() {
            document.getElementById('modalTambah').classList.add('hidden');
        }

        document.getElementById('modalTambah').addEventListener('click', function(e) {
            if (e.target === this) tutupModal();
        });

        function hapusKategori(id, nama) {
            if (confirm('Yakin ingin menghapus kategori "' + nama + '"?\n\nKategori yang sudah digunakan di laporan tidak akan terpengaruh.')) {
                window.location.href = 'kategori_laporan.php?hapus=' + id;
            }
        }
    </script>
</body>
</html>