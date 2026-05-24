<?php
session_start();
require __DIR__ . '/database/conection.php';

// Deteksi status login & tentukan link yang benar
$is_logged_in = false;
$link_dashboard = 'login.php';
$link_laporan = 'login.php';
$link_formulir = 'login.php';
$link_berita = 'login.php';
$logged_in_name = '';
$logged_in_role = '';

if (isset($_SESSION['user_id'])) {
    $is_logged_in = true;
    $link_dashboard = 'user/beranda.php';
    $link_laporan = 'user/daftarLaporan.php';
    $link_formulir = 'user/buatLaporan.php';
    $link_berita = 'user/beranda.php';
    $logged_in_name = $_SESSION['nama'] ?? 'User';
    $logged_in_role = 'Masyarakat';
} elseif (isset($_SESSION['petugas_id'])) {
    $is_logged_in = true;
    $link_dashboard = 'petugas/beranda.php';
    $link_laporan = 'petugas/pengaduan.php?status=Selesai';
    $link_formulir = 'force_logout.php';
    $link_berita = 'petugas/beranda.php';
    $logged_in_name = $_SESSION['petugas_nama'] ?? 'Petugas';
    $logged_in_role = 'Petugas';
} elseif (isset($_SESSION['admin_id'])) {
    $is_logged_in = true;
    $link_dashboard = 'admin/beranda.php';
    $link_laporan = 'admin/dataLaporan.php';
    $link_formulir = 'force_logout.php';
    $link_berita = 'admin/daftarBerita.php';
    $logged_in_name = $_SESSION['admin_nama'] ?? 'Admin';
    $logged_in_role = 'Admin';
}
// Ambil data berita
$query_berita = $pdo->query("SELECT * FROM berita ORDER BY tanggal DESC LIMIT 3");

