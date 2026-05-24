<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$admin_nama = $_SESSION['admin_nama'];
require __DIR__ . '/../database/conection.php';

$pesan_error = '';
$pesan_sukses = '';

if (!isset($_GET['id'])) {
    header("Location: dataPetugas.php");
    exit();
}

$id = (int) $_GET['id'];

// Ambil data dinas
$query_dinas = $pdo->query("SELECT * FROM dinas ORDER BY nama_dinas ASC");

// Ambil data petugas saat ini
$stmt_petugas = $pdo->prepare("SELECT * FROM petugas WHERE id = :id");
$stmt_petugas->execute([':id' => $id]);
$petugas = $stmt_petugas->fetch();

if (!$petugas) {
    header("Location: dataPetugas.php");
    exit();
}

// Cek kiriman form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama     = trim($_POST['nama']);
    $nip      = trim($_POST['nip']);
    $username = trim($_POST['username']);
    $password = $_POST['password']; // Jika tidak diisi, gunakan password lama
    $jabatan  = trim($_POST['jabatan']);
    $dinas_id = (int) $_POST['dinas_id'];

    // Cek duplikat data
    $stmt_cek = $pdo->prepare("SELECT id FROM petugas WHERE (username = :username OR nip = :nip) AND id != :id");
    $stmt_cek->execute([':username' => $username, ':nip' => $nip, ':id' => $id]);

    if ($stmt_cek->rowCount() > 0) {
        $pesan_error = "Username atau NIP sudah terdaftar pada petugas lain!";
    } else {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE petugas SET nama = :nama, nip = :nip, username = :username, password = :password, jabatan = :jabatan, dinas_id = :dinas_id WHERE id = :id");
            $params = [
                ':nama' => $nama,
                ':nip' => $nip,
                ':username' => $username,
                ':password' => $hashed_password,
                ':jabatan' => $jabatan,
                ':dinas_id' => $dinas_id,
                ':id' => $id
            ];
        } else {
            $stmt = $pdo->prepare("UPDATE petugas SET nama = :nama, nip = :nip, username = :username, jabatan = :jabatan, dinas_id = :dinas_id WHERE id = :id");
            $params = [
                ':nama' => $nama,
                ':nip' => $nip,
                ':username' => $username,
                ':jabatan' => $jabatan,
                ':dinas_id' => $dinas_id,
                ':id' => $id
            ];
        }

        $success = $stmt->execute($params);

        if ($success) {
            echo "<script>
                alert('Data petugas berhasil diperbarui!');
                window.location.href = 'datapetugas.php';
            </script>";
            exit();
        } else {
            $pesan_error = "Gagal menyimpan data!";
        }
    }
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Petugas - LaporIn Mataram</title>
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

    <?php include 'sidebar.php'; ?>

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
                    <span class="text-primary font-bold">Edit Petugas</span>
                </nav>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto bg-light p-4 sm:p-6 lg:p-8">
            <div class="max-w-3xl mx-auto space-y-6">
                
                <div>
                    <h2 class="text-2xl font-bold text-dark tracking-tight">Edit Petugas</h2>
                    <p class="text-muted mt-1 text-sm">Ubah informasi untuk petugas lapangan.</p>
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
                            <i data-lucide="edit" class="w-4 h-4 mr-2 text-primary"></i>
                            Informasi Petugas & Kredensial Akun
                        </h3>
                    </div>
                    
                    <form action="" method="POST" class="p-6 space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div class="space-y-1.5">
                                <label for="nama" class="block text-sm font-semibold text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" id="nama" name="nama" required value="<?php echo htmlspecialchars($petugas['nama'] ?? ''); ?>"
                                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm text-dark placeholder:text-slate-400">
                            </div>

                            <div class="space-y-1.5">
                                <label for="nip" class="block text-sm font-semibold text-dark">NIP / Nomor Pegawai <span class="text-danger">*</span></label>
                                <input type="text" id="nip" name="nip" required value="<?php echo htmlspecialchars($petugas['nip'] ?? ''); ?>"
                                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm text-dark placeholder:text-slate-400">
                            </div>

                            <div class="space-y-1.5">
                                <label for="username" class="block text-sm font-semibold text-dark">Username Akun <span class="text-danger">*</span></label>
                                <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($petugas['username'] ?? ''); ?>"
                                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm text-dark placeholder:text-slate-400">
                                <p class="text-[10px] text-muted">Username ini akan digunakan untuk login petugas.</p>
                            </div>

                            <div class="space-y-1.5">
                                <label for="password" class="block text-sm font-semibold text-dark">Password Baru</label>
                                <input type="password" id="password" name="password" placeholder="Biarkan kosong jika tidak ingin mengubah password"
                                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm text-dark placeholder:text-slate-400">
                                <p class="text-[10px] text-muted">Isi jika ingin mengganti password lama.</p>
                            </div>
                            
                            <div class="space-y-1.5 md:col-span-2">
                                <label for="jabatan" class="block text-sm font-semibold text-dark">Jabatan <span class="text-danger">*</span></label>
                                <input type="text" id="jabatan" name="jabatan" required value="<?php echo htmlspecialchars($petugas['jabatan'] ?? ''); ?>"
                                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm text-dark placeholder:text-slate-400">
                            </div>

                        </div>

                        <div class="border-t border-slate-200"></div>

                        <div class="space-y-1.5">
                            <label for="dinas_id" class="block text-sm font-semibold text-dark">Penempatan Instansi / Dinas <span class="text-danger">*</span></label>
                            <div class="relative">
                                <select id="dinas_id" name="dinas_id" required
                                    class="w-full pl-4 pr-10 py-2.5 appearance-none rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm bg-white text-dark">
                                    <option value="" disabled>-- Pilih Dinas --</option>
                                    <?php while($dinas = $query_dinas->fetch()): ?>
                                        <option value="<?php echo $dinas['id']; ?>" <?php echo ($petugas['dinas_id'] == $dinas['id']) ? 'selected' : ''; ?>>
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
                                Simpan Perubahan
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
