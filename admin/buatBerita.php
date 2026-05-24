<?php
session_start();

// Cek sesi admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

require __DIR__ . '/../database/conection.php';

$admin_nama = $_SESSION['admin_nama'];
$pesan_error = "";
$pesan_berhasil = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = trim($_POST['judul']);
    $isi = trim($_POST['isi']);
    $kategori = trim($_POST['kategori']);

    // Proses unggah foto

    $nama_foto = "";
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $folder = __DIR__ . '/../uploads/foto_berita/';
        $nama_file = basename($_FILES['foto']['name']);
        $nama_foto = time() . '_' . preg_replace("/[^a-zA-Z0-9.-]/", "_", $nama_file);
        $ukuran_file = $_FILES['foto']['size'];
        $tmp_name = $_FILES['foto']['tmp_name'];

        // Ekstensi file diizinkan
        $ekstensi_diperbolehkan = ['jpg', 'jpeg', 'png', 'webp'];
        $x = explode('.', $nama_file);
        $ekstensi = strtolower(end($x));

        if (!in_array($ekstensi, $ekstensi_diperbolehkan)) {
            $pesan_error = "Format foto tidak didukung! Harus JPG, JPEG, PNG, atau WEBP.";
        } elseif ($ukuran_file > 5 * 1024 * 1024) {
            $pesan_error = "Ukuran foto maksimal 5MB!";
        } else {
            if (!move_uploaded_file($tmp_name, $folder . $nama_foto)) {
                $pesan_error = "Gagal mengunggah foto ke server.";
            }
        }
    } elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        $pesan_error = "Terjadi kesalahan saat mengunggah foto (Error Code: " . $_FILES['foto']['error'] . ").";
    }

    if ($pesan_error == "") {
        $stmt = $pdo->prepare("INSERT INTO berita (judul, isi, foto, kategori) VALUES (:judul, :isi, :foto, :kategori)");
        $success = $stmt->execute([
            ':judul' => $judul,
            ':isi' => $isi,
            ':foto' => $nama_foto,
            ':kategori' => $kategori
        ]);

        if ($success) {
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
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
    <?php include 'sidebar.php'; ?>

    <!-- Main -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-16 bg-accent border-b border-slate-200 flex items-center px-6 z-30">
            <h1 class="text-lg font-bold text-dark">Buat Berita Baru</h1>
        </header>

        <main class="flex-1 overflow-y-auto bg-light p-6">
            <div class="max-w-3xl mx-auto">

                <?php if ($pesan_error != ""): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm mb-6">
                        <?php echo $pesan_error; ?>
                    </div>
                <?php endif; ?>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <form action="" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">

                        <!-- Judul -->
                        <div>
                            <label class="block text-sm font-semibold text-dark mb-2">Judul Berita <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="judul" required maxlength="200"
                                placeholder="Masukkan judul berita..."
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none text-sm transition-colors">
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label class="block text-sm font-semibold text-dark mb-2">Kategori <span
                                    class="text-danger">*</span></label>
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
                            <label class="block text-sm font-semibold text-dark mb-2">Foto Berita <span
                                    class="text-danger">*</span></label>
                            <div onclick="document.getElementById('foto').click()"
                                class="cursor-pointer border-2 border-dashed border-slate-300 rounded-lg p-6 text-center hover:border-primary/50 transition-colors bg-slate-50">
                                <i data-lucide="image-plus" class="w-10 h-10 text-slate-400 mx-auto mb-3"></i>
                                <span class="cursor-pointer text-primary font-medium text-sm hover:underline">
                                    Pilih foto
                                    <input type="file" id="foto" name="foto" accept="image/*" class="hidden"
                                        onchange="previewFoto(this)">
                                </span>
                                <p class="text-xs text-muted mt-1">Format: JPG, PNG, WEBP. Maks 5MB</p>
                            </div>
                            <!-- Preview foto -->
                            <div id="preview_container" class="hidden mt-3">
                                <img id="preview_foto" src="" alt="Preview"
                                    class="w-full h-48 object-cover rounded-lg border border-slate-200">
                            </div>
                        </div>

                        <!-- Isi Berita -->
                        <div>
                            <label class="block text-sm font-semibold text-dark mb-2">Isi Berita <span
                                    class="text-danger">*</span></label>
                            <textarea name="isi" rows="8" required placeholder="Tulis isi berita di sini..."
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none text-sm resize-y transition-colors"></textarea>
                        </div>

                        <!-- Tombol -->
                        <div
                            class="pt-4 flex flex-col-reverse sm:flex-row gap-3 sm:justify-end border-t border-slate-100">
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
    <?php if (isset($_GET['added'])): ?>
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
                reader.onload = function (e) {
                    document.getElementById('preview_foto').src = e.target.result;
                    document.getElementById('preview_container').classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

    </script>
</body>

</html>