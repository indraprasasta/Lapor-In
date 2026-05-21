<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require __DIR__ . '/../database/conection.php';

$user_id = $_SESSION['user_id'];

$query_user = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$user       = mysqli_fetch_assoc($query_user);

// Ambil ID laporan dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: daftarLaporan.php");
    exit();
}
$id_laporan = $_GET['id'];

// Ambil data laporan - pastikan milik user yang login
$query_laporan = mysqli_query($koneksi, "SELECT * FROM laporan WHERE id = '$id_laporan' AND user_id = '$user_id'");

if (mysqli_num_rows($query_laporan) == 0) {
    header("Location: daftarLaporan.php");
    exit();
}
$laporan = mysqli_fetch_assoc($query_laporan);

// Proses hapus laporan
if (isset($_GET['hapus']) && $_GET['hapus'] == 'true') {
    // Hapus foto dari folder jika ada
    if (!empty($laporan['foto'])) {
        $path_foto = __DIR__ . '/../uploads/foto_laporan/' . $laporan['foto'];
        if (file_exists($path_foto)) {
            unlink($path_foto);
        }
    }
    
    // Hapus dari database
    mysqli_query($koneksi, "DELETE FROM laporan WHERE id = '$id_laporan' AND user_id = '$user_id'");   
    header("Location: daftarLaporan.php?hapus=success");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan - LaporIn Mataram</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        /* Timeline specific styles */
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 11px;
            top: 28px;
            bottom: -8px;
            width: 2px;
            background-color: #E2E8F0;
            /* slate-200 */
        }

        .timeline-item:last-child::before {
            display: none;
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
                    <a href="beranda.php" class="hover:text-dark">Dashboard</a>
                    <span class="mx-2">/</span>
                    <a href="daftarLaporan.php" class="hover:text-dark">Laporan Saya</a>
                    <span class="mx-2">/</span>
                    <span class="text-dark">Detail Laporan</span>
                </nav>
            </div>
            <div class="flex items-center ml-auto">
                <button
                    class="relative p-2 text-muted hover:text-dark rounded-full hover:bg-slate-50 transition-colors">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-primary p-4 sm:p-6 lg:p-8">
            <div class="max-w-5xl mx-auto space-y-6">

                <!-- Page Header & Actions -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-center">
                        <button
                            class="mr-4 p-2 rounded-lg hover:bg-slate-200 transition-colors text-slate-500 hover:text-dark focus:outline-none focus:ring-2 focus:ring-primary"
                            title="Kembali" onclick="history.back()">
                            <i data-lucide="arrow-left" class="w-5 h-5"></i>
                        </button>
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <h2 class="text-2xl font-bold text-white tracking-tight">
                                    <?php echo $laporan['judul']; ?>
                                </h2>
                                <?php
                                $status = $laporan['status'];
                                if($status == 'Menunggu') {
                                    echo '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-warning text-white uppercase">Menunggu</span>';
                                } elseif($status == 'Diproses') {
                                    echo '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-info text-white uppercase">Diproses</span>';
                                } elseif($status == 'Selesai') {
                                    echo '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-secondary text-white uppercase">Selesai</span>';
                                } elseif($status == 'Ditolak') {
                                    echo '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-danger text-white uppercase">Ditolak</span>';
                                }
                                ?>
                            </div>
                            <p class="text-muted text-sm">
                                Dilaporkan pada <?php echo date('d F Y, H:i', strtotime($laporan['tanggal'])); ?> WITA
                            </p>
                        </div>
                    </div>

                    <!-- Tombol Edit/Hapus -->
                    <?php if($laporan['status'] == 'Menunggu' || $laporan['status'] == 'Selesai'): ?>
                    <div class="flex items-center gap-2">
                        <a href="editLaporan.php?id=<?php echo $laporan['id']; ?>"
                            class="px-4 py-2 bg-white border border-slate-300 text-dark font-medium rounded-lg hover:bg-slate-50 transition-colors flex items-center text-sm shadow-sm">
                            <i data-lucide="edit" class="w-4 h-4 mr-2"></i> Edit
                        </a>
                        <button onclick="bukaModalHapus()"
                            class="px-4 py-2 bg-white border border-danger text-danger font-medium rounded-lg hover:bg-danger/5 transition-colors flex items-center text-sm shadow-sm">
                            <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i> Hapus
                        </button>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Left Column: Informasi Laporan (2/3 width on LG) -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Card Informasi Utama -->
                        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-dark mb-4"><?php echo $laporan['judul']; ?></h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 mb-6">
                                    <div>
                                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">
                                            Kategori</p>
                                        <div class="flex items-center text-dark font-medium">
                                            <p class="text-dark font-medium"><?php echo $laporan['kategori']; ?></p>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-dark font-medium"><?php echo $laporan['kelurahan'] . ', ' . $laporan['kecamatan']; ?></p>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-dark text-sm leading-relaxed bg-slate-50 p-4 rounded-lg border border-slate-100">
                                    <?php echo $laporan['deskripsi']; ?>
                                </p>
                                </div>

                                <div class="mt-6">
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Alamat
                                        Lengkap</p>
                                    <p class="text-dark text-sm"><?php echo $laporan['alamat']; ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Card Foto Bukti -->
                        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                            <div
                                class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                                <h3 class="font-bold text-dark">Foto Bukti Kerusakan</h3>
                                <span
                                    class="text-xs font-medium text-slate-500 bg-white px-2 py-1 rounded border border-slate-200">2
                                    Foto</span>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Dummy Images using Unsplash -->
                                    <?php if(!empty($laporan['foto'])): ?>
                                    <div class="aspect-video bg-slate-100 rounded-lg overflow-hidden border border-slate-200">
                                        <img src="../uploads/foto_laporan/<?php echo $laporan['foto']; ?>" 
                                            alt="Foto Laporan" class="w-full h-full object-cover">
                                    </div>
                                    <?php else: ?>
                                    <p class="text-muted text-sm">Tidak ada foto</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </main>
    </div>

    <div id="modalHapus" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-xl">
            
            <h3 class="text-lg font-bold text-dark mb-2">Hapus Laporan</h3>
            <p class="text-sm text-muted mb-4">
                Apakah kamu yakin ingin menghapus laporan ini?
            </p>

            <div class="flex justify-end gap-3">
                <button onclick="tutupModalHapus()"
                    class="px-4 py-2 bg-slate-200 rounded-lg text-sm">
                    Batal
                </button>

                <button onclick="hapusLaporan()"
                    class="px-4 py-2 bg-danger text-white rounded-lg text-sm">
                    Hapus
                </button>
            </div>

        </div>
    </div>

    <!-- Script Logic -->
    <script>
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

    function bukaModalHapus() {
    document.getElementById('modalHapus').classList.remove('hidden');
    }

    function tutupModalHapus() {
        document.getElementById('modalHapus').classList.add('hidden');
    }

    function hapusLaporan() {
        window.location.href = 'detailLaporan.php?id=<?php echo $laporan['id']; ?>&hapus=true';
    }
    </script>
</body>

</html>