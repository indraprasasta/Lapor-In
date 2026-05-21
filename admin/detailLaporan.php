<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require __DIR__ . '/../database/conection.php';

$admin_nama = $_SESSION['admin_nama'];

// Ambil ID laporan dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dataLaporan.php");
    exit();
}
$id_laporan = $_GET['id'];

// Ambil data laporan dengan info pelapor
$query_laporan = mysqli_query($koneksi, "
    SELECT laporan.*, users.nama as nama_pelapor
    FROM laporan
    JOIN users ON laporan.user_id = users.id
    WHERE laporan.id = '$id_laporan'
");

if (mysqli_num_rows($query_laporan) == 0) {
    header("Location: dataLaporan.php");
    exit();
}
$laporan = mysqli_fetch_assoc($query_laporan);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan - Admin LaporIn</title>
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

            <a href="dataLaporan.php" class="flex items-center px-3 py-2.5 bg-primary/10 text-primary rounded-lg font-medium group">
                <i data-lucide="file-text" class="w-5 h-5 mr-3"></i> Data Laporan
            </a>

            <a href="buatBerita.php" class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="plus-circle" class="w-5 h-5 mr-3 group-hover:text-primary"></i> Buat Berita
            </a>

            <!-- Dropdown Manajemen Pengguna -->
            <div>
                <button onclick="toggleDropdownUser()" id="dropdownUserBtn"
                    class="w-full flex items-center justify-between px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors">
                    <div class="flex items-center">
                        <i data-lucide="users" class="w-5 h-5 mr-3 group-hover:text-primary"></i>
                        Manajemen Pengguna
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" id="dropdownUserIcon"></i>
                </button>
                <div id="dropdownUserMenu" class="hidden ml-4 mt-1 space-y-1 border-l-2 border-slate-100 pl-3">
                    <a href="datapetugas.php" class="flex items-center gap-2 px-3 py-2 text-sm text-muted hover:text-dark hover:bg-slate-50 rounded-lg transition-colors">
                        <span class="w-2 h-2 rounded-full bg-primary inline-block"></span> Data Petugas
                    </a>
                    <a href="datauser.php" class="flex items-center gap-2 px-3 py-2 text-sm text-muted hover:text-dark hover:bg-slate-50 rounded-lg transition-colors">
                        <span class="w-2 h-2 rounded-full bg-info inline-block"></span> Data User
                    </a>
                </div>
            </div>
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
            <h1 class="text-lg font-bold text-dark hidden sm:block">Detail Laporan</h1>
        </header>

        <main class="flex-1 overflow-y-auto bg-light p-4 sm:p-6 lg:p-8">
            <div class="max-w-5xl mx-auto space-y-6">

                <!-- Page Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-center gap-4">
                        <button
                            class="p-2 rounded-lg hover:bg-slate-200 transition-colors text-slate-500 hover:text-dark"
                            onclick="history.back()">
                            <i data-lucide="arrow-left" class="w-5 h-5"></i>
                        </button>
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <h2 class="text-2xl font-bold text-dark">
                                    <?php echo $laporan['judul']; ?>
                                </h2>
                                <?php
                                $status = $laporan['status'];
                                $badge_colors = [
                                    'Menunggu' => 'bg-warning/10 text-warning border-warning/20',
                                    'Diproses' => 'bg-info/10 text-info border-info/20',
                                    'Selesai'  => 'bg-secondary/10 text-secondary border-secondary/20',
                                    'Ditolak'  => 'bg-danger/10 text-danger border-danger/20',
                                ];
                                $cls = $badge_colors[$status] ?? 'bg-slate-100 text-muted border-slate-200';
                                echo "<span class='inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold border $cls'>" . strtoupper($status) . "</span>";
                                ?>
                            </div>
                            <p class="text-muted text-sm">
                                Dilaporkan pada <?php echo date('d F Y, H:i', strtotime($laporan['tanggal'])); ?> WITA
                            </p>
                        </div>
                    </div>

                    <a href="dataLaporan.php" class="px-4 py-2 bg-slate-100 text-dark font-medium rounded-lg hover:bg-slate-200 transition-colors flex items-center gap-2 text-sm">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                    </a>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Left Column: Detail Laporan -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Card Informasi Utama -->
                        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                                <h3 class="font-bold text-dark">Informasi Laporan</h3>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-6">
                                    <div>
                                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                                            Kategori</p>
                                        <p class="text-dark font-medium"><?php echo $laporan['kategori']; ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                                            Status</p>
                                        <p class="text-dark font-medium"><?php echo $laporan['status']; ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                                            Kelurahan</p>
                                        <p class="text-dark font-medium"><?php echo $laporan['kelurahan']; ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                                            Kecamatan</p>
                                        <p class="text-dark font-medium"><?php echo $laporan['kecamatan']; ?></p>
                                    </div>
                                </div>

                                <div class="mt-6 pt-6 border-t border-slate-100">
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Deskripsi</p>
                                    <p class="text-dark text-sm leading-relaxed bg-slate-50 p-4 rounded-lg border border-slate-100">
                                        <?php echo $laporan['deskripsi']; ?>
                                    </p>
                                </div>

                                <div class="mt-6 pt-6 border-t border-slate-100">
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Alamat Lengkap</p>
                                    <p class="text-dark text-sm"><?php echo $laporan['alamat']; ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Card Foto Bukti -->
                        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                                <h3 class="font-bold text-dark">Foto Bukti Kerusakan</h3>
                            </div>
                            <div class="p-6">
                                <?php if(!empty($laporan['foto'])): ?>
                                <div class="aspect-video bg-slate-100 rounded-lg overflow-hidden border border-slate-200">
                                    <img src="../uploads/foto_laporan/<?php echo $laporan['foto']; ?>" 
                                        alt="Foto Laporan" class="w-full h-full object-cover">
                                </div>
                                <p class="text-xs text-muted mt-2 text-center"><?php echo $laporan['foto']; ?></p>
                                <?php else: ?>
                                <div class="aspect-video bg-slate-100 rounded-lg border border-slate-200 flex items-center justify-center">
                                    <div class="text-center">
                                        <i data-lucide="image-off" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                                        <p class="text-muted text-sm">Tidak ada foto</p>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Info Pelapor & Aksi -->
                    <div class="space-y-6">

                        <!-- Card Info Pelapor -->
                        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                                <h3 class="font-bold text-dark">Info Pelapor</h3>
                            </div>
                            <div class="p-6">
                                <div class="flex items-center mb-4">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($laporan['nama_pelapor']); ?>&background=A3B18A&color=ffffff" 
                                        alt="Avatar" class="w-12 h-12 rounded-full border border-slate-200">
                                    <div class="ml-3">
                                        <p class="font-semibold text-dark text-sm"><?php echo $laporan['nama_pelapor']; ?></p>
                                        <p class="text-xs text-muted">Pelapor</p>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">
                                            ID Laporan</p>
                                        <p class="text-dark text-sm font-mono">#<?php echo str_pad($laporan['id'], 4, '0', STR_PAD_LEFT); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Tanggal & Status -->
                        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                                <h3 class="font-bold text-dark">Timeline</h3>
                            </div>
                            <div class="p-6">
                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                                        Tanggal Dibuat</p>
                                    <p class="text-dark font-medium"><?php echo date('d F Y, H:i', strtotime($laporan['tanggal'])); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Card Aksi -->
                        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                                <h3 class="font-bold text-dark">Aksi</h3>
                            </div>
                            <div class="p-6">
                                <button onclick="bukaModalStatus(<?php echo $laporan['id']; ?>, '<?php echo $laporan['status']; ?>', '<?php echo addslashes($laporan['judul']); ?>')"
                                    class="w-full px-4 py-2.5 bg-primary text-white rounded-lg font-semibold hover:bg-primary-dark transition-colors flex items-center justify-center gap-2 text-sm">
                                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                    Ubah Status
                                </button>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </main>
    </div>

    <!-- Modal Update Status -->
    <div id="statusModal" class="hidden fixed inset-0 z-[100] items-center justify-center p-4 bg-dark/60">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                <h3 class="font-bold text-dark">Ubah Status Laporan</h3>
                <button onclick="tutupModal()" class="p-1.5 rounded-lg text-muted hover:text-danger hover:bg-red-50 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" action="dataLaporan.php" class="px-6 py-5 space-y-4">
                <input type="hidden" name="update_status" value="1">
                <input type="hidden" name="id_laporan" id="modal_id_laporan">

                <div class="bg-slate-50 rounded-lg p-3 border border-slate-200">
                    <p class="text-xs text-muted mb-1">Judul Laporan</p>
                    <p class="font-semibold text-dark text-sm" id="modal_judul">—</p>
                    <p class="text-xs text-muted mt-2">Status saat ini: <span id="modal_status_sekarang" class="font-semibold text-dark"></span></p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Ubah Status Menjadi:</label>
                    <select name="status_baru" required
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none text-sm bg-white">
                        <option value="" disabled selected>Pilih status baru</option>
                        <option value="Menunggu">Menunggu</option>
                        <option value="Diproses">Diproses</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>
                </div>

                <div class="flex gap-3 justify-end pt-2 border-t border-slate-100">
                    <button type="button" onclick="tutupModal()"
                        class="px-4 py-2 text-sm font-medium text-muted bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-lg transition-colors flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan
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

        function bukaModalStatus(id, status, judul) {
            document.getElementById('modal_id_laporan').value = id;
            document.getElementById('modal_judul').textContent = judul;
            document.getElementById('modal_status_sekarang').textContent = status;
            document.getElementById('statusModal').classList.remove('hidden');
        }

        function tutupModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }

        // Tutup modal klik di luar
        document.getElementById('statusModal').addEventListener('click', function(e) {
            if (e.target === this) tutupModal();
        });
    </script>
</body>
</html>
