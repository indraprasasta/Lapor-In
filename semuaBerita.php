<?php
session_start();
require __DIR__ . '/database/conection.php';

// Pagination logic
$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Ambil total berita
$total_query = $pdo->query("SELECT COUNT(*) FROM berita");
$total_rows = $total_query->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Ambil data berita
$query_berita = $pdo->prepare("SELECT * FROM berita ORDER BY tanggal DESC LIMIT :limit OFFSET :offset");
$query_berita->bindValue(':limit', $limit, PDO::PARAM_INT);
$query_berita->bindValue(':offset', $offset, PDO::PARAM_INT);
$query_berita->execute();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Berita - LaporIn Mataram</title>

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
<body class="bg-graybg">

<!-- NAVBAR (Minimal) -->
<nav class="navbar bg-primary" style="background-color: var(--primary) !important;">
    <div class="container">
        <div class="navbar__inner">
            <a href="index.php" class="navbar__brand" style="color: white !important;">
                <span class="navbar__logo-icon">
                    <i class="fa-solid fa-arrow-left"></i>
                </span>
                <span class="navbar__brand-text">Kembali ke <span>Beranda</span></span>
            </a>
        </div>
    </div>
</nav>

<!-- BERITA -->
<section class="py-20">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="mb-12 text-center">
            <h2 class="text-3xl lg:text-4xl font-bold text-primary mb-2">Kabar Mataram</h2>
            <p class="text-gray-600">Semua berita terbaru seputar pembangunan dan infrastruktur kota.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
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
        
        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <div class="flex justify-center mt-12 gap-2">
            <?php if($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="w-10 h-10 flex items-center justify-center rounded-lg font-medium bg-white text-dark hover:bg-accent hover:text-white border border-slate-200 transition-colors">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            <?php endif; ?>
            
            <?php for($i=1; $i<=$total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="w-10 h-10 flex items-center justify-center rounded-lg font-medium transition-colors <?php echo ($i == $page) ? 'bg-primary text-white' : 'bg-white text-dark hover:bg-accent hover:text-white border border-slate-200'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="w-10 h-10 flex items-center justify-center rounded-lg font-medium bg-white text-dark hover:bg-accent hover:text-white border border-slate-200 transition-colors">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="container">
        <div class="footer__bottom">
            <p>&copy; 2026 LaporIn Mataram. Seluruh hak cipta dilindungi.</p>
        </div>
    </div>
</footer>

<!-- POPUP BERITA (MODAL WINDOW) -->
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
// Popup Berita (Modal Window)
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
</script>
</body>
</html>
