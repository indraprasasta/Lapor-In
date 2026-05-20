<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require __DIR__ . '/../database/conection.php';

$admin_nama  = $_SESSION['admin_nama'];
$pesan_error = "";
$pesan_berhasil = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul    = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $isi      = mysqli_real_escape_string($koneksi, $_POST['isi']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);

    // Upload foto
    $nama_foto = "";
    if (!empty($_FILES['foto']['name'])) {
        $folder      = __DIR__ . '/../uploads/foto_berita/';
        $nama_foto   = time() . '_' . $_FILES['foto']['name'];
        $tipe_file   = $_FILES['foto']['type'];
        $ukuran_file = $_FILES['foto']['size'];

        $tipe_allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

        if (!in_array($tipe_file, $tipe_allowed)) {
            $pesan_error = "Format foto tidak didukung!";
        } elseif ($ukuran_file > 5 * 1024 * 1024) {
            $pesan_error = "Ukuran foto maksimal 5MB!";
        } else {
            move_uploaded_file($_FILES['foto']['tmp_name'], $folder . $nama_foto);
        }
    }

    if ($pesan_error == "") {
        $query = "INSERT INTO berita (judul, isi, foto, kategori)
                VALUES ('$judul', '$isi', '$nama_foto', '$kategori')";

        if (mysqli_query($koneksi, $query)) {
            header("Location: daftarBerita.php?added=1");
            exit();
        } else {
            $pesan_error = "Gagal menyimpan berita!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Berita - Admin LaporIn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                    colors: {
                        primary: { DEFAULT: '#3A5A40', dark: '#2B4330' },
                        accent: { DEFAULT: '#A3B18A', dark: '#8b9a70' },
                        danger: '#DC2626',
                        info: "#0284C7",
                        dark: '#1E293B',
                        light: '#F8FAFC',
                        muted: '#94A3B8',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-light text-dark font-sans h-screen flex overflow-hidden">

    <!-- Sidebar -->
    <aside class="hidden lg:flex flex-col w-64 bg-white border-r border-slate-200">
        <div class="h-16 flex items-center px-6 border-b border-slate-200">
            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white mr-3">
                <i data-lucide="leaf" class="w-5 h-5"></i>
            </div>
            <span class="text-primary font-extrabold text-2xl tracking-tight">Lapor<span class="text-accent">In</span></span>
            <span class="ml-2 text-[10px] font-bold text-white bg-primary px-2 py-0.5 rounded-full uppercase">Admin</span>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1">
            <div
            class="px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider"
            >
            Dashboard
        </div>
            <a href="beranda.php" class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="pie-chart" class="w-5 h-5 mr-3 group-hover:text-primary"></i> Beranda Admin
            </a>
            <div
            class="px-3 mt-6 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider"
            >
            Manajemen Data
            </div>
            <a
            href="dataLaporan.php"
            class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group"
            >
            <i
                data-lucide="file-text"
                class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"
            ></i>
            Data Laporan
            </a>
            <a href="buatBerita.php" class="flex items-center px-3 py-2.5 bg-primary/10 text-primary rounded-lg font-medium group">
                <i data-lucide="plus-circle" class="w-5 h-5 mr-3"></i> Buat Berita
            </a>
            <a href="daftarBerita.php" class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="file-text" class="w-5 h-5 mr-3 group-hover:text-primary"></i> Daftar Berita
            </a>
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
            <a
            href="kategoriLaporan.php"
            class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group"
            >
            <i
                data-lucide="tags"
                class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"
            ></i>
            Kategori Laporan
            </a>
        </nav>

        <div class="p-4 border-t border-slate-200">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-full bg-slate-100 overflow-hidden border border-slate-200">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_nama); ?>&background=A3B18A&color=ffffff" 
                        alt="Avatar" class="w-full h-full object-cover">
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
        <header class="h-16 bg-accent border-b border-slate-200 flex items-center px-6 z-30">
            <h1 class="text-lg font-bold text-dark">Buat Berita Baru</h1>
        </header>

        <main class="flex-1 overflow-y-auto bg-light p-6">
            <div class="max-w-3xl mx-auto">

                <?php if($pesan_error != ""): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm mb-6">
                    <?php echo $pesan_error; ?>
                </div>
                <?php endif; ?>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <form action="" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">

                        <!-- Judul -->
                        <div>
                            <label class="block text-sm font-semibold text-dark mb-2">Judul Berita <span class="text-danger">*</span></label>
                            <input type="text" name="judul" required maxlength="200"
                                placeholder="Masukkan judul berita..."
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none text-sm transition-colors">
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label class="block text-sm font-semibold text-dark mb-2">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori" required
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none text-sm bg-white">
                                <option value="" disabled selected>Pilih Kategori</option>
                                <option value="Infrastruktur">Infrastruktur</option>
                                <option value="Lingkungan">Lingkungan</option>
                                <option value="Teknologi">Teknologi</option>
                                <option value="Sosial">Sosial</option>
                                <option value="Kesehatan">Kesehatan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <!-- Foto -->
                        <div>
                            <label class="block text-sm font-semibold text-dark mb-2">Foto Berita <span class="text-danger">*</span></label>
                            <div class="border-2 border-dashed border-slate-300 rounded-lg p-6 text-center hover:border-primary/50 transition-colors bg-slate-50">
                                <i data-lucide="image-plus" class="w-10 h-10 text-slate-400 mx-auto mb-3"></i>
                                <label for="foto" class="cursor-pointer text-primary font-medium text-sm hover:underline">
                                    Pilih foto
                                    <input type="file" id="foto" name="foto" accept="image/*" class="hidden" onchange="previewFoto(this)">
                                </label>
                                <p class="text-xs text-muted mt-1">Format: JPG, PNG, WEBP. Maks 5MB</p>
                            </div>
                            <!-- Preview foto -->
                            <div id="preview_container" class="hidden mt-3">
                                <img id="preview_foto" src="" alt="Preview" class="w-full h-48 object-cover rounded-lg border border-slate-200">
                            </div>
                        </div>

                        <!-- Isi Berita -->
                        <div>
                            <label class="block text-sm font-semibold text-dark mb-2">Isi Berita <span class="text-danger">*</span></label>
                            <textarea name="isi" rows="8" required
                                placeholder="Tulis isi berita di sini..."
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none text-sm resize-y transition-colors"></textarea>
                        </div>

                        <!-- Tombol -->
                        <div class="pt-4 flex flex-col-reverse sm:flex-row gap-3 sm:justify-end border-t border-slate-100">
                            <a href="daftarBerita.php"
                                class="w-full sm:w-auto px-6 py-2.5 border border-slate-300 text-dark font-semibold rounded-lg hover:bg-slate-50 transition-colors text-center text-sm">
                                Batal
                            </a>
                            <button type="submit"
                                class="w-full sm:w-auto px-6 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors flex items-center justify-center text-sm">
                                <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                                Publikasikan Berita
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </main>
    </div>
    <?php if(isset($_GET['added'])): ?>
    <script>
    Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: 'Berhasil!',
    text: '<?php echo $pesan_berhasil; ?>',
    timer: 2000,
    showConfirmButton: false
        });
    </script>
    <?php endif; ?>

    <script>
        lucide.createIcons();

        function previewFoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview_foto').src = e.target.result;
                    document.getElementById('preview_container').classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        function toggleDropdownUser() {
        const menu = document.getElementById('dropdownUserMenu');
        const icon = document.getElementById('dropdownUserIcon');
        const btn  = document.getElementById('dropdownUserBtn');

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
    </script>
</body>
</html>