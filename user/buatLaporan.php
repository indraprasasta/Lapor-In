<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}


require __DIR__ . '/../database/conection.php';

$user_id = $_SESSION['user_id'];

$query_user = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$user       = mysqli_fetch_assoc($query_user);
$pesan_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $judul     = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $kategori  = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $alamat    = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $kecamatan = mysqli_real_escape_string($koneksi, $_POST['kecamatan']);
    $kelurahan = mysqli_real_escape_string($koneksi, $_POST['kelurahan']);
    $latitude  = mysqli_real_escape_string($koneksi, $_POST['latitude']);
    $longitude = mysqli_real_escape_string($koneksi, $_POST['longitude']);

    // Upload foto
    $nama_foto = "";
    if (!empty($_FILES['file_upload']['name'][0])) {
        $folder      = __DIR__ . '/../uploads/foto_laporan/';
        $nama_foto   = time() . '_' . $_FILES['file_upload']['name'][0];
        $tipe_file   = $_FILES['file_upload']['type'][0];
        $ukuran_file = $_FILES['file_upload']['size'][0];

        $tipe_allowed = ['image/jpeg', 'image/png', 'image/jpg'];

        if (!in_array($tipe_file, $tipe_allowed)) {
            $pesan_error = "Format foto tidak didukung!";
        } elseif ($ukuran_file > 5 * 1024 * 1024) {
            $pesan_error = "Ukuran foto maksimal 5MB!";
        } else {
            move_uploaded_file($_FILES['file_upload']['tmp_name'][0], $folder . $nama_foto);
        }
    }

    if ($pesan_error == "") {
        $query = "INSERT INTO laporan (user_id, judul, kategori, deskripsi, foto, alamat, kecamatan, kelurahan, latitude, longitude)
                VALUES ('$user_id', '$judul', '$kategori', '$deskripsi', '$nama_foto', '$alamat', '$kecamatan', '$kelurahan', '$latitude', '$longitude')";

        if (mysqli_query($koneksi, $query)) {
            echo "<script>
                alert('Laporan berhasil dikirim!');
                window.location.href = 'daftarLaporan.php';
            </script>";
            exit();
        } else {
            $pesan_error = "Gagal menyimpan laporan: " . mysqli_error($koneksi);
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Laporan - LaporIn Mataram</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Leaflet CSS for Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Tailwind Config to match LaporIn Design System -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            DEFAULT: '#3A5A40',
                            dark: '#2B4330',
                        },
                        accent: {
                            DEFAULT: '#A3B18A',
                            dark: '#8b9a70',
                        },
                        secondary: '#588157',
                        warning: '#D97706',
                        danger: '#DC2626',
                        info: '#0284C7',
                        dark: '#1E293B',
                        light: '#F8FAFC',
                        muted: '#94A3B8',
                    }
                }
            }
        }
    </script>
    <style>
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #F8FAFC;
        }

        ::-webkit-scrollbar-thumb {
            background: #A3B18A;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #3A5A40;
        }

        #map {
            height: 300px;
            z-index: 10;
        }
    </style>
</head>

