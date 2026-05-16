<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_nama = $_SESSION['admin_nama'];
require __DIR__ . '/../database/conection.php';

$pesan_error = '';
$pesan_sukses = '';

// Mengambil data dinas untuk dropdown
$query_dinas = mysqli_query($koneksi, "SELECT * FROM dinas ORDER BY nama_dinas ASC");

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nip      = mysqli_real_escape_string($koneksi, $_POST['nip']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    $jabatan  = mysqli_real_escape_string($koneksi, $_POST['jabatan']);
    $dinas_id = (int) $_POST['dinas_id'];

    // Cek duplikat username atau NIP
    $cek = mysqli_query($koneksi, "SELECT id FROM petugas WHERE username = '$username' OR nip = '$nip'");

    if (mysqli_num_rows($cek) > 0) {
        $pesan_error = "Username atau NIP sudah terdaftar!";
    } else {
        $query = "INSERT INTO petugas (nama, nip, username, password, jabatan, dinas_id)
                VALUES ('$nama', '$nip', '$username', '$password', '$jabatan', '$dinas_id')";

        if (mysqli_query($koneksi, $query)) {
            echo "<script>
                alert('Petugas berhasil ditambahkan!');
                window.location.href = 'datapetugas.php';
            </script>";
            exit();
        } else {
            $pesan_error = "Gagal menyimpan: " . mysqli_error($koneksi);
        }
    }
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tambah Petugas - LaporIn Mataram</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ["Poppins", "sans-serif"] },
                    colors: {
                        primary: { DEFAULT: "#3A5A40", dark: "#2B4330" },
                        accent: { DEFAULT: "#A3B18A", dark: "#8b9a70" },
                        secondary: "#588157",
                        warning: "#D97706",
                        danger: "#DC2626",
                        info: "#0284C7",
                        dark: "#1E293B",
                        light: "#F8FAFC",
                        muted: "#94A3B8",
                    },
                },
            },
        };
    </script>
</head>

