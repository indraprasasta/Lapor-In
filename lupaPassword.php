<?php
session_start();
require __DIR__ . '/database/conection.php';

// Jika sudah login, redirect
if (isset($_SESSION['user_id']) || isset($_SESSION['petugas_id'])) {
    header("Location: index.php");
    exit();
}

$pesan_error  = '';
$pesan_sukses = '';

// verifikasi identitas (step 1)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == '1') {

    $role     = $_POST['role'] ?? '';
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $verif    = mysqli_real_escape_string($koneksi, trim($_POST['verifikasi']));

    if ($role === 'masyarakat') {
        // Verifikasi dengan NIK
        $query = mysqli_query($koneksi,
            "SELECT id, nama FROM users WHERE username = '$username' AND nik = '$verif'"
        );
        if (mysqli_num_rows($query) === 1) {
            $data = mysqli_fetch_assoc($query);
            $_SESSION['reset_step'] = 2;
            $_SESSION['reset_id']   = $data['id'];
            $_SESSION['reset_role'] = 'masyarakat';
            $_SESSION['reset_nama'] = $data['nama'];
        } else {
            $pesan_error = 'Username atau NIK tidak cocok. Periksa kembali data Anda.';
        }

    } elseif ($role === 'petugas') {
        // Verifikasi dengan NIP
        $query = mysqli_query($koneksi,
            "SELECT id, nama FROM petugas WHERE username = '$username' AND nip = '$verif'"
        );
        if (mysqli_num_rows($query) === 1) {
            $data = mysqli_fetch_assoc($query);
            $_SESSION['reset_step'] = 2;
            $_SESSION['reset_id']   = $data['id'];
            $_SESSION['reset_role'] = 'petugas';
            $_SESSION['reset_nama'] = $data['nama'];
        } else {
            $pesan_error = 'Username atau NIP tidak cocok. Periksa kembali data Anda.';
        }

    } else {
        $pesan_error = 'Pilih peran Anda terlebih dahulu.';
    }
}

// STEP 2 — Reset password baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == '2') {

    if (!isset($_SESSION['reset_step']) || $_SESSION['reset_step'] != 2) {
        header("Location: lupaPassword.php");
        exit();
    }

    $password_baru    = $_POST['password_baru'] ?? '';
    $password_konfirm = $_POST['password_konfirm'] ?? '';

    if (empty($password_baru) || empty($password_konfirm)) {
        $pesan_error = 'Password tidak boleh kosong.';

    } elseif (strlen($password_baru) < 6) {
        $pesan_error = 'Password minimal 6 karakter.';

    } elseif ($password_baru !== $password_konfirm) {
        $pesan_error = 'Konfirmasi password tidak cocok.';

    } else {
        $id    = $_SESSION['reset_id'];
        $role  = $_SESSION['reset_role'];
        $pass  = mysqli_real_escape_string($koneksi, $password_baru);
        $tabel = $role === 'petugas' ? 'petugas' : 'users';

        $update = mysqli_query($koneksi,
            "UPDATE $tabel SET password = '$pass' WHERE id = '$id'"
        );

        if ($update) {
            // Bersihkan session reset
            unset($_SESSION['reset_step'], $_SESSION['reset_id'],
                $_SESSION['reset_role'], $_SESSION['reset_nama']);
            $pesan_sukses = 'Password berhasil diperbarui! Silakan login kembali.';
        } else {
            $pesan_error = 'Gagal memperbarui password. Coba lagi.';
        }
    }
}

