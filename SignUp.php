<?php
session_start();

require __DIR__ . '/database/conection.php';

$pesan_error = "";
$pesan_sukses = "";

// Cek kiriman form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Tangkap data form
    $nama         = trim($_POST['nama']);
    $nik          = trim($_POST['nik']);
    $username     = trim($_POST['username']);
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $gender       = trim($_POST['gender']);
    $alamat       = trim($_POST['alamat']);

    // Validasi data ganda
    $stmt_cek = $pdo->prepare("SELECT * FROM users WHERE username = :username OR nik = :nik");
    $stmt_cek->execute([':username' => $username, ':nik' => $nik]);

    if (strlen($password) < 6) {
        $pesan_error = 'Password minimal 6 karakter.';

    } elseif ($stmt_cek->rowCount() > 0) {
        $pesan_error = "Pendaftaran Gagal! Username atau NIK sudah terdaftar.";
    } else {
        // Simpan data pendaftar
        $stmt_insert = $pdo->prepare("INSERT INTO users (nama, nik, username, password, gender, alamat) VALUES (:nama, :nik, :username, :password, :gender, :alamat)");

        // Eksekusi query database
        if ($stmt_insert->execute([':nama' => $nama, ':nik' => $nik, ':username' => $username, ':password' => $password_hash, ':gender' => $gender, ':alamat' => $alamat])) {
            // Berhasil daftar akun
            $pesan_sukses = "Pendaftaran berhasil! Silakan login.";
        } else {
            $pesan_error = "Terjadi kesalahan sistem!";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - LaporIn Mataram</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind Config for Custom Colors -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3A5A40',
                        accent: '#A3B18A',
                        graybg: '#f8fafc',
                    },
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .custom-input {
            background: #ffffff;
            border-bottom: 2px solid transparent;
            box-shadow: 0 10px 10px -5px rgba(163, 177, 138, 0.15);
            transition: all 0.3s ease;
        }
        .custom-input:focus {
            border-bottom-color: #A3B18A;
            box-shadow: 0 10px 15px -5px rgba(58, 90, 64, 0.2);
            outline: none;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 py-20">

    <img src="https://i.pinimg.com/1200x/fc/02/64/fc026433a20db53bc4447d4e41f8f830.jpg" 
        alt="Latar Belakang Kota Mataram"
        class="absolute w-full h-700px object-cover top-0 left-0 -z-10">

        <div class="absolute w-full h-full bg-black/40 top-0 left-0 -z-10"></div>

    <!-- Container Utama -->
    <div class="w-full max-w-[550px] bg-gradient-to-t from-[#eef2f0] to-white rounded-[40px] p-8 md:p-12 border-[5px] border-white shadow-[0_30px_50px_-20px_rgba(58,90,64,0.25)]">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="font-bold text-3xl text-primary tracking-tight">
                Daftar Akun <span class="text-accent">LaporIn</span>
            </h1>
            <p class="text-gray-400 text-sm mt-2 font-medium">Lengkapi data diri Anda untuk bergabung</p>
        </div>

        <!-- Area Pesan Error (Akan muncul jika ada masalah) -->
        <?php if($pesan_error != ""): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 text-sm text-center" role="alert">
                <span class="block sm:inline"><?php echo $pesan_error; ?></span>
            </div>
        <?php endif; ?>
        
        <!-- Perhatikan penambahan method="POST" dan action="" (action kosong berarti memproses di file yang sama) -->
        <form action="" autocomplete="off" class="space-y-6" method="POST" >
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <!-- Nama Lengkap -->
                <div class="relative group">
                    <i class="fa-solid fa-user absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-accent transition-colors"></i>
                    <!-- TAMBAHAN: name="nama_lengkap" -->
                    <input type="text" name="nama" required 
                        class="w-full custom-input pl-12 pr-5 py-4 rounded-[20px] text-sm text-primary placeholder-gray-300" 
                        placeholder="Nama Lengkap">
                </div>

                <!-- NIK -->
                <div class="relative group">
                    <i class="fa-solid fa-id-card absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-accent transition-colors"></i>
                    <!-- TAMBAHAN: name="nik" -->
                    <input type="text" name="nik" required minlength="16" maxlength="16"
                        class="w-full custom-input pl-12 pr-5 py-4 rounded-[20px] text-sm text-primary placeholder-gray-300" 
                        placeholder="NIK (16 Digit)">
                </div>

                <!-- Username -->
                <div class="relative group">
                    <i class="fa-solid fa-user absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-accent transition-colors"></i>
                    <!-- TAMBAHAN: name="username" -->
                    <input type="text" name="username" required 
                        class="w-full custom-input pl-12 pr-5 py-4 rounded-[20px] text-sm text-primary placeholder-gray-300" 
                        placeholder="Username">
                </div>

                <!-- Password -->
                <div class="relative group">
                    <i class="fa-solid fa-lock absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-accent transition-colors"></i>
                    <!-- TAMBAHAN: name="password" -->
                    <input type="password" name="password" required 
                        class="w-full custom-input pl-12 pr-5 py-4 rounded-[20px] text-sm text-primary placeholder-gray-300" 
                        autocomplete="new-password"
                        placeholder="Kata Sandi">
                </div>

                <!-- Jenis Kelamin -->
                <div class="md:col-span-2 flex flex-col sm:flex-row items-center gap-6 p-4 bg-white/50 rounded-[20px] border border-white/50">
                    <span class="text-sm text-gray-400 font-medium ml-2">Jenis Kelamin:</span>
                    <div class="flex gap-8">
                        <label class="flex items-center gap-2 text-sm text-primary cursor-pointer font-medium group">
                            <!-- name="gender" sudah benar -->
                            <input type="radio" name="gender" value="Laki-laki" required 
                                class="w-5 h-5 accent-primary cursor-pointer">
                            Laki-laki
                        </label>
                        <label class="flex items-center gap-2 text-sm text-primary cursor-pointer font-medium group">
                            <input type="radio" name="gender" value="Perempuan" required 
                                class="w-5 h-5 accent-primary cursor-pointer">
                            Perempuan
                        </label>
                    </div>
                </div>

                <!-- Alamat -->
                <div class="md:col-span-2 relative group">
                    <i class="fa-solid fa-location-dot absolute left-5 top-6 text-gray-300 group-focus-within:text-accent transition-colors"></i>
                    <!-- TAMBAHAN: name="alamat" -->
                    <textarea name="alamat" required 
                        class="w-full custom-input pl-12 pr-5 py-4 rounded-[20px] text-sm text-primary placeholder-gray-300 resize-none" 
                        rows="3" 
                        placeholder="Alamat Lengkap Tempat Tinggal Sekarang"></textarea>
                </div>
            </div>

            <!-- Tombol Daftar -->
            <button type="submit" name="submit_daftar"
                class="w-full font-bold bg-primary text-white py-4 mt-4 rounded-[20px] shadow-[0_20px_10px_-15px_rgba(58,90,64,0.4)] transition-all duration-200 hover:scale-[1.02] hover:bg-opacity-95 active:scale-95 flex items-center justify-center gap-2">
                Daftar Sekarang <i class="fa-solid fa-user-plus text-xs"></i>
            </button>
        </form>

        <!-- Link ke Login -->
        <div class="mt-10 text-center border-t border-white/50 pt-6">
            <p class="text-[11px] text-gray-400">Sudah memiliki akun LaporIn? 
                <!-- Pastikan kamu mengubah login.html menjadi login.php jika kamu merubah ekstensinya -->
                <a href="login.php" class="text-primary font-bold hover:text-accent transition-colors hover:underline ml-1">Masuk di sini</a>
            </p>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- untuk pesan sukses -->
<?php if ($pesan_sukses != ""): ?>
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
<?php endif; ?>

</body>

</html>