<?php
require __DIR__ . '/database/conection.php';
$query_berita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LaporIn - Portal Pelaporan Infrastruktur Mataram</title>

    <!-- Hanya untuk bagian Berita & Portofolio (Tailwind) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3A5A40',
                        accent:  '#A3B18A',
                        graybg:  '#f8fafc',
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ========================
     NAVBAR
========================= -->
<nav class="navbar">
    <div class="container">
        <div class="navbar__inner">

            <a href="#" class="navbar__brand">
                <span class="navbar__logo-icon">
                    <i class="fa-solid fa-leaf"></i>
                </span>
                <span class="navbar__brand-text">Lapor<span>In</span></span>
            </a>

            <ul class="navbar__menu" role="list">
                <li><a href="#beranda">Beranda</a></li>
                <li><a href="#tentang">Tentang Kami</a></li>
                <li><a href="#layanan">Layanan</a></li>
                <li><a href="#portofolio">Portofolio</a></li>
                <li><a href="#blog">Blog</a></li>
                <li><a href="#kontak">Kontak</a></li>
            </ul>
            <a href="login.php" class="navbar__cta">Login Now</a>
            
            <button class="navbar__toggle" id="mobile-menu-btn" aria-label="Buka menu">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="navbar__mobile" id="mobile-menu" role="navigation">
        <a href="#beranda">Beranda</a>
        <a href="#tentang">Tentang Kami</a>
        <a href="#layanan">Layanan</a>
        <a href="#portofolio">Portofolio</a>
        <a href="#kontak">Kontak</a>
        <a href="login.php" class="navbar__mobile-cta">Lapor Sekarang</a>
    </div>
</nav>
<!-- HERO -->
<section class="hero" id="beranda">
    <div class="hero__bg">
        <img src="https://i.pinimg.com/1200x/fc/02/64/fc026433a20db53bc4447d4e41f8f830.jpg" alt="Kota Mataram">
        <div class="hero__overlay"></div>
    </div>

    <div class="container">
        <div class="hero__content">
            <h1 class="hero__title">
                Wujudkan <span>Mataram</span> yang Lebih Nyaman
            </h1>
            <p class="hero__desc">
                Temukan jalan rusak, pohon rawan tumbang, atau lampu jalan mati? Laporkan dengan mudah hanya melalui foto dan detail lokasi. Kami pastikan aduan Anda ditangani dengan cepat.
            </p>
            <div class="hero__actions">
                <a href="login.php" class="btn btn--accent">
                    <i class="fa-solid fa-camera"></i> Buat Laporan
                </a>
                <a href="#tentang" class="btn btn--outline">
                    Pelajari Lebih Lanjut
                </a>
            </div>
        </div>
    </div>
</section>
<!-- tentang kami -->
<section class="section section--gray" id="tentang">
    <div class="container">
        <div class="about__grid">

            <div class="about__images">
                <img src="https://images.unsplash.com/photo-1760331339913-4c915f2ac5c5?q=80&w=688&auto=format&fit=crop" alt="Infrastruktur Mataram">
            </div>

            <div class="about__text">
                <h2 class="section__title">Tentang <span>LaporIn</span></h2>
                <p>
                    LaporIn adalah inisiatif digital yang menjembatani warga Kota Mataram dengan instansi terkait untuk menciptakan infrastruktur kota yang tangguh. Kami percaya bahwa setiap warga memiliki peran penting dalam memelihara keindahan dan keamanan lingkungan.
                </p>
                <ul class="about__list">
                    <li><i class="fa-solid fa-circle-check"></i> Transparansi proses perbaikan</li>
                    <li><i class="fa-solid fa-circle-check"></i> Respon cepat dari dinas terkait</li>
                    <li><i class="fa-solid fa-circle-check"></i> Terintegrasi dengan sistem tata kota</li>
                </ul>
            </div>

        </div>
    </div>
</section>