// Tentukan step saat ini untuk tampilan
$step_sekarang = $_SESSION['reset_step'] ?? 1;
$nama_user     = $_SESSION['reset_nama'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - LaporIn</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3A5A40',
                        accent:  '#A3B18A',
                    },
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Poppins', sans-serif; }

        /* Animasi pergantian step */
        .step-box { animation: fadeSlideUp 0.35s ease; }
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Radio role styling */
        .role-option input[type="radio"]:checked + label {
            border-color: #3A5A40;
            background-color: #f0f4f1;
            color: #3A5A40;
        }
        .role-option input[type="radio"]:checked + label .role-dot {
            background-color: #3A5A40;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">

    <!-- Background -->
    <img src="https://i.pinimg.com/1200x/fc/02/64/fc026433a20db53bc4447d4e41f8f830.jpg"
        alt="Background"
        class="absolute w-full  h-700px object-cover top-0 left-0 -z-10">
    <div class="absolute w-full h-full bg-black/40 top-0 left-0 -z-10"></div>

    <!-- Card -->
    <div class="w-full max-w-[400px] bg-gradient-to-t from-[#eef2f0] to-white rounded-[40px] p-8 border-[5px] border-white shadow-[0_30px_30px_-20px_rgba(58,90,64,0.3)] relative step-box">

        <!-- Tombol Tutup -->
        <a href="login.php"
            class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-red-100 text-gray-400 hover:text-red-500 transition-colors">
            <i class="fa-solid fa-xmark text-sm"></i>
        </a>

        <!-- ===================== STEP INDICATOR ===================== -->
        <div class="flex items-center justify-center gap-2 mb-6">
            <!-- Step 1 -->
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                    <?php echo $step_sekarang == 1 ? 'bg-primary text-white' : 'bg-accent text-white'; ?>">
                    <?php echo $step_sekarang > 1 ? '<i class="fa-solid fa-check text-[10px]"></i>' : '1'; ?>
                </div>
                <span class="text-xs font-medium <?php echo $step_sekarang == 1 ? 'text-primary' : 'text-accent'; ?>">
                    Verifikasi
                </span>
            </div>
            <!-- Garis -->
            <div class="w-8 h-0.5 <?php echo $step_sekarang == 2 ? 'bg-primary' : 'bg-slate-200'; ?> rounded-full"></div>
            <!-- Step 2 -->
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                    <?php echo $step_sekarang == 2 ? 'bg-primary text-white' : 'bg-slate-200 text-slate-400'; ?>">
                    2
                </div>
                <span class="text-xs font-medium <?php echo $step_sekarang == 2 ? 'text-primary' : 'text-slate-400'; ?>">
                    Password Baru
                </span>
            </div>
        </div>

        <?php if ($step_sekarang == 1): ?>
        <!-- ===================== STEP 1 ===================== -->

            <!-- Header -->
            <div class="text-center mb-6">
                <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-lock text-primary text-xl"></i>
                </div>
                <h2 class="font-bold text-2xl text-primary tracking-tight">Lupa Password?</h2>
                <p class="text-gray-400 text-xs mt-1">Verifikasi identitas Anda untuk melanjutkan</p>
            </div>

            <!-- Error -->
            <?php if ($pesan_error): ?>
            <div class="bg-red-50 border border-red-300 text-red-600 px-4 py-3 rounded-[15px] text-xs text-center mb-4 flex items-center gap-2 justify-center">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?php echo $pesan_error; ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <input type="hidden" name="step" value="1">

                <!-- Pilih Role -->
                <div>
                    <p class="text-xs font-semibold text-gray-500 mb-2 ml-1">Saya adalah:</p>
                    <div class="grid grid-cols-2 gap-2">
                        <!-- Masyarakat -->
                        <div class="role-option">
                            <input type="radio" name="role" id="role_masyarakat" value="masyarakat"
                                class="hidden" required>
                            <label for="role_masyarakat"
                                class="flex items-center gap-2 px-4 py-3 rounded-[16px] border-2 border-slate-200 bg-white cursor-pointer transition-all text-sm font-medium text-gray-500 hover:border-accent">
                                <span class="role-dot w-2 h-2 rounded-full bg-slate-300 shrink-0 transition-colors"></span>
                                Masyarakat
                            </label>
                        </div>
                        <!-- Petugas -->
                        <div class="role-option">
                            <input type="radio" name="role" id="role_petugas" value="petugas"
                                class="hidden">
                            <label for="role_petugas"
                                class="flex items-center gap-2 px-4 py-3 rounded-[16px] border-2 border-slate-200 bg-white cursor-pointer transition-all text-sm font-medium text-gray-500 hover:border-accent">
                                <span class="role-dot w-2 h-2 rounded-full bg-slate-300 shrink-0 transition-colors"></span>
                                Petugas
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Username -->
                <div>
                    <input type="text" name="username" required
                        placeholder="Username"
                        class="w-full bg-white border-y-0 border-x-2 border-transparent p-[13px_20px] rounded-[20px] shadow-[0_10px_10px_-5px_rgba(163,177,138,0.2)] focus:outline-none focus:border-x-accent placeholder-gray-400 text-sm transition-all">
                </div>

                <!-- Verifikasi (NIK / NIP) — label berubah sesuai role -->
                <div>
                    <input type="text" name="verifikasi" id="inputVerifikasi" required
                        placeholder="NIK (untuk Masyarakat) / NIP (untuk Petugas)"
                        class="w-full bg-white border-y-0 border-x-2 border-transparent p-[13px_20px] rounded-[20px] shadow-[0_10px_10px_-5px_rgba(163,177,138,0.2)] focus:outline-none focus:border-x-accent placeholder-gray-400 text-sm transition-all">
                    <p class="text-[10px] text-gray-400 ml-2 mt-1" id="hintVerifikasi">
                        Masukkan NIK atau NIP sesuai peran yang dipilih
                    </p>
                </div>

                <!-- Tombol Lanjut -->
                <button type="submit"
                    class="w-full font-bold bg-primary text-white py-4 rounded-[20px] shadow-[0_20px_10px_-15px_rgba(58,90,64,0.4)] transition-all duration-200 hover:scale-[1.03] hover:bg-opacity-90 active:scale-95 mt-2">
                    Lanjut Verifikasi
                </button>
            </form>

            <div class="mt-5 text-center">
                <a href="login.php" class="text-[11px] text-accent hover:text-primary transition-colors hover:underline">
                    <i class="fa-solid fa-arrow-left text-[10px] mr-1"></i> Kembali ke Login
                </a>
            </div>

        <?php elseif ($step_sekarang == 2): ?>
        <!-- ===================== STEP 2 ===================== -->

            <!-- Header -->
            <div class="text-center mb-6">
                <div class="w-14 h-14 bg-accent/30 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-key text-primary text-xl"></i>
                </div>
                <h2 class="font-bold text-2xl text-primary tracking-tight">Password Baru</h2>
                <p class="text-gray-400 text-xs mt-1">
                    Halo, <span class="font-semibold text-primary"><?php echo htmlspecialchars($nama_user); ?></span>! Buat password baru Anda
                </p>
            </div>

            <!-- Error -->
            <?php if ($pesan_error): ?>
            <div class="bg-red-50 border border-red-300 text-red-600 px-4 py-3 rounded-[15px] text-xs text-center mb-4 flex items-center gap-2 justify-center">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?php echo $pesan_error; ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <input type="hidden" name="step" value="2">

                <!-- Password Baru -->
                <div class="relative">
                    <input type="password" name="password_baru" id="passwordBaru" required
                        placeholder="Password baru (min. 6 karakter)"
                        class="w-full bg-white border-y-0 border-x-2 border-transparent p-[13px_20px] rounded-[20px] shadow-[0_10px_10px_-5px_rgba(163,177,138,0.2)] focus:outline-none focus:border-x-accent placeholder-gray-400 text-sm transition-all pr-12">
                    <button type="button" onclick="togglePass('passwordBaru', 'eyeBaru')"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-primary transition-colors">
                        <i id="eyeBaru" class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <!-- Konfirmasi Password -->
                <div class="relative">
                    <input type="password" name="password_konfirm" id="passwordKonfirm" required
                        placeholder="Konfirmasi password baru"
                        class="w-full bg-white border-y-0 border-x-2 border-transparent p-[13px_20px] rounded-[20px] shadow-[0_10px_10px_-5px_rgba(163,177,138,0.2)] focus:outline-none focus:border-x-accent placeholder-gray-400 text-sm transition-all pr-12"
                        oninput="cekKonfirmasi()">
                    <button type="button" onclick="togglePass('passwordKonfirm', 'eyeKonfirm')"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-primary transition-colors">
                        <i id="eyeKonfirm" class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <!-- Indikator cocok/tidak -->
                <p id="pesanKonfirmasi" class="text-[11px] ml-2 hidden"></p>

                <!-- Strength Indicator -->
                <div>
                    <div class="flex gap-1 mt-1">
                        <div class="h-1 flex-1 rounded-full bg-slate-200" id="bar1"></div>
                        <div class="h-1 flex-1 rounded-full bg-slate-200" id="bar2"></div>
                        <div class="h-1 flex-1 rounded-full bg-slate-200" id="bar3"></div>
                        <div class="h-1 flex-1 rounded-full bg-slate-200" id="bar4"></div>
                    </div>
                    <p class="text-[10px] text-gray-400 ml-1 mt-1" id="strengthText">Kekuatan password</p>
                </div>

                <!-- Tombol Simpan -->
                <button type="submit"
                    class="w-full font-bold bg-primary text-white py-4 rounded-[20px] shadow-[0_20px_10px_-15px_rgba(58,90,64,0.4)] transition-all duration-200 hover:scale-[1.03] hover:bg-opacity-90 active:scale-95 mt-2">
                    Simpan Password Baru
                </button>
            </form>

            <!-- Batalkan reset -->
            <div class="mt-5 text-center">
                <a href="lupaPassword.php?reset=1"
                    onclick="return confirm('Batalkan proses reset password?')"
                    class="text-[11px] text-gray-400 hover:text-danger transition-colors hover:underline">
                    <i class="fa-solid fa-rotate-left text-[10px] mr-1"></i> Mulai ulang
                </a>
            </div>

        <?php endif; ?>
    </div>

    <?php
    // Handle batalkan reset (hapus session)
    if (isset($_GET['reset'])) {
        unset($_SESSION['reset_step'], $_SESSION['reset_id'],
              $_SESSION['reset_role'], $_SESSION['reset_nama']);
        header("Location: lupaPassword.php");
        exit();
    }
    ?>

    <!-- ===== SWEETALERT NOTIFIKASI ===== -->
    <?php if ($pesan_sukses != ''): ?>
    <script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= $pesan_sukses ?>',
        confirmButtonColor: '#3A5A40',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    }).then(() => {
        window.location.href = 'login.php';
    });
    </script>
    <?php endif; ?>

    <script>
    /* ---- Toggle Password Visibility ---- */
    function togglePass(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    /* ---- Placeholder verifikasi berubah sesuai role ---- */
    const radios    = document.querySelectorAll('input[name="role"]');
    const inputVerf = document.getElementById('inputVerifikasi');
    const hintVerf  = document.getElementById('hintVerifikasi');

    radios?.forEach(r => {
        r.addEventListener('change', () => {
            if (r.value === 'masyarakat') {
                inputVerf.placeholder = 'Masukkan NIK Anda';
                hintVerf.textContent  = 'NIK terdaftar saat pendaftaran akun';
            } else {
                inputVerf.placeholder = 'Masukkan NIP Anda';
                hintVerf.textContent  = 'NIP sesuai data petugas di sistem';
            }
        });
    });

    /* ---- Cek Konfirmasi Password ---- */
    function cekKonfirmasi() {
        const baru    = document.getElementById('passwordBaru').value;
        const konfirm = document.getElementById('passwordKonfirm').value;
        const pesan   = document.getElementById('pesanKonfirmasi');

        if (konfirm.length === 0) {
            pesan.classList.add('hidden');
            return;
        }
        pesan.classList.remove('hidden');
        if (baru === konfirm) {
            pesan.textContent  = '✓ Password cocok';
            pesan.className    = 'text-[11px] ml-2 text-green-500';
        } else {
            pesan.textContent  = '✗ Password tidak cocok';
            pesan.className    = 'text-[11px] ml-2 text-red-500';
        }
    }

    /* ---- Password Strength Indicator ---- */
    document.getElementById('passwordBaru')?.addEventListener('input', function() {
        const val    = this.value;
        const bars   = [document.getElementById('bar1'), document.getElementById('bar2'),
                        document.getElementById('bar3'), document.getElementById('bar4')];
        const text   = document.getElementById('strengthText');
        const colors = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-green-500'];
        const labels = ['Sangat Lemah', 'Lemah', 'Cukup', 'Kuat'];

        let score = 0;
        if (val.length >= 6)                      score++;
        if (val.length >= 10)                     score++;
        if (/[A-Z]/.test(val) && /[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val))             score++;

        bars.forEach((bar, i) => {
            bar.className = 'h-1 flex-1 rounded-full ';
            bar.className += i < score ? colors[score - 1] : 'bg-slate-200';
        });

        text.textContent = val.length === 0 ? 'Kekuatan password' : labels[score - 1] || 'Sangat Lemah';
    });
    </script>
</body>
</html>