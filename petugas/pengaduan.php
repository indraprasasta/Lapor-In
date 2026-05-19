<?php
session_start();
require __DIR__ . '/../database/conection.php';
 
if (!isset($_SESSION['petugas_id'])) {
    header("Location: ../login.php");
    exit();
}
 
$dinas_id      = $_SESSION['petugas_dinas_id'];
$petugas_nama  = $_SESSION['petugas_nama'];
$petugas_jabatan = $_SESSION['petugas_jabatan'];
$petugas_dinas = $_SESSION['petugas_dinas'];
 
// Ambil kategori dinas petugas
$query_kategori = mysqli_query($koneksi,
    "SELECT kategori FROM dinas_kategori WHERE dinas_id = '$dinas_id'"
);
$kategori_list = [];
while ($row = mysqli_fetch_assoc($query_kategori)) {
    $kategori_list[] = "'" . mysqli_real_escape_string($koneksi, $row['kategori']) . "'";
}
 
// Status yang valid
$allowed_status = ['Menunggu', 'Diproses', 'Ditolak', 'Selesai'];
$status_aktif   = isset($_GET['status']) && in_array($_GET['status'], $allowed_status)
                  ? $_GET['status']
                  : 'Menunggu';
 
// Config tiap status
$status_cfg = [
    'Menunggu' => ['label' => 'Pengaduan Masuk',   'dot' => 'bg-warning',   'badge_bg' => 'bg-warning/10',   'badge_text' => 'text-warning',   'badge_border' => 'border-warning/20',   'border_l' => 'border-warning'],
    'Diproses' => ['label' => 'Pengaduan Proses',  'dot' => 'bg-info',      'badge_bg' => 'bg-info/10',      'badge_text' => 'text-info',      'badge_border' => 'border-info/20',      'border_l' => 'border-info'],
    'Ditolak'  => ['label' => 'Pengaduan Ditolak', 'dot' => 'bg-danger',    'badge_bg' => 'bg-danger/10',    'badge_text' => 'text-danger',    'badge_border' => 'border-danger/20',    'border_l' => 'border-danger'],
    'Selesai'  => ['label' => 'Pengaduan Selesai', 'dot' => 'bg-secondary', 'badge_bg' => 'bg-secondary/10', 'badge_text' => 'text-secondary', 'badge_border' => 'border-secondary/20', 'border_l' => 'border-secondary'],
];

// Query laporan
if (empty($kategori_list)) {
    $query_laporan = null;
    $total_status  = 0;
} else {
    $kategori_in   = implode(',', $kategori_list);
    $status_escape = mysqli_real_escape_string($koneksi, $status_aktif);
 
    $query_laporan = mysqli_query($koneksi,
        "SELECT * FROM laporan
         WHERE kategori IN ($kategori_in)
         AND status = '$status_escape'
         ORDER BY tanggal DESC"
    );
    $total_status = mysqli_num_rows($query_laporan);
}
 
