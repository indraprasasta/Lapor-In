<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LaporIn - Portal Pelaporan Infrastruktur Mataram</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind Config for Custom Colors -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3A5A40',   /* 30% - Dark Green */
                        accent: '#A3B18A',    /* 10% - Light Green */
                        light: '#ffffff',     /* 60% - White */
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
            background-color: #ffffff;
            color: #333333;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #ffffff; }
        ::-webkit-scrollbar-thumb { background: #A3B18A; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #3A5A40; }

        /* Accordion transition */
        .faq-answer {
            transition: max-height 0.3s ease-out, opacity 0.3s ease-out;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }
        .faq-answer.open {
            max-height: 500px;
            opacity: 1;
        }
    </style>
</head>
<body class="antialiased">

    <!-- Navbar -->
    <nav class="bg-white sticky top-0 z-50 border-b border-gray-100 shadow-sm py-4">
        <div class="container mx-auto px-4 lg:px-8 flex justify-between items-center">
            <a href="#" class="flex items-center gap-2 group">
                <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center text-white font-bold text-xl group-hover:bg-accent transition-colors">
                    <i class="fa-solid fa-leaf"></i>
                </div>
                <span class="font-bold text-2xl text-primary tracking-tight">Lapor<span class="text-accent">In</span></span>
            </a>
            
            <!-- Desktop Menu -->
            <div class="hidden lg:flex space-x-6 text-sm font-medium">
                <a href="#beranda" class="text-primary hover:text-accent transition">Beranda</a>
                <a href="#tentang" class="text-primary hover:text-accent transition">Tentang Kami</a>
                <a href="#layanan" class="text-primary hover:text-accent transition">Layanan</a>
                <a href="#portofolio" class="text-primary hover:text-accent transition">Portofolio</a>
                <a href="#blog" class="text-primary hover:text-accent transition">Blog</a>
                <a href="#kontak" class="text-primary hover:text-accent transition">Kontak</a>
            </div>

            <!-- CTA Button -->
            <div class="hidden lg:block">
                <a href="login.php" class="bg-primary text-white px-6 py-2.5 rounded-full font-semibold hover:bg-opacity-90 hover:shadow-lg transition-all">Lapor Sekarang</a>
            </div>

            <!-- Mobile Menu Toggle -->
            <button class="lg:hidden text-primary focus:outline-none" id="mobile-menu-btn">
                <i class="fa-solid fa-bars text-2xl"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div class="hidden lg:hidden bg-white border-t border-gray-100 absolute w-full shadow-lg" id="mobile-menu">
            <div class="flex flex-col px-4 py-4 space-y-4 text-center text-primary font-medium">
                <a href="#beranda" class="hover:text-accent">Beranda</a>
                <a href="#tentang" class="hover:text-accent">Tentang Kami</a>
                <a href="#layanan" class="hover:text-accent">Layanan</a>
                <a href="#portofolio" class="hover:text-accent">Portofolio</a>
                <a href="#kontak" class="hover:text-accent">Kontak</a>
                <a href="login.html" class="bg-primary text-white py-2 rounded-full">Lapor Sekarang</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Background Image -->
    <section id="beranda" class="relative min-h-[90vh] flex items-center overflow-hidden">
        <!-- Background Image Container -->
        <div class="absolute inset-0 z-0">
            <img src="https://i.pinimg.com/1200x/fc/02/64/fc026433a20db53bc4447d4e41f8f830.jpg" alt="Latar Belakang Kota Mataram" class="w-full h-full object-cover">
            <!-- Dark Overlay for Readability -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
        </div>

        <div class="container mx-auto px-4 lg:px-8 relative z-10">
            <div class="max-w-2xl space-y-6">
                <h1 class="text-4xl lg:text-7xl font-bold text-white leading-tight">
                    Wujudkan <span class="text-accent">Mataram</span> yang Lebih Nyaman
                </h1>
                <p class="text-gray-200 text-lg leading-relaxed max-w-lg">
                    Temukan jalan rusak, pohon rawan tumbang, atau lampu jalan mati? Laporkan dengan mudah hanya melalui foto dan detail lokasi. Kami pastikan aduan Anda ditangani dengan cepat.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <a href="login.php" class="bg-accent text-primary px-8 py-3 rounded-full font-bold text-center hover:bg-white transition-all shadow-lg hover:-translate-y-1">
                        <i class="fa-solid fa-camera mr-2"></i> Buat Laporan
                    </a>
                    <a href="#tentang" class="bg-transparent text-white border-2 border-white/50 px-8 py-3 rounded-full font-semibold text-center hover:bg-white hover:text-primary transition-all backdrop-blur-sm">
                        Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="tentang" class="py-20 bg-graybg">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="order-2 lg:order-1 grid grid-cols-2 gap-4 h-full">
                    <!-- gambar bg -->
                    <img src="https://images.unsplash.com/photo-1760331339913-4c915f2ac5c5?q=80&w=688&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Infrastruktur" class="rounded-2xl shadow-lg mt-100 w-full h-full object-cover">
                    <img src="https://images.unsplash.com/photo-1760331339913-4c915f2ac5c5?q=80&w=688&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Infrastruktur" class="rounded-2xl shadow-lg mt-100 w-full h-full object-cover">
                </div>
                <div class="order-1 lg:order-2 space-y-6">
                    <h2 class="text-3xl lg:text-4xl font-bold text-primary">Tentang <span class="text-accent">LaporIn</span></h2>
                    <p class="text-gray-600 leading-relaxed text-lg">
                        LaporIn adalah inisiatif digital yang menjembatani warga Kota Mataram dengan instansi terkait untuk menciptakan infrastruktur kota yang tangguh. Kami percaya bahwa setiap warga memiliki peran penting dalam memelihara keindahan dan keamanan lingkungan.
                    </p>
                    <ul class="space-y-4 text-primary font-medium mt-6">
                        <li class="flex items-center gap-3"><i class="fa-solid fa-circle-check text-accent text-xl"></i> Transparansi proses perbaikan</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-circle-check text-accent text-xl"></i> Respon cepat dari dinas terkait</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-circle-check text-accent text-xl"></i> Terintegrasi dengan sistem tata kota</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="layanan" class="py-20 bg-white">
        <div class="container mx-auto px-4 lg:px-8 text-center">
            <span class="text-accent font-semibold tracking-wider uppercase text-sm">Apa Yang Bisa Dilaporkan?</span>
            <h2 class="text-3xl lg:text-4xl font-bold text-primary mt-2 mb-12">Layanan Pengaduan Kami</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="p-8 rounded-2xl border border-gray-100 hover:border-accent hover:shadow-xl transition-all group bg-white">
                    <div class="w-16 h-16 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-primary transition-colors">
                        <i class="fa-solid fa-road text-2xl text-primary group-hover:text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-3">Jalan Rusak</h3>
                    <p class="text-gray-600 text-sm">Laporkan jalan berlubang, aspal terkelupas, atau trotoar yang membahayakan pengguna jalan dan pejalan kaki.</p>
                </div>
                <!-- Card 2 -->
                <div class="p-8 rounded-2xl border border-gray-100 hover:border-accent hover:shadow-xl transition-all group bg-white shadow-lg shadow-accent/5">
                    <div class="w-16 h-16 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-primary transition-colors">
                        <i class="fa-solid fa-tree text-2xl text-primary group-hover:text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-3">Pohon Tumbang</h3>
                    <p class="text-gray-600 text-sm">Bantu cegah kecelakaan dengan melaporkan pohon rawan tumbang, dahan patah, atau pohon tumbang yang menutupi jalan.</p>
                </div>
                <!-- Card 3 -->
                <div class="p-8 rounded-2xl border border-gray-100 hover:border-accent hover:shadow-xl transition-all group bg-white">
                    <div class="w-16 h-16 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-primary transition-colors">
                        <i class="fa-regular fa-lightbulb text-2xl text-primary group-hover:text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-3">Lampu Jalan Mati</h3>
                    <p class="text-gray-600 text-sm">Beri tahu kami jika ada Penerangan Jalan Umum (PJU) yang padam untuk mengembalikan rasa aman di malam hari.</p>
                </div>
                <!-- card 4: Fasilitas Umum (Ikon Diperbarui) -->
                <div class="p-8 rounded-2xl border border-gray-100 hover:border-accent hover:shadow-xl transition-all group bg-white">
                    <div class="w-16 h-16 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-primary transition-colors">
                        <i class="fa-solid fa-building text-2xl text-primary group-hover:text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-3">Fasilitas Umum</h3>
                    <p class="text-gray-600 text-sm">Laporkan kerusakan pada fasilitas publik seperti taman kota, halte bus, atau sarana umum lainnya agar segera diperbaiki.</p>
                </div>
                <!-- card 5: Jembatan (Ikon Diperbarui) -->
                <div class="p-8 rounded-2xl border border-gray-100 hover:border-accent hover:shadow-xl transition-all group bg-white">
                    <div class="w-16 h-16 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-primary transition-colors">
                        <i class="fa-solid fa-bridge text-2xl text-primary group-hover:text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-3">Jembatan</h3>
                    <p class="text-gray-600 text-sm">Informasikan jika ada kerusakan struktur jembatan, aspal berlubang di jembatan, atau pembatas yang membahayakan.</p>
                </div>
                <!-- card 6: Lainnya (Ikon Diperbarui) -->
                <div class="p-8 rounded-2xl border border-gray-100 hover:border-accent hover:shadow-xl transition-all group bg-white">
                    <div class="w-16 h-16 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-primary transition-colors">
                        <i class="fa-solid fa-ellipsis text-2xl text-primary group-hover:text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-3">Lainnya</h3>
                    <p class="text-gray-600 text-sm">Sampaikan aduan infrastruktur lainnya yang tidak termasuk dalam kategori di atas untuk segera kami tindaklanjuti.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio / Recent Reports -->
    <section id="portofolio" class="py-20 bg-primary">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex flex-col lg:flex-row justify-between items-end mb-12">
                <div>
                    <span class="text-accent font-semibold tracking-wider uppercase text-sm">Aksi Nyata</span>
                    <h2 class="text-3xl lg:text-4xl font-bold text-white mt-2">Laporan Terselesaikan</h2>
                </div>
                <a href="#" class="text-white border-b border-accent hover:text-accent transition-colors mt-4 lg:mt-0">Lihat Semua Laporan</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Portofolio 1 -->
                <div class="bg-white rounded-2xl overflow-hidden group">
                    <div class="relative h-56 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?auto=format&fit=crop&w=600&q=80" alt="Jalan Diperbaiki" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-4 right-4 bg-accent text-white text-xs font-bold px-3 py-1 rounded-full">Selesai Diperbaiki</div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-lg text-primary mb-2">Penambalan Jl. Majapahit</h3>
                        <p class="text-sm text-gray-500"><i class="fa-regular fa-calendar mr-2"></i> 12 April 2026</p>
                    </div>
                </div>
                <!-- Portofolio 2 -->
                <div class="bg-white rounded-2xl overflow-hidden group">
                    <div class="relative h-56 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1595054226583-eb561c210815?auto=format&fit=crop&w=600&q=80" alt="Lampu Diganti" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-4 right-4 bg-accent text-white text-xs font-bold px-3 py-1 rounded-full">Selesai Diperbaiki</div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-lg text-primary mb-2">Pergantian PJU Udayana</h3>
                        <p class="text-sm text-gray-500"><i class="fa-regular fa-calendar mr-2"></i> 08 April 2026</p>
                    </div>
                </div>
                <!-- Portofolio 3 -->
                <div class="bg-white rounded-2xl overflow-hidden group">
                    <div class="relative h-56 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1605230971032-9c1787d558b0?auto=format&fit=crop&w=600&q=80" alt="Evakuasi Pohon" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-4 right-4 bg-accent text-white text-xs font-bold px-3 py-1 rounded-full">Selesai Diperbaiki</div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-lg text-primary mb-2">Evakuasi Pohon Ampenan</h3>
                        <p class="text-sm text-gray-500"><i class="fa-regular fa-calendar mr-2"></i> 02 April 2026</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Steps / Cara Kerja -->
    <section class="py-20 bg-graybg">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-primary mb-4">Cara Kerja LaporIn</h2>
                <p class="text-gray-600">Sistem kami membutuhkan 4 langkah mudah untuk memastikan laporan Anda valid dan cepat ditangani.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center relative">
                <!-- Line connector -->
                <div class="hidden md:block absolute top-8 left-[10%] right-[10%] h-0.5 bg-accent/30 z-0"></div>
                
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-white border-4 border-accent rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold text-primary shadow-lg">1</div>
                    <h4 class="font-bold text-primary mb-2">Ambil Foto</h4>
                    <p class="text-sm text-gray-600">Foto infrastruktur yang rusak dengan jelas.</p>
                </div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-white border-4 border-accent rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold text-primary shadow-lg">2</div>
                    <h4 class="font-bold text-primary mb-2">Detail Alamat</h4>
                    <p class="text-sm text-gray-600">Berikan alamat spesifik & deskripsi kerusakannya.</p>
                </div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-white border-4 border-accent rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold text-primary shadow-lg">3</div>
                    <h4 class="font-bold text-primary mb-2">Waktu Kejadian</h4>
                    <p class="text-sm text-gray-600">Cantumkan tanggal & jam pantauan Anda.</p>
                </div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-white border-4 border-primary rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold text-white bg-primary shadow-lg"><i class="fa-solid fa-check"></i></div>
                    <h4 class="font-bold text-primary mb-2">Selesai</h4>
                    <p class="text-sm text-gray-600">Laporan diverifikasi & diteruskan ke dinas.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-20 bg-white">
        <div class="container mx-auto px-4 lg:px-8 max-w-4xl">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-bold text-primary">Pertanyaan Umum (FAQ)</h2>
            </div>
            
            <div class="space-y-4">
                <!-- FAQ 1 -->
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button class="faq-btn w-full px-6 py-4 text-left font-semibold text-primary bg-graybg hover:bg-gray-100 flex justify-between items-center transition-colors">
                        Bagaimana cara melampirkan foto laporan?
                        <i class="fa-solid fa-chevron-down text-accent transition-transform"></i>
                    </button>
                    <div class="faq-answer bg-white px-6">
                        <p class="py-4 text-gray-600">Anda cukup menekan tombol "Lapor Sekarang", lalu pada formulir akan tersedia kolom unggah file. Pastikan foto jelas, tidak buram, dan menunjukkan masalah (jalan, pohon, lampu) secara utuh.</p>
                    </div>
                </div>
                <!-- FAQ 2 -->
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button class="faq-btn w-full px-6 py-4 text-left font-semibold text-primary bg-graybg hover:bg-gray-100 flex justify-between items-center transition-colors">
                        Berapa lama laporan akan ditindaklanjuti?
                        <i class="fa-solid fa-chevron-down text-accent transition-transform"></i>
                    </button>
                    <div class="faq-answer bg-white px-6">
                        <p class="py-4 text-gray-600">Sistem LaporIn terintegrasi dengan dinas terkait di Mataram. Rata-rata waktu verifikasi adalah 1x24 jam, dan penindakan fisik memakan waktu 1-3 hari kerja tergantung tingkat urgensi dan cuaca.</p>
                    </div>
                </div>
                <!-- FAQ 3 -->
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button class="faq-btn w-full px-6 py-4 text-left font-semibold text-primary bg-graybg hover:bg-gray-100 flex justify-between items-center transition-colors">
                        Apakah saya perlu mencantumkan waktu kejadian secara spesifik?
                        <i class="fa-solid fa-chevron-down text-accent transition-transform"></i>
                    </button>
                    <div class="faq-answer bg-white px-6">
                        <p class="py-4 text-gray-600">Ya, tanggal dan keterangan waktu sangat penting. Ini membantu tim lapangan menilai kondisi terbaru (misalnya, lampu mati hanya pada jam 9 malam ke atas) untuk investigasi yang akurat.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog / Berita Section -->
    <section id="blog" class="py-20 bg-graybg">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-bold text-primary mb-2">Kabar Mataram</h2>
                    <p class="text-gray-600">Berita terbaru seputar pembangunan dan infrastruktur kota.</p>
                </div>
                <a href="#" class="hidden md:block text-accent hover:text-primary font-semibold">Lihat Semua Berita <i class="fa-solid fa-arrow-right ml-1"></i></a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Blog 1 -->
                <article class="bg-white rounded-2xl overflow-hidden shadow-sm">
                    <img src="https://images.unsplash.com/photo-1449824913935-59a10b8d2000?auto=format&fit=crop&w=600&q=80" alt="Blog" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <div class="text-xs text-accent font-semibold mb-2">INFRASTRUKTUR</div>
                        <h3 class="font-bold text-primary text-xl mb-3 hover:text-accent cursor-pointer">Pemkot Mataram Anggarkan Dana Ekstra untuk Perbaikan Jalan Provinsi</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">Pemerintah merespon tingginya laporan dari masyarakat di kuartal pertama dengan mempercepat pencairan dana perbaikan jalan.</p>
                        <a href="#" class="text-primary text-sm font-bold hover:text-accent">Baca Selengkapnya &rarr;</a>
                    </div>
                </article>
                <!-- Blog 2 -->
                <article class="bg-white rounded-2xl overflow-hidden shadow-sm">
                    <img src="https://images.unsplash.com/photo-1542361345-89e58247f2d5?auto=format&fit=crop&w=600&q=80" alt="Blog" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <div class="text-xs text-accent font-semibold mb-2">LINGKUNGAN</div>
                        <h3 class="font-bold text-primary text-xl mb-3 hover:text-accent cursor-pointer">Antisipasi Cuaca Ekstrem, Ratusan Pohon Tua Dipangkas</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">DLH Kota Mataram mengintensifkan pemangkasan ranting di area protokol untuk mencegah pohon tumbang akibat angin kencang.</p>
                        <a href="#" class="text-primary text-sm font-bold hover:text-accent">Baca Selengkapnya &rarr;</a>
                    </div>
                </article>
                <!-- Blog 3 -->
                <article class="bg-white rounded-2xl overflow-hidden shadow-sm">
                    <img src="https://images.unsplash.com/photo-1522881113590-b1ff854bf763?auto=format&fit=crop&w=600&q=80" alt="Blog" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <div class="text-xs text-accent font-semibold mb-2">TEKNOLOGI</div>
                        <h3 class="font-bold text-primary text-xl mb-3 hover:text-accent cursor-pointer">LaporIn Catat Rekor 1.000 Laporan Warga Diselesaikan</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">Aplikasi LaporIn terbukti efektif menjembatani aspirasi warga. Tingkat keberhasilan perbaikan kini mencapai 92% di Mataram.</p>
                        <a href="#" class="text-primary text-sm font-bold hover:text-accent">Baca Selengkapnya &rarr;</a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="" class="py-24 relative bg-primary overflow-hidden text-center">
        <div class="absolute inset-0 opacity-10">
            <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="w-full h-full">
                <path d="M0,0 L100,0 L100,100 L0,100 Z" fill="url(#pattern-dots)"></path>
                <defs>
                    <pattern id="pattern-dots" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse">
                        <circle fill="#ffffff" cx="2" cy="2" r="2"></circle>
                    </pattern>
                </defs>
            </svg>
        </div>
        <div class="container mx-auto px-4 lg:px-8 relative z-10">
            <h2 class="text-4xl lg:text-5xl font-bold text-white mb-6">Siap Melaporkan Masalah?</h2>
            <p class="text-accent text-lg max-w-2xl mx-auto mb-10">
                Siapkan foto bukti, alamat lengkap, deskripsi singkat, serta tanggal dan waktu kejadian. Mari bersinergi membangun kota ini.
            </p>
            <button class="bg-white text-primary px-10 py-4 rounded-full font-bold text-lg hover:bg-gray-100 hover:shadow-xl transition-all inline-flex items-center gap-3">
                <i class="fa-solid fa-file-pen"></i> Menuju Formulir Pengaduan
            </button>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="kontak" class="py-20 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                <!-- Info Kontak -->
                <div class="space-y-8">
                    <div>
                        <h2 class="text-3xl font-bold text-primary mb-4">Hubungi Kami</h2>
                        <p class="text-gray-600">Punya pertanyaan teknis atau ingin menjalin kerjasama? Tim layanan pelanggan kami siap membantu Anda di jam kerja.</p>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-graybg text-accent rounded-full flex items-center justify-center flex-shrink-0 text-xl">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-primary">Kantor Operasional LaporIn</h4>
                                <p class="text-gray-600 text-sm">Gedung Pelayanan Publik Lt. 2<br>Jl. Pejanggik No. 1, Mataram, NTB 83112</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-graybg text-accent rounded-full flex items-center justify-center flex-shrink-0 text-xl">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-primary">Email</h4>
                                <p class="text-gray-600 text-sm">bantuan@laporin-mataram.go.id</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-graybg text-accent rounded-full flex items-center justify-center flex-shrink-0 text-xl">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-primary">Telepon / WhatsApp</h4>
                                <p class="text-gray-600 text-sm">+62 811-3800-xxxx (Chat Only)<br>Call Center: 112</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Kontak -->
                <div class="bg-graybg p-8 rounded-2xl border border-gray-100">
                    <form class="space-y-6" onsubmit="event.preventDefault(); alert('Pesan Anda terkirim!');">
                        <div>
                            <label class="block text-sm font-semibold text-primary mb-2">Nama Lengkap</label>
                            <input type="text" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-accent" placeholder="Nama Anda" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-primary mb-2">Email</label>
                            <input type="email" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-accent" placeholder="email@contoh.com" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-primary mb-2">Pesan</label>
                            <textarea rows="4" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-accent" placeholder="Tuliskan pesan Anda di sini..." required></textarea>
                        </div>
                        <button type="submit" class="w-full bg-primary text-white py-3 rounded-xl font-bold hover:bg-opacity-90 transition-all">
                            Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-primary text-white pt-16 pb-8 border-t-[8px] border-accent">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <div class="lg:col-span-1">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-primary font-bold text-xl">
                            <i class="fa-solid fa-leaf"></i>
                        </div>
                        <span class="font-bold text-2xl tracking-tight">Lapor<span class="text-accent">In</span></span>
                    </div>
                    <p class="text-gray-300 text-sm leading-relaxed mb-6">
                        Menjadikan Kota Mataram lebih tertata dan aman lewat partisipasi aktif warganya. Setiap laporan Anda sangat berharga bagi kemajuan kota.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-accent transition-colors"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-accent transition-colors"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-accent transition-colors"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-6 text-accent">Menu Cepat</h4>
                    <ul class="space-y-3 text-gray-300 text-sm">
                        <li><a href="#beranda" class="hover:text-white hover:pl-1 transition-all">Beranda</a></li>
                        <li><a href="#tentang" class="hover:text-white hover:pl-1 transition-all">Tentang Kami</a></li>
                        <li><a href="#layanan" class="hover:text-white hover:pl-1 transition-all">Layanan</a></li>
                        <li><a href="#faq" class="hover:text-white hover:pl-1 transition-all">FAQ</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-6 text-accent">Layanan Pengaduan</h4>
                    <ul class="space-y-3 text-gray-300 text-sm">
                        <li><a href="#cta" class="hover:text-white hover:pl-1 transition-all">Lapor Jalan Rusak</a></li>
                        <li><a href="#cta" class="hover:text-white hover:pl-1 transition-all">Lapor Pohon Tumbang</a></li>
                        <li><a href="#cta" class="hover:text-white hover:pl-1 transition-all">Lapor Lampu Mati</a></li>
                        <li><a href="#portofolio" class="hover:text-white hover:pl-1 transition-all">Status Laporan</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-6 text-accent">Darurat Mataram</h4>
                    <div class="space-y-4 text-sm text-gray-300">
                        <div class="flex items-center gap-3 bg-white/5 p-3 rounded-lg border border-white/10">
                            <i class="fa-solid fa-phone-volume text-xl text-accent"></i>
                            <div>
                                <p class="text-xs text-gray-400">Pusat Panggilan Darurat</p>
                                <p class="font-bold text-white">112</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-white/5 p-3 rounded-lg border border-white/10">
                            <i class="fa-solid fa-fire text-xl text-accent"></i>
                            <div>
                                <p class="text-xs text-gray-400">Pemadam Kebakaran</p>
                                <p class="font-bold text-white">113</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-400 text-sm text-center md:text-left">&copy; 2026 LaporIn Mataram. Seluruh hak cipta dilindungi.</p>
                <div class="flex gap-4 text-sm text-gray-400">
                    <a href="#" class="hover:text-white">Kebijakan Privasi</a>
                    <span class="text-gray-600">|</span>
                    <a href="#" class="hover:text-white">Syarat Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Script for Interactions -->
    <script>
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Close mobile menu when a link is clicked
        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
            });
        });

        // FAQ Accordion
        const faqBtns = document.querySelectorAll('.faq-btn');
        faqBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const answer = btn.nextElementSibling;
                const icon = btn.querySelector('i');
                
                // Toggle open class
                answer.classList.toggle('open');
                
                // Rotate Icon
                if(answer.classList.contains('open')) {
                    icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
                    btn.classList.add('text-accent');
                } else {
                    icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
                    btn.classList.remove('text-accent');
                }
            });
        });
    </script>
</body>
</html>