<!-- LAYANAN -->
<section class="section section--white" id="layanan">
    <div class="container">
        <header class="services__head">
            <span class="section__label">Apa Yang Bisa Dilaporkan?</span>
            <h2 class="section__title">Layanan Pengaduan Kami</h2>
        </header>

        <div class="services__grid">

            <article class="service-card">
                <div class="service-card__icon"><i class="fa-solid fa-road"></i></div>
                <h3>Jalan Rusak</h3>
                <p>Laporkan jalan berlubang, aspal terkelupas, atau trotoar yang membahayakan pengguna jalan dan pejalan kaki.</p>
            </article>

            <article class="service-card ">
                <div class="service-card__icon"><i class="fa-solid fa-tree"></i></div>
                <h3>Pohon Tumbang</h3>
                <p>Bantu cegah kecelakaan dengan melaporkan pohon rawan tumbang, dahan patah, atau pohon tumbang yang menutupi jalan.</p>
            </article>

            <article class="service-card">
                <div class="service-card__icon"><i class="fa-regular fa-lightbulb"></i></div>
                <h3>Lampu Jalan Mati</h3>
                <p>Beri tahu kami jika ada Penerangan Jalan Umum (PJU) yang padam untuk mengembalikan rasa aman di malam hari.</p>
            </article>

            <article class="service-card">
                <div class="service-card__icon"><i class="fa-solid fa-building"></i></div>
                <h3>Fasilitas Umum</h3>
                <p>Laporkan kerusakan pada fasilitas publik seperti taman kota, halte bus, atau sarana umum lainnya agar segera diperbaiki.</p>
            </article>

            <article class="service-card ">
                <div class="service-card__icon"><i class="fa-solid fa-bridge"></i></div>
                <h3>Jembatan</h3>
                <p>Informasikan jika ada kerusakan struktur jembatan, aspal berlubang di jembatan, atau pembatas yang membahayakan.</p>
            </article>

            <article class="service-card ">
                <div class="service-card__icon"><i class="fa-solid fa-ellipsis"></i></div>
                <h3>Lainnya</h3>
                <p>Sampaikan aduan infrastruktur lainnya yang tidak termasuk dalam kategori di atas untuk segera kami tindaklanjuti.</p>
            </article>

        </div>
    </div>
</section>

<!-- PORTOFOLIO -->
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

<!-- CARA KERJA -->
<section class="section section--gray">
    <div class="container">
        <header class="howto__head">
            <h2 class="section__title">Cara Kerja LaporIn</h2>
            <p>Sistem kami membutuhkan 4 langkah mudah untuk memastikan laporan Anda valid dan cepat ditangani.</p>
        </header>

        <div class="howto__steps">
            <div class="howto__step">
                <div class="howto__circle">1</div>
                <h4>Ambil Foto</h4>
                <p>Foto infrastruktur yang rusak dengan jelas.</p>
            </div>
            <div class="howto__step">
                <div class="howto__circle">2</div>
                <h4>Detail Alamat</h4>
                <p>Berikan alamat spesifik & deskripsi kerusakannya.</p>
            </div>
            <div class="howto__step">
                <div class="howto__circle">3</div>
                <h4>Waktu Kejadian</h4>
                <p>Cantumkan tanggal & jam pantauan Anda.</p>
            </div>
            <div class="howto__step">
                <div class="howto__circle howto__circle--filled">
                    <i class="fa-solid fa-check"></i>
                </div>
                <h4>Selesai</h4>
                <p>Laporan diverifikasi & diteruskan ke dinas.</p>
            </div>
        </div>
    </div>
</section>

<!-- FAQ ITEM -->
<section class="section section--white" id="faq">
    <div class="container">
        <header class="faq__head">
            <h2 class="section__title">Pertanyaan Umum (FAQ)</h2>
        </header>

        <ul class="faq__list">
            <li class="faq__item">
                <button class="faq__btn" aria-expanded="false">
                    Bagaimana cara melampirkan foto laporan?
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
                <div class="faq__answer">
                    <p>Anda cukup menekan tombol "Lapor Sekarang", lalu pada formulir akan tersedia kolom unggah file. Pastikan foto jelas, tidak buram, dan menunjukkan masalah (jalan, pohon, lampu) secara utuh.</p>
                </div>
            </li>
            <li class="faq__item">
                <button class="faq__btn" aria-expanded="false">
                    Berapa lama laporan akan ditindaklanjuti?
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
                <div class="faq__answer">
                    <p>Sistem LaporIn terintegrasi dengan dinas terkait di Mataram. Rata-rata waktu verifikasi adalah 1x24 jam, dan penindakan fisik memakan waktu 1-3 hari kerja tergantung tingkat urgensi dan cuaca.</p>
                </div>
            </li>
            <li class="faq__item">
                <button class="faq__btn" aria-expanded="false">
                    Apakah saya perlu mencantumkan waktu kejadian secara spesifik?
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
                <div class="faq__answer">
                    <p>Ya, tanggal dan keterangan waktu sangat penting. Ini membantu tim lapangan menilai kondisi terbaru untuk investigasi yang akurat.</p>
                </div>
            </li>
        </ul>
    </div>
