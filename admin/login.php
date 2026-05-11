<?php
session_start();

if (isset($_SESSION['admin_id'])) {
    header("Location: beranda.php");
    exit();
}

require __DIR__ . '/../database/conection.php';

$pesan_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM admin WHERE username = '$username'");

    if (mysqli_num_rows($query) == 1) {
        $data = mysqli_fetch_assoc($query);

        if ($data['password'] == $password) {
            $_SESSION['admin_id']   = $data['id'];
            $_SESSION['admin_nama'] = $data['nama'];
            $_SESSION['admin_username'] = $data['username'];

            header("Location: beranda.php");
            exit();
        } else {
            $pesan_error = "Password salah!";
        }
    } else {
        $pesan_error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - LaporIn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3A5A40',
                        accent: '#A3B18A',
                    },
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-primary">

    <div class="w-full max-w-[380px] bg-white rounded-[40px] p-8 border-[5px] border-white/20 shadow-2xl relative">
        
        <!-- Tombol X kembali ke landing page -->
        <a href="../index.php" 
            class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-red-100 text-gray-400 hover:text-red-500 transition-colors">
            <i class="fa-solid fa-xmark text-sm"></i>
        </a>

        <!-- Badge Admin -->
        <div class="text-center mb-6">
            <h1 class="font-bold text-2xl text-primary tracking-tight">
                Login <span class="text-accent">Admin</span>
            </h1>
            <p class="text-gray-400 text-xs mt-1">Panel Administrasi LaporIn</p>
        </div>

        <?php if($pesan_error != ""): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-[15px] text-sm text-center mb-4">
                <?php echo $pesan_error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4" autocomplete="off">
            <!-- Username -->
            <div>
                <input required type="text" name="username" placeholder="Username Admin"
                    autocomplete="off"
                    class="w-full bg-gray-50 border-2 border-transparent px-5 py-3.5 rounded-[15px] text-sm focus:outline-none focus:border-accent transition-all placeholder-gray-300">
            </div>

            <!-- Password -->
            <div class="relative">
                <input required type="password" name="password" id="password" placeholder="Password"
                    autocomplete="new-password"
                    class="w-full bg-gray-50 border-2 border-transparent px-5 py-3.5 pr-12 rounded-[15px] text-sm focus:outline-none focus:border-accent transition-all placeholder-gray-300">
                <button type="button" onclick="togglePassword()"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-primary transition-colors">
                    <i id="eyeIcon" class="fa-solid fa-eye"></i>
                </button>
            </div>

            <button type="submit"
                class="w-full font-bold bg-primary text-white py-3.5 rounded-[15px] shadow-lg transition-all duration-200 hover:scale-[1.02] hover:bg-opacity-95 active:scale-95 mt-2">
                Masuk sebagai Admin
            </button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>