<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: user/beranda.php");
    exit();
} elseif (isset($_SESSION['petugas_id'])) {
    header("Location: petugas/beranda.php");
    exit();
} elseif (isset($_SESSION['admin_id'])) {
    header("Location: admin/beranda.php");
    exit();
}

require __DIR__ . '/database/conection.php';

$pesan_error = "";

// if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['username'])) {
//     $username = trim($_GET['username']);
//     $password = $_GET['password'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Bersihkan input user
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Cek data user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);

    if ($stmt->rowCount() == 1) {
        $data = $stmt->fetch();
        if (password_verify($password, $data['password'])) {
            $_SESSION['user_id']  = $data['id'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['nama']     = $data['nama'];
            header("Location: user/beranda.php");
            exit();
        } else {
            $pesan_error = "Password salah!";
        }
    } else {
        // Cek data petugas
        $stmt_petugas = $pdo->prepare("
            SELECT petugas.*, dinas.nama_dinas 
            FROM petugas 
            JOIN dinas ON petugas.dinas_id = dinas.id 
            WHERE petugas.username = :username
        ");
        $stmt_petugas->execute([':username' => $username]);

        if ($stmt_petugas->rowCount() == 1) {
            $petugas = $stmt_petugas->fetch();
            if (password_verify($password, $petugas['password'])) {
                $_SESSION['petugas_id']       = $petugas['id'];
                $_SESSION['petugas_username'] = $petugas['username'];
                $_SESSION['petugas_nama']     = $petugas['nama'];
                $_SESSION['petugas_jabatan']  = $petugas['jabatan'];
                $_SESSION['petugas_dinas']    = $petugas['nama_dinas'];
                $_SESSION['petugas_dinas_id'] = $petugas['dinas_id'];
                header("Location: petugas/beranda.php");
                exit();
            } else {
                $pesan_error = "Password salah!";
            }
        } else {
            // Cek data admin
            $stmt_admin = $pdo->prepare("SELECT * FROM admin WHERE username = :username");
            $stmt_admin->execute([':username' => $username]);
            
            if ($stmt_admin->rowCount() == 1) {
                $admin = $stmt_admin->fetch();
                if (password_verify($password, $admin['password'])) {
                    $_SESSION['admin_id']   = $admin['id'];
                    $_SESSION['admin_nama'] = $admin['nama'];
                    $_SESSION['admin_username'] = $admin['username'];
                    header("Location: admin/beranda.php");
                    exit();
                } else {
                    $pesan_error = "Password salah!";
                }
            } else {
                $pesan_error = "Username tidak ditemukan!";
            }
        }
    }
}
?> 
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - LaporIn</title>
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Google Fonts: Poppins -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Tailwind Config for Custom Colors -->
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: '#3A5A40',   /* Dark Green */
                            accent: '#A3B18A',    /* Light Green */
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
        </style>
    </head>
    <body class="min-h-screen flex items-center justify-center p-4">

        <img src="https://i.pinimg.com/1200x/fc/02/64/fc026433a20db53bc4447d4e41f8f830.jpg" 
            alt="Latar Belakang Kota Mataram"
            class="absolute w-full h-full object-cover top-0 left-0 -z-10">
            <div class="absolute w-full h-full bg-black/40 top-0 left-0 -z-10"></div>


        <!-- Container -->
        <div class="w-full max-w-[380px] bg-gradient-to-t from-[#eef2f0] to-white rounded-[40px] p-8 border-[5px] border-white shadow-[0_30px_30px_-20px_rgba(58,90,64,0.3)] relative">

        <a href="index.php" 
        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-red-100 text-gray-400 hover:text-red-500 transition-colors">
        <i class="fa-solid fa-xmark text-sm"></i>
        </a>
            
            <!-- Header -->
            <div class="text-center font-bold text-3xl text-primary mb-8 tracking-tight">
                Masuk <span class="text-accent">LaporIn</span>
            </div>

            <?php if($pesan_error != ""): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-[15px] text-sm text-center mb-2">
                <?php echo $pesan_error; ?>
                </div>
            <?php endif; ?>
            
            <form action="" method="POST" class="space-y-5" autocomplete="off">
                <!-- Username -->
                <div>
                    <input required 
                        class="w-full bg-white border-y-0 border-x-2 border-transparent p-[15px_20px] rounded-[20px] shadow-[0_10px_10px_-5px_rgba(163,177,138,0.2)] focus:outline-none focus:border-x-accent placeholder-gray-400 text-sm transition-all" 
                        type="text" 
                        name="username" 
                        id="username" 
                        placeholder="Username"
                        autocomplete="off">
                </div>

                <!-- Password -->
                <div class="relative">
                    <input required 
                        class="w-full bg-white border-y-0 border-x-2 border-transparent p-[15px_20px] rounded-[20px] shadow-[0_10px_10px_-5px_rgba(163,177,138,0.2)] focus:outline-none focus:border-x-accent placeholder-gray-400 text-sm transition-all" 
                        type="password" 
                        name="password"
                        id="password"
                        autocomplete="new-password"
                        placeholder="Password">

                        <!-- tombol mata -->
                        <button type="button" onclick="togglePassword()" 
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-primary transition-colors">
                        <i id="eyeIcon" class="fa-solid fa-eye"></i>
                        </button>
                    
                    </div>
                    <!-- Forgot Password -->
                <div class="mt-2 ml-2">
                    <a href="lupaPassword.php" class="text-[11px] text-accent hover:text-primary transition-colors hover:underline">Lupa Password?</a>
                </div>

                <!-- Submit Button -->
                <button
                    class="block w-full font-bold bg-primary text-white py-4 mt-8 rounded-[20px] shadow-[0_20px_10px_-15px_rgba(58,90,64,0.4)] border-none transition-all duration-200 hover:scale-[1.03] hover:shadow-[0_23px_10px_-20px_rgba(58,90,64,0.5)] hover:bg-opacity-90 active:scale-95 cursor-pointer" 
                    type="submit">
                    Sign In
                </button>
            </form>

            <!-- Footer Link -->
            <div class="mt-8 text-center">
                <span class="text-[11px] text-gray-400">Belum punya akun? 
                    <a href="signup.php" class="text-primary font-semibold hover:text-accent transition-colors hover:underline">Daftar sekarang</a>
                </span>
            </div>
        </div>

    </body>
    
    <script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('eyeIcon');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
    </html>