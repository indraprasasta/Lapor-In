<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_nama = $_SESSION['admin_nama'];

require __DIR__ . '/../database/conection.php';

$total      = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM laporan"))['total'];
$hari_ini   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM laporan WHERE DATE(tanggal) = CURDATE()"))['total'];
$menunggu   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM laporan WHERE status = 'Menunggu'"))['total'];
$diproses   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM laporan WHERE status = 'Diproses'"))['total'];
$selesai    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM laporan WHERE status = 'Selesai'"))['total'];
$ditolak    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM laporan WHERE status = 'Ditolak'"))['total'];

// Laporan terbaru 4 data
$query_terbaru = mysqli_query($koneksi, "
    SELECT laporan.*, users.nama as nama_pelapor 
    FROM laporan 
    JOIN users ON laporan.user_id = users.id 
    ORDER BY laporan.tanggal DESC 
    LIMIT 4
");

// Data per kecamatan untuk chart
$query_kecamatan = mysqli_query($koneksi, "
    SELECT kecamatan, COUNT(*) as total 
    FROM laporan 
    GROUP BY kecamatan 
    ORDER BY total DESC
");
$kecamatan_labels = [];
$kecamatan_data   = [];
while($row = mysqli_fetch_assoc($query_kecamatan)) {
    $kecamatan_labels[] = $row['kecamatan'];
    $kecamatan_data[]   = $row['total'];
}
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Beranda Admin - LaporIn Mataram</title>
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
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Tailwind Config to match LaporIn Design System -->
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ["Poppins", "sans-serif"],
            },
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

      <!-- Navigation -->
      <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        <div
          class="px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider"
        >
          Dashboard
        </div>
        <!-- Active Menu -->
        <a
          href="#"
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
        <a href="daftarBerita.php" class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
        <i data-lucide="file-text" class="w-5 h-5 mr-3 group-hover:text-primary"></i> Daftar Berita
        </a>
        <!-- Dropdown Manajemen User & Petugas -->
        <div>
            <button onclick="toggleDropdownUser()"
                class="w-full flex items-center justify-between px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group"
                id="dropdownUserBtn">
                <div class="flex items-center">
                    <i data-lucide="users" class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"></i>
                    Manajemen Pengguna
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" id="dropdownUserIcon"></i>
            </button>
            <div id="dropdownUserMenu" class="hidden ml-4 mt-1 space-y-1 border-l-2 border-slate-100 pl-3">
                <a href="dataPetugas.php" class="flex items-center gap-2 px-3 py-2 text-sm text-muted hover:text-dark hover:bg-slate-50 rounded-lg transition-colors">
                    <span class="w-2 h-2 rounded-full bg-primary inline-block"></span>
                    Data Petugas
                </a>
                <a href="dataUser.php" class="flex items-center gap-2 px-3 py-2 text-sm text-muted hover:text-dark hover:bg-slate-50 rounded-lg transition-colors">
                    <span class="w-2 h-2 rounded-full bg-info inline-block"></span>
                    Data User
                </a>
            </div>
        </div>
        <a
          href="kategoriLaporan.php"
          class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group"
        >
          <i
            data-lucide="tags"
            class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"
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
              src="https://ui-avatars.com/api/?name=Ibu+Sari&background=A3B18A&color=ffffff"
              alt="Avatar"
              class="w-full h-full object-cover"
            />
          </div>
          <div class="ml-3">
           <!-- Nama admin -->
            <p class="text-sm font-semibold text-dark"><?php echo $admin_nama; ?></p>
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

    <!-- Main Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
      <!-- Top Navbar -->
      <header
        class="h-16 bg-accent border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-30 shrink-0 shadow-sm"
      >
        <button
          class="lg:hidden text-dark hover:text-primary p-2 -ml-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
          onclick="toggleSidebar()"
        >
          <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
        <div class="hidden sm:block">
          <h1 class="text-lg font-bold text-dark">
            Ringkasan Sistem Pelaporan
          </h1>
        </div>
      </header>

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto bg-light p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto space-y-6">
          <!-- Welcome Section -->
          <div>
            <h2 class="text-2xl font-bold text-dark tracking-tight">
              Dashboard Utama
            </h2>
            <p class="text-muted mt-1 text-sm">
              Pantau statistik dan rekapitulasi data infrastruktur Kota Mataram.
            </p>
          </div>

          <!-- 6 Stats Cards Grid -->
          <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            <!-- Total Laporan -->
            <div
              class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm"
            >
              <div class="flex items-center justify-between mb-2">
                <p class="text-2xl font-bold text-dark"><?php echo $total; ?></p>
                
                <i data-lucide="layers" class="w-4 h-4 text-primary"></i>
              </div>
              <p class="text-xs text-muted font-medium mt-1">
                Total Laporan
              </p>
            </div>

            <!-- Hari Ini -->
            <div
              class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm"
            >
              <div class="flex items-center justify-between mb-2">
                <p class="text-2xl font-bold text-dark"><?php echo $hari_ini; ?></p>
                <i data-lucide="calendar-plus" class="w-4 h-4 text-primary"></i>
              </div>
              <p class="text-xs text-muted font-medium mt-1">
                Laporan masuk hari ini
              </p>
            </div>

            <!-- Menunggu -->
            <div
              class="bg-white p-4 rounded-xl border border-warning/30 shadow-sm bg-warning/5"
            >
              <div class="flex items-center justify-between mb-2">
                <p class="text-2xl font-bold text-warning"><?php echo $menunggu; ?></p>
                <i data-lucide="clock" class="w-4 h-4 text-warning"></i>
              </div>
              <p class="text-xs text-warning/80 font-medium mt-1">
                Menunggu validasi
              </p>
            </div>

            <!-- Diproses -->
            <div
              class="bg-white p-4 rounded-xl border border-info/30 shadow-sm bg-info/5"
            >
              <div class="flex items-center justify-between mb-2">
                <p class="text-2xl font-bold text-info"><?php echo $diproses; ?></p>
                <i
                  data-lucide="settings"
                  class="w-4 h-4 text-info animate-[spin_3s_linear_infinite]"
                ></i>
              </div>
              <p class="text-xs text-info/80 font-medium mt-1">
                Sedang ditangani
              </p>
            </div>

            <!-- Selesai -->
            <div
              class="bg-white p-4 rounded-xl border border-secondary/30 shadow-sm bg-secondary/5"
            >
              <div class="flex items-center justify-between mb-2">
                <p class="text-2xl font-bold text-secondary"><?php echo $selesai; ?></p>
                <i
                  data-lucide="check-circle"
                  class="w-4 h-4 text-secondary"
                ></i>
              </div>
              <p class="text-xs text-secondary/80 font-medium mt-1">
                Telah diselesaikan
              </p>
            </div>

            <!-- Ditolak -->
            <div
              class="bg-white p-4 rounded-xl border border-danger/30 shadow-sm bg-danger/5"
            >
              <div class="flex items-center justify-between mb-2">
                <p class="text-2xl font-bold text-danger"><?php echo $ditolak; ?></p>
                <i data-lucide="x-circle" class="w-4 h-4 text-danger"></i>
              </div>
              <p class="text-xs text-danger/80 font-medium mt-1">
                Tidak valid/Duplikat
              </p>
            </div>
          </div>

          <!-- Bottom Section: Kecamatan & Recent Table -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Bar Chart: Laporan per Kecamatan (Span 1) -->
            <div
              class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm"
            >
              <div class="flex justify-between items-center mb-4">
                <div>
                  <h3 class="font-bold text-dark">Distribusi Wilayah</h3>
                  <p class="text-xs text-muted">Jumlah laporan per Kecamatan</p>
                </div>
              </div>
              <div class="relative h-[300px] w-full">
                <canvas id="kecamatanChart"></canvas>
              </div>
            </div>

            <!-- Recent Reports Table (Span 2) -->
            <div
              class="bg-white border border-slate-200 rounded-xl shadow-sm lg:col-span-2 flex flex-col"
            >
              <div
                class="px-5 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50/50 rounded-t-xl"
              >
                <h3 class="font-bold text-dark">
                  Laporan Terbaru Membutuhkan Tindakan
                </h3>
                <a
                  href="dataLaporan.php"
                  class="text-sm font-semibold text-primary hover:text-primary-dark"
                  >Lihat Semua Data</a
                >
              </div>
              <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse">
                  <thead>
                    <tr
                      class="border-b border-slate-200 text-xs text-slate-500 uppercase tracking-wider bg-white"
                    >
                      <th class="py-3 px-5 font-semibold">ID Laporan</th>
                      <th class="py-3 px-5 font-semibold">
                        Kategori & Kerusakan
                      </th>
                      <th class="py-3 px-5 font-semibold">Pelapor</th>
                      <th class="py-3 px-5 font-semibold">Status</th>
                      <th class="py-3 px-5 text-right font-semibold">Aksi</th>
                    </tr>
                  </thead>
                 <tbody class="text-sm divide-y divide-slate-100 bg-white">
                      <?php if(mysqli_num_rows($query_terbaru) > 0): ?>
                          <?php while($laporan = mysqli_fetch_assoc($query_terbaru)): ?>
                          <tr class="hover:bg-slate-50 transition-colors">
                              <td class="py-3 px-5 font-mono text-xs text-slate-500">
                                  #<?php echo str_pad($laporan['id'], 4, '0', STR_PAD_LEFT); ?>
                              </td>
                              <td class="py-3 px-5">
                                  <p class="font-bold text-dark mb-0.5 line-clamp-1"><?php echo $laporan['judul']; ?></p>
                                  <p class="text-xs text-muted"><?php echo $laporan['kategori']; ?></p>
                              </td>
                              <td class="py-3 px-5 text-muted"><?php echo $laporan['nama_pelapor']; ?></td>
                              <td class="py-3 px-5">
                                  <?php
                                  $status = $laporan['status'];
                                  if($status == 'Menunggu') {
                                      echo '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-warning/10 text-warning border border-warning/20">MENUNGGU</span>';
                                  } elseif($status == 'Diproses') {
                                      echo '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-info/10 text-info border border-info/20">DIPROSES</span>';
                                  } elseif($status == 'Selesai') {
                                      echo '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-secondary/10 text-secondary border border-secondary/20">SELESAI</span>';
                                  } elseif($status == 'Ditolak') {
                                      echo '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-danger/10 text-danger border border-danger/20">DITOLAK</span>';
                                  }
                                  ?>
                              </td>
                              <td class="py-3 px-5 text-right">
                                  <a href="detailLaporan.php?id=<?php echo $laporan['id']; ?>"
                                      class="border border-slate-300 text-slate-600 text-xs px-3 py-1.5 rounded font-semibold hover:bg-slate-50 shadow-sm">
                                      Detail
                                  </a>
                              </td>
                          </tr>
                          <?php endwhile; ?>
                      <?php else: ?>
                          <tr>
                              <td colspan="5" class="py-8 text-center text-muted">Belum ada laporan</td>
                          </tr>
                      <?php endif; ?>
                  </tbody>
                </table>
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

      // Sidebar Toggle Logic for Mobile
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

      // --- Chart.js Configurations ---

      // Global Chart Styling
      Chart.defaults.font.family = "'Poppins', sans-serif";
      Chart.defaults.color = "#94A3B8"; // text-slate-400
      Chart.defaults.scale.grid.color = "#F1F5F9"; // slate-100

      const kecamatanLabels = <?php echo json_encode($kecamatan_labels); ?>;
      const kecamatanData   = <?php echo json_encode($kecamatan_data); ?>;

      const ctxKec = document.getElementById("kecamatanChart").getContext("2d");
      new Chart(ctxKec, {
          type: "bar",
          data: {
              labels: kecamatanLabels,
              datasets: [{
                  label: "Jumlah Laporan",
                  data: kecamatanData,
                  backgroundColor: "#3A5A40",
                  borderRadius: 4,
              }],
          },
          options: {
              indexAxis: "y",
              responsive: true,
              maintainAspectRatio: false,
              plugins: { legend: { display: false } },
              scales: {
                  x: { beginAtZero: true, grid: { drawBorder: false } },
                  y: { grid: { display: false, drawBorder: false } },
              },
          },
      });
      // function dropdown
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