</section>

<!-- BERITA -->
<section id="blog" class="py-20 bg-gray-50">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="text-3xl lg:text-4xl font-bold text-primary mb-2">Kabar Mataram</h2>
                <p class="text-gray-600">Berita terbaru seputar pembangunan dan infrastruktur kota.</p>
            </div>
            <a href="#" class="hidden md:block text-accent hover:text-primary font-semibold">
                Lihat Semua Berita <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php if(mysqli_num_rows($query_berita) > 0): ?>
                <?php while($berita = mysqli_fetch_assoc($query_berita)): ?>
                <article class="bg-white rounded-2xl overflow-hidden shadow-sm">
                    <?php if(!empty($berita['foto'])): ?>
                    <img src="uploads/foto_berita/<?php echo $berita['foto']; ?>"
                        alt="<?php echo htmlspecialchars($berita['judul']); ?>"
                        class="w-full h-48 object-cover">
                    <?php else: ?>
                    <div class="w-full h-48 bg-slate-200 flex items-center justify-center">
                        <span class="text-slate-400 text-sm">Tidak ada foto</span>
                    </div>
                    <?php endif; ?>
                    <div class="p-6">
                        <p class="text-xs text-accent font-semibold mb-2 uppercase"><?php echo $berita['kategori']; ?></p>
                        <h3 class="font-bold text-primary text-xl mb-3 hover:text-accent cursor-pointer"><?php echo htmlspecialchars($berita['judul']); ?></h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3"><?php echo substr(strip_tags($berita['isi']), 0, 150) . '...'; ?></p>
                        <a href="#" class="text-primary text-sm font-bold hover:text-accent">Baca Selengkapnya &rarr;</a>
                    </div>
                </article>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-gray-400 col-span-3 text-center py-8">Belum ada berita tersedia.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta">
    <div class="cta__dots">
        <svg viewBox="0 0 100 100" preserveAspectRatio="none" width="100%" height="100%">
            <defs>
                <pattern id="dots" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse">
                    <circle fill="#ffffff" cx="2" cy="2" r="2"></circle>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#dots)"></rect>
        </svg>
    </div>
    <div class="container cta__inner">
        <h2>Siap Melaporkan Masalah?</h2>
        <p>Siapkan foto bukti, alamat lengkap, deskripsi singkat, serta tanggal dan waktu kejadian. Mari bersinergi membangun kota ini.</p>
        <a href="login.php" class="btn--primary-cta">
            <i class="fa-solid fa-file-pen"></i> Menuju Formulir Pengaduan
        </a>
    </div>
</section>