<body class="bg-light text-dark font-sans h-screen flex overflow-hidden">

    <div id="sidebarOverlay" class="fixed inset-0 bg-dark/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 bg-white w-64 border-r border-slate-200 z-50 transform -translate-x-full lg:translate-x-0 lg:static lg:flex lg:flex-col transition-transform duration-300 ease-in-out">
        <div class="h-16 flex items-center px-6 border-b border-slate-200">
            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white mr-3">
                <i data-lucide="leaf" class="w-5 h-5"></i>
            </div>
            <span class="text-primary font-extrabold text-2xl tracking-tight">Lapor<span class="text-accent">In</span></span>
            <span class="ml-2 text-[10px] font-bold text-white bg-primary px-2 py-0.5 rounded-full uppercase tracking-wider">Admin</span>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <div class="px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">Dashboard</div>
            <a href="beranda.php" class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="pie-chart" class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"></i> Beranda Admin
            </a>

            <div class="px-3 mt-6 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">Manajemen Data</div>
            <a href="dataLaporan.php" class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="file-text" class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"></i> Data Laporan
            </a>
            
            <a href="dataPetugas.php" class="flex items-center px-3 py-2.5 bg-primary/10 text-primary rounded-lg font-medium group">
                <i data-lucide="hard-hat" class="w-5 h-5 mr-3"></i> Data Petugas
            </a>
            
            <a href="kategori.php" class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="tags" class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"></i> Kategori Laporan
            </a>
        </nav>

        <div class="p-4 border-t border-slate-200">
            <div class="flex items-center group">
                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-primary font-bold overflow-hidden border border-slate-200">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_nama); ?>&background=A3B18A&color=ffffff" alt="Avatar" class="w-full h-full object-cover" />
                </div>
                <div class="ml-3">
                    <p class="text-sm font-semibold text-dark"><?php echo $admin_nama; ?></p>
                    <p class="text-[10px] text-muted font-mono">Admin</p>
                </div>
            </div>
            <a href="logout.php" class="mt-4 w-full flex items-center justify-center px-3 py-2 text-sm text-danger bg-red-50 hover:bg-red-100 rounded-lg font-medium transition-colors">
                <i data-lucide="log-out" class="w-4 h-4 mr-2"></i> Keluar
            </a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-16 bg-accent border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-30 shrink-0 shadow-sm">
            <button class="lg:hidden text-dark hover:text-primary p-2 -ml-2 rounded-lg" onclick="toggleSidebar()">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <div class="hidden sm:block">
                <nav class="flex text-sm text-dark font-medium items-center">
                    <span class="hover:text-primary cursor-pointer">Manajemen Data</span>
                    <i data-lucide="chevron-right" class="w-4 h-4 mx-1"></i>
                    <a href="dataPetugas.php" class="hover:text-primary cursor-pointer">Data Petugas Lapangan</a>
                    <i data-lucide="chevron-right" class="w-4 h-4 mx-1"></i>
                    <span class="text-primary font-bold">Tambah Petugas</span>
                </nav>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto bg-light p-4 sm:p-6 lg:p-8">
            <div class="max-w-3xl mx-auto space-y-6">
                
                <div>
                    <h2 class="text-2xl font-bold text-dark tracking-tight">Tambah Petugas Baru</h2>
                    <p class="text-muted mt-1 text-sm">Buat akun untuk petugas lapangan baru agar dapat mengakses sistem LaporIn.</p>
                </div>

                <?php if($pesan_sukses): ?>
                <div class="bg-secondary/10 border-l-4 border-secondary p-4 rounded-r-lg flex items-start">
                    <i data-lucide="check-circle" class="w-5 h-5 text-secondary mt-0.5 mr-3 shrink-0"></i>
                    <div>
                        <h3 class="text-secondary font-bold text-sm">Berhasil!</h3>
                        <p class="text-secondary/80 text-xs mt-1"><?php echo $pesan_sukses; ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if($pesan_error): ?>
                <div class="bg-danger/10 border-l-4 border-danger p-4 rounded-r-lg flex items-start">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-danger mt-0.5 mr-3 shrink-0"></i>
                    <div>
                        <h3 class="text-danger font-bold text-sm">Terjadi Kesalahan!</h3>
                        <p class="text-danger/80 text-xs mt-1"><?php echo $pesan_error; ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50">
                        <h3 class="font-bold text-dark flex items-center">
                            <i data-lucide="user-plus" class="w-4 h-4 mr-2 text-primary"></i>
                            Informasi Petugas & Kredensial Akun
                        </h3>
                    </div>
                    
                    <form action="" method="POST" class="p-6 space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div class="space-y-1.5">
                                <label for="nama" class="block text-sm font-semibold text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" id="nama" name="nama" required placeholder="Cth: indra prasasta"
                                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm text-dark placeholder:text-slate-400">
                            </div>

                            <div class="space-y-1.5">
                                <label for="nip" class="block text-sm font-semibold text-dark">NIP / Nomor Pegawai <span class="text-danger">*</span></label>
                                <input type="text" id="nip" name="nip" required placeholder="Cth: 198502142015"
                                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm text-dark placeholder:text-slate-400">
                            </div>

                            <div class="space-y-1.5">
                                <label for="username" class="block text-sm font-semibold text-dark">username Akun <span class="text-danger">*</span></label>
                                <input type="text" id="username" name="username" required placeholder="petugas"
                                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm text-dark placeholder:text-slate-400">
                                <p class="text-[10px] text-muted">username ini akan digunakan untuk login petugas.</p>
                            </div>

                            <div class="space-y-1.5">
                                <label for="password" class="block text-sm font-semibold text-dark">Password <span class="text-danger">*</span></label>
                                <input type="password" id="password" name="password" required placeholder="••••••••"
                                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm text-dark placeholder:text-slate-400">
                            </div>

                        </div>

                        <div class="border-t border-slate-200"></div>

                        <div class="space-y-1.5">
                            <label for="dinas_id" class="block text-sm font-semibold text-dark">Penempatan Instansi / Dinas <span class="text-danger">*</span></label>
                            <div class="relative">
                                <select id="dinas_id" name="dinas_id" required
                                    class="w-full pl-4 pr-10 py-2.5 appearance-none rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm bg-white text-dark">
                                    <option value="" disabled selected>-- Pilih Dinas --</option>
                                    <?php while($dinas = mysqli_fetch_assoc($query_dinas)): ?>
                                        <option value="<?php echo $dinas['id']; ?>">
                                            <?php echo $dinas['kode_dinas']; ?> - <?php echo $dinas['nama_dinas']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-4">
                            <a href="dataPetugas.php" class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                                Batal
                            </a>
                            <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-lg flex items-center shadow-sm transition-colors">
                                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                                Simpan Data Petugas
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("sidebarOverlay");
            sidebar.classList.toggle("-translate-x-full");
            overlay.classList.toggle("hidden");
        }
    </script>
</body>
</html>