<body class="bg-primary text-dark font-sans h-screen flex overflow-hidden">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-dark/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 bg-white w-64 border-r border-slate-200 z-50 transform -translate-x-full lg:translate-x-0 lg:static lg:flex lg:flex-col transition-transform duration-300 ease-in-out">
        <!-- Logo -->
        <div class="h-16 flex items-center px-6 border-b border-slate-200">
            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white mr-3">
                <i data-lucide="leaf" class="w-5 h-5"></i>
            </div>
            <span class="text-primary font-extrabold text-2xl tracking-tight">Lapor<span class="text-accent">In</span></span>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <a href="beranda.php"
                class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"></i>
                Beranda
            </a>
            <a href="buatLaporan.php" class="flex items-center px-3 py-2.5 bg-primary/10 text-primary rounded-lg font-medium group">
                <i data-lucide="plus-circle" class="w-5 h-5 mr-3"></i>
                Buat Laporan
            </a>
            <a href="daftarLaporan.php"
                class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="file-text" class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"></i>
                Laporan Saya
            </a>
            <a href="profile.php"
                class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="user" class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"></i>
                Profil
            </a>
        </nav>

        <!-- User Info -->
        <div class="p-4 border-t border-slate-200">
            <a href="#" class="flex items-center group">
                <div
                    class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-primary font-bold overflow-hidden border border-slate-200">
                    <img src="https://ui-avatars.com/api/?name=Pak+Andi&background=A3B18A&color=ffffff" alt="Avatar"
                        class="w-full h-full object-cover">
                </div>
                <div class="ml-3">
                    <p class="text-sm font-semibold text-dark"><?php echo $user['nama']; ?></p>
                    <p class="text-xs text-muted">Masyarakat</p>
                </div>
            </a>
            <a href="logout.php"
                class="mt-4 w-full flex items-center justify-center px-3 py-2 text-sm text-danger bg-red-50 hover:bg-red-100 rounded-lg font-medium transition-colors">
                <i data-lucide="log-out" class="w-4 h-4 mr-2"></i>
                Keluar
            </a>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">

        <!-- Top Navbar -->
        <header class="h-16 bg-accent border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-30">
            <button
                class="lg:hidden text-muted hover:text-dark p-2 -ml-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                onclick="toggleSidebar()">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <div class="hidden sm:block">
                <nav class="flex text-white text-muted font-medium">
                    <a href="beranda.html" class="hover:text-dark">Dashboard</a>
                    <span class="mx-2">/</span>
                    <span class="text-dark">Buat Laporan Baru</span>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <!-- Mengubah background main menjadi bg-primary (#3A5A40) -->
        <main class="flex-1 overflow-y-auto bg-primary p-4 sm:p-6 lg:p-8">
            <div class="max-w-3xl mx-auto">

                <div class="mb-6">
                    <!-- Mengubah teks judul menjadi warna putih agar kontras dengan background hijau -->
                    <h2 class="text-2xl font-bold text-white tracking-tight">Kirim Laporan Kerusakan</h2>
                    <p class="text-accent mt-1 text-sm">Mohon isi detail kerusakan infrastruktur dengan jelas agar petugas dapat menanganinya dengan tepat.</p>
                </div>

                <!-- Form Card: Dikembalikan ke warna putih bersih -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl overflow-hidden">
                    <form action="" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
                        <!-- Judul Laporan -->
                        <div>
                            <label for="judul" class="block text-sm font-semibold text-dark mb-2">Judul Laporan <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="judul" name="judul"
                                placeholder="Contoh: Jalan berlubang cukup dalam di simpang empat" maxlength="100"
                                required
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none transition-colors text-sm">
                            <p class="text-xs text-muted mt-1 text-right">0/100 karakter</p>
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label for="kategori" class="block text-sm font-semibold text-dark mb-2">Kategori <span
                                    class="text-danger">*</span></label>
                            <div class="relative">
                                <select id="kategori" name="kategori" required
                                    class="w-full pl-4 pr-10 py-2.5 appearance-none rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none transition-colors text-sm bg-white">
                                    <option value="" disabled selected>Pilih Kategori Kerusakan</option>
                                    <option value="Jalan Rusak">Jalan Rusak</option>
                                    <option value="Pohon Tumbang">Pohon Tumbang</option>
                                    <option value="Lampu Jalan Mati">Lampu Jalan Mati</option>
                                    <option value="Saluran Air">Saluran Air</option>
                                    <option value="Jembatan">Jembatan</option>
                                    <option value="Trotoar">Trotoar</option>
                                    <option value="Fasilitas Umum">Fasilitas Umum</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label for="deskripsi" class="block text-sm font-semibold text-dark mb-2">Deskripsi Detail
                                <span class="text-danger">*</span></label>
                            <textarea id="deskripsi" name="deskripsi" rows="4"
                                placeholder="Jelaskan kondisi kerusakan secara detail..." maxlength="1000" required
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none transition-colors text-sm resize-y"></textarea>
                            <p class="text-xs text-muted mt-1 text-right">0/1000 karakter</p>
                        </div>

                        <!-- Upload Foto -->
                        <div>
                            <label class="block text-sm font-semibold text-dark mb-2">Foto Bukti <span
                                    class="text-danger">*</span></label>
                            <div
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-lg hover:border-primary/50 transition-colors cursor-pointer bg-slate-50 group">
                                <div class="space-y-1 text-center">
                                    <i data-lucide="image-plus"
                                        class="mx-auto h-12 w-12 text-slate-400 group-hover:text-primary transition-colors"></i>
                                    <div class="flex text-sm text-slate-600 justify-center mt-2">
                                        <label for="file-upload"
                                            class="relative cursor-pointer bg-transparent rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none focus-within:ring-2 focus-within:ring-primary">
                                            <span>Upload  file</span>
                                            <input id="file-upload" name="file_upload[]" type="file" class="sr-only"
                                            accept="image/jpeg, image/png" multiple>
                                        </label>
                                        <p class="pl-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-muted">Maks. 3 foto (Format: JPG/PNG, Maks: 5MB/foto)</p>
                                </div>
                            </div>
                        </div>

                        <hr class="border-slate-200">

                        <!-- Detail Lokasi Title -->
                        <h3 class="text-lg font-bold text-dark">Detail Lokasi</h3>

                        <!-- Alamat Lengkap -->
                        <div>
                            <label for="alamat" class="block text-sm font-semibold text-dark mb-2">Alamat Lengkap <span
                                    class="text-danger">*</span></label>
                            <textarea id="alamat" name="alamat" rows="2"
                                placeholder="Contoh: Jl. Majapahit No. 12, depan minimarket X..." required
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none transition-colors text-sm resize-y"></textarea>
                        </div>

                        <!-- Kecamatan & Kelurahan Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="kecamatan" class="block text-sm font-semibold text-dark mb-2">Kecamatan
                                    <span class="text-danger">*</span></label>
                                <div class="relative">
                                    <select id="kecamatan" name="kecamatan" required onchange="updateKelurahan()"
                                        class="w-full pl-4 pr-10 py-2.5 appearance-none rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none transition-colors text-sm bg-white">
                                        <option value="" disabled selected>Pilih Kecamatan</option>
                                        <option value="Ampenan">Ampenan</option>
                                        <option value="Cakranegara">Cakranegara</option>
                                        <option value="Mataram">Mataram</option>
                                        <option value="Sekarbela">Sekarbela</option>
                                        <option value="Sandubaya">Sandubaya</option>
                                        <option value="Selaparang">Selaparang</option>
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="kelurahan" class="block text-sm font-semibold text-dark mb-2">Kelurahan
                                    <span class="text-danger">*</span></label>
                                <div class="relative">
                                    <select id="kelurahan" name="kelurahan" required disabled
                                        class="w-full pl-4 pr-10 py-2.5 appearance-none rounded-lg border border-slate-300 focus:ring-2 focus:ring-accent outline-none transition-colors text-sm bg-slate-50 disabled:opacity-70 disabled:cursor-not-allowed">
                                        <option value="" disabled selected>Pilih Kecamatan Dahulu</option>
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Map / Titik Koordinat -->
                        <div>
                            <label class="block text-sm font-semibold text-dark mb-2">Titik Koordinat (Opsional)</label>
                            <p class="text-xs text-muted mb-3">Geser pin merah pada peta untuk menentukan lokasi
                                presisi.</p>
                            <div class="rounded-lg border border-slate-300 overflow-hidden relative">
                                <!-- Map Container -->
                                <div id="map"></div>
                                <input type="hidden" id="latitude" name="latitude">
                                <input type="hidden" id="longitude" name="longitude">
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="pt-4 flex flex-col-reverse sm:flex-row gap-3 sm:justify-end">
                            <button type="button" onclick="history.back()"
                                class="w-full sm:w-auto px-6 py-2.5 border border-slate-300 text-dark font-semibold rounded-lg hover:bg-slate-50 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-200">
                                Batal
                            </button>
                            <button type="submit"
                                class="w-full sm:w-auto px-6 py-2.5 bg-primary hover:bg-primary-dark text-white font-semibold rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 flex items-center justify-center">
                                <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                                Kirim Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Script Logic -->
    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Sidebar Toggle Logic for Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        // Data Kelurahan Kota Mataram
        const dataKelurahan = {
            "Ampenan": ["Ampenan Selatan", "Ampenan Tengah", "Ampenan Utara", "Banjar", "Bintaro", "Dayan Peken", "Kebon Sari", "Pejarakan Karya", "Pejeruk", "Taman Sari"],
            "Cakranegara": ["Cakranegara Barat", "Cakranegara Selatan", "Cilinaya", "Karang Taliwang", "Mayura", "Sapta Marga", "Sayang-Sayang"],
            "Mataram": ["Mataram Barat", "Mataram Timur", "Pagutan", "Pejanggik", "Pagesangan"],
            "Sekarbela": ["Jempong Baru", "Karang Pule", "Kekalik Jaya", "Tanjung Karang", "Tanjung Karang Permai"],
            "Sandubaya": ["Abian Tubuh Baru", "Babakan", "Bertais", "Dasan Cermen", "Mandalika", "Selagalas", "Turida"],
            "Selaparang": ["Dasan Agung", "Gomong", "Karang Baru", "Monjok", "Rembiga"]
        };

        // Update dropdown Kelurahan
        function updateKelurahan() {
            const kecSelect = document.getElementById('kecamatan');
            const kelSelect = document.getElementById('kelurahan');
            const selectedKec = kecSelect.value;

            // Clear current options
            kelSelect.innerHTML = '<option value="" disabled selected>Pilih Kelurahan</option>';

            if (selectedKec && dataKelurahan[selectedKec]) {
                kelSelect.disabled = false;
                kelSelect.classList.remove('bg-slate-50', 'disabled:opacity-70', 'disabled:cursor-not-allowed');
                kelSelect.classList.add('bg-white');

                dataKelurahan[selectedKec].forEach(kel => {
                    const option = document.createElement('option');
                    option.value = kel;
                    option.textContent = kel;
                    kelSelect.appendChild(option);
                });
            } else {
                kelSelect.disabled = true;
                kelSelect.classList.add('bg-slate-50', 'disabled:opacity-70', 'disabled:cursor-not-allowed');
                kelSelect.classList.remove('bg-white');
            }
        }

        // Map Initialization (Leaflet)
        const map = L.map('map').setView([-8.5833, 116.1167], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Add draggable marker
        let marker = L.marker([-8.5833, 116.1167], {
            draggable: true
        }).addTo(map);

        // Update hidden inputs when marker is dragged
        marker.on('dragend', function (e) {
            const lat = marker.getLatLng().lat;
            const lng = marker.getLatLng().lng;
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        });

        // Click on map to move marker
        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            document.getElementById('latitude').value = e.latlng.lat;
            document.getElementById('longitude').value = e.latlng.lng;
        });
    </script>
</body>

</html>