<!-- CONTACT -->
<section class="section section--white" id="kontak">
    <div class="container">
        <div class="contact__grid">

            <div class="contact__info">
                <h2>Hubungi Kami</h2>
                <p>Punya pertanyaan teknis atau ingin menjalin kerjasama? Tim layanan pelanggan kami siap membantu Anda di jam kerja.</p>
                <div class="contact__items">
                    <div class="contact__item">
                        <div class="contact__item-icon"><i class="fa-solid fa-location-dot"></i></div>
                        <div>
                            <h4>Kantor Operasional LaporIn</h4>
                            <p>Gedung Pelayanan Publik Lt. 2<br>Jl. Pejanggik No. 1, Mataram, NTB 83112</p>
                        </div>
                    </div>
                    <div class="contact__item">
                        <div class="contact__item-icon"><i class="fa-solid fa-envelope"></i></div>
                        <div>
                            <h4>Email</h4>
                            <p>bantuan@laporin-mataram.go.id</p>
                        </div>
                    </div>
                    <div class="contact__item">
                        <div class="contact__item-icon"><i class="fa-solid fa-phone"></i></div>
                        <div>
                            <h4>Telepon / WhatsApp</h4>
                            <p>+62 811-3800-xxxx (Chat Only)<br>Call Center: 112</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact__form">
                <form onsubmit="event.preventDefault(); alert('Pesan Anda terkirim!');">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" placeholder="Nama Anda" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" placeholder="email@contoh.com" required>
                    </div>
                    <div class="form-group">
                        <label for="pesan">Pesan</label>
                        <textarea id="pesan" rows="4" placeholder="Tuliskan pesan Anda di sini..." required></textarea>
                    </div>
                    <button type="submit" class="btn--submit">Kirim Pesan</button>
                </form>
            </div>

        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="container">
        <div class="footer__grid">

            <div>
                <div class="footer__brand-logo">
                    <span class="footer__brand-icon"><i class="fa-solid fa-leaf"></i></span>
                    <span class="footer__brand-name">Lapor<span>In</span></span>
                </div>
                <p class="footer__desc">Menjadikan Kota Mataram lebih tertata dan aman lewat partisipasi aktif warganya. Setiap laporan Anda sangat berharga bagi kemajuan kota.</p>
                <div class="footer__socials">
                    <a href="#" class="footer__social"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="footer__social"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" class="footer__social"><i class="fa-brands fa-instagram"></i></a>
                </div>
            </div>

            <div class="footer__col">
                <h4>Menu Cepat</h4>
                <ul class="footer__links">
                    <li><a href="#beranda">Beranda</a></li>
                    <li><a href="#tentang">Tentang Kami</a></li>
                    <li><a href="#layanan">Layanan</a></li>
                    <li><a href="#faq">FAQ</a></li>
                </ul>
            </div>

            <div class="footer__col">
                <h4>Layanan Pengaduan</h4>
                <ul class="footer__links">
                    <li><a href="login.php">Lapor Jalan Rusak</a></li>
                    <li><a href="login.php">Lapor Pohon Tumbang</a></li>
                    <li><a href="login.php">Lapor Lampu Mati</a></li>
                    <li><a href="#portofolio">Status Laporan</a></li>
                </ul>
            </div>

            <div class="footer__col">
                <h4>Darurat Mataram</h4>
                <div class="footer__emergency">
                    <div class="footer__emergency-item">
                        <i class="fa-solid fa-phone-volume"></i>
                        <div>
                            <small>Pusat Panggilan Darurat</small>
                            <strong>112</strong>
                        </div>
                    </div>
                    <div class="footer__emergency-item">
                        <i class="fa-solid fa-fire"></i>
                        <div>
                            <small>Pemadam Kebakaran</small>
                            <strong>113</strong>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="footer__bottom">
            <p>&copy; 2026 LaporIn Mataram. Seluruh hak cipta dilindungi.</p>
            <div class="footer__bottom-links">
                <a href="#">Kebijakan Privasi</a>
                <span>|</span>
                <a href="#">Syarat Ketentuan</a>
                <span>|</span>
                <a href="admin/login.php">Portal Admin</a>
            </div>
        </div>
    </div>
</footer>

<!-- JAVASCRIPT -->
<script>
    // Mobile Menu Toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu    = document.getElementById('mobile-menu');

    mobileMenuBtn.addEventListener('click', () => {
        mobileMenu.classList.toggle('open');
    });

    // Tutup mobile menu saat link diklik
    mobileMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => mobileMenu.classList.remove('open'));
    });

    // FAQ Accordion
    document.querySelectorAll('.faq__btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const answer = btn.nextElementSibling;
            const icon   = btn.querySelector('i');
            const isOpen = answer.classList.contains('open');

            // Tutup semua dulu
            document.querySelectorAll('.faq__answer').forEach(a => a.classList.remove('open'));
            document.querySelectorAll('.faq__btn i').forEach(i => {
                i.classList.replace('fa-chevron-up', 'fa-chevron-down');
            });

            // Buka yang diklik (jika belum terbuka)
            if (!isOpen) {
                answer.classList.add('open');
                icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
                btn.setAttribute('aria-expanded', 'true');
            } else {
                btn.setAttribute('aria-expanded', 'false');
            }
        });
    });
</script>

</body>
</html>