$cfg = $status_cfg[$status_aktif];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?php echo $cfg['label']; ?> - LaporIn Mataram</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
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
                    },
                },
            },
        };
    </script>
    <style>
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f8fafc; }
        ::-webkit-scrollbar-thumb { background: #a3b18a; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #3a5a40; }
        #statusModal { transition: opacity 0.2s ease; }
        #modalBox { transition: transform 0.25s ease, opacity 0.25s ease; }
        #statusModal.modal-hidden { opacity: 0; pointer-events: none; }
        #statusModal.modal-hidden #modalBox { transform: scale(0.95); opacity: 0; }
        #toast { transition: all 0.35s cubic-bezier(0.4,0,0.2,1); }
    </style>
</head>
<body class="bg-light text-dark font-sans h-screen flex overflow-hidden">
 
<!-- Mobile Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-dark/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>
 
<!-- ===== SIDEBAR ===== -->
<aside id="sidebar" class="fixed inset-y-0 left-0 bg-white w-64 border-r border-slate-200 z-50 transform -translate-x-full lg:translate-x-0 lg:static lg:flex lg:flex-col transition-transform duration-300 ease-in-out">
    <!-- Logo -->
    <div class="h-16 flex items-center px-6 border-b border-slate-200">
        <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white mr-3">
            <i data-lucide="leaf" class="w-5 h-5"></i>
        </div>
        <span class="text-primary font-extrabold text-2xl tracking-tight">Lapor<span class="text-accent">In</span></span>
    </div>
 
    <!-- Nav -->
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        <a href="beranda.php" class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
            <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"></i>
            Dashboard
        </a>
 
        <!-- Kelola Pengaduan — selalu terbuka di halaman ini -->
        <div>
            <button onclick="toggleDropdown()" id="dropdownBtn"
                class="w-full flex items-center justify-between px-3 py-2.5 text-primary bg-primary/5 rounded-lg font-medium transition-colors">
                <div class="flex items-center">
                    <i data-lucide="clipboard-list" class="w-5 h-5 mr-3"></i>
                    Kelola Pengaduan
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" id="dropdownIcon" style="transform:rotate(180deg)"></i>
            </button>
 
            <!-- Submenu — tidak hidden karena halaman ini bagian dari submenu -->
            <div id="dropdownMenu" class="ml-4 mt-1 space-y-1 border-l-2 border-slate-100 pl-3">
                <a href="pengaduan.php?status=Menunggu"
                    class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-colors <?php echo $status_aktif === 'Menunggu' ? 'text-primary bg-primary/10 font-semibold' : 'text-muted hover:text-dark hover:bg-slate-50'; ?>">
                    <span class="w-2 h-2 rounded-full bg-warning inline-block"></span>
                    Pengaduan Masuk
                </a>
                <a href="pengaduan.php?status=Diproses"
                    class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-colors <?php echo $status_aktif === 'Diproses' ? 'text-primary bg-primary/10 font-semibold' : 'text-muted hover:text-dark hover:bg-slate-50'; ?>">
                    <span class="w-2 h-2 rounded-full bg-info inline-block"></span>
                    Pengaduan Proses
                </a>
                <a href="pengaduan.php?status=Ditolak"
                    class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-colors <?php echo $status_aktif === 'Ditolak' ? 'text-primary bg-primary/10 font-semibold' : 'text-muted hover:text-dark hover:bg-slate-50'; ?>">
                    <span class="w-2 h-2 rounded-full bg-danger inline-block"></span>
                    Pengaduan Ditolak
                </a>
                <a href="pengaduan.php?status=Selesai"
                    class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-colors <?php echo $status_aktif === 'Selesai' ? 'text-primary bg-primary/10 font-semibold' : 'text-muted hover:text-dark hover:bg-slate-50'; ?>">
                    <span class="w-2 h-2 rounded-full bg-secondary inline-block"></span>
                    Pengaduan Selesai
                </a>
            </div>
        </div>
 
    </nav>
 
    <!-- User Info -->
    <div class="p-4 border-t border-slate-200">
        <div class="flex items-center group">
            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center overflow-hidden border border-slate-200">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($petugas_nama); ?>&background=A3B18A&color=ffffff"
                    class="w-full h-full object-cover" alt="Avatar">
            </div>
            <div class="ml-3">
                <p class="text-sm font-semibold text-dark"><?php echo htmlspecialchars($petugas_nama); ?></p>
                <p class="text-xs text-muted"><?php echo htmlspecialchars($petugas_jabatan); ?></p>
            </div>
        </div>
        <a href="logout.php" class="mt-4 w-full flex items-center justify-center px-3 py-2 text-sm text-danger bg-red-50 hover:bg-red-100 rounded-lg font-medium transition-colors">
            <i data-lucide="log-out" class="w-4 h-4 mr-2"></i> Keluar
        </a>
    </div>
</aside>
 
<!-- ===== MAIN WRAPPER ===== -->
<div class="flex-1 flex flex-col h-screen overflow-hidden">
 
    <!-- Navbar -->
    <header class="h-16 bg-accent border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-30 shrink-0">
        <button class="lg:hidden text-white hover:text-dark p-2 -ml-2 rounded-lg" onclick="toggleSidebar()">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
        <div class="hidden sm:flex items-center gap-2 text-sm text-dark">
            <a href="beranda.php" class="hover:text-primary transition-colors font-medium">Dashboard</a>
            <i data-lucide="chevron-right" class="w-4 h-4 text-muted"></i>
            <span class="font-bold"><?php echo $cfg['label']; ?></span>
        </div>
        <div class="ml-auto">
            <button class="relative p-2 text-dark hover:text-primary rounded-full hover:bg-white/40 transition-colors">
                <i data-lucide="bell" class="w-5 h-5"></i>
            </button>
        </div>
    </header>
 
    <!-- ===== MAIN CONTENT ===== -->
    <main class="flex-1 overflow-y-auto bg-light p-4 sm:p-6 lg:p-8">
        <div class="max-w-6xl mx-auto space-y-6">
 
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h2 class="text-2xl font-bold text-dark tracking-tight"><?php echo $cfg['label']; ?></h2>
                    <p class="text-muted text-sm mt-1">
                        Menampilkan <span class="font-semibold text-dark"><?php echo $total_status; ?></span> laporan
                        dengan status <span class="font-semibold <?php echo $cfg['badge_text']; ?>"><?php echo $status_aktif; ?></span>
                    </p>
                </div>
 
                <!-- Tab Filter (shortcut cepat) -->
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($status_cfg as $s => $c): ?>
                    <a href="pengaduan.php?status=<?php echo $s; ?>"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-colors
                        <?php echo $status_aktif === $s
                            ? $c['badge_bg'] . ' ' . $c['badge_text'] . ' ' . $c['badge_border'] . ' border'
                            : 'bg-white text-muted border-slate-200 hover:border-slate-300'; ?>">
                        <?php echo $c['label']; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
 
            <!-- Tabel -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
 
                <!-- Desktop -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500">
                                <th class="py-3 px-4 font-semibold">Laporan</th>
                                <th class="py-3 px-4 font-semibold">Lokasi</th>
                                <th class="py-3 px-4 font-semibold">Tanggal</th>
                                <th class="py-3 px-4 text-center font-semibold">Status</th>
                                <?php if (in_array($status_aktif, ['Menunggu', 'Diproses'])): ?>
                                <th class="py-3 px-4 text-center font-semibold">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody id="taskTableBody" class="text-sm divide-y divide-slate-100">
                            <?php if ($query_laporan && mysqli_num_rows($query_laporan) > 0): ?>
                                <?php while ($lap = mysqli_fetch_assoc($query_laporan)): ?>
                                <tr class="hover:bg-slate-50 transition-colors" id="row-<?php echo $lap['id']; ?>">
                                    <td class="py-4 px-4 align-top">
                                        <div class="flex items-start gap-3">
                                            <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center shrink-0 border border-slate-200">
                                                <i data-lucide="file-text" class="w-5 h-5 text-primary"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-dark mb-0.5"><?php echo htmlspecialchars($lap['judul']); ?></p>
                                                <p class="text-xs text-muted"><?php echo htmlspecialchars($lap['kategori']); ?></p>
                                                <p class="text-xs text-muted">#LP-<?php echo str_pad($lap['id'], 8, '0', STR_PAD_LEFT); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 align-top">
                                        <p class="text-dark font-medium text-sm"><?php echo htmlspecialchars($lap['kelurahan']); ?></p>
                                        <p class="text-xs text-muted">Kec. <?php echo htmlspecialchars($lap['kecamatan']); ?></p>
                                    </td>
                                    <td class="py-4 px-4 align-top text-sm">
                                        <p class="text-dark"><?php echo date('d M Y', strtotime($lap['tanggal'])); ?></p>
                                        <p class="text-xs text-muted"><?php echo date('H:i', strtotime($lap['tanggal'])); ?> WITA</p>
                                    </td>
                                    <td class="py-4 px-4 align-top text-center" id="status-badge-<?php echo $lap['id']; ?>">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold <?php echo $cfg['badge_bg'] . ' ' . $cfg['badge_text'] . ' border ' . $cfg['badge_border']; ?>">
                                            <?php echo strtoupper($status_aktif); ?>
                                        </span>
                                    </td>
                                    <?php if (in_array($status_aktif, ['Menunggu', 'Diproses'])): ?>
                                    <td class="py-4 px-4 align-top text-center" id="aksi-<?php echo $lap['id']; ?>">
                                        <?php if ($status_aktif === 'Menunggu'): ?>
                                        <button onclick="openModal(<?php echo $lap['id']; ?>, 'Menunggu', '<?php echo addslashes($lap['judul']); ?>')"
                                            class="bg-white border border-primary text-primary hover:bg-primary/5 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors w-full shadow-sm">
                                            Mulai Proses
                                        </button>
                                        <?php else: ?>
                                        <button onclick="openModal(<?php echo $lap['id']; ?>, 'Diproses', '<?php echo addslashes($lap['judul']); ?>')"
                                            class="bg-primary hover:bg-primary-dark text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors w-full shadow-sm">
                                            Update Status
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="py-14 text-center text-muted">
                                        <div class="flex flex-col items-center">
                                            <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                                                <i data-lucide="inbox" class="w-7 h-7 text-slate-400"></i>
                                            </div>
                                            <p class="font-semibold text-dark">Tidak ada laporan</p>
                                            <p class="text-sm mt-1">Belum ada laporan dengan status "<?php echo $status_aktif; ?>"</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card -->
                <div class="md:hidden flex flex-col divide-y divide-slate-100">
                    <?php
                    $kategori_list  = [];
                    $kategori_in    = '';      
                    $status_escape  = '';
                    // query ulang untuk mobile
                    if (!empty($kategori_list)) {
                        $q_mobile = mysqli_query($koneksi,
                            "SELECT * FROM laporan
                            WHERE kategori IN ($kategori_in)
                            AND status = '$status_escape'
                            ORDER BY tanggal DESC"
                        );
                        if (mysqli_num_rows($q_mobile) > 0):
                            while ($lap = mysqli_fetch_assoc($q_mobile)):
                    ?>
                    <div class="p-4 bg-white border-l-4 <?php echo $cfg['border_l']; ?>">
                        <div class="flex justify-between items-start mb-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold <?php echo $cfg['badge_bg'] . ' ' . $cfg['badge_text'] . ' border ' . $cfg['badge_border']; ?> uppercase tracking-wide">
                                <?php echo $status_aktif; ?>
                            </span>
                            <span class="text-xs text-muted"><?php echo date('d M Y', strtotime($lap['tanggal'])); ?></span>
                        </div>
                        <h4 class="font-bold text-dark text-sm mb-1"><?php echo htmlspecialchars($lap['judul']); ?></h4>
                        <p class="text-xs text-muted mb-3"><?php echo htmlspecialchars($lap['kategori']); ?> | #LP-<?php echo str_pad($lap['id'], 8, '0', STR_PAD_LEFT); ?></p>
                        <div class="bg-slate-50 rounded-lg p-3 mb-3 border border-slate-100 space-y-2">
                            <div class="flex items-start text-sm">
                                <i data-lucide="map-pin" class="w-4 h-4 text-slate-400 mr-2 mt-0.5 shrink-0"></i>
                                <span class="text-dark font-medium"><?php echo htmlspecialchars($lap['kelurahan']); ?>, Kec. <?php echo htmlspecialchars($lap['kecamatan']); ?></span>
                            </div>
                            <div class="flex items-start text-sm">
                                <i data-lucide="clock" class="w-4 h-4 text-slate-400 mr-2 mt-0.5 shrink-0"></i>
                                <span class="text-muted"><?php echo date('d M Y, H:i', strtotime($lap['tanggal'])); ?> WITA</span>
                            </div>
                        </div>
                        <?php if ($status_aktif === 'Menunggu'): ?>
                        <button onclick="openModal(<?php echo $lap['id']; ?>, 'Menunggu', '<?php echo addslashes($lap['judul']); ?>')"
                            class="w-full bg-white border border-primary text-primary hover:bg-primary/5 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                            Mulai Tangani
                        </button>
                        <?php elseif ($status_aktif === 'Diproses'): ?>
                        <button onclick="openModal(<?php echo $lap['id']; ?>, 'Diproses', '<?php echo addslashes($lap['judul']); ?>')"
                            class="w-full bg-primary hover:bg-primary-dark text-white py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                            Update Status
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php
                            endwhile;
                        else:
                    ?>
                    <div class="p-10 text-center text-muted">
                        <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-3 text-slate-300"></i>
                        <p class="font-semibold text-dark">Tidak ada laporan</p>
                        <p class="text-sm mt-1">Belum ada laporan dengan status "<?php echo $status_aktif; ?>"</p>
                    </div>
                    <?php
                        endif;
                    }
                    ?>
                </div>
            </div>
 
        </div>
    </main>
</div>
 
<!-- ===== MODAL UPDATE STATUS ===== -->
<?php if (in_array($status_aktif, ['Menunggu', 'Diproses'])): ?>
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
 
<!-- ===== TOAST ===== -->
<div id="toast" class="fixed bottom-6 right-6 z-[200] flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-xl text-white text-sm font-medium opacity-0 translate-y-6 pointer-events-none" style="min-width:260px">
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
        btn.classList.remove('text-muted');
    } else {
        icon.style.transform = 'rotate(0deg)';
        btn.classList.remove('text-primary', 'bg-primary/5');
        btn.classList.add('text-muted');
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
    document.getElementById('modalJudul').textContent    = judul;
    document.getElementById('modalLaporanId').textContent = '#LP-' + String(id).padStart(8,'0');
    const c = STATUS_CFG[statusSekarang];
    document.getElementById('modalStatusSekarang').innerHTML =
        `<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold ${c.bg} ${c.text} border ${c.border}">${c.label.toUpperCase()}</span>`;
    const wrap = document.getElementById('statusOptions');
    wrap.innerHTML = '';
    (TRANSISI[statusSekarang]||[]).forEach(s => {
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
    btnEl.classList.remove('border-slate-200','text-muted');
    btnEl.classList.add('border-2', cfg.border.replace('/20',''), cfg.text, cfg.bg);
    document.getElementById('btnSimpanStatus').disabled = false;
}
 
function closeModal() {
    document.getElementById('statusModal').classList.add('modal-hidden');
    activeLaporanId = null; activeStatusBaru = null;
}
document.getElementById('statusModal')?.addEventListener('click', function(e){ if(e.target===this) closeModal(); });
 
function simpanStatus() {
    if (!activeLaporanId || !activeStatusBaru) return;
    const formData = new FormData();
    formData.append('id_laporan',  activeLaporanId);
    formData.append('status_baru', activeStatusBaru);
    formData.append('catatan',     document.getElementById('catatanPetugas').value);
 
    fetch('update_status.php', { method:'POST', body:formData })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Hapus baris dari tabel karena status sudah berubah
            const row = document.getElementById('row-' + activeLaporanId);
            if (row) {
                row.style.transition = 'opacity 0.4s ease';
                row.style.opacity = '0';
                setTimeout(() => {
                    row.remove();
                    const tbody = document.getElementById('taskTableBody');
                    if (tbody && tbody.querySelectorAll('tr').length === 0) {
                        tbody.innerHTML = `<tr><td colspan="5" class="py-14 text-center text-muted">
                            <div class="flex flex-col items-center">
                                <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                                    <i data-lucide="inbox" class="w-7 h-7 text-slate-400"></i>
                                </div>
                                <p class="font-semibold text-dark">Tidak ada laporan</p>
                            </div>
                        </td></tr>`;
                        lucide.createIcons();
                    }
                }, 400);
            }
            showToast('success', 'Status diperbarui menjadi ' + activeStatusBaru + '!');
            closeModal();
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
    toast.className = toast.className.replace(/bg-\S+/g,'').trim();
    icon.setAttribute('data-lucide', type==='success' ? 'check-circle' : 'x-circle');
    toast.classList.add(type==='success' ? 'bg-secondary' : 'bg-danger');
    lucide.createIcons();
    toast.classList.remove('opacity-0','translate-y-6','pointer-events-none');
    toast.classList.add('opacity-100','translate-y-0');
    toastTimer = setTimeout(() => {
        toast.classList.add('opacity-0','translate-y-6','pointer-events-none');
        toast.classList.remove('opacity-100','translate-y-0');
    }, 3500);
}
</script>
</body>
</html>