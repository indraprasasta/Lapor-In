<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require __DIR__ . '/../database/conection.php';

$admin_nama = $_SESSION['admin_nama'];

// Validasi ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: daftarBerita.php");
    exit();
}

$id_berita = (int) $_GET['id'];

// Ambil data berita
$query = mysqli_query($koneksi, "SELECT * FROM berita WHERE id = '$id_berita'");
if (mysqli_num_rows($query) == 0) {
    header("Location: daftarBerita.php");
    exit();
}
$berita = mysqli_fetch_assoc($query);
$pesan_sukses = '';
$pesan_error  = '';

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul    = mysqli_real_escape_string($koneksi, trim($_POST['judul']));
    $kategori = mysqli_real_escape_string($koneksi, trim($_POST['kategori']));
    $isi      = mysqli_real_escape_string($koneksi, trim($_POST['isi']));
    $nama_foto = $berita['foto']; // default foto lama

    // Upload foto baru jika ada
    if (!empty($_FILES['foto']['name'])) {
        $ekstensi_ok = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $ekstensi_ok)) {
            $pesan_error = 'Format foto tidak didukung. Gunakan JPG, PNG, atau WEBP.';
        } elseif ($_FILES['foto']['size'] > 3 * 1024 * 1024) {
            $pesan_error = 'Ukuran foto maksimal 3MB.';
        } else {
            $nama_foto = 'berita_' . time() . '.' . $ext;
            $tujuan    = __DIR__ . '/../uploads/foto_berita/' . $nama_foto;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $tujuan)) {
                // Hapus foto lama jika ada
                if (!empty($berita['foto'])) {
                    $foto_lama = __DIR__ . '/../uploads/foto_berita/' . $berita['foto'];
                    if (file_exists($foto_lama)) unlink($foto_lama);
                }
            } else {
                $pesan_error = 'Gagal mengupload foto. Coba lagi.';
                $nama_foto   = $berita['foto'];
            }
        }
    }

    if (empty($pesan_error)) {
        $update = mysqli_query($koneksi,
            "UPDATE berita SET
                judul    = '$judul',
                kategori = '$kategori',
                isi      = '$isi',
                foto     = '$nama_foto'
             WHERE id = '$id_berita'"
        );

        if ($update) {
            // Refresh data berita
            $berita = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM berita WHERE id = '$id_berita'"));
            $pesan_sukses = 'Berita berhasil diperbarui!';
        } else {
            $pesan_error = 'Gagal memperbarui berita. Coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Berita - Admin LaporIn</title>
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

        /* Preview foto fade in */
        #previewFoto { transition: opacity 0.3s ease; }

        /* Drag area */
        #dropZone.drag-over {
            border-color: #3A5A40;
            background-color: #f0f4f0;
        }
    </style>
</head>

<body class="bg-light text-dark font-sans h-screen flex overflow-hidden">

    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-dark/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <!-- ===== SIDEBAR ===== -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 bg-white w-64 border-r border-slate-200 z-50 transform -translate-x-full lg:translate-x-0 lg:static lg:flex lg:flex-col transition-transform duration-300 ease-in-out">
        <div class="h-16 flex items-center px-6 border-b border-slate-200">
            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white mr-3">
                <i data-lucide="leaf" class="w-5 h-5"></i>
            </div>
            <span class="text-primary font-extrabold text-2xl tracking-tight">Lapor<span class="text-accent">In</span></span>
            <span class="ml-2 text-[10px] font-bold text-white bg-primary px-2 py-0.5 rounded-full uppercase">Admin</span>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <div class="px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">Dashboard</div>
            <a href="beranda.php" class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="pie-chart" class="w-5 h-5 mr-3 group-hover:text-primary"></i> Beranda Admin
            </a>

            <div class="px-3 mt-6 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">Manajemen Data</div>
            <a href="dataLaporan.php" class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="file-text" class="w-5 h-5 mr-3 group-hover:text-primary"></i> Data Laporan
            </a>
            <a href="buatBerita.php" class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="plus-circle" class="w-5 h-5 mr-3 group-hover:text-primary"></i> Buat Berita
            </a>
            <!-- Daftar Berita — AKTIF -->
            <a href="daftarBerita.php" class="flex items-center px-3 py-2.5 bg-primary/10 text-primary rounded-lg font-medium group">
                <i data-lucide="newspaper" class="w-5 h-5 mr-3"></i> Daftar Berita
            </a>

            <div>
                <button onclick="toggleDropdownUser()" id="dropdownUserBtn"
                    class="w-full flex items-center justify-between px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                    <div class="flex items-center">
                        <i data-lucide="users" class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"></i>
                        Manajemen Pengguna
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" id="dropdownUserIcon"></i>
                </button>
                <div id="dropdownUserMenu" class="hidden ml-4 mt-1 space-y-1 border-l-2 border-slate-100 pl-3">
                    <a href="dataPetugas.php" class="flex items-center gap-2 px-3 py-2 text-sm text-muted hover:text-dark hover:bg-slate-50 rounded-lg transition-colors">
                        <span class="w-2 h-2 rounded-full bg-primary inline-block"></span> Data Petugas
                    </a>
                    <a href="dataUser.php" class="flex items-center gap-2 px-3 py-2 text-sm text-muted hover:text-dark hover:bg-slate-50 rounded-lg transition-colors">
                        <span class="w-2 h-2 rounded-full bg-info inline-block"></span> Data User
                    </a>
                </div>
            </div>

            <a href="kategoriLaporan.php" class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="tags" class="w-5 h-5 mr-3 group-hover:text-primary"></i> Kategori Laporan
            </a>
        </nav>

        <div class="p-4 border-t border-slate-200">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-full bg-slate-100 overflow-hidden border border-slate-200">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_nama); ?>&background=A3B18A&color=ffffff"
                        alt="Avatar" class="w-full h-full object-cover">
                </div>
                <div class="ml-3">
                    <p class="text-sm font-semibold text-dark"><?php echo htmlspecialchars($admin_nama); ?></p>
                    <p class="text-[10px] text-muted">Administrator</p>
                </div>
            </div>
            <a href="logout.php" class="w-full flex items-center justify-center px-3 py-2 text-sm text-danger bg-red-50 hover:bg-red-100 rounded-lg font-medium transition-colors">
                <i data-lucide="log-out" class="w-4 h-4 mr-2"></i> Keluar
            </a>
        </div>
    </aside>

    <!-- ===== MAIN WRAPPER ===== -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">

        <!-- Navbar -->
        <header class="h-16 bg-accent border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-30 shrink-0">
            <button class="lg:hidden text-dark p-2 -ml-2 rounded-lg" onclick="toggleSidebar()">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <div class="hidden sm:flex items-center gap-2 text-sm text-dark">
                <a href="daftarBerita.php" class="hover:text-primary transition-colors font-medium">Daftar Berita</a>
                <i data-lucide="chevron-right" class="w-4 h-4 text-muted"></i>
                <span class="font-bold">Edit Berita</span>
            </div>
            <div class="ml-auto flex items-center gap-3">
                <a href="daftarBerita.php"
                    class="hidden sm:flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-dark bg-white/60 hover:bg-white rounded-lg transition-colors border border-white/40">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                </a>
            </div>
        </header>

        <!-- ===== MAIN CONTENT ===== -->
        <main class="flex-1 overflow-y-auto bg-light p-4 sm:p-6 lg:p-8">
            <div class="max-w-5xl mx-auto space-y-6">

                <!-- Page Header -->
                <div class="flex items-center gap-4">
                    <button onclick="history.back()" class="p-2 rounded-lg hover:bg-slate-200 transition-colors text-slate-500">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </button>
                    <div>
                        <h2 class="text-2xl font-bold text-dark">Edit Berita</h2>
                        <p class="text-muted text-sm mt-0.5">Perbarui informasi berita yang sudah dipublikasikan</p>
                    </div>
                </div>

                <!-- Notifikasi -->
                <?php if ($pesan_sukses): ?>
                <div class="flex items-center gap-3 bg-secondary/10 border border-secondary/30 text-secondary px-4 py-3 rounded-lg text-sm font-medium">
                    <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
                    <?php echo $pesan_sukses; ?>
                </div>
                <?php endif; ?>
                <?php if ($pesan_error): ?>
                <div class="flex items-center gap-3 bg-danger/10 border border-danger/30 text-danger px-4 py-3 rounded-lg text-sm font-medium">
                    <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
                    <?php echo $pesan_error; ?>
                </div>
                <?php endif; ?>

                <!-- Form -->
                <form method="POST" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                        <!-- Kolom Kiri: Form Utama -->
                        <div class="lg:col-span-2 space-y-5">

                            <!-- Card Konten Berita -->
                            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                                    <h3 class="font-bold text-dark text-sm flex items-center gap-2">
                                        <i data-lucide="file-text" class="w-4 h-4 text-primary"></i>
                                        Konten Berita
                                    </h3>
                                </div>
                                <div class="p-6 space-y-5">

                                    <!-- Judul -->
                                    <div>
                                        <label class="block text-sm font-semibold text-dark mb-1.5" for="judul">
                                            Judul Berita <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="judul" name="judul" required
                                            value="<?php echo htmlspecialchars($berita['judul']); ?>"
                                            placeholder="Masukkan judul berita..."
                                            class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm">
                                    </div>

                                    <!-- Kategori -->
                                    <div>
                                        <label class="block text-sm font-semibold text-dark mb-1.5" for="kategori">
                                            Kategori <span class="text-danger">*</span>
                                        </label>
                                        <div class="relative">
                                            <select id="kategori" name="kategori" required
                                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm bg-white appearance-none">
                                                <?php
                                                $kategori_list = ['Infrastruktur', 'Lingkungan', 'Keamanan', 'Sosial', 'Pengumuman', 'Lainnya'];
                                                foreach ($kategori_list as $kat):
                                                    $selected = $berita['kategori'] === $kat ? 'selected' : '';
                                                ?>
                                                <option value="<?php echo $kat; ?>" <?php echo $selected; ?>><?php echo $kat; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Isi Berita -->
                                    <div>
                                        <label class="block text-sm font-semibold text-dark mb-1.5" for="isi">
                                            Isi Berita <span class="text-danger">*</span>
                                        </label>
                                        <textarea id="isi" name="isi" required rows="12"
                                            placeholder="Tulis isi berita di sini..."
                                            class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm resize-y leading-relaxed"><?php echo htmlspecialchars($berita['isi']); ?></textarea>
                                        <p class="text-xs text-muted mt-1.5" id="charCount">0 karakter</p>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <!-- Kolom Kanan: Foto & Aksi -->
                        <div class="space-y-5">

                            <!-- Card Foto -->
                            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                                    <h3 class="font-bold text-dark text-sm flex items-center gap-2">
                                        <i data-lucide="image" class="w-4 h-4 text-primary"></i>
                                        Foto Berita
                                    </h3>
                                </div>
                                <div class="p-6 space-y-4">

                                    <!-- Preview Foto Saat Ini -->
                                    <div id="previewContainer">
                                        <?php if (!empty($berita['foto'])): ?>
                                        <div class="relative rounded-lg overflow-hidden border border-slate-200 bg-slate-100">
                                            <img id="previewFoto"
                                                src="../uploads/foto_berita/<?php echo $berita['foto']; ?>"
                                                alt="Preview" class="w-full aspect-video object-cover">
                                            <div class="absolute top-2 right-2">
                                                <span class="bg-secondary text-white text-[10px] font-bold px-2 py-0.5 rounded-full">Foto Saat Ini</span>
                                            </div>
                                        </div>
                                        <?php else: ?>
                                        <div id="previewFoto" class="w-full aspect-video bg-slate-100 rounded-lg border border-slate-200 flex items-center justify-center">
                                            <div class="text-center">
                                                <i data-lucide="image-off" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                                                <p class="text-xs text-muted">Belum ada foto</p>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Drop Zone Upload -->
                                    <div id="dropZone"
                                        class="border-2 border-dashed border-slate-300 rounded-lg p-5 text-center cursor-pointer hover:border-primary hover:bg-primary/5 transition-colors"
                                        onclick="document.getElementById('foto').click()"
                                        ondragover="handleDragOver(event)"
                                        ondragleave="handleDragLeave(event)"
                                        ondrop="handleDrop(event)">
                                        <i data-lucide="upload-cloud" class="w-8 h-8 text-slate-400 mx-auto mb-2"></i>
                                        <p class="text-sm font-medium text-dark">Klik atau drag foto baru</p>
                                        <p class="text-xs text-muted mt-1">JPG, PNG, WEBP — Maks. 3MB</p>
                                    </div>

                                    <input type="file" id="foto" name="foto" accept="image/*" class="hidden"
                                        onchange="previewGambar(this)">

                                    <!-- Nama file yang dipilih -->
                                    <div id="namaFile" class="hidden flex items-center gap-2 px-3 py-2 bg-primary/5 border border-primary/20 rounded-lg">
                                        <i data-lucide="file-image" class="w-4 h-4 text-primary shrink-0"></i>
                                        <span id="namaFileTeks" class="text-xs text-primary font-medium truncate"></span>
                                        <button type="button" onclick="hapusPilihan()" class="ml-auto text-muted hover:text-danger transition-colors">
                                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>

                                    <p class="text-xs text-muted text-center">Kosongkan jika tidak ingin mengubah foto</p>
                                </div>
                            </div>

                            <!-- Card Aksi -->
                            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                                    <h3 class="font-bold text-dark text-sm flex items-center gap-2">
                                        <i data-lucide="settings" class="w-4 h-4 text-primary"></i>
                                        Aksi
                                    </h3>
                                </div>
                                <div class="p-6 space-y-3">
                                    <!-- Info Berita -->
                                    <div class="bg-slate-50 rounded-lg p-3 border border-slate-100 space-y-2 text-xs text-muted">
                                        <div class="flex justify-between">
                                            <span>ID Berita</span>
                                            <span class="font-mono font-semibold text-dark">#<?php echo str_pad($berita['id'], 4, '0', STR_PAD_LEFT); ?></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Dibuat</span>
                                            <span class="font-medium text-dark"><?php echo date('d M Y', strtotime($berita['tanggal'])); ?></span>
                                        </div>
                                    </div>

                                    <!-- Tombol Simpan -->
                                    <button type="submit"
                                        class="w-full bg-primary hover:bg-primary-dark text-white py-2.5 rounded-lg font-semibold text-sm transition-colors flex items-center justify-center gap-2 shadow-sm">
                                        <i data-lucide="save" class="w-4 h-4"></i>
                                        Simpan Perubahan
                                    </button>

                                    <!-- Tombol Batal -->
                                    <a href="daftarBerita.php"
                                        class="w-full flex items-center justify-center gap-2 py-2.5 rounded-lg font-semibold text-sm text-muted bg-slate-100 hover:bg-slate-200 transition-colors">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                        Batal
                                    </a>

                                    <!-- Tombol Hapus -->
                                    <button type="button" onclick="konfirmasiHapus()"
                                        class="w-full flex items-center justify-center gap-2 py-2.5 rounded-lg font-semibold text-sm text-danger bg-red-50 hover:bg-red-100 transition-colors border border-danger/20">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        Hapus Berita
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>

            </div>
        </main>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div id="modalHapus" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-dark/60">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 text-center">
            <div class="w-14 h-14 rounded-full bg-danger/10 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="trash-2" class="w-7 h-7 text-danger"></i>
            </div>
            <h3 class="font-bold text-dark text-lg mb-2">Hapus Berita?</h3>
            <p class="text-muted text-sm mb-6">Berita "<strong class="text-dark"><?php echo htmlspecialchars($berita['judul']); ?></strong>" akan dihapus permanen dan tidak bisa dikembalikan.</p>
            <div class="flex gap-3">
                <button onclick="tutupModalHapus()"
                    class="flex-1 py-2.5 rounded-lg font-semibold text-sm text-muted bg-slate-100 hover:bg-slate-200 transition-colors">
                    Batal
                </button>
                <a href="daftarBerita.php?hapus=<?php echo $berita['id']; ?>"
                    class="flex-1 py-2.5 rounded-lg font-semibold text-sm text-white bg-danger hover:bg-red-700 transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="trash-2" class="w-4 h-4"></i> Ya, Hapus
                </a>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        /* ---- Sidebar ---- */
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        /* ---- Dropdown ---- */
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

        /* ---- Preview Gambar ---- */
        function previewGambar(input) {
            if (!input.files || !input.files[0]) return;
            const file   = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const container = document.getElementById('previewContainer');
                container.innerHTML = `
                    <div class="relative rounded-lg overflow-hidden border border-primary/30 bg-slate-100">
                        <img src="${e.target.result}" alt="Preview" class="w-full aspect-video object-cover">
                        <div class="absolute top-2 right-2">
                            <span class="bg-warning text-white text-[10px] font-bold px-2 py-0.5 rounded-full">Foto Baru</span>
                        </div>
                    </div>`;
            };
            reader.readAsDataURL(file);

            // Tampilkan nama file
            const namaFileEl  = document.getElementById('namaFile');
            const namaTeksEl  = document.getElementById('namaFileTeks');
            namaFileEl.classList.remove('hidden');
            namaTeksEl.textContent = file.name;
            lucide.createIcons();
        }

        function hapusPilihan() {
            document.getElementById('foto').value = '';
            document.getElementById('namaFile').classList.add('hidden');
            // Kembalikan preview ke foto lama
            <?php if (!empty($berita['foto'])): ?>
            document.getElementById('previewContainer').innerHTML = `
                <div class="relative rounded-lg overflow-hidden border border-slate-200 bg-slate-100">
                    <img src="../uploads/foto_berita/<?php echo $berita['foto']; ?>" alt="Preview" class="w-full aspect-video object-cover">
                    <div class="absolute top-2 right-2">
                        <span class="bg-secondary text-white text-[10px] font-bold px-2 py-0.5 rounded-full">Foto Saat Ini</span>
                    </div>
                </div>`;
            <?php else: ?>
            document.getElementById('previewContainer').innerHTML = `
                <div class="w-full aspect-video bg-slate-100 rounded-lg border border-slate-200 flex items-center justify-center">
                    <div class="text-center">
                        <i data-lucide="image-off" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                        <p class="text-xs text-muted">Belum ada foto</p>
                    </div>
                </div>`;
            <?php endif; ?>
            lucide.createIcons();
        }

        /* ---- Drag & Drop ---- */
        function handleDragOver(e) {
            e.preventDefault();
            document.getElementById('dropZone').classList.add('drag-over');
        }
        function handleDragLeave(e) {
            document.getElementById('dropZone').classList.remove('drag-over');
        }
        function handleDrop(e) {
            e.preventDefault();
            document.getElementById('dropZone').classList.remove('drag-over');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const input = document.getElementById('foto');
                const dt    = new DataTransfer();
                dt.items.add(files[0]);
                input.files = dt.files;
                previewGambar(input);
            }
        }

        /* ---- Char Counter ---- */
        const isiEl     = document.getElementById('isi');
        const countEl   = document.getElementById('charCount');
        function updateCount() {
            const len = isiEl.value.length;
            countEl.textContent = len.toLocaleString('id-ID') + ' karakter';
            countEl.className   = len < 50
                ? 'text-xs text-danger mt-1.5'
                : 'text-xs text-muted mt-1.5';
        }
        isiEl.addEventListener('input', updateCount);
        updateCount(); // init

        /* ---- Modal Hapus ---- */
        function konfirmasiHapus() {
            document.getElementById('modalHapus').classList.remove('hidden');
        }
        function tutupModalHapus() {
            document.getElementById('modalHapus').classList.add('hidden');
        }
        document.getElementById('modalHapus').addEventListener('click', function(e) {
            if (e.target === this) tutupModalHapus();
        });
    </script>
</body>
</html>