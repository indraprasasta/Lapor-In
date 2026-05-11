<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require __DIR__ . '/../database/conection.php';

$user_id = $_SESSION['user_id'];

// Cek ID laporan
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: daftarLaporan.php");
    exit();
}

$id_laporan = $_GET['id'];

// Ambil data laporan
$query_laporan = mysqli_query($koneksi, "SELECT * FROM laporan WHERE id = '$id_laporan' AND user_id = '$user_id'");

if (mysqli_num_rows($query_laporan) == 0) {
    header("Location: daftarLaporan.php");
    exit();
}

$laporan = mysqli_fetch_assoc($query_laporan);

// Hanya bisa edit jika status Menunggu
if ($laporan['status'] != 'Menunggu') {
    echo "<script>alert('Laporan tidak dapat diedit karena sudah diproses!'); window.location.href = 'detailLaporan.php?id=$id_laporan';</script>";
    exit();
}

$pesan_error = "";

// Proses update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul     = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $kategori  = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $alamat    = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $kecamatan = mysqli_real_escape_string($koneksi, $_POST['kecamatan']);
    $kelurahan = mysqli_real_escape_string($koneksi, $_POST['kelurahan']);

    // Proses upload foto baru jika ada
    $foto_query = "";
    if (!empty($_FILES['foto']['name'])) {
        $folder      = __DIR__ . '/../uploads/foto_laporan/';
        $nama_foto   = time() . '_' . $_FILES['foto']['name'];
        $tipe_file   = $_FILES['foto']['type'];
        $ukuran_file = $_FILES['foto']['size'];

        $tipe_allowed = ['image/jpeg', 'image/png', 'image/jpg'];

        if (!in_array($tipe_file, $tipe_allowed)) {
            $pesan_error = "Format foto tidak didukung!";
        } elseif ($ukuran_file > 5 * 1024 * 1024) {
            $pesan_error = "Ukuran foto maksimal 5MB!";
        } else {
            // Hapus foto lama
            if (!empty($laporan['foto']) && file_exists($folder . $laporan['foto'])) {
                unlink($folder . $laporan['foto']);
            }
            move_uploaded_file($_FILES['foto']['tmp_name'], $folder . $nama_foto);
            $foto_query = ", foto = '$nama_foto'";
        }
    }

    if ($pesan_error == "") {
        $query = "UPDATE laporan SET judul='$judul', kategori='$kategori', deskripsi='$deskripsi', alamat='$alamat', kecamatan='$kecamatan', kelurahan='$kelurahan' $foto_query WHERE id='$id_laporan' AND user_id='$user_id'";

        if (mysqli_query($koneksi, $query)) {
            echo "<script>
                alert('Laporan berhasil diperbarui!');
                window.location.href = 'detailLaporan.php?id=$id_laporan';
            </script>";
            exit();
        } else {
            $pesan_error = "Gagal memperbarui laporan!";
        }
    }
}

// Ambil data user
$query_user = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$user       = mysqli_fetch_assoc($query_user);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laporan - LaporIn</title>
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
                        dark: '#1E293B',
                        light: '#F8FAFC',
                        muted: '#94A3B8',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-primary text-dark font-sans min-h-screen">

    <div class="max-w-3xl mx-auto p-4 sm:p-6 lg:p-8">

        <!-- Header -->
        <div class="flex items-center gap-4 mb-6">
            <button onclick="history.back()"
                class="p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </button>
            <div>
                <h2 class="text-2xl font-bold text-white">Edit Laporan</h2>
                <p class="text-accent text-sm mt-1">Perbarui informasi laporan Anda</p>
            </div>
        </div>

        <!-- Pesan Error -->
        <?php if($pesan_error != ""): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm mb-6">
            <?php echo $pesan_error; ?>
        </div>
        <?php endif; ?>

        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <form action="" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">

                <!-- Judul -->
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Judul Laporan</label>
                    <input type="text" name="judul" required maxlength="100"
                        value="<?php echo $laporan['judul']; ?>"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none text-sm">
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Kategori</label>
                    <select name="kategori" required
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none text-sm bg-white">
                        <?php
                        $kategoris = ['Jalan Rusak','Pohon Tumbang','Lampu Jalan Mati','Saluran Air','Jembatan','Trotoar','Fasilitas Umum','Lainnya'];
                        foreach($kategoris as $kat):
                        ?>
                        <option value="<?php echo $kat; ?>" <?php echo $laporan['kategori'] == $kat ? 'selected' : ''; ?>>
                            <?php echo $kat; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="4" required maxlength="1000"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none text-sm resize-y"><?php echo $laporan['deskripsi']; ?></textarea>
                </div>

                <!-- Foto -->
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Foto Bukti</label>
                    <?php if(!empty($laporan['foto'])): ?>
                    <div class="mb-3">
                        <img src="../uploads/foto_laporan/<?php echo $laporan['foto']; ?>"
                            alt="Foto saat ini" class="h-32 rounded-lg object-cover border border-slate-200">
                        <p class="text-xs text-muted mt-1">Foto saat ini. Upload baru untuk mengganti.</p>
                    </div>
                    <?php endif; ?>
                    <input type="file" name="foto" accept="image/jpeg, image/png"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm">
                </div>

                <!-- Alamat -->
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Alamat Lengkap</label>
                    <textarea name="alamat" rows="2" required
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none text-sm resize-y"><?php echo $laporan['alamat']; ?></textarea>
                </div>

                <!-- Kecamatan & Kelurahan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-dark mb-2">Kecamatan</label>
                        <input type="text" name="kecamatan" required
                            value="<?php echo $laporan['kecamatan']; ?>"
                            class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-dark mb-2">Kelurahan</label>
                        <input type="text" name="kelurahan" required
                            value="<?php echo $laporan['kelurahan']; ?>"
                            class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none text-sm">
                    </div>
                </div>

                <!-- Tombol -->
                <div class="pt-4 flex flex-col-reverse sm:flex-row gap-3 sm:justify-end border-t border-slate-100">
                    <button type="button" onclick="history.back()"
                        class="w-full sm:w-auto px-6 py-2.5 border border-slate-300 text-dark font-semibold rounded-lg hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="w-full sm:w-auto px-6 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors flex items-center justify-center">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>