// Ambil laporan selesai
$query_selesai = $pdo->query("
    SELECT laporan.*, users.nama as nama_pelapor
    FROM laporan
    JOIN users ON laporan.user_id = users.id
    WHERE laporan.status = 'Selesai'
    ORDER BY laporan.tanggal DESC
    LIMIT 3
");
$query_kategori = $pdo->query("
    SELECT * FROM kategori_laporan
    WHERE aktif = 1
    ORDER BY tanggal_dibuat ASC
");

// Ambil akumulasi rating
$query_rating = $pdo->query("SELECT AVG(rating) as avg_rating, COUNT(rating) as total_rating FROM laporan WHERE rating IS NOT NULL AND rating > 0");
$rating_data = $query_rating->fetch();
$avg_rating = $rating_data['total_rating'] > 0 ? round($rating_data['avg_rating'], 1) : 0;
$total_rating = $rating_data['total_rating'];

// Ambil akumulasi rating per kategori
$query_rating_kategori = $pdo->query("
    SELECT kategori, AVG(rating) as avg_rating, COUNT(rating) as total_rating 
    FROM laporan 
    WHERE rating IS NOT NULL AND rating > 0 
    GROUP BY kategori 
    ORDER BY total_rating DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LaporIn - Portal Pelaporan Infrastruktur Mataram</title>

    <!--CONFIG TAILWIND-->
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
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="src/style.css">
</head>
<!-- untuk animasi transisi pada saat muncul -->
<style>
.reveal {
    opacity: 0;
    transform: translateY(40px);
    transition: opacity 0.7s ease, transform 0.7s ease;
}
.reveal.visible {
    opacity: 1;
    transform: translateY(0);
}
.reveal-delay-1 { transition-delay: 0.1s; }
.reveal-delay-2 { transition-delay: 0.2s; }
</style>
<body>

<!-- NAVBAR -->
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
            <?php if($is_logged_in): ?>
            <a href="<?php echo $link_dashboard; ?>" class="navbar__cta">Dashboard</a>
            <?php else: ?>
            <a href="login.php" class="navbar__cta">Login Now</a>
            <?php endif; ?>
            
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
        <?php if($is_logged_in): ?>
        <a href="<?php echo $link_dashboard; ?>" class="navbar__mobile-cta">Dashboard</a>
        <?php else: ?>
        <a href="login.php" class="navbar__mobile-cta">Lapor Sekarang</a>
        <?php endif; ?>
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
                <a href="<?php echo $link_formulir; ?>" class="btn btn--accent">
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

            <div class="about__images reveal reveal-delay-1">
                <img src="https://images.unsplash.com/photo-1760331339913-4c915f2ac5c5?q=80&w=688&auto=format&fit=crop" alt="Infrastruktur Mataram">
            </div>

            <div class="about__text reveal reveal-delay-2">
                <h2 class="section__title">Tentang <span>Kami</span></h2>
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
    <div class="container reveal reveal-delay-1">
        <header class="services__head">
            <span class="section__label">Apa Yang Bisa Dilaporkan?</span>
            <h2 class="section__title">Layanan Pengaduan Kami</h2>
        </header>

        <div class="services__grid reveal reveal-delay-1">

        <?php if($query_kategori->rowCount() > 0): ?>
            
            <?php while($kategori = $query_kategori->fetch()): ?>
                
            <article class="service-card">
                <div class="service-card__icon reveal reveal-delay-2">
                    <i data-lucide="<?php echo $kategori['icon']; ?>"></i>
                </div>

                <h3><?php echo htmlspecialchars($kategori['nama_kategori']); ?></h3>

                <p><?php echo htmlspecialchars($kategori['deskripsi']); ?></p>
            </article>

            <?php endwhile; ?>

        <?php else: ?>

            <p class="text-center col-span-3 text-gray-400">
                Belum ada kategori tersedia.
            </p>

        <?php endif; ?>

        </div>
        </div>
    </div>
</section>



<!-- PORTOFOLIO -->
<section id="portofolio" class="py-20 bg-primary">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="flex flex-col lg:flex-row justify-between items-end mb-12">
            <div class="reveal reveal-delay-1">
                <span class="text-accent font-semibold tracking-wider uppercase text-sm">Aksi Nyata</span>
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 mt-2">
                    <h2 class="text-3xl lg:text-4xl font-bold text-white">Laporan Terselesaikan</h2>
                    <?php if($total_rating > 0): ?>
                    <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md border border-white/20 px-3 py-1.5 rounded-xl text-white">
                        <span class="text-warning font-bold text-lg"><?php echo number_format($avg_rating, 1); ?></span>
                        <div class="flex items-center">
                            <?php 
                            $rating_val = round($avg_rating);
                            for($i=1; $i<=5; $i++): 
                            ?>
                                <i data-lucide="star" class="w-4 h-4 <?php echo ($i <= $rating_val) ? 'text-warning fill-warning' : 'text-white/30'; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <span class="text-xs text-white/80 ml-1">(<?php echo $total_rating; ?> ulasan)</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <a href="semuaLaporan.php" class="text-white border-b border-accent hover:text-accent transition-colors mt-4 lg:mt-0">Lihat Semua Laporan</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 reveal reveal-delay-2">
            <?php if($query_selesai->rowCount() > 0): ?>
                <?php while($selesai = $query_selesai->fetch()): ?>
                <div class="bg-white rounded-2xl overflow-hidden group">
                    <div class="relative h-56 overflow-hidden">
                        <?php if(!empty($selesai['foto'])): ?>
                        <img src="uploads/foto_laporan/<?php echo $selesai['foto']; ?>"
                            alt="<?php echo htmlspecialchars($selesai['judul']); ?>"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <?php else: ?>
                        <div class="w-full h-full bg-slate-200 flex items-center justify-center">
                            <i class="fa-solid fa-image text-4xl text-slate-400"></i>
                        </div>
                        <?php endif; ?>
                        <div class="absolute top-4 right-4 bg-accent text-white text-xs font-bold px-3 py-1 rounded-full">Selesai Diperbaiki</div>
                        <!-- Badge kategori -->
                        <div class="absolute bottom-4 left-4 bg-black/50 text-white text-xs px-3 py-1 rounded-full backdrop-blur-sm">
                            <?php echo $selesai['kategori']; ?>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-lg text-primary mb-2 line-clamp-1"><?php echo htmlspecialchars($selesai['judul']); ?></h3>
                        <p class="text-sm text-gray-500 mb-1">
                            <i class="fa-solid fa-map-pin mr-2"></i>
                            <?php echo $selesai['kelurahan'] . ', ' . $selesai['kecamatan']; ?>
                        </p>
                        <p class="text-sm text-gray-500 mb-4">
                            <i class="fa-regular fa-calendar mr-2"></i>
                            <?php echo date('d F Y', strtotime($selesai['tanggal'])); ?>
                        </p>
                        <?php if(!empty($selesai['rating'])): ?>
                        <div class="pt-4 border-t border-slate-100">
                            <div class="flex items-center gap-1 mb-2">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <i data-lucide="star" class="w-4 h-4 <?php echo ($i <= $selesai['rating']) ? 'text-warning fill-warning' : 'text-slate-300'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <?php if(!empty($selesai['ulasan'])): ?>
                            <p class="text-xs text-gray-600 italic line-clamp-2">"<?php echo htmlspecialchars($selesai['ulasan']); ?>"</p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-3 text-center py-12">
                    <p class="text-white/60 text-sm">Belum ada laporan yang selesai.</p>
                </div>
            <?php endif; ?>
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
            <div class="howto__step reveal reveal-delay-1">
                <div class="howto__circle">1</div>
                <h4>Ambil Foto</h4>
                <p>Foto infrastruktur yang rusak dengan jelas.</p>
            </div>
            <div class="howto__step reveal reveal-delay-2">
                <div class="howto__circle">2</div>
                <h4>Detail Alamat</h4>
                <p>Berikan alamat spesifik & deskripsi kerusakannya.</p>
            </div>
            <div class="howto__step reveal reveal-delay-3">
                <div class="howto__circle">3</div>
                <h4>Waktu Kejadian</h4>
                <p>Cantumkan tanggal & jam pantauan Anda.</p>
            </div>
            <div class="howto__step reveal reveal-delay-4">
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
        <header class="faq__head reveal reveal-delay-1">
            <h2 class="section__title">Pertanyaan Umum (FAQ)</h2>
        </header>

        <ul class="faq__list reveal reveal-delay-2">
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
            <div class="reveal reveal-delay-1">
                <h2 class="text-3xl lg:text-4xl font-bold text-primary mb-2">Kabar Mataram</h2>
                <p class="text-gray-600">Berita terbaru seputar pembangunan dan infrastruktur kota.</p>
            </div>
            <a href="semuaBerita.php" class="hidden md:block text-accent hover:text-primary font-semibold">
                Lihat Semua Berita <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 reveal reveal-delay-2">
            <?php if($query_berita->rowCount() > 0): ?>
                <?php while($berita = $query_berita->fetch()): ?>
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
                        <h3 class="font-bold text-primary text-xl mb-3"><?php echo htmlspecialchars($berita['judul']); ?></h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3"><?php echo substr(strip_tags($berita['isi']), 0, 150) . '...'; ?></p>
                        
                        <!-- Tombol Baca Selengkapnya dengan data attribute -->
                        <button 
                            onclick="bukaBerita(this)"
                            data-judul="<?php echo htmlspecialchars($berita['judul']); ?>"
                            data-kategori="<?php echo $berita['kategori']; ?>"
                            data-tanggal="<?php echo date('d F Y', strtotime($berita['tanggal'])); ?>"
                            data-isi="<?php echo htmlspecialchars($berita['isi']); ?>"
                            data-foto="<?php echo !empty($berita['foto']) ? 'uploads/foto_berita/' . $berita['foto'] : ''; ?>"
                            class="text-primary text-sm font-bold hover:text-accent transition-colors cursor-pointer bg-transparent border-none p-0">
                            Baca Selengkapnya &rarr;
                        </button>
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
    <div class="container cta__inner reveal reveal-delay-1">
        <h2>Siap Melaporkan Masalah?</h2>
        <p>Siapkan foto bukti, alamat lengkap, deskripsi singkat, serta tanggal dan waktu kejadian. Mari bersinergi membangun kota ini.</p>
        <a href="<?php echo $link_formulir; ?>" class="btn--primary-cta">
            <i class="fa-solid fa-file-pen"></i> Menuju Formulir Pengaduan
        </a>
    </div>
</section>

<!-- CONTACT -->
<section class="section section--white" id="kontak">
    <div class="container">
        <div class="contact__grid">

            <!-- Info Kontak -->
            <div class="contact__info reveal reveal-delay-1">
                <h2>Hubungi Kami</h2>
                <p>Punya pertanyaan atau butuh bantuan? Tim kami siap membantu Anda di jam kerja. Kunjungi kantor kami atau hubungi melalui kontak berikut.</p>

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
                            <p>+62 811-3800-1827 (Chat Only)<br>Call Center: 112</p>
                        </div>
                    </div>

                    <div class="contact__item">
                        <div class="contact__item-icon"><i class="fa-solid fa-clock"></i></div>
                        <div>
                            <h4>Jam Operasional</h4>
                            <p>Senin – Jumat: 08.00 – 16.00 WITA<br>Sabtu – Minggu: 08.00 - 12.00 WITA</p>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi Cepat -->
                <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem; flex-wrap: wrap;">
                    <a href="https://wa.me/628113801827" target="_blank"
                        style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--accent); color: white; padding: 0.6rem 1.2rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; text-decoration: none; transition: opacity 0.2s;">
                        <i class="fa-brands fa-whatsapp"></i> WhatsApp
                    </a>
                    <a href="mailto:bantuan@laporin-mataram.go.id"
                        style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--primary); color: white; padding: 0.6rem 1.2rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; text-decoration: none; transition: opacity 0.2s;">
                        <i class="fa-solid fa-envelope"></i> Kirim Email
                    </a>
                </div>
            </div>

            <!-- Google Maps -->
            <div class="contact__map reveal reveal-delay-2">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3956.0!2d116.1167!3d-8.5833!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dcdb5b4c1234567%3A0x1234567890abcdef!2sJl.%20Pejanggik%2C%20Mataram%2C%20Nusa%20Tenggara%20Bar.!5e0!3m2!1sid!2sid!4v1234567890"
                    width="90%"
                    height="90%"
                    style="border: 0; border-radius: 16px;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
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
                    <a href="https://facebook.com" class="footer__social" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://twitter.com" class="footer__social" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                    <a href="https://instagram.com" class="footer__social" target="_blank"><i class="fa-brands fa-instagram"></i></a>
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
                    <li><a href="<?php echo $link_formulir; ?>">Lapor Jalan Rusak</a></li>
                    <li><a href="<?php echo $link_formulir; ?>">Lapor Pohon Tumbang</a></li>
                    <li><a href="<?php echo $link_formulir; ?>">Lapor Lampu Mati</a></li>
                    <li><a href="<?php echo $is_logged_in ? $link_laporan : '#portofolio'; ?>">Status Laporan</a></li>
                </ul>
            </div>

            <div class="footer__col reveal reveal-delay-1">
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

        </div>
    </div>
</footer>
<!-- POPUP BERITA -->
<div class="popup-overlay" id="popupBerita" onclick="tutupBerita(event)">
    <div class="popup-box">
        <div class="popup-box__wrapper">
            <button class="popup-box__close" onclick="tutupBeritaBtn()">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div id="popup-img-container"></div>
        </div>
        <div class="popup-box__body">
            <span class="popup-box__kategori" id="popup-kategori"></span>
            <h2 class="popup-box__judul" id="popup-judul"></h2>
            <p class="popup-box__meta">
                <i class="fa-regular fa-calendar"></i>
                <span id="popup-tanggal"></span>
            </p>
            <div class="popup-box__isi" id="popup-isi"></div>
        </div>
    </div>
</div>

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

// Popup Berita
function bukaBerita(btn) {
    const judul    = btn.getAttribute('data-judul');
    const kategori = btn.getAttribute('data-kategori');
    const tanggal  = btn.getAttribute('data-tanggal');
    const isi      = btn.getAttribute('data-isi');
    const foto     = btn.getAttribute('data-foto');

    // Isi konten popup
    document.getElementById('popup-judul').textContent    = judul;
    document.getElementById('popup-kategori').textContent = kategori;
    document.getElementById('popup-tanggal').textContent  = tanggal;
    document.getElementById('popup-isi').innerHTML        = isi.replace(/\n/g, '<br>');

    // Foto
    const imgContainer = document.getElementById('popup-img-container');
    if (foto) {
        imgContainer.innerHTML = `<img src="${foto}" alt="${judul}" class="popup-box__img">`;
    } else {
        imgContainer.innerHTML = `<div class="popup-box__img-placeholder"><i class="fa-solid fa-image"></i></div>`;
    }

    // Buka popup
    document.getElementById('popupBerita').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function tutupBeritaBtn() {
    document.getElementById('popupBerita').classList.remove('open');
    document.body.style.overflow = 'auto';
}

function tutupBerita(e) {
    // Tutup hanya jika klik di luar popup-box
    if (e.target === document.getElementById('popupBerita')) {
        tutupBeritaBtn();
    }
}

// Tutup dengan tombol Escape
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') tutupBeritaBtn();
});

lucide.createIcons();
// Scroll Reveal
window.addEventListener('load', function () {
    const revealElements = document.querySelectorAll('.reveal');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    revealElements.forEach(el => observer.observe(el));
});

</script>

</body>
</html>
