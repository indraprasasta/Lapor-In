<?php
$current_page = basename($_SERVER['PHP_SELF']);
$sidebar_user_id = $_SESSION['user_id'] ?? null;
$sidebar_nama = 'User';
$sidebar_foto = null;

if ($sidebar_user_id && isset($pdo)) {
    $stmt_sidebar = $pdo->prepare("SELECT nama, foto FROM users WHERE id = :id");
    $stmt_sidebar->execute([':id' => $sidebar_user_id]);
    if ($sidebar_row = $stmt_sidebar->fetch()) {
        $sidebar_nama = $sidebar_row['nama'];
        $sidebar_foto = $sidebar_row['foto'];
    }
}
?>
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
        <a href="beranda.php" class="flex items-center px-3 py-2.5 <?php echo ($current_page == 'beranda.php') ? 'bg-primary/10 text-primary' : 'text-muted hover:text-dark hover:bg-slate-50 transition-colors'; ?> rounded-lg font-medium group">
            <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 <?php echo ($current_page == 'beranda.php') ? '' : 'group-hover:text-primary transition-colors'; ?>"></i>
            Beranda
        </a>
        <a href="buatLaporan.php"
            class="flex items-center px-3 py-2.5 <?php echo ($current_page == 'buatLaporan.php') ? 'bg-primary/10 text-primary' : 'text-muted hover:text-dark hover:bg-slate-50 transition-colors'; ?> rounded-lg font-medium group">
            <i data-lucide="plus circle" class="w-5 h-5 mr-3 <?php echo ($current_page == 'buatLaporan.php') ? '' : 'group-hover:text-primary transition-colors'; ?>"></i>
            Buat Laporan
        </a>
        <a href="daftarLaporan.php"
            class="flex items-center px-3 py-2.5 <?php echo (in_array($current_page, ['daftarLaporan.php', 'detailLaporan.php', 'editLaporan.php'])) ? 'bg-primary/10 text-primary' : 'text-muted hover:text-dark hover:bg-slate-50 transition-colors'; ?> rounded-lg font-medium group">
            <i data-lucide="file-text" class="w-5 h-5 mr-3 <?php echo (in_array($current_page, ['daftarLaporan.php', 'detailLaporan.php', 'editLaporan.php'])) ? '' : 'group-hover:text-primary transition-colors'; ?>"></i>
            Laporan Saya
        </a>
        <a href="profile.php"
            class="flex items-center px-3 py-2.5 <?php echo ($current_page == 'profile.php') ? 'bg-primary/10 text-primary' : 'text-muted hover:text-dark hover:bg-slate-50 transition-colors'; ?> rounded-lg font-medium group">
            <i data-lucide="user" class="w-5 h-5 mr-3 <?php echo ($current_page == 'profile.php') ? '' : 'group-hover:text-primary transition-colors'; ?>"></i>
            Profil
        </a>
    </nav>

    <!-- User Info (Bottom Sidebar) -->
    <div class="p-4 border-t border-slate-200">
        <a href="profile.php" class="flex items-center group">
            <div
                class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-primary font-bold overflow-hidden border border-slate-200">
                <?php if(!empty($sidebar_foto)): ?>
                <img src="../uploads/foto_profil/<?php echo htmlspecialchars($sidebar_foto); ?>"
                alt="Avatar" class="w-full h-full object-cover">
                <?php else: ?>
                <img src="<?php echo 'https://ui-avatars.com/api/?name=' . urlencode($sidebar_nama) . '&background=A3B18A&color=ffffff'; ?>"
                alt="Avatar" class="w-full h-full object-cover">
                <?php endif; ?>
            </div>
            <div class="ml-3">
                <p class="text-sm font-semibold text-dark group-hover:text-primary transition-colors">
                    <?php echo htmlspecialchars($sidebar_nama); ?>
                </p>
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

<script>
// Sidebar Toggle Logic for Mobile
if (typeof toggleSidebar === 'undefined') {
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (sidebar && sidebar.classList.contains('-translate-x-full')) {
            // Open Sidebar
            sidebar.classList.remove('-translate-x-full');
            if (overlay) overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scrolling background
        } else if (sidebar) {
            // Close Sidebar
            sidebar.classList.add('-translate-x-full');
            if (overlay) overlay.classList.add('hidden');
            document.body.style.overflow = 'auto'; // Re-enable scrolling
        }
    }
}
</script>
