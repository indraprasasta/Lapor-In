<?php
session_start();
require __DIR__ . '/database/conection.php';

// Pagination logic
$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Ambil total laporan selesai
$total_query = $pdo->query("SELECT COUNT(*) FROM laporan WHERE status = 'Selesai'");
$total_rows = $total_query->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Ambil data laporan selesai dengan pagination
$query_laporan = $pdo->prepare("SELECT laporan.*, users.nama as nama_pelapor FROM laporan JOIN users ON laporan.user_id = users.id WHERE status = 'Selesai' ORDER BY tanggal DESC LIMIT :limit OFFSET :offset");
$query_laporan->bindValue(':limit', $limit, PDO::PARAM_INT);
$query_laporan->bindValue(':offset', $offset, PDO::PARAM_INT);
$query_laporan->execute();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Laporan Terselesaikan - LaporIn Mataram</title>

    <!--CONFIG TAILWIND-->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3A5A40',
                        accent:  '#A3B18A',
                        warning: '#D97706',
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

<!-- LAPORAN TERSELESAIKAN -->
<section class="py-20">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="mb-12 text-center">
            <h2 class="text-3xl lg:text-4xl font-bold text-primary mb-2">Laporan Terselesaikan</h2>
            <p class="text-gray-600">Daftar semua laporan masyarakat yang telah berhasil diselesaikan beserta ulasannya.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if($query_laporan->rowCount() > 0): ?>
                <?php while($laporan = $query_laporan->fetch()): ?>
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm group">
                    <div class="relative h-56 overflow-hidden">
                        <?php if(!empty($laporan['foto'])): ?>
                        <img src="uploads/foto_laporan/<?php echo $laporan['foto']; ?>"
                            alt="<?php echo htmlspecialchars($laporan['judul']); ?>"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <?php else: ?>
                        <div class="w-full h-full bg-slate-200 flex items-center justify-center">
                            <i class="fa-solid fa-image text-4xl text-slate-400"></i>
                        </div>
                        <?php endif; ?>
                        <div class="absolute top-4 right-4 bg-accent text-white text-xs font-bold px-3 py-1 rounded-full">Selesai Diperbaiki</div>
                        <div class="absolute bottom-4 left-4 bg-black/50 text-white text-xs px-3 py-1 rounded-full backdrop-blur-sm">
                            <?php echo htmlspecialchars($laporan['kategori']); ?>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col h-[calc(100%-14rem)]">
                        <h3 class="font-bold text-lg text-primary mb-2 line-clamp-1"><?php echo htmlspecialchars($laporan['judul']); ?></h3>
                        <p class="text-sm text-gray-500 mb-1">
                            <i class="fa-solid fa-map-pin mr-2"></i>
                            <?php echo htmlspecialchars($laporan['kelurahan'] . ', ' . $laporan['kecamatan']); ?>
                        </p>
                        <p class="text-sm text-gray-500 mb-4">
                            <i class="fa-regular fa-calendar mr-2"></i>
                            <?php echo date('d F Y', strtotime($laporan['tanggal'])); ?>
                        </p>
                        
                        <?php if(!empty($laporan['rating'])): ?>
                        <div class="pt-4 border-t border-slate-100 mt-auto">
                            <div class="flex items-center gap-1 mb-2">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <i data-lucide="star" class="w-4 h-4 <?php echo ($i <= $laporan['rating']) ? 'text-warning fill-warning' : 'text-slate-300'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <?php if(!empty($laporan['ulasan'])): ?>
                            <p class="text-xs text-gray-600 italic line-clamp-3">"<?php echo htmlspecialchars($laporan['ulasan']); ?>"</p>
                            <?php endif; ?>
                            <p class="text-xs text-gray-400 mt-2 text-right">- <?php echo htmlspecialchars($laporan['nama_pelapor']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-gray-400 col-span-1 md:col-span-2 lg:col-span-3 text-center py-8">Belum ada laporan yang diselesaikan.</p>
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

<!-- JAVASCRIPT -->
<script>
lucide.createIcons();
</script>
</body>
</html>
