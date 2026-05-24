<?php
$current_page = basename($_SERVER['PHP_SELF']);
// Determine if we are in User/Petugas management pages
$is_user_management = in_array($current_page, ['dataPetugas.php', 'dataUser.php', 'tambahpetugas.php', 'editPetugas.php']);
?>
<aside
  id="sidebar"
  class="fixed inset-y-0 left-0 bg-white w-64 border-r border-slate-200 z-50 transform -translate-x-full lg:translate-x-0 lg:static lg:flex lg:flex-col transition-transform duration-300 ease-in-out"
>
  <!-- Logo -->
  <div class="h-16 flex items-center px-6 border-b border-slate-200">
    <div
      class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white mr-3"
    >
      <i data-lucide="leaf" class="w-5 h-5"></i>
    </div>
    <span class="text-primary font-extrabold text-2xl tracking-tight"
      >Lapor<span class="text-accent">In</span></span
    >
    <span
      class="ml-2 text-[10px] font-bold text-white bg-primary px-2 py-0.5 rounded-full uppercase tracking-wider"
      >Admin</span
    >
  </div>

  <!-- Navigation -->
  <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
    <div
      class="px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider"
    >
      Dashboard
    </div>
    <!-- Active Menu -->
    <a
      href="beranda.php"
      class="flex items-center px-3 py-2.5 <?php echo ($current_page == 'beranda.php') ? 'bg-primary/10 text-primary' : 'text-muted hover:text-dark hover:bg-slate-50 transition-colors'; ?> rounded-lg font-medium group"
    >
      <i data-lucide="pie-chart" class="w-5 h-5 mr-3 <?php echo ($current_page == 'beranda.php') ? '' : 'group-hover:text-primary transition-colors'; ?>"></i>
      Beranda Admin
    </a>

    <div
      class="px-3 mt-6 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider"
    >
      Manajemen Data
    </div>
    <a
      href="dataLaporan.php"
      class="flex items-center px-3 py-2.5 <?php echo (in_array($current_page, ['dataLaporan.php', 'detailLaporan.php'])) ? 'bg-primary/10 text-primary' : 'text-muted hover:text-dark hover:bg-slate-50 transition-colors'; ?> rounded-lg font-medium group"
    >
      <i
        data-lucide="file-text"
        class="w-5 h-5 mr-3 <?php echo (in_array($current_page, ['dataLaporan.php', 'detailLaporan.php'])) ? '' : 'group-hover:text-primary transition-colors'; ?>"
      ></i>
      Data Laporan
    </a>
    <a
      href="buatBerita.php"
      class="flex items-center px-3 py-2.5 <?php echo ($current_page == 'buatBerita.php') ? 'bg-primary/10 text-primary' : 'text-muted hover:text-dark hover:bg-slate-50 transition-colors'; ?> rounded-lg font-medium group"
    >
      <i
        data-lucide="plus-circle"
        class="w-5 h-5 mr-3 <?php echo ($current_page == 'buatBerita.php') ? '' : 'group-hover:text-primary transition-colors'; ?>"
      ></i>
      Buat Berita
    </a>
    <a href="daftarBerita.php" class="flex items-center px-3 py-2.5 <?php echo (in_array($current_page, ['daftarBerita.php', 'editBerita.php'])) ? 'bg-primary/10 text-primary' : 'text-muted hover:text-dark hover:bg-slate-50 transition-colors'; ?> rounded-lg font-medium group">
      <i data-lucide="file-text" class="w-5 h-5 mr-3 <?php echo (in_array($current_page, ['daftarBerita.php', 'editBerita.php'])) ? '' : 'group-hover:text-primary transition-colors'; ?>"></i> 
      Daftar Berita
    </a>
    <!-- Dropdown Manajemen User & Petugas -->
    <div>
        <button onclick="toggleDropdownUser()"
            class="w-full flex items-center justify-between px-3 py-2.5 <?php echo $is_user_management ? 'text-primary bg-primary/5' : 'text-muted hover:text-dark hover:bg-slate-50'; ?> rounded-lg font-medium transition-colors group"
            id="dropdownUserBtn">
            <div class="flex items-center">
                <i data-lucide="users" class="w-5 h-5 mr-3 <?php echo $is_user_management ? '' : 'group-hover:text-primary transition-colors'; ?>"></i>
                Manajemen Pengguna
            </div>
            <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" id="dropdownUserIcon" style="<?php echo $is_user_management ? 'transform: rotate(180deg);' : ''; ?>"></i>
        </button>
        <div id="dropdownUserMenu" class="<?php echo $is_user_management ? '' : 'hidden'; ?> ml-4 mt-1 space-y-1 border-l-2 border-slate-100 pl-3">
            <a href="dataPetugas.php" class="flex items-center gap-2 px-3 py-2 text-sm <?php echo (in_array($current_page, ['dataPetugas.php', 'tambahpetugas.php', 'editPetugas.php'])) ? 'text-primary bg-slate-50 font-semibold' : 'text-muted hover:text-dark hover:bg-slate-50'; ?> rounded-lg transition-colors">
                <span class="w-2 h-2 rounded-full bg-primary inline-block"></span>
                Data Petugas
            </a>
            <a href="dataUser.php" class="flex items-center gap-2 px-3 py-2 text-sm <?php echo ($current_page == 'dataUser.php') ? 'text-primary bg-slate-50 font-semibold' : 'text-muted hover:text-dark hover:bg-slate-50'; ?> rounded-lg transition-colors">
                <span class="w-2 h-2 rounded-full bg-info inline-block"></span>
                Data User
            </a>
        </div>
    </div>
    <a
      href="kategoriLaporan.php"
      class="flex items-center px-3 py-2.5 <?php echo ($current_page == 'kategoriLaporan.php') ? 'bg-primary/10 text-primary' : 'text-muted hover:text-dark hover:bg-slate-50 transition-colors'; ?> rounded-lg font-medium group"
    >
      <i
        data-lucide="tags"
        class="w-5 h-5 mr-3 <?php echo ($current_page == 'kategoriLaporan.php') ? '' : 'group-hover:text-primary transition-colors'; ?>"
      ></i>
      Kategori Laporan
    </a>
  </nav>

  <!-- Admin Info -->
  <div class="p-4 border-t border-slate-200">
    <div class="flex items-center group">
      <div
        class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-primary font-bold overflow-hidden border border-slate-200"
      >
        <img
          src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_nama ?? 'Admin'); ?>&background=A3B18A&color=ffffff"
          alt="Avatar"
          class="w-full h-full object-cover"
        />
      </div>
      <div class="ml-3">
       <!-- Nama admin -->
        <p class="text-sm font-semibold text-dark"><?php echo htmlspecialchars($admin_nama ?? 'Admin'); ?></p>
        <p class="text-[10px] text-muted font-mono">Admin</p>
      </div>
    </div>
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
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");
    if (sidebar && sidebar.classList.contains("-translate-x-full")) {
      sidebar.classList.remove("-translate-x-full");
      if(overlay) overlay.classList.remove("hidden");
    } else if (sidebar) {
      sidebar.classList.add("-translate-x-full");
      if(overlay) overlay.classList.add("hidden");
    }
  }
}

// function dropdown
if (typeof toggleDropdownUser === 'undefined') {
  function toggleDropdownUser() {
    const menu = document.getElementById('dropdownUserMenu');
    const icon = document.getElementById('dropdownUserIcon');
    const btn  = document.getElementById('dropdownUserBtn');

    if (menu) {
      menu.classList.toggle('hidden');

      if (!menu.classList.contains('hidden')) {
          if (icon) icon.style.transform = 'rotate(180deg)';
          if (btn) {
            btn.classList.add('text-primary', 'bg-primary/5');
            btn.classList.remove('text-muted');
          }
      } else {
          if (icon) icon.style.transform = 'rotate(0deg)';
          if (btn) {
            btn.classList.remove('text-primary', 'bg-primary/5');
            btn.classList.add('text-muted');
          }
      }
    }
  }
}
</script>
