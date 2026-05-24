<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require __DIR__ . '/../database/conection.php';
// Ambil nama user
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$stmt_user = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt_user->execute([':id' => $user_id]);
$user = $stmt_user->fetch();

// Ambil data laporan
$stmt_laporan = $pdo->prepare("SELECT * FROM laporan WHERE user_id = :id ORDER BY tanggal DESC");
$stmt_laporan->execute([':id' => $user_id]);
$total_laporan = $stmt_laporan->rowCount();

// Cek pencarian filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';
$filter_kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';

// Bangun string query
$where = "WHERE user_id = :id";
$params = [':id' => $user_id];

if ($search != '') {
    $where .= " AND judul LIKE :search";
    $params[':search'] = "%$search%";
}
if ($filter_status != '' && $filter_status != 'all') {
    $where .= " AND status = :status";
    $params[':status'] = $filter_status;
}
if ($filter_kategori != '' && $filter_kategori != 'all') {
    $where .= " AND kategori = :kategori";
    $params[':kategori'] = $filter_kategori;
}

$query_laporan = $pdo->prepare("SELECT * FROM laporan $where ORDER BY tanggal DESC");
$query_laporan->execute($params);
$total_laporan = $query_laporan->rowCount();
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
    <?php include 'sidebar.php'; ?>

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
                            <?php while($laporan = $query_laporan->fetch()): ?>
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
                                       <i data-lucide="trash-2" class="w-5 h-5"></i>
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
                        // Eksekusi ulang query untuk reset
                        $query_laporan->execute($params);
                        if($total_laporan > 0):
                            while($laporan = $query_laporan->fetch()): 
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

    <!-- Script Logic -->
    <script>

        // Initialize Lucide Icons
        lucide.createIcons();

        

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