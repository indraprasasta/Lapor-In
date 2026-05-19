<?php
session_start();

// Jika belum login, redirect ke login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require __DIR__ . '/../database/conection.php';

$username = $_SESSION['username'];
$id_user = $_SESSION['user_id'];
$query   = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$id_user'");
$user    = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - LaporIn Mataram</title>
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

<body class="bg-primary text-dark font-sans h-screen flex overflow-hidden">

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
            <a href="daftarLaporan.php"
                class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="file-text" class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"></i>
                Laporan Saya
            </a>
            <!-- Menu Profil Aktif -->
            <a href="profile.php" class="flex items-center px-3 py-2.5 bg-primary/10 text-primary rounded-lg font-medium group">
                <i data-lucide="user" class="w-5 h-5 mr-3"></i>
                Profil
            </a>
        </nav>

        <!-- User Info -->
        <div class="p-4 border-t border-slate-200">
            <a href="profile.php" class="flex items-center group">
                <div
                    class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-primary font-bold overflow-hidden border border-slate-200">
                    <img src="<?php echo 'https://ui-avatars.com/api/?name=' . urlencode($username) . '&background=A3B18A&color=ffffff'; ?>"
                    alt="Avatar" class="w-full h-full object-cover">
                </div>
                <div class="ml-3">
                    <p class="text-sm font-semibold text-dark group-hover:text-primary transition-colors">
                        <?php echo $username; ?>
                    </p>
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
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-30">
            <button
                class="lg:hidden text-muted hover:text-dark p-2 -ml-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                onclick="toggleSidebar()">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <div class="hidden sm:block">
                <nav class="flex text-sm text-muted font-medium">
                    <a href="beranda.html" class="hover:text-dark">Dashboard</a>
                    <span class="mx-2">/</span>
                    <span class="text-dark">Profil Pengguna</span>
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
        <!-- Background halaman hijau tua (primary) -->
        <main class="flex-1 overflow-y-auto bg-primary p-4 sm:p-6 lg:p-8">
            <div class="max-w-6xl mx-auto">

                <div class="mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div>
                        <!-- Judul putih agar kontras dengan bg hijau -->
                        <h2 class="text-2xl font-bold text-white tracking-tight">Profil Pengguna</h2>
                        <p class="text-accent mt-1 text-sm">Kelola informasi pribadi dan keamanan akun Anda.</p>
                    </div>
                </div>

                <!-- Grid Profil -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Kartu Info Singkat (Kiri) -->
                    <div class="lg:col-span-1">
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl overflow-hidden p-6 text-center">
                            
                            <!-- Foto Profil -->
                            <div class="relative inline-block mt-4 mb-4">
                                <div class="w-32 h-32 rounded-full border-4 border-slate-50 overflow-hidden shadow-lg mx-auto">
                                <?php if(!empty($user['foto'])): ?>
                                <img id="preview_foto" src="../uploads/foto_profil/<?php echo $user['foto']; ?>" 
                                alt="Foto Profil" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <img id="preview_foto" src="<?php echo 'https://ui-avatars.com/api/?name=' . urlencode($user['nama']) . '&background=A3B18A&color=ffffff&size=200'; ?>" 
                                    alt="Avatar Profil" class="w-full h-full object-cover">
                                    <?php endif; ?>
                                </div>
                                    <!-- Label pengganti button -->
                                    <label for="foto_input" class="absolute bottom-1 right-1 bg-primary text-white p-2 rounded-full shadow-md hover:bg-accent transition-colors border-2 border-white cursor-pointer" title="Ubah Foto">
                                    <i data-lucide="camera" class="w-4 h-4"></i>
                                    </label>
                            </div>

                            <h3 class="text-xl font-bold text-dark"><?php echo $user['nama']; ?></h3>
                            <p class="text-sm font-medium text-muted mb-6">Warga Kota Mataram</p>

                            <div class="border-t border-slate-100 pt-6 space-y-4 text-left">
                                <div class="flex items-center gap-3 text-sm text-dark">
                                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center shrink-0">
                                    <i data-lucide="user" class="w-4 h-4 text-primary"></i>
                                        </div>
                                    <span><?php echo $user['gender']; ?></span>
                                    </div>
                                <div class="flex items-center gap-3 text-sm text-dark">
                                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center shrink-0">
                                        <i data-lucide="phone" class="w-4 h-4 text-primary"></i>
                                    </div>
                                    <span>+62 812-3456-7890</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kartu Form Edit Profil (Kanan) -->
                    <div class="lg:col-span-2">
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl overflow-hidden">
                            
                            <!-- Form Info Personal -->
                            <div class="p-6 sm:p-8">
                                <h3 class="text-lg font-bold text-dark border-b border-slate-100 pb-4 mb-6">Informasi Pribadi</h3>
                                <form action="proses_profil.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                                    <input type="file" id="foto_input" name="foto" accept="image/*" class="hidden" onchange="previewFoto(this)">      
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                        <!-- Nama Lengkap -->
                                        <div>
                                            <label for="nama" class="block text-sm font-semibold text-dark mb-2">Nama Lengkap</label>
                                            <input type="text" id="nama" name="nama" 
                                                value="<?php echo $user['nama']; ?>" required
                                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none transition-colors text-sm text-dark">
                                        </div>
                                        
                                        <!-- NIK (Readonly/Disabled) -->
                                        <div>
                                            <label for="nik" class="block text-sm font-semibold text-dark mb-2">NIK <span class="text-xs text-muted font-normal">(Tidak dapat diubah)</span></label>
                                            <input type="text" id="nik" name="nik" 
                                                value="<?php echo $user['nik']; ?>" disabled
                                                class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50 outline-none text-sm text-slate-500 cursor-not-allowed">
                                        </div>

                                        <!-- username -->
                                        <div>
                                            <label for="username" class="block text-sm font-semibold text-dark mb-2">Username</label>
                                            <input type="text" id="username" name="username" 
                                                value="<?php echo $user['username']; ?>" required
                                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none transition-colors text-sm text-dark">
                                        </div>

                                        <!-- Telepon -->
                                        <div>
                                            <label for="telepon" class="block text-sm font-semibold text-dark mb-2">No. Telepon / WhatsApp</label>
                                            <input type="text" id="telepon" name="telepon" value="081234567890" required
                                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none transition-colors text-sm text-dark">
                                        </div>
                                    </div>

                                    <!-- Alamat -->
                                    <div>
                                        <label for="alamat" class="block text-sm font-semibold text-dark mb-2">Alamat Tempat Tinggal</label>
                                        <textarea id="alamat" name="alamat" rows="3" required
                                            class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none transition-colors text-sm resize-y text-dark"><?php echo $user['alamat']; ?></textarea>
                                    </div>

                                    <h3 class="text-lg font-bold text-dark border-b border-slate-100 pb-4 mt-10 mb-6">Keamanan Akun</h3>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                        <!-- Password Baru -->
                                        <div>
                                            <label for="new_password" class="block text-sm font-semibold text-dark mb-2">Kata Sandi Baru</label>
                                            <div class="relative">
                                                <input type="password" id="new_password" name="new_password" placeholder="Biarkan kosong jika tidak diubah"
                                                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none transition-colors text-sm text-dark">
                                            </div>
                                        </div>

                                        <!-- Konfirmasi Password -->
                                        <div>
                                            <label for="confirm_password" class="block text-sm font-semibold text-dark mb-2">Konfirmasi Sandi Baru</label>
                                            <div class="relative">
                                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi kata sandi baru"
                                                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none transition-colors text-sm text-dark">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="pt-6 mt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row gap-3 sm:justify-end">
                                        <button type="button" onclick="history.back()"
                                            class="w-full sm:w-auto px-6 py-2.5 border-2 border-slate-300 text-slate-600 font-semibold rounded-lg hover:bg-slate-50 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-200">
                                            Batal
                                        </button>
                                        <button type="submit"
                                            class="w-full sm:w-auto px-6 py-2.5 bg-accent hover:bg-white text-primary font-bold border-2 border-transparent hover:border-accent rounded-lg shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 flex items-center justify-center">
                                            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                                            Simpan Perubahan
                                        </button>
                                    </div>

                                </form>
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

        function previewFoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview_foto').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>

</html>