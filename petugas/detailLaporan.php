<?php
session_start();
require __DIR__ . '/../database/conection.php';

if (!isset($_SESSION['petugas_id'])) {
    header("Location: ../login.php");
    exit();
}

$petugas_nama    = $_SESSION['petugas_nama'];
$petugas_jabatan = $_SESSION['petugas_jabatan'] ?? 'Petugas Lapangan';
$petugas_dinas   = $_SESSION['petugas_dinas'];
$dinas_id        = $_SESSION['petugas_dinas_id'];

// Ambil ID laporan dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: beranda.php");
    exit();
}
$id_laporan = (int) $_GET['id'];

// Ambil data laporan dengan info pelapor
$query_laporan = mysqli_query($koneksi, "
    SELECT laporan.*, users.nama as nama_pelapor
    FROM laporan
    JOIN users ON laporan.user_id = users.id
    WHERE laporan.id = '$id_laporan'
");

if (mysqli_num_rows($query_laporan) == 0) {
    header("Location: beranda.php");
    exit();
}
$laporan = mysqli_fetch_assoc($query_laporan);

// Ambil kategori dinas petugas — pastikan laporan ini memang milik dinas petugas
$query_kategori = mysqli_query($koneksi,
    "SELECT kategori FROM dinas_kategori WHERE dinas_id = '$dinas_id'"
);
$kategori_list = [];
while ($row = mysqli_fetch_assoc($query_kategori)) {
    $kategori_list[] = $row['kategori'];
}

// Keamanan: petugas hanya boleh lihat laporan sesuai kategori dinasnya
if (!in_array($laporan['kategori'], $kategori_list)) {
    header("Location: beranda.php");
    exit();
}

// Tentukan halaman kembali
$back_url = isset($_GET['from']) ? 'pengaduan.php?status=' . $_GET['from'] : 'beranda.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan - LaporIn Mataram</title>
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
        #statusModal { transition: opacity 0.2s ease; }
        #modalBox { transition: transform 0.25s ease, opacity 0.25s ease; }
        #statusModal.modal-hidden { opacity: 0; pointer-events: none; }
        #statusModal.modal-hidden #modalBox { transform: scale(0.95); opacity: 0; }
        #toast { transition: all 0.35s cubic-bezier(0.4,0,0.2,1); }
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
            <span class="ml-2 text-[10px] font-bold text-white bg-primary px-2 py-0.5 rounded-full uppercase">Petugas</span>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <a href="beranda.php" class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 group-hover:text-primary"></i> Dashboard
            </a>

            <!-- Kelola Pengaduan Dropdown -->
            <div>
                <button onclick="toggleDropdown()" id="dropdownBtn"
                    class="w-full flex items-center justify-between px-3 py-2.5 text-primary bg-primary/5 rounded-lg font-medium transition-colors">
                    <div class="flex items-center">
                        <i data-lucide="clipboard-list" class="w-5 h-5 mr-3"></i>
                        Kelola Pengaduan
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4" id="dropdownIcon" style="transform: rotate(180deg)"></i>
                </button>
                <div id="dropdownMenu" class="ml-4 mt-1 space-y-1 border-l-2 border-slate-100 pl-3">
                    <a href="pengaduan.php?status=Menunggu" class="flex items-center gap-2 px-3 py-2 text-sm text-muted hover:text-dark hover:bg-slate-50 rounded-lg transition-colors">
                        <span class="w-2 h-2 rounded-full bg-warning inline-block"></span> Pengaduan Masuk
                    </a>
                    <a href="pengaduan.php?status=Diproses" class="flex items-center gap-2 px-3 py-2 text-sm text-muted hover:text-dark hover:bg-slate-50 rounded-lg transition-colors">
                        <span class="w-2 h-2 rounded-full bg-info inline-block"></span> Pengaduan Proses
                    </a>
                    <a href="pengaduan.php?status=Ditolak" class="flex items-center gap-2 px-3 py-2 text-sm text-muted hover:text-dark hover:bg-slate-50 rounded-lg transition-colors">
                        <span class="w-2 h-2 rounded-full bg-danger inline-block"></span> Pengaduan Ditolak
                    </a>
                    <a href="pengaduan.php?status=Selesai" class="flex items-center gap-2 px-3 py-2 text-sm text-muted hover:text-dark hover:bg-slate-50 rounded-lg transition-colors">
                        <span class="w-2 h-2 rounded-full bg-secondary inline-block"></span> Pengaduan Selesai
                    </a>
                </div>
            </div>

        </nav>

        <div class="p-4 border-t border-slate-200">
            <div class="flex items-center mb-4 group">
                <div class="w-10 h-10 rounded-full bg-slate-100 overflow-hidden border border-slate-200">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($petugas_nama); ?>&background=A3B18A&color=ffffff" class="w-full h-full object-cover">
                </div>
                <div class="ml-3">
                    <p class="text-sm font-semibold text-dark"><?php echo $petugas_nama; ?></p>
                    <p class="text-xs text-muted"><?php echo $petugas_jabatan; ?></p>
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
            <div class="hidden sm:flex items-center gap-2 text-sm text-dark">
                <a href="beranda.php" class="hover:text-primary font-medium">Dashboard</a>
                <i data-lucide="chevron-right" class="w-4 h-4 text-muted"></i>
                <span class="font-bold">Detail Laporan</span>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto bg-light p-4 sm:p-6 lg:p-8">
            <div class="max-w-5xl mx-auto space-y-6">

                <!-- Page Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-center gap-4">
                        <button onclick="history.back()"
                            class="p-2 rounded-lg hover:bg-slate-200 transition-colors text-slate-500 hover:text-dark">
                            <i data-lucide="arrow-left" class="w-5 h-5"></i>
                        </button>
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <h2 class="text-2xl font-bold text-dark"><?php echo htmlspecialchars($laporan['judul']); ?></h2>
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
                    <a href="<?php echo $back_url; ?>" class="px-4 py-2 bg-slate-100 text-dark font-medium rounded-lg hover:bg-slate-200 transition-colors flex items-center gap-2 text-sm">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                    </a>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Left: Detail -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Informasi Laporan -->
                        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                                <h3 class="font-bold text-dark">Informasi Laporan</h3>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-6">
                                    <div>
                                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Kategori</p>
                                        <p class="text-dark font-medium"><?php echo $laporan['kategori']; ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Status</p>
                                        <?php
                                        echo "<span class='inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold border $cls'>" . strtoupper($status) . "</span>";
                                        ?>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Kelurahan</p>
                                        <p class="text-dark font-medium"><?php echo $laporan['kelurahan']; ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Kecamatan</p>
                                        <p class="text-dark font-medium"><?php echo $laporan['kecamatan']; ?></p>
                                    </div>
                                </div>

                                <div class="mt-6 pt-6 border-t border-slate-100">
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Deskripsi</p>
                                    <p class="text-dark text-sm leading-relaxed bg-slate-50 p-4 rounded-lg border border-slate-100">
                                        <?php echo nl2br(htmlspecialchars($laporan['deskripsi'])); ?>
                                    </p>
                                </div>

                                <div class="mt-6 pt-6 border-t border-slate-100">
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Alamat Lengkap</p>
                                    <p class="text-dark text-sm"><?php echo htmlspecialchars($laporan['alamat']); ?></p>
                                </div>

                                <!-- Koordinat jika ada -->
                                <?php if(!empty($laporan['latitude']) && !empty($laporan['longitude'])): ?>
                                <div class="mt-6 pt-6 border-t border-slate-100">
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Koordinat</p>
                                    <p class="text-dark text-sm font-mono">
                                        <?php echo $laporan['latitude']; ?>, <?php echo $laporan['longitude']; ?>
                                    </p>
                                    <a href="https://maps.google.com/?q=<?php echo $laporan['latitude']; ?>,<?php echo $laporan['longitude']; ?>"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 text-xs text-primary hover:underline mt-1">
                                        <i data-lucide="map-pin" class="w-3 h-3"></i> Lihat di Google Maps
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Foto Bukti -->
                        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                                <h3 class="font-bold text-dark">Foto Bukti Kerusakan</h3>
                            </div>
                            <div class="p-6">
                                <?php if(!empty($laporan['foto'])): ?>
                                <div class="aspect-video bg-slate-100 rounded-lg overflow-hidden border border-slate-200">
                                    <img src="../uploads/foto_laporan/<?php echo $laporan['foto']; ?>"
                                        alt="Foto Laporan" class="w-full h-full object-cover cursor-pointer"
                                        onclick="this.requestFullscreen()">
                                </div>
                                <p class="text-xs text-muted mt-2 text-center">Klik foto untuk memperbesar</p>
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

                    <!-- Right: Info & Aksi -->
                    <div class="space-y-6">

                        <!-- Info Pelapor -->
                        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                                <h3 class="font-bold text-dark">Info Pelapor</h3>
                            </div>
                            <div class="p-6">
                                <div class="flex items-center mb-4">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($laporan['nama_pelapor']); ?>&background=A3B18A&color=ffffff"
                                        alt="Avatar" class="w-12 h-12 rounded-full border border-slate-200">
                                    <div class="ml-3">
                                        <p class="font-semibold text-dark text-sm"><?php echo htmlspecialchars($laporan['nama_pelapor']); ?></p>
                                        <p class="text-xs text-muted">Pelapor</p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">ID Laporan</p>
                                    <p class="text-dark text-sm font-mono">#<?php echo str_pad($laporan['id'], 8, '0', STR_PAD_LEFT); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                                <h3 class="font-bold text-dark">Timeline</h3>
                            </div>
                            <div class="p-6 space-y-3">
                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Tanggal Dilaporkan</p>
                                    <p class="text-dark font-medium text-sm"><?php echo date('d F Y', strtotime($laporan['tanggal'])); ?></p>
                                    <p class="text-muted text-xs"><?php echo date('H:i', strtotime($laporan['tanggal'])); ?> WITA</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Dinas Penanganan</p>
                                    <p class="text-dark font-medium text-sm"><?php echo htmlspecialchars($petugas_dinas); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Aksi (hanya untuk laporan yang bisa diubah statusnya) -->
                        <?php if(in_array($status, ['Menunggu', 'Diproses'])): ?>
                        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                                <h3 class="font-bold text-dark">Aksi Penanganan</h3>
                            </div>
                            <div class="p-6 space-y-3">
                                <?php if($status == 'Menunggu'): ?>
                                <button onclick="event.stopPropagation(); openModal(<?php echo $laporan['id']; ?>, 'Menunggu', '<?php echo addslashes($laporan['judul']); ?>')"
                                    class="w-full px-4 py-2.5 bg-primary text-white rounded-lg font-semibold hover:bg-primary-dark transition-colors flex items-center justify-center gap-2 text-sm">
                                    <i data-lucide="play" class="w-4 h-4"></i> Mulai Proses
                                </button>
                                <button onclick="event.stopPropagation(); openModal(<?php echo $laporan['id']; ?>, 'Menunggu', '<?php echo addslashes($laporan['judul']); ?>')"
                                    class="w-full px-4 py-2.5 bg-danger/10 text-danger rounded-lg font-semibold hover:bg-danger/20 transition-colors flex items-center justify-center gap-2 text-sm border border-danger/20">
                                    <i data-lucide="x-circle" class="w-4 h-4"></i> Tolak Laporan
                                </button>
                                <?php elseif($status == 'Diproses'): ?>
                                <button onclick="event.stopPropagation();   openModal(<?php echo $laporan['id']; ?>, 'Diproses', '<?php echo addslashes($laporan['judul']); ?>')"
                                    class="w-full px-4 py-2.5 bg-secondary text-white rounded-lg font-semibold hover:bg-primary transition-colors flex items-center justify-center gap-2 text-sm">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i> Tandai Selesai
                                </button>
                                <button onclick="event.stopPropagation(); openModal(<?php echo $laporan['id']; ?>, 'Diproses', '<?php echo addslashes($laporan['judul']); ?>')"
                                    class="w-full px-4 py-2.5 bg-danger/10 text-danger rounded-lg font-semibold hover:bg-danger/20 transition-colors flex items-center justify-center gap-2 text-sm border border-danger/20">
                                    <i data-lucide="x-circle" class="w-4 h-4"></i> Tolak Laporan
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php else: ?>
                        <!-- Status sudah final -->
                        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                            <div class="p-6 text-center">
                                <?php if($status == 'Selesai'): ?>
                                <div class="w-14 h-14 rounded-full bg-secondary/10 flex items-center justify-center mx-auto mb-3">
                                    <i data-lucide="check-circle" class="w-7 h-7 text-secondary"></i>
                                </div>
                                <p class="font-semibold text-secondary">Laporan Selesai</p>
                                <p class="text-xs text-muted mt-1">Laporan ini telah selesai ditangani</p>
                                <?php elseif($status == 'Ditolak'): ?>
                                <div class="w-14 h-14 rounded-full bg-danger/10 flex items-center justify-center mx-auto mb-3">
                                    <i data-lucide="x-circle" class="w-7 h-7 text-danger"></i>
                                </div>
                                <p class="font-semibold text-danger">Laporan Ditolak</p>
                                <p class="text-xs text-muted mt-1">Laporan ini telah ditolak</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Update Status -->
    <?php if(in_array($status, ['Menunggu', 'Diproses'])): ?>
    <div id="statusModal" class="modal-hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-dark/60">
        <div id="modalBox" class="bg-white rounded-xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                <div class="flex items-center">
                    <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center mr-3">
                        <i data-lucide="refresh-cw" class="w-5 h-5 text-primary"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-dark text-base">Update Status Laporan</h3>
                        <p class="text-xs text-muted" id="modalLaporanId"></p>
                    </div>
                </div>
                <button onclick="closeModal()" class="p-1.5 rounded-lg text-muted hover:text-danger hover:bg-red-50 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="px-6 py-5 space-y-5">
                <div class="bg-slate-50 rounded-lg p-3 border border-slate-200">
                    <p class="text-xs text-muted mb-0.5">Judul Laporan</p>
                    <p class="font-semibold text-dark text-sm" id="modalJudul">—</p>
                    <div class="mt-2 flex items-center gap-2">
                        <span class="text-xs text-muted">Status saat ini:</span>
                        <span id="modalStatusSekarang"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-dark mb-3">Ubah status menjadi:</label>
                    <div class="grid grid-cols-2 gap-2" id="statusOptions"></div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-dark mb-1.5" for="catatanPetugas">
                        Catatan Penanganan <span class="text-muted font-normal">(opsional)</span>
                    </label>
                    <textarea id="catatanPetugas" rows="3"
                        class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm text-dark focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-colors resize-none"
                        placeholder="Tuliskan keterangan penanganan atau alasan penolakan..."></textarea>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-slate-200 flex gap-3 justify-end">
                <button onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-muted bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                    Batal
                </button>
                <button id="btnSimpanStatus" onclick="simpanStatus()"
                    class="px-5 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-lg transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Toast -->
    <div id="toast" class="fixed bottom-6 right-6 z-[200] flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-xl text-white text-sm font-medium opacity-0 translate-y-6 pointer-events-none" style="min-width: 260px">
        <i id="toastIcon" class="w-5 h-5 shrink-0"></i>
        <span id="toastMsg"></span>
    </div>

    <script>
        lucide.createIcons();

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function toggleDropdown() {
            const menu = document.getElementById('dropdownMenu');
            const icon = document.getElementById('dropdownIcon');
            const btn  = document.getElementById('dropdownBtn');
            menu.classList.toggle('hidden');
            if (!menu.classList.contains('hidden')) {
                icon.style.transform = 'rotate(180deg)';
                btn.classList.add('text-primary', 'bg-primary/5');
            } else {
                icon.style.transform = 'rotate(0deg)';
            }
        }

        const STATUS_CFG = {
            Menunggu: { label:'Menunggu', bg:'bg-warning/10',   text:'text-warning',   border:'border-warning/20',   icon:'clock' },
            Diproses: { label:'Diproses', bg:'bg-info/10',      text:'text-info',      border:'border-info/20',      icon:'settings' },
            Selesai:  { label:'Selesai',  bg:'bg-secondary/10', text:'text-secondary', border:'border-secondary/20', icon:'check-circle' },
            Ditolak:  { label:'Ditolak',  bg:'bg-danger/10',    text:'text-danger',    border:'border-danger/20',    icon:'x-circle' },
        };
        const TRANSISI = {
            Menunggu: ['Diproses', 'Ditolak'],
            Diproses: ['Selesai',  'Ditolak'],
        };

        let activeLaporanId = null, activeStatusBaru = null;

        function openModal(id, statusSekarang, judul) {
            activeLaporanId = id; activeStatusBaru = null;
            document.getElementById('modalJudul').textContent     = judul;
            document.getElementById('modalLaporanId').textContent = '#LP-' + String(id).padStart(8, '0');
            const c = STATUS_CFG[statusSekarang];
            document.getElementById('modalStatusSekarang').innerHTML =
                `<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold ${c.bg} ${c.text} border ${c.border}">${c.label.toUpperCase()}</span>`;
            const wrap = document.getElementById('statusOptions');
            wrap.innerHTML = '';
            (TRANSISI[statusSekarang] || []).forEach(s => {
                const cfg = STATUS_CFG[s];
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'status-opt flex items-center justify-center gap-2 px-3 py-3 rounded-lg border-2 border-slate-200 text-sm font-semibold text-muted transition-all hover:opacity-80 focus:outline-none';
                btn.innerHTML = `<i data-lucide="${cfg.icon}" class="w-4 h-4"></i>${cfg.label}`;
                btn.addEventListener('click', () => pilihStatus(s, btn, cfg));
                wrap.appendChild(btn);
            });
            lucide.createIcons();
            document.getElementById('catatanPetugas').value = '';
            document.getElementById('btnSimpanStatus').disabled = true;
            document.getElementById('statusModal').classList.remove('modal-hidden');
        }

        function pilihStatus(status, btnEl, cfg) {
            activeStatusBaru = status;
            document.querySelectorAll('.status-opt').forEach(b => {
                b.className = 'status-opt flex items-center justify-center gap-2 px-3 py-3 rounded-lg border-2 border-slate-200 text-sm font-semibold text-muted transition-all hover:opacity-80 focus:outline-none';
            });
            btnEl.classList.remove('border-slate-200', 'text-muted');
            btnEl.classList.add('border-2', cfg.border.replace('/20', ''), cfg.text, cfg.bg);
            document.getElementById('btnSimpanStatus').disabled = false;
        }

        function closeModal() {
            document.getElementById('statusModal').classList.add('modal-hidden');
            activeLaporanId = null; activeStatusBaru = null;
        }

        document.getElementById('statusModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        function simpanStatus() {
            if (!activeLaporanId || !activeStatusBaru) return;
            const formData = new FormData();
            formData.append('id_laporan',  activeLaporanId);
            formData.append('status_baru', activeStatusBaru);
            formData.append('catatan',     document.getElementById('catatanPetugas').value);

            fetch('update_status.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Status diperbarui menjadi ' + activeStatusBaru + '!');
                    closeModal();
                    // Reload halaman setelah 1.5 detik agar tampilan status terupdate
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showToast('error', 'Gagal: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(() => showToast('error', 'Terjadi kesalahan koneksi!'));
        }

        let toastTimer;
        function showToast(type, msg) {
            clearTimeout(toastTimer);
            const toast = document.getElementById('toast');
            const icon  = document.getElementById('toastIcon');
            document.getElementById('toastMsg').textContent = msg;
            toast.className = toast.className.replace(/bg-\S+/g, '').trim();
            icon.setAttribute('data-lucide', type === 'success' ? 'check-circle' : 'x-circle');
            toast.classList.add(type === 'success' ? 'bg-secondary' : 'bg-danger');
            lucide.createIcons();
            toast.classList.remove('opacity-0', 'translate-y-6', 'pointer-events-none');
            toast.classList.add('opacity-100', 'translate-y-0');
            toastTimer = setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-6', 'pointer-events-none');
                toast.classList.remove('opacity-100', 'translate-y-0');
            }, 3500);
        }
    </script>
</body>
</html>