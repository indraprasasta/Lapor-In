<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require __DIR__ . '/../database/conection.php';
//ngambil nama dari database 
$user_id = $_SESSION['user_id'];

$query_user = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$user       = mysqli_fetch_assoc($query_user);
// Ambil laporan milik user
$query_laporan = mysqli_query($koneksi, "SELECT * FROM laporan WHERE user_id = '$user_id' ORDER BY tanggal DESC");
$total_laporan = mysqli_num_rows($query_laporan);

// Ambil kata pencarian jika ada
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($koneksi, $_GET['status']) : '';
$filter_kategori = isset($_GET['kategori']) ? mysqli_real_escape_string($koneksi, $_GET['kategori']) : '';

// Bangun query dengan filter
$where = "WHERE user_id = '$user_id'";

if ($search != '') {
    $where .= " AND judul LIKE '%$search%'";
}
if ($filter_status != '' && $filter_status != 'all') {
    $where .= " AND status = '$filter_status'";
}
if ($filter_kategori != '' && $filter_kategori != 'all') {
    $where .= " AND kategori = '$filter_kategori'";
}

$query_laporan = mysqli_query($koneksi, "SELECT * FROM laporan $where ORDER BY tanggal DESC");
$total_laporan = mysqli_num_rows($query_laporan);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Saya - LaporIn Mataram</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind Config to match LaporIn Design System -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            DEFAULT: '#3A5A40',
                            dark: '#2B4330',
                        },
                        accent: {
                            DEFAULT: '#A3B18A',
                            dark: '#8b9a70',
                        },
                        secondary: '#588157',
                        warning: '#D97706',
                        danger: '#DC2626',
                        info: '#0284C7',
                        dark: '#1E293B',
                        light: '#F8FAFC',
                        muted: '#94A3B8',
                    }
                }
            }
        }
    </script>
    <style>
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #F8FAFC;
        }

        ::-webkit-scrollbar-thumb {
            background: #A3B18A;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #3A5A40;
        }
    </style>
</head>

<body class="bg-light text-dark font-sans h-screen flex overflow-hidden">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-dark/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 bg-white w-64 border-r border-slate-200 z-50 transform -translate-x-full lg:translate-x-0 lg:static lg:flex lg:flex-col transition-transform duration-300 ease-in-out">
        <!-- Logo -->
        <div class="h-16 flex items-center px-6 border-b border-slate-200">
            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white mr-3">
                <i data-lucide="leaf" class="w-5 h-5"></i>
            </div>
            <span class="text-primary font-extrabold text-2xl tracking-tight">Lapor<span class="text-accent">In</span></span>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <a href="beranda.php"
                class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"></i>
                Beranda
            </a>
            <a href="buatLaporan.php"
                class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="plus-circle" class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"></i>
                Buat Laporan
            </a>
            <a href="daftarLaporan.php" class="flex items-center px-3 py-2.5 bg-primary/10 text-primary rounded-lg font-medium group">
                <i data-lucide="file-text" class="w-5 h-5 mr-3"></i>
                Laporan Saya
            </a>
            <a href="profile.php"
                class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="user" class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"></i>
                Profil
            </a>
        </nav>

        <!-- User Info -->
        <div class="p-4 border-t border-slate-200">
            <a href="profile.php" class="flex items-center group">
                <div
                    class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-primary font-bold overflow-hidden border border-slate-200">
                    <img src="https://ui-avatars.com/api/?name=Pak+Andi&background=A3B18A&color=ffffff" alt="Avatar"
                        class="w-full h-full object-cover">
                </div>
                <div class="ml-3">
                    <p class="text-sm font-semibold text-dark"><?php echo $user['nama']; ?></p>
                    <p class="text-xs text-muted">Masyarakat</p>
                </div>
            </a>
            <a href="logout.php"
                class="mt-4 w-full flex items-center justify-center px-3 py-2 text-sm text-danger bg-red-50 hover:bg-red-100 rounded-lg font-medium transition-colors">
                <i data-lucide="log-out" class="w-4 h-4 mr-2"></i>
                Keluar
            </a>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">

        <!-- Top Navbar -->
        <header
            class="h-16 bg-accent border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-30 shrink-0">
            <button
                class="lg:hidden text-muted hover:text-dark p-2 -ml-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                onclick="toggleSidebar()">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <div class="hidden sm:block">
                <nav class="flex text-white text-muted font-medium">
                    <a href="beranda.html" class="hover:text-dark">Dashboard</a>
                    <span class="mx-2">/</span>
                    <span class="text-dark">Daftar Laporan</span>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-primary p-4 sm:p-6 lg:p-8">
            <div class="max-w-6xl mx-auto flex flex-col h-full space-y-6">

                <!-- Page Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-white tracking-tight">Daftar Laporan Saya</h2>
                        <p class="text-accent mt-1 text-sm">Riwayat dan status seluruh laporan infrastruktur yang pernah
                            Anda buat.</p>
                    </div>
                    <a href="buatLaporan.php"
                        class="bg-accent hover:bg-primary-dark text-white px-4 py-2.5 rounded-lg font-semibold flex items-center justify-center transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Buat Laporan
                    </a>
                </div>

                <!-- Filters & Search -->
                <form method="GET" action="" class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex flex-col lg:flex-row gap-4">
                    <!-- Search Input -->
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-5 h-5 text-slate-400"></i>
                        </div>
                        <input type="text" name="search" 
                            value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>"
                            placeholder="Cari judul laporan..."
                            class="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm">
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- Filter Kategori -->
                        <div class="relative w-full sm:w-48">
                            <select name="kategori"
                                class="w-full pl-4 pr-10 py-2 appearance-none rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm bg-white">
                                <option value="all">Semua Kategori</option>
                                <?php
                                $kategoris = ['Jalan Rusak','Pohon Tumbang','Lampu Jalan Mati','Saluran Air','Jembatan','Trotoar','Fasilitas Umum','Lainnya'];
                                foreach($kategoris as $kat):
                                ?>
                                <option value="<?php echo $kat; ?>" <?php echo (isset($_GET['kategori']) && $_GET['kategori'] == $kat) ? 'selected' : ''; ?>>
                                    <?php echo $kat; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                            </div>
                        </div>

                        <!-- Filter Status -->
                        <div class="relative w-full sm:w-48">
                            <select name="status"
                                class="w-full pl-4 pr-10 py-2 appearance-none rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm bg-white">
                                <option value="all">Semua Status</option>
                                <option value="Menunggu" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                                <option value="Diproses" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Diproses') ? 'selected' : ''; ?>>Diproses</option>
                                <option value="Selesai" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                                <option value="Ditolak" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Ditolak') ? 'selected' : ''; ?>>Ditolak</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                            </div>
                        </div>

                        <!-- Tombol Cari -->
                        <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-primary-dark transition-colors text-sm flex items-center gap-2">
                            <i data-lucide="search" class="w-4 h-4"></i>
                            Cari
                        </button>

                        <!-- Tombol Reset -->
                        <?php if($search != '' || $filter_status != '' || $filter_kategori != ''): ?>
                        <a href="daftarLaporan.php"
                            class="px-4 py-2 bg-slate-100 text-dark rounded-lg font-semibold hover:bg-slate-200 transition-colors text-sm flex items-center gap-2">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            Reset
                        </a>
                        <?php endif; ?>
                    </div>
                </form>

                <!-- Reports List (Table for Desktop, Cards for Mobile) -->
                <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">

                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-sm">
                                    <th class="py-3 px-4 font-semibold text-dark">Laporan</th>
                                    <th class="py-3 px-4 font-semibold text-dark">Kategori</th>
                                    <th class="py-3 px-4 font-semibold text-dark">Lokasi</th>
                                    <th class="py-3 px-4 font-semibold text-dark">Tanggal</th>
                                    <th class="py-3 px-4 font-semibold text-dark">Status</th>
                                    <th class="py-3 px-4 text-center font-semibold text-dark">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-slate-100">
                        <?php if($total_laporan > 0): ?>
                            <?php while($laporan = mysqli_fetch_assoc($query_laporan)): ?>
                            <tr class="hover:bg-slate-50 transition-colors group cursor-pointer" 
                                onclick="window.location='detailLaporan.php?id=<?php echo $laporan['id']; ?>'">
                                <td class="py-4 px-4 align-top">
                                    <p class="font-bold text-dark mb-1 line-clamp-1 group-hover:text-primary transition-colors">
                                        <?php echo $laporan['judul']; ?>
                                    </p>
                                    <p class="text-xs text-muted line-clamp-1"><?php echo $laporan['deskripsi']; ?></p>
                                </td>
                                <td class="py-4 px-4 align-top text-dark"><?php echo $laporan['kategori']; ?></td>
                                <td class="py-4 px-4 align-top text-muted"><?php echo $laporan['kelurahan'] . ', ' . $laporan['kecamatan']; ?></td>
                                <td class="py-4 px-4 align-top text-muted"><?php echo date('d M Y', strtotime($laporan['tanggal'])); ?></td>
                                <td class="py-4 px-4 align-top">
                                    <?php
                                    $status = $laporan['status'];
                                    if($status == 'Menunggu') {
                                        echo '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-warning/10 text-warning border border-warning/20">MENUNGGU</span>';
                                    } elseif($status == 'Diproses') {
                                        echo '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-info/10 text-info border border-info/20">DIPROSES</span>';
                                    } elseif($status == 'Selesai') {
                                        echo '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-secondary/10 text-secondary border border-secondary/20">SELESAI</span>';
                                    } elseif($status == 'Ditolak') {
                                        echo '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-danger/10 text-danger border border-danger/20">DITOLAK</span>';
                                    }
                                    ?>
                                </td>
                                <td class="py-4 px-4 align-top text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" onclick="openDeleteModal(<?php echo $laporan['id']; ?>, '<?php echo addslashes($laporan['judul']); ?>')"
                                            class="text-danger hover:text-danger p-1.5 rounded-lg hover:bg-danger/10 transition-colors inline-block">
                                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-12 text-center text-muted">
                                    <i data-lucide="file-x" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                                    <p class="font-medium">Belum ada laporan</p>
                                    <p class="text-xs mt-1">Klik tombol "Buat Laporan" untuk membuat laporan pertama Anda</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                    </div>
                                        <!-- Mobile Card View -->
                    <div class="md:hidden flex flex-col divide-y divide-slate-100">
                        <?php 
                        // Reset pointer query
                        mysqli_data_seek($query_laporan, 0);
                        if($total_laporan > 0):
                            while($laporan = mysqli_fetch_assoc($query_laporan)): 
                        ?>
                        <div class="p-4 hover:bg-slate-50 transition-colors relative cursor-pointer group">
                            <div class="flex justify-between items-start mb-3">
                                <?php
                                $status = $laporan['status'];
                                if($status == 'Menunggu') {
                                    echo '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-warning/10 text-warning uppercase">Menunggu</span>';
                                } elseif($status == 'Diproses') {
                                    echo '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-info/10 text-info uppercase">Diproses</span>';
                                } elseif($status == 'Selesai') {
                                    echo '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-secondary/10 text-secondary uppercase">Selesai</span>';
                                } elseif($status == 'Ditolak') {
                                    echo '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-danger/10 text-danger uppercase">Ditolak</span>';
                                }
                                ?>
                                <span class="text-xs text-muted"><?php echo date('d M Y', strtotime($laporan['tanggal'])); ?></span>
                            </div>
                            <h4 class="font-bold text-dark text-sm mb-1 group-hover:text-primary transition-colors pr-6">
                                <?php echo $laporan['judul']; ?>
                            </h4>
                            <div class="flex flex-wrap gap-y-2 text-xs text-muted mt-2">
                                <div class="flex items-center w-full">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 mr-1.5"></i>
                                    <?php echo $laporan['kelurahan'] . ', ' . $laporan['kecamatan']; ?>
                                </div>
                            </div>
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 flex gap-1">
                                <a href="detailLaporan.php?id=<?php echo $laporan['id']; ?>"
                                    class="text-primary hover:text-primary-dark p-1.5 rounded-lg hover:bg-primary/10 transition-colors inline-block">
                                    <i data-lucide="eye" class="w-5 h-5"></i>
                                </a>
                                <button type="button" onclick="event.stopPropagation(); openDeleteModal(<?php echo $laporan['id']; ?>, '<?php echo addslashes($laporan['judul']); ?>')"
                                    class="text-danger hover:text-danger p-1.5 rounded-lg hover:bg-danger/10 transition-colors inline-block">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                        <?php 
                            endwhile;
                        else: ?>
                        <div class="p-8 text-center text-muted">
                            <p class="font-medium">Belum ada laporan</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <div
                        class="border-t border-slate-200 bg-slate-50 px-4 py-3 flex items-center justify-between sm:px-6">
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-muted">
                                    Menampilkan <span class="font-medium text-dark"><?php echo $total_laporan; ?></span> laporan
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                    aria-label="Pagination">
                                    <a href="#"
                                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-slate-300 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50">
                                        <span class="sr-only">Previous</span>
                                        <i data-lucide="chevron-left" class="h-4 w-4"></i>
                                    </a>
                                    <a href="#" aria-current="page"
                                        class="z-10 bg-primary/10 border-primary text-primary relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                        1
                                    </a>
                                    <a href="#"
                                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-slate-300 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50">
                                        <span class="sr-only">Next</span>
                                        <i data-lucide="chevron-right" class="h-4 w-4"></i>
                                    </a>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-danger/10 flex items-center justify-center">
                        <i data-lucide="alert-circle" class="w-6 h-6 text-danger"></i>
                    </div>
                    <h3 class="text-lg font-bold text-dark">Hapus Laporan</h3>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <p class="text-muted mb-2">Anda yakin ingin menghapus laporan berikut?</p>
                <p id="deleteReportTitle" class="font-semibold text-dark bg-slate-50 p-3 rounded-lg border border-slate-200"></p>
                <p class="text-xs text-muted mt-4">Tindakan ini tidak dapat dibatalkan. Laporan dan semua data terkait akan dihapus secara permanen.</p>
            </div>

            <!-- Modal Footer -->
            <div class="p-6 border-t border-slate-200 flex gap-3">
                <button type="button" onclick="closeDeleteModal()"
                    class="flex-1 px-4 py-2 bg-slate-100 text-dark rounded-lg font-semibold hover:bg-slate-200 transition-colors">
                    Batal
                </button>
                <button type="button" id="confirmDeleteBtn" onclick="confirmDelete()"
                    class="flex-1 px-4 py-2 bg-danger text-white rounded-lg font-semibold hover:bg-red-700 transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- Script Logic -->
    <script>
        let deleteReportId = null;

        // Initialize Lucide Icons
        lucide.createIcons();

        // Sidebar Toggle Logic for Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        // Delete Modal Functions
        function openDeleteModal(reportId, reportTitle) {
            deleteReportId = reportId;
            document.getElementById('deleteReportTitle').textContent = reportTitle;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            deleteReportId = null;
            document.getElementById('deleteModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function confirmDelete() {
            if (!deleteReportId) return;

            const confirmBtn = document.getElementById('confirmDeleteBtn');
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Menghapus...';

            // Send delete request
            fetch('proses_delete_laporan.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + deleteReportId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    alert('Laporan berhasil dihapus');
                    // Reload page
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = '<i data-lucide="trash-2" class="w-4 h-4"></i> Hapus';
                    lucide.createIcons();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus laporan');
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i data-lucide="trash-2" class="w-4 h-4"></i> Hapus';
                lucide.createIcons();
            });
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeDeleteModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
</body>

</html>