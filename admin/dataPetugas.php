<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Petugas - LaporIn Admin</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
      rel="stylesheet"
    />
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind Config to match LaporIn Design System -->
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: { sans: ["Poppins", "sans-serif"] },
            colors: {
              primary: {
                DEFAULT: "#3A5A40", // Dark Green
                dark: "#2B4330", // Darker Green for hover
              },
              accent: {
                DEFAULT: "#A3B18A", // Light Green
                dark: "#8b9a70",
              },
              secondary: "#588157", // Medium Green
              warning: "#D97706", // Amber
              danger: "#DC2626", // Red
              info: "#0284C7", // Blue
              dark: "#1E293B", // Slate 800
              light: "#F8FAFC", // Slate 50
              muted: "#94A3B8", // Slate 400
            },
          },
        },
      };
    </script>
    <style>
      ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
      }

      ::-webkit-scrollbar-track {
        background: #f8fafc;
      }

      ::-webkit-scrollbar-thumb {
        background: #a3b18a;
        border-radius: 4px;
      }

      ::-webkit-scrollbar-thumb:hover {
        background: #3a5a40;
      }
    </style>
  </head>

  <body class="bg-light text-dark font-sans h-screen flex overflow-hidden">
    <!-- Mobile Sidebar Overlay -->
    <div
      id="sidebarOverlay"
      class="fixed inset-0 bg-dark/50 z-40 hidden lg:hidden"
      onclick="toggleSidebar()"
    ></div>

    <!-- Sidebar Admin -->
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

      <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        <div
          class="px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider"
        >
          Dashboard
        </div>
        <a
          href="beranda.php"
          class="flex items-center px-3 py-2.5 bg-primary/10 text-primary rounded-lg font-medium group"
        >
          <i data-lucide="pie-chart" class="w-5 h-5 mr-3"></i>
          Beranda Admin
        </a>

        <div
          class="px-3 mt-6 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider"
        >
          Manajemen Data
        </div>
        <a
          href="dataLaporan.php"
          class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group"
        >
          <i
            data-lucide="file-text"
            class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"
          ></i>
          Data Laporan
        </a>
        <a
          href="buatBerita.php"
          class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group"
        >
          <i
            data-lucide="plus-circle"
            class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"
          ></i>
          Buat Berita
        </a>
        <!-- Dropdown Manajemen User & Petugas -->
        <div>
            <button onclick="toggleDropdownUser()" id="dropdownUserBtn"
                class="w-full flex items-center justify-between px-3 py-2.5 text-primary bg-primary/5 rounded-lg font-medium transition-colors">
                <div class="flex items-center">
                    <i data-lucide="users" class="w-5 h-5 mr-3"></i>
                    Manajemen Pengguna
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" 
                  id="dropdownUserIcon" style="transform: rotate(180deg)"></i>
                <!-- ↑ rotate(180deg) = chevron sudah menghadap atas (terbuka) -->
            </button>

            <!-- Tidak pakai class "hidden" agar langsung terbuka -->
            <div id="dropdownUserMenu" class="ml-4 mt-1 space-y-1 border-l-2 border-slate-100 pl-3">
                
                <!-- Data Petugas = AKTIF -->
                <a href="datapetugas.php" 
                  class="flex items-center gap-2 px-3 py-2 text-sm text-primary bg-primary/10 rounded-lg font-medium">
                    <span class="w-2 h-2 rounded-full bg-primary inline-block"></span> 
                    Data Petugas
                </a>

                <!-- Data User = tidak aktif -->
                <a href="datauser.php" 
                  class="flex items-center gap-2 px-3 py-2 text-sm text-muted hover:text-dark hover:bg-slate-50 rounded-lg transition-colors">
                    <span class="w-2 h-2 rounded-full bg-info inline-block"></span> 
                    Data User
                </a>

            </div>
        </div>
        <a
          href="#"
          class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group"
        >
          <i
            data-lucide="tags"
            class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"
          ></i>
          Kategori Laporan
        </a>

        <div
          class="px-3 mt-6 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider"
        >
          Sistem
        </div>
        <a
          href="#"
          class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group"
        >
          <i
            data-lucide="settings"
            class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"
          ></i>
          Pengaturan
        </a>
      </nav>

      <div class="p-4 border-t border-slate-200">
        <div class="flex items-center group">
          <div
            class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-primary font-bold overflow-hidden border border-slate-200"
          >
            <img
              src="https://ui-avatars.com/api/?name=Ibu+Sari&background=A3B18A&color=ffffff"
              alt="Avatar"
              class="w-full h-full object-cover"
            />
          </div>
          <div class="ml-3">
            <p
              class="text-sm font-semibold text-dark group-hover:text-primary transition-colors"
            >
              Ibu Sari
            </p>
            <p class="text-[10px] text-muted font-mono">Dinas Kominfo</p>
          </div>
        </div>
        <button
          class="mt-4 w-full flex items-center justify-center px-3 py-2 text-sm text-danger bg-red-50 hover:bg-red-100 rounded-lg font-medium transition-colors"
        >
          <i data-lucide="log-out" class="w-4 h-4 mr-2"></i>
          Keluar
        </button>
      </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
      <!-- Top Navbar -->
      <header
        class="h-16 bg-accent border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-30 shrink-0 shadow-sm"
      >
        <button
          class="lg:hidden text-dark hover:text-primary p-2 -ml-2 rounded-lg"
          onclick="toggleSidebar()"
        >
          <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
        <div class="hidden sm:block">
          <nav class="flex text-sm text-dark font-medium items-center">
            <a href="#" class="hover:text-primary transition-colors"
              >Manajemen Data</a
            >
            <i data-lucide="chevron-right" class="w-4 h-4 mx-1"></i>
            <span class="text-dark font-bold">Data Petugas Lapangan</span>
          </nav>
        </div>
        <div class="flex items-center ml-auto">
          <button
            class="relative p-2 text-dark hover:text-primary rounded-full hover:bg-white/50 transition-colors"
          >
            <i data-lucide="bell" class="w-5 h-5"></i>
          </button>
        </div>
      </header>

      <main class="flex-1 overflow-y-auto bg-light p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto flex flex-col h-full space-y-6">
          <!-- Page Header & Actions -->
          <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
          >
            <div>
              <h2 class="text-2xl font-bold text-dark tracking-tight">
                Manajemen Petugas Lapangan
              </h2>
              <p class="text-muted mt-1 text-sm">
                Pantau beban kerja, performa, dan ketersediaan petugas dari
                setiap instansi.
              </p>
            </div>
            <div class="flex space-x-2 w-full sm:w-auto">
              <a href="tambahpetugas.php" class="w-full sm:w-auto bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center shadow-sm text-decoration-none">
              <i data-lucide="plus" class="w-4 h-4 mr-2"></i> 
              Tambah Petugas Baru 
            </a>
            </div>
          </div>

          <!-- Stats Summary -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div
              class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm"
            >
              <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-slate-500 uppercase">
                  Total Petugas
                </p>
                <div class="p-1.5 bg-slate-100 rounded text-slate-600">
                  <i data-lucide="users" class="w-4 h-4"></i>
                </div>
              </div>
              <p class="text-2xl font-bold text-dark">45</p>
              <p class="text-xs text-muted font-medium mt-1">
                Terdaftar dalam sistem
              </p>
            </div>
            <div
              class="bg-white p-4 rounded-xl border border-secondary/30 shadow-sm bg-secondary/5"
            >
              <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-secondary uppercase">
                  Tersedia (Standby)
                </p>
                <div class="p-1.5 bg-secondary/10 rounded text-secondary">
                  <i data-lucide="check-circle" class="w-4 h-4"></i>
                </div>
              </div>
              <p class="text-2xl font-bold text-secondary">28</p>
              <p class="text-xs text-secondary/80 font-medium mt-1">
                Siap menerima tugas
              </p>
            </div>
            <div
              class="bg-white p-4 rounded-xl border border-info/30 shadow-sm bg-info/5"
            >
              <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-info uppercase">
                  Sedang Bertugas
                </p>
                <div class="p-1.5 bg-info/10 rounded text-info">
                  <i data-lucide="briefcase" class="w-4 h-4"></i>
                </div>
              </div>
              <p class="text-2xl font-bold text-info">15</p>
              <p class="text-xs text-info/80 font-medium mt-1">
                Menangani laporan aktif
              </p>
            </div>
            <div
              class="bg-white p-4 rounded-xl border border-danger/30 shadow-sm bg-danger/5"
            >
              <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-danger uppercase">
                  Cuti / Nonaktif
                </p>
                <div class="p-1.5 bg-danger/10 rounded text-danger">
                  <i data-lucide="power-off" class="w-4 h-4"></i>
                </div>
              </div>
              <p class="text-2xl font-bold text-danger">2</p>
              <p class="text-xs text-danger/80 font-medium mt-1">
                Tidak dapat ditugaskan
              </p>
            </div>
          </div>

          <!-- Filters & Search Toolbar -->
          <div
            class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm"
          >
            <div
              class="flex flex-col lg:flex-row gap-4 items-end lg:items-center"
            >
              <!-- Search -->
              <div class="flex-1 w-full relative">
                <div
                  class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                >
                  <i data-lucide="search" class="w-5 h-5 text-slate-400"></i>
                </div>
                <input
                  type="text"
                  placeholder="Cari nama petugas atau NIP..."
                  class="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm"
                />
              </div>

              <!-- Filter Group -->
              <div class="flex flex-wrap sm:flex-nowrap gap-3 w-full lg:w-auto">
                <!-- Filter Divisi -->
                <div class="relative w-full sm:w-48">
                  <select
                    class="w-full pl-3 pr-8 py-2 appearance-none rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm bg-white text-slate-700"
                  >
                    <option value="all">Semua Divisi / Dinas</option>
                    <option value="pju">Dinas Perhubungan (PJU)</option>
                    <option value="pu">Dinas PU (Jalan & Saluran)</option>
                    <option value="taman">
                      Dinas Lingkungan Hidup (Taman)
                    </option>
                  </select>
                  <div
                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400"
                  >
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                  </div>
                </div>

                <!-- Filter Ketersediaan -->
                <div class="relative w-full sm:w-40">
                  <select
                    class="w-full pl-3 pr-8 py-2 appearance-none rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm bg-white text-slate-700"
                  >
                    <option value="all">Semua Status</option>
                    <option value="tersedia">Tersedia</option>
                    <option value="bertugas">Sedang Bertugas</option>
                    <option value="penuh">Beban Penuh</option>
                  </select>
                  <div
                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400"
                  >
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Table -->
          <div
            class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden flex flex-col flex-1"
          >
            <div class="overflow-x-auto">
              <table
                class="w-full text-left border-collapse whitespace-nowrap lg:whitespace-normal"
              >
                <thead>
                  <tr
                    class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500"
                  >
                    <th class="py-3 px-6 font-semibold">Profil Petugas</th>
                    <th class="py-3 px-6 font-semibold">Divisi / Instansi</th>
                    <th class="py-3 px-6 font-semibold text-center">
                      Beban Saat Ini
                    </th>
                    <th class="py-3 px-6 font-semibold text-center">
                      Total Selesai
                    </th>
                    <th class="py-3 px-6 font-semibold">Ketersediaan</th>
                    <th class="py-3 px-6 text-center font-semibold">Aksi</th>
                  </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                  <!-- Petugas 1: Sedang Bertugas -->
                  <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-6">
                      <div class="flex items-center">
                        <div
                          class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 font-bold flex items-center justify-center mr-3 shrink-0 border border-orange-200"
                        >
                          <img
                            src="https://ui-avatars.com/api/?name=Budi+Santoso&background=ffedd5&color=ea580c"
                            alt="Avatar"
                            class="w-full h-full rounded-full object-cover"
                          />
                        </div>
                        <div>
                          <p class="font-bold text-dark">Budi Santoso</p>
                          <p class="text-xs font-mono text-slate-500 mt-0.5">
                            NIP: 198001012010
                          </p>
                        </div>
                      </div>
                    </td>
                    <td class="py-4 px-6 align-middle">
                      <div class="flex items-center">
                        <i
                          data-lucide="tree-deciduous"
                          class="w-4 h-4 text-slate-400 mr-2"
                        ></i>
                        <span class="text-dark font-medium"
                          >Dinas Lingkungan Hidup</span
                        >
                      </div>
                      <p class="text-xs text-muted mt-1">Tim Pertamanan</p>
                    </td>
                    <td class="py-4 px-6 text-center align-middle">
                      <span
                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-info/10 text-info font-bold border border-info/20"
                      >
                        2
                      </span>
                    </td>
                    <td class="py-4 px-6 text-center align-middle">
                      <p class="text-lg font-bold text-slate-700">142</p>
                    </td>
                    <td class="py-4 px-6 align-middle">
                      <span
                        class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-info/10 text-info border border-info/20"
                      >
                        <span
                          class="w-1.5 h-1.5 rounded-full bg-info mr-1.5 animate-pulse"
                        ></span>
                        SEDANG BERTUGAS
                      </span>
                    </td>
                    <td class="py-4 px-6 text-center align-middle">
                      <button
                        class="bg-white border border-slate-300 text-slate-600 hover:bg-slate-50 px-3 py-1.5 rounded-md text-xs font-semibold shadow-sm transition-colors"
                      >
                        Lihat Detail
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div
              class="border-t border-slate-200 bg-white px-4 py-3 flex items-center justify-between sm:px-6 mt-auto"
            >
              <div
                class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
              >
                <div>
                  <p class="text-sm text-muted">
                    Menampilkan
                    <span class="font-medium text-dark">1</span> sampai
                    <span class="font-medium text-dark">4</span> dari
                    <span class="font-medium text-dark">45</span> petugas
                  </p>
                </div>
                <div>
                  <nav
                    class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                    aria-label="Pagination"
                  >
                    <a
                      href="#"
                      class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-slate-300 bg-white text-sm font-medium text-slate-400 cursor-not-allowed"
                    >
                      <span class="sr-only">Previous</span>
                      <i data-lucide="chevron-left" class="h-4 w-4"></i>
                    </a>
                    <a
                      href="#"
                      aria-current="page"
                      class="z-10 bg-primary border-primary text-white relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                      >1</a
                    >
                    <a
                      href="#"
                      class="bg-white border-slate-300 text-slate-500 hover:bg-slate-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                      >2</a
                    >
                    <a
                      href="#"
                      class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-slate-300 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50"
                    >
                      <span class="sr-only">Next</span>
                      <i data-lucide="chevron-right" class="h-4 w-4"></i>
                    </a>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Script Logic -->
    <script>
      // Initialize Lucide Icons
      lucide.createIcons();

      // Sidebar Toggle Logic
      function toggleSidebar() {
        const sidebar = document.getElementById("sidebar");
        const overlay = document.getElementById("sidebarOverlay");
        if (sidebar.classList.contains("-translate-x-full")) {
          sidebar.classList.remove("-translate-x-full");
          overlay.classList.remove("hidden");
        } else {
          sidebar.classList.add("-translate-x-full");
          overlay.classList.add("hidden");
        }
      }
                function toggleDropdownUser() {
        const menu = document.getElementById('dropdownUserMenu');
        const icon = document.getElementById('dropdownUserIcon');
        const btn  = document.getElementById('dropdownUserBtn');

        menu.classList.toggle('hidden');

        if (!menu.classList.contains('hidden')) {
            icon.style.transform = 'rotate(180deg)';
            btn.classList.add('text-primary', 'bg-primary/5');
            btn.classList.remove('text-muted');
        } else {
            icon.style.transform = 'rotate(0deg)';
            btn.classList.remove('text-primary', 'bg-primary/5');
            btn.classList.add('text-muted');
        }
    }
    </script>
  </body>
</html>
