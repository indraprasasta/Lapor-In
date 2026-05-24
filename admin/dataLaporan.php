<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

require __DIR__ . '/../database/conection.php';

$admin_nama = $_SESSION['admin_nama'];

// Filter dan Cari
$search          = isset($_GET['search'])   ? trim($_GET['search'])   : '';
$filter_status   = isset($_GET['status'])   ? trim($_GET['status'])   : '';
$filter_kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';

$where = "WHERE 1=1";
$params = [];
if ($search != '') {
    $where .= " AND (laporan.judul LIKE :search OR users.nama LIKE :search)";
    $params[':search'] = "%$search%";
}
if ($filter_status != '') {
    $where .= " AND laporan.status = :status";
    $params[':status'] = $filter_status;
}
if ($filter_kategori != '') {
    $where .= " AND laporan.kategori = :kategori";
    $params[':kategori'] = $filter_kategori;
}

$stmt_laporan = $pdo->prepare("
    SELECT laporan.*, users.nama as nama_pelapor
    FROM laporan
    JOIN users ON laporan.user_id = users.id
    $where
    ORDER BY laporan.tanggal DESC
");
$stmt_laporan->execute($params);
$query_laporan = $stmt_laporan->fetchAll();
$total = count($query_laporan);

// Proses ubah status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $id_laporan  = (int) $_POST['id_laporan'];
    $status_baru = trim($_POST['status_baru']);
    $allowed     = ['Menunggu', 'Diproses', 'Selesai', 'Ditolak'];

    if (in_array($status_baru, $allowed)) {
        $stmt_update = $pdo->prepare("UPDATE laporan SET status = :status WHERE id = :id");
        $stmt_update->execute([':status' => $status_baru, ':id' => $id_laporan]);
        header("Location: dataLaporan.php?updated=1");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Laporan - Admin LaporIn</title>
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

        /* Modal */
        #statusModal { transition: opacity 0.2s ease; }
        #statusModal.hidden { display: none; }
    </style>
</head>

<body class="bg-light text-dark font-sans h-screen flex overflow-hidden">

    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-dark/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-16 bg-accent border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-30 shrink-0">
            <button class="lg:hidden text-dark p-2 -ml-2 rounded-lg" onclick="toggleSidebar()">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <h1 class="text-lg font-bold text-dark hidden sm:block">Manajemen Data Laporan</h1>
        </header>

        <main class="flex-1 overflow-y-auto bg-light p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto space-y-6">

                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-dark">Data Laporan</h2>
                        <p class="text-muted text-sm mt-1">Total <span class="font-semibold text-dark"><?php echo $total; ?></span> laporan ditemukan</p>
                    </div>
                </div>

                <!-- Notifikasi -->
                <?php if(isset($_GET['updated'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-sm">
                    ✅ Status laporan berhasil diperbarui!
                </div>
                <?php endif; ?>

                <!-- Filter & Search -->
                <form method="GET" class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex flex-col lg:flex-row gap-3">
                    <!-- Search -->
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                        </div>
                        <input type="text" name="search" value="<?php echo $search; ?>"
                            placeholder="Cari judul laporan atau nama pelapor..."
                            class="w-full pl-9 pr-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 outline-none text-sm">
                    </div>

                    <!-- Filter Status -->
                    <select name="status" class="px-4 py-2 rounded-lg border border-slate-300 outline-none text-sm bg-white">
                        <option value="">Semua Status</option>
                        <option value="Menunggu"  <?php echo $filter_status == 'Menunggu'  ? 'selected' : ''; ?>>Menunggu</option>
                        <option value="Diproses"  <?php echo $filter_status == 'Diproses'  ? 'selected' : ''; ?>>Diproses</option>
                        <option value="Selesai"   <?php echo $filter_status == 'Selesai'   ? 'selected' : ''; ?>>Selesai</option>
                        <option value="Ditolak"   <?php echo $filter_status == 'Ditolak'   ? 'selected' : ''; ?>>Ditolak</option>
                    </select>

                    <!-- Filter Kategori -->
                    <select name="kategori" class="px-4 py-2 rounded-lg border border-slate-300 outline-none text-sm bg-white">
                        <option value="">Semua Kategori</option>
                        <?php
                        $kategoris = ['Jalan Rusak','Pohon Tumbang','Lampu Jalan Mati','Saluran Air','Jembatan','Trotoar','Fasilitas Umum','Lainnya'];
                        foreach($kategoris as $k):
                        ?>
                        <option value="<?php echo $k; ?>" <?php echo $filter_kategori == $k ? 'selected' : ''; ?>><?php echo $k; ?></option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-primary-dark transition-colors">
                        Filter
                    </button>
                    <?php if($search || $filter_status || $filter_kategori): ?>
                    <a href="dataLaporan.php" class="bg-slate-100 text-dark px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-200 transition-colors text-center">
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
                                    <th class="py-3 px-4 font-semibold">ID</th>
                                    <th class="py-3 px-4 font-semibold">Laporan</th>
                                    <th class="py-3 px-4 font-semibold">Pelapor</th>
                                    <th class="py-3 px-4 font-semibold">Lokasi</th>
                                    <th class="py-3 px-4 font-semibold">Tanggal</th>
                                    <th class="py-3 px-4 font-semibold">Status</th>
                                    <th class="py-3 px-4 text-center font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-slate-100">
                                <?php if($total > 0): ?>
                                    <?php foreach($query_laporan as $laporan): ?>
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="py-4 px-4 font-mono text-xs text-slate-500">
                                            #<?php echo str_pad($laporan['id'], 4, '0', STR_PAD_LEFT); ?>
                                        </td>
                                        <td class="py-4 px-4">
                                            <p class="font-bold text-dark line-clamp-1"><?php echo $laporan['judul']; ?></p>
                                            <p class="text-xs text-muted mt-0.5"><?php echo $laporan['kategori']; ?></p>
                                        </td>
                                        <td class="py-4 px-4 text-muted"><?php echo $laporan['nama_pelapor']; ?></td>
                                        <td class="py-4 px-4 text-muted text-xs">
                                            <?php echo $laporan['kelurahan'] . ', ' . $laporan['kecamatan']; ?>
                                        </td>
                                        <td class="py-4 px-4 text-muted text-xs">
                                            <?php echo date('d M Y', strtotime($laporan['tanggal'])); ?>
                                        </td>
                                        <td class="py-4 px-4">
                                            <?php
                                            $s = $laporan['status'];
                                            $badge = [
                                                'Menunggu' => 'bg-warning/10 text-warning border-warning/20',
                                                'Diproses' => 'bg-info/10 text-info border-info/20',
                                                'Selesai'  => 'bg-secondary/10 text-secondary border-secondary/20',
                                                'Ditolak'  => 'bg-danger/10 text-danger border-danger/20',
                                            ];
                                            $cls = $badge[$s] ?? 'bg-slate-100 text-muted border-slate-200';
                                            echo "<span class='inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold border $cls'>" . strtoupper($s) . "</span>";
                                            ?>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <!-- Tombol ubah status -->
                                                <button onclick="bukaModalStatus(<?php echo $laporan['id']; ?>, '<?php echo $laporan['status']; ?>', '<?php echo addslashes($laporan['judul']); ?>')"
                                                    class="p-1.5 text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Ubah Status">
                                                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                                </button>
                                                <!-- Tombol detail -->
                                                <a href="detailLaporan.php?id=<?php echo $laporan['id']; ?>"
                                                    class="p-1.5 text-slate-500 hover:bg-slate-100 rounded-lg transition-colors" title="Lihat Detail">
                                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="py-12 text-center text-muted">
                                            <i data-lucide="file-x" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                                            <p class="font-medium">Tidak ada laporan ditemukan</p>
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

    <!-- Modal Update Status -->
    <div id="statusModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-dark/60">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                <h3 class="font-bold text-dark">Ubah Status Laporan</h3>
                <button onclick="tutupModal()" class="p-1.5 rounded-lg text-muted hover:text-danger hover:bg-red-50 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" class="px-6 py-5 space-y-4">
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