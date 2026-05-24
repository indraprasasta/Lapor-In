<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require __DIR__ . '/../database/conection.php';

$user_id  = $_SESSION['user_id'];
$nama     = $_SESSION['nama'];
$username = $_SESSION['username'];

// Ambil statistik laporan
$total     = $pdo->query("SELECT COUNT(*) as total FROM laporan WHERE user_id = '$user_id'")->fetch()['total'];
$menunggu  = $pdo->query("SELECT COUNT(*) as total FROM laporan WHERE user_id = '$user_id' AND status = 'Menunggu'")->fetch()['total'];
$diproses  = $pdo->query("SELECT COUNT(*) as total FROM laporan WHERE user_id = '$user_id' AND status = 'Diproses'")->fetch()['total'];
$selesai   = $pdo->query("SELECT COUNT(*) as total FROM laporan WHERE user_id = '$user_id' AND status = 'Selesai'")->fetch()['total'];

// Ambil 3 laporan terbaru
$query_laporan = $pdo->prepare("SELECT * FROM laporan WHERE user_id = :id ORDER BY tanggal DESC LIMIT 3");
$query_laporan->execute([':id' => $user_id]);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda User - LaporIn Mataram</title>
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
                            DEFAULT: '#3A5A40', // Dark Green
                            dark: '#2B4330',    // Darker Green for hover
                        },
                        accent: {
                            DEFAULT: '#A3B18A', // Light Green
                            dark: '#8b9a70',
                        },
                        secondary: '#588157',   // Medium Green (For Selesai Status)
                        warning: '#D97706',     // Amber
                        danger: '#DC2626',      // Red
                        info: '#0284C7',        // Blue
                        dark: '#1E293B',        // Slate 800
                        light: '#F8FAFC',       // Slate 50
                        muted: '#94A3B8',       // Slate 400
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom scrollbar matching landing page */
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
        <header class="h-16 bg-accent border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-30">
            <!-- Mobile Menu Button -->
            <button
                class="lg:hidden text-muted hover:text-dark p-2 -ml-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                onclick="toggleSidebar()">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>

            <!-- Title (Hidden on small screens) -->
            <div class="hidden sm:block">
                <h1 class="text-lg font-bold text-dark">Dashboard Masyarakat</h1>
            </div>

        </header>

        <!-- Main Content (Scrollable) -->
        <main class="flex-1 overflow-y-auto bg-primary p-4 sm:p-6 lg:p-8">

            <div class="max-w-6xl mx-auto space-y-6">
                <!-- Welcome Section & Action -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                            Halo, <?php echo $username; ?>
                        </h2>
                        <p class="text-muted mt-1  sm:text-sm">Pantau dan kelola laporan infrastruktur di
                            lingkungan Anda.</p>
                    </div>
                    <a href="buatLaporan.php"
                        class="w-full sm:w-auto bg-accent hover:bg-accent-dark text-white px-5 py-2.5 rounded-lg font-semibold flex items-center justify-center transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                        <i data-lucide="plus circle" class="w-5 h-5 mr-2"></i>
                        Buat Laporan Baru
                    </a>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Total Laporan -->
                    <div
                        class="bg-white p-5 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between hover:border-primary/30 transition-colors">
                        <div>
                            <p class="text-3xl font-bold text-dark mt-1"><?php echo $total; ?></p>
                            <p class="text-sm text-muted">Total Laporan</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                            <i data-lucide="layers" class="w-6 h-6"></i>
                        </div>
                    </div>

                    <!-- Menunggu -->
                    <div
                        class="bg-white p-5 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between hover:border-warning/30 transition-colors">
                        <div>
                            <p class="text-3xl font-bold text-dark mt-1"><?php echo $menunggu; ?></p>
                            <p class="text-sm text-muted">Menunggu</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-warning/10 flex items-center justify-center text-warning">
                            <i data-lucide="clock" class="w-6 h-6"></i>
                        </div>
                    </div>

                    <!-- Diproses -->
                    <div
                        class="bg-white p-5 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between hover:border-info/30 transition-colors">
                        <div>
                            <p class="text-3xl font-bold text-dark mt-1"><?php echo $diproses; ?></p>
                            <p class="text-sm text-muted">Diproses</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-info/10 flex items-center justify-center text-info">
                            <i data-lucide="settings" class="w-6 h-6 animate-[spin_3s_linear_infinite]"></i>
                        </div>
                    </div>

                    <!-- Selesai -->
                    <div
                        class="bg-white p-5 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between hover:border-secondary/30 transition-colors">
                        <div>
                            <p class="text-3xl font-bold text-dark mt-1"><?php echo $selesai; ?></p>
                            <p class="text-sm text-muted">Selesai</p>
                        </div>
                        <div
                            class="w-12 h-12 rounded-full bg-secondary/10 flex items-center justify-center text-secondary">
                            <i data-lucide="check-circle" class="w-6 h-6"></i>
                        </div>
                    </div>
                </div>

                <!-- Recent Reports Section -->
                <div>
                    <div class="flex items-center justify-between mb-4 mt-8">
                        <h3 class="text-lg font-bold text-white">Laporan Terbaru Anda</h3>
                        <a href="daftarLaporan.php" class="text-sm font-medium text-primary hover:text-primary-dark">Lihat Semua
                            &rarr;</a>
                    </div>

                    <!-- Reports Table (Desktop) & Cards (Mobile) -->
                    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">

                        <!-- Desktop Table View -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-sm">
                                        <th class="py-3 px-4 font-semibold text-dark">Judul Laporan & Kategori</th>
                                        <th class="py-3 px-4 font-semibold text-dark">Lokasi</th>
                                        <th class="py-3 px-4 font-semibold text-dark">Tanggal</th>
                                        <th class="py-3 px-4 font-semibold text-dark">Status</th>
                                        <th class="py-3 px-4 text-right font-semibold text-dark">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y divide-slate-100">
                                    <?php if($query_laporan->rowCount() > 0): ?>
                                        <?php while($laporan = $query_laporan->fetch()): ?>
                                        <tr class="hover:bg-slate-50 transition-colors group cursor-pointer"
                                            onclick="window.location='detailLaporan.php?id=<?php echo $laporan['id']; ?>'">
                                            <td class="py-4 px-4">
                                                <p class="font-bold text-dark group-hover:text-primary transition-colors">
                                                    <?php echo $laporan['judul']; ?>
                                                </p>
                                                <div class="flex items-center text-muted mt-1 text-xs">
                                                    <?php echo $laporan['kategori']; ?>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 text-muted"><?php echo $laporan['kelurahan'] . ', ' . $laporan['kecamatan']; ?></td>
                                            <td class="py-4 px-4 text-muted"><?php echo date('d M Y', strtotime($laporan['tanggal'])); ?></td>
                                            <td class="py-4 px-4">
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
                                            <td class="py-4 px-4 text-right">
                                                <a href="detailLaporan.php?id=<?php echo $laporan['id']; ?>"
                                                    class="text-primary hover:text-primary-dark p-2 rounded-lg hover:bg-primary/10 transition-colors inline-block">
                                                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="py-10 text-center text-muted">
                                                <p class="font-medium">Belum ada laporan</p>
                                                <a href="buatLaporan.php" class="text-primary text-sm hover:underline mt-1 inline-block">Buat laporan pertama Anda</a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="md:hidden flex flex-col divide-y divide-slate-100">
                            <div class="md:hidden flex flex-col divide-y divide-slate-100">
                                <?php
                                // Reset query pointer ke awal
                                $query_laporan->execute([':id' => $user_id]);
                                if($query_laporan->rowCount() > 0):
                                    while($laporan = $query_laporan->fetch()):
                                ?>
                                <div class="p-4 hover:bg-slate-50 active:bg-slate-100 transition-colors cursor-pointer"
                                    onclick="window.location='detailLaporan.php?id=<?php echo $laporan['id']; ?>'">
                                    <div class="flex justify-between items-start mb-2">
                                        <?php
                                        $status = $laporan['status'];
                                        if($status == 'Menunggu') {
                                            echo '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-warning/10 text-warning uppercase tracking-wide">Menunggu</span>';
                                        } elseif($status == 'Diproses') {
                                            echo '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-info/10 text-info uppercase tracking-wide">Diproses</span>';
                                        } elseif($status == 'Selesai') {
                                            echo '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-secondary/10 text-secondary uppercase tracking-wide">Selesai</span>';
                                        } elseif($status == 'Ditolak') {
                                            echo '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-danger/10 text-danger uppercase tracking-wide">Ditolak</span>';
                                        }
                                        ?>
                                        <span class="text-xs text-muted"><?php echo date('d M Y', strtotime($laporan['tanggal'])); ?></span>
                                    </div>
                                    <h4 class="font-bold text-dark text-sm mb-1"><?php echo $laporan['judul']; ?></h4>
                                    <div class="flex items-center text-xs text-muted mb-2">
                                        <i data-lucide="tag" class="w-3 h-3 mr-1"></i> <?php echo $laporan['kategori']; ?>
                                    </div>
                                    <div class="flex items-center text-xs text-muted">
                                        <i data-lucide="map-pin" class="w-3 h-3 mr-1"></i> <?php echo $laporan['kelurahan'] . ', ' . $laporan['kecamatan']; ?>
                                    </div>
                                </div>
                                <?php
                                    endwhile;
                                else:
                                ?>
                                <div class="p-8 text-center text-muted">
                                    <p class="font-medium text-sm">Belum ada laporan</p>
                                    <a href="buatLaporan.php" class="text-primary text-xs hover:underline mt-1 inline-block">Buat laporan pertama</a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Script to handle Sidebar Mobile & Icons -->
    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        
    </script>
</body>

</html>