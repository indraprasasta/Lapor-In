<?php
session_start();
require __DIR__ . '/../database/conection.php';

if (!isset($_SESSION['petugas_id'])) {
    header("Location: ../login.php");
    exit();
}
// Ambil kategori yang menjadi tanggung jawab dinas petugas
$dinas_id = $_SESSION['petugas_dinas_id'];
$query_kategori = mysqli_query($koneksi,
    "SELECT kategori FROM dinas_kategori WHERE dinas_id = '$dinas_id'"
);

$kategori_list = [];
while($row = mysqli_fetch_assoc($query_kategori)) {
    $kategori_list[] = "'" . mysqli_real_escape_string($koneksi, $row['kategori']) . "'";
}
//validasi
if (empty($kategori_list)) {
    $query_laporan     = null;
    $total_ditugaskan  = 0;
    $sedang_diproses   = 0;
    $selesai_ditangani = 0;
} else {
    $kategori_in = implode(',', $kategori_list);

    $query_laporan = mysqli_query($koneksi,
        "SELECT * FROM laporan 
         WHERE kategori IN ($kategori_in) 
         AND status IN ('Menunggu', 'Diproses')
         ORDER BY tanggal DESC"
    );

    $total_ditugaskan = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT COUNT(*) as total FROM laporan 
         WHERE kategori IN ($kategori_in)"))['total'];

    $sedang_diproses = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT COUNT(*) as total FROM laporan 
         WHERE kategori IN ($kategori_in) AND status = 'Diproses'"))['total'];

    $selesai_ditangani = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT COUNT(*) as total FROM laporan 
         WHERE kategori IN ($kategori_in) AND status = 'Selesai'"))['total'];
}

$petugas_nama    = $_SESSION['petugas_nama'];
$petugas_jabatan = $_SESSION['petugas_jabatan'];
$petugas_dinas   = $_SESSION['petugas_dinas'];
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Beranda Petugas - LaporIn Mataram</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
      rel="stylesheet"
    />
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: { sans: ["Poppins", "sans-serif"] },
            colors: {
              primary: { DEFAULT: "#3A5A40", dark: "#2B4330" },
              accent: { DEFAULT: "#A3B18A", dark: "#8b9a70" },
              secondary: "#588157",
              warning: "#D97706",
              danger: "#DC2626",
              info: "#0284C7",
              dark: "#1E293B",
              light: "#F8FAFC",
              muted: "#94A3B8",
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

      /* Modal */
      #statusModal {
        transition: opacity 0.2s ease;
      }
      #modalBox {
        transition:
          transform 0.25s ease,
          opacity 0.25s ease;
      }
      #statusModal.modal-hidden {
        opacity: 0;
        pointer-events: none;
      }
      #statusModal.modal-hidden #modalBox {
        transform: scale(0.95);
        opacity: 0;
      }

      /* Toast */
      #toast {
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      }
    </style>
  </head>

  <body class="bg-light text-dark font-sans h-screen flex overflow-hidden">
    <!-- Mobile Overlay -->
    <div
      id="sidebarOverlay"
      class="fixed inset-0 bg-dark/50 z-40 hidden lg:hidden"
      onclick="toggleSidebar()"
    ></div>

    <!-- ===== SIDEBAR ===== -->
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
      </div>

      <!-- Nav -->
      <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        <!-- Beranda -->
        <a
          href="#"
          class="flex items-center px-3 py-2.5 bg-primary/10 text-primary rounded-lg font-medium group"
        >
          <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3"></i>
          Dashboard
        </a>

        <!-- Kelola Pengaduan (Dropdown) -->
        <div>
          <button
            onclick="toggleDropdown()"
            class="w-full flex items-center justify-between px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group"
            id="dropdownBtn"
          >
            <div class="flex items-center">
              <i
                data-lucide="clipboard-list"
                class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"
              ></i>
              Kelola Pengaduan
            </div>
            <i
              data-lucide="chevron-down"
              class="w-4 h-4 transition-transform duration-300"
              id="dropdownIcon"
            ></i>
          </button>

          <!-- Sub Menu -->
          <div
            id="dropdownMenu"
            class="hidden ml-4 mt-1 space-y-1 border-l-2 border-slate-100 pl-3"
          >
            <a
              href="pengaduan.php?status=Menunggu"
              class="flex items-center gap-2 px-3 py-2 text-sm text-muted hover:text-dark hover:bg-slate-50 rounded-lg transition-colors"
            >
              <span class="w-2 h-2 rounded-full bg-warning inline-block"></span>
              Pengaduan Masuk
            </a>
            <a
              href="pengaduan.php?status=Diproses"
              class="flex items-center gap-2 px-3 py-2 text-sm text-muted hover:text-dark hover:bg-slate-50 rounded-lg transition-colors"
            >
              <span class="w-2 h-2 rounded-full bg-info inline-block"></span>
              Pengaduan Proses
            </a>
            <a
              href="pengaduan.php?status=Ditolak"
              class="flex items-center gap-2 px-3 py-2 text-sm text-muted hover:text-dark hover:bg-slate-50 rounded-lg transition-colors"
            >
              <span class="w-2 h-2 rounded-full bg-danger inline-block"></span>
              Pengaduan Ditolak
            </a>
            <a
              href="pengaduan.php?status=Selesai"
              class="flex items-center gap-2 px-3 py-2 text-sm text-muted hover:text-dark hover:bg-slate-50 rounded-lg transition-colors"
            >
              <span
                class="w-2 h-2 rounded-full bg-secondary inline-block"
              ></span>
              Pengaduan Selesai
            </a>
          </div>
        </div>

        <div
          class="px-3 mt-6 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider"
        >
          Pengaturan
        </div>

        <a
          href="#"
          class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group"
        >
          <i
            data-lucide="user"
            class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"
          ></i>
          Profil Saya
        </a>
      </nav>

      <!-- User Info -->
      <div class="p-4 border-t border-slate-200">
        <a href="#" class="flex items-center group">
          <div
            class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center overflow-hidden border border-slate-200"
          >
            <!-- Avatar diisi dinamis nanti -->
            <i data-lucide="user" class="w-5 h-5 text-muted"></i>
          </div>
          <div class="ml-3">
            <p class="text-sm font-semibold text-dark group-hover:text-primary transition-colors">
                <?php echo $petugas_nama; ?>
            </p>
            <p class="text-xs text-muted"><?php echo $petugas_jabatan; ?></p>
        </div>
        </a>
        <a
          href="logout.php"
          class="mt-4 w-full flex items-center justify-center px-3 py-2 text-sm text-danger bg-red-50 hover:bg-red-100 rounded-lg font-medium transition-colors"
        >
          <i data-lucide="log-out" class="w-4 h-4 mr-2"></i>
          Keluar
        </a>
      </div>
    </aside>

    <!-- ===== MAIN WRAPPER ===== -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
      <!-- Navbar -->
      <header
        class="h-16 bg-accent border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-30 shrink-0"
      >
        <button
          class="lg:hidden text-white hover:text-dark p-2 -ml-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
          onclick="toggleSidebar()"
        >
          <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
        <div class="hidden sm:block">
          <h1 class="text-lg font-bold text-dark">
            Dashboard Petugas Lapangan
          </h1>
      </header>

      <!-- ===== MAIN CONTENT ===== -->
      <main class="flex-1 overflow-y-auto bg-primary p-4 sm:p-6 lg:p-8">
        <div class="max-w-6xl mx-auto space-y-6">
          <!-- Welcome -->
          <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
          >
            <div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                  Selamat Bertugas, <?php echo $petugas_nama; ?>
              </h2>
              <p class="text-muted mt-1 text-sm">
                  <?php echo $petugas_jabatan; ?> — <?php echo $petugas_dinas; ?>
              </p>
            </div>
          </div>

          <!-- Stats Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Total Ditugaskan -->
            <div
              class="bg-white p-5 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between hover:border-primary/30 transition-colors"
            >
              <div>
                <p class="text-sm font-medium text-muted">Total Ditugaskan</p>
                <p class="text-3xl font-bold text-dark mt-1">
                  <?php echo $total_ditugaskan; ?>
                </p>
              </div>
              <div
                class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary"
              >
                <i data-lucide="clipboard-list" class="w-6 h-6"></i>
              </div>
            </div>
            <!-- Sedang Diproses -->
            <div
              class="bg-white p-5 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between hover:border-info/30 transition-colors relative overflow-hidden"
            >
              <div class="absolute inset-x-0 bottom-0 h-1 bg-info"></div>
              <div>
                <p class="text-sm font-medium text-muted">Sedang Diproses</p>
                <p class="text-3xl font-bold text-dark mt-1">
                  <?= $sedang_diproses ?>
                </p>
              </div>
              <div
                class="w-12 h-12 rounded-full bg-info/10 flex items-center justify-center text-info"
              >
                <i
                  data-lucide="settings"
                  class="w-6 h-6 animate-[spin_4s_linear_infinite]"
                ></i>
              </div>
            </div>
            <!-- Selesai -->
            <div
              class="bg-white p-5 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between hover:border-secondary/30 transition-colors"
            >
              <div>
                <p class="text-sm font-medium text-muted">Selesai Ditangani</p>
                <p class="text-3xl font-bold text-dark mt-1"><?= $selesai_ditangani ?></p>
              </div>
              <div
                class="w-12 h-12 rounded-full bg-secondary/10 flex items-center justify-center text-secondary"
              >
                <i data-lucide="check-square" class="w-6 h-6"></i>
              </div>
            </div>
          </div>

          <!-- Active Tasks -->
          <div>
            <div class="flex items-center justify-between mb-4 mt-4">
              <h3 class="text-lg font-bold text-white flex items-center">
                <i data-lucide="zap" class="w-5 h-5 text-accent mr-2"></i>
                Tugas Aktif
              </h3>
              <a
                href="daftarTugas.php"
                class="text-sm font-medium text-accent hover:text-accent-dark transition-colors"
              >
                Lihat Semua &rarr;
              </a>
            </div>

            <div
              class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden"
            >
              <!-- Desktop Table -->
              <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                  <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-sm">
                      <th class="py-3 px-4 font-semibold text-dark">
                        Informasi Kerusakan
                      </th>
                      <th class="py-3 px-4 font-semibold text-dark">Lokasi</th>
                      <th class="py-3 px-4 font-semibold text-dark">
                        Waktu Ditugaskan
                      </th>
                      <th class="py-3 px-4 text-center font-semibold text-dark">
                        Status
                      </th>
                      <th class="py-3 px-4 text-center font-semibold text-dark">
                        Aksi
                      </th>
                    </tr>
                  </thead>
                  <tbody id="taskTableBody" class="text-sm divide-y divide-slate-100">
                      <?php if($query_laporan && mysqli_num_rows($query_laporan) > 0): ?>
                          <?php while($laporan = mysqli_fetch_assoc($query_laporan)): ?>
                          <tr class="hover:bg-slate-50 transition-colors" id="row-<?php echo $laporan['id']; ?>">
                              <td class="py-4 px-4 align-top">
                                  <div class="flex items-start">
                                      <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center mr-3 shrink-0 border border-slate-200">
                                          <i data-lucide="file-text" class="w-5 h-5 text-primary"></i>
                                      </div>
                                      <div>
                                          <p class="font-bold text-dark mb-1"><?php echo $laporan['judul']; ?></p>
                                          <p class="text-xs text-muted"><?php echo $laporan['kategori']; ?></p>
                                      </div>
                                  </div>
                              </td>
                              <td class="py-4 px-4 align-top">
                                  <p class="text-dark font-medium text-sm"><?php echo $laporan['kelurahan']; ?></p>
                                  <p class="text-xs text-muted">Kec. <?php echo $laporan['kecamatan']; ?></p>
                              </td>
                              <td class="py-4 px-4 align-top text-sm text-muted">
                                  <p class="text-dark"><?php echo date('d M Y', strtotime($laporan['tanggal'])); ?></p>
                                  <p class="text-xs"><?php echo date('H:i', strtotime($laporan['tanggal'])); ?> WITA</p>
                              </td>
                              <td class="py-4 px-4 align-top text-center" id="status-badge-<?php echo $laporan['id']; ?>">
                                  <?php
                                  $s = $laporan['status'];
                                  if($s == 'Menunggu') echo '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-warning/10 text-warning border border-warning/20">MENUNGGU</span>';
                                  elseif($s == 'Diproses') echo '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-info/10 text-info border border-info/20">DIPROSES</span>';
                                  ?>
                              </td>
                              <td class="py-4 px-4 align-top text-center">
                                  <?php if($laporan['status'] == 'Menunggu'): ?>
                                  <button onclick="openModal(<?php echo $laporan['id']; ?>, 'Menunggu', '<?php echo addslashes($laporan['judul']); ?>')"
                                      class="bg-white border border-primary text-primary hover:bg-primary/5 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors w-full shadow-sm">
                                      Mulai Proses
                                  </button>
                                  <?php else: ?>
                                  <button onclick="openModal(<?php echo $laporan['id']; ?>, 'Diproses', '<?php echo addslashes($laporan['judul']); ?>')"
                                      class="bg-primary hover:bg-primary-dark text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors w-full shadow-sm">
                                      Update Status
                                  </button>
                                  <?php endif; ?>
                              </td>
                          </tr>
                          <?php endwhile; ?>
                      <?php else: ?>
                          <tr>
                              <td colspan="5" class="py-12 text-center text-muted">
                                  <div class="flex flex-col items-center">
                                      <div class="w-14 h-14 rounded-full bg-secondary/10 flex items-center justify-center mb-3">
                                          <i data-lucide="check-circle" class="w-7 h-7 text-secondary"></i>
                                      </div>
                                      <p class="font-semibold text-dark">Tidak ada tugas aktif</p>
                                      <p class="text-sm mt-1">Semua laporan telah ditangani.</p>
                                  </div>
                              </td>
                          </tr>
                      <?php endif; ?>
                  </tbody>
                </table>
              </div>

              <!-- Mobile Card View -->
              <div class="md:hidden flex flex-col divide-y divide-slate-100">
              <?php
              $kategori_in = implode(',', $kategori_list);
              // query ulang untuk mobile (pointer sudah habis)
              if (!empty($kategori_list)) {
                  $q_mobile = mysqli_query($koneksi,
                      "SELECT * FROM laporan
                      WHERE kategori IN ($kategori_in)
                      AND status IN ('Menunggu', 'Diproses')
                      ORDER BY tanggal DESC"
                  );
                  if ($q_mobile && mysqli_num_rows($q_mobile) > 0):
                      while ($lap = mysqli_fetch_assoc($q_mobile)):
                          $border_color = $lap['status'] === 'Menunggu' ? 'border-warning' : 'border-info';
                          $badge_class  = $lap['status'] === 'Menunggu'
                              ? 'bg-warning/10 text-warning border-warning/20'
                              : 'bg-info/10 text-info border-info/20';
              ?>
              <div class="p-4 bg-white border-l-4 <?php echo $border_color; ?>">
                  <div class="flex justify-between items-start mb-2">
                      <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold <?php echo $badge_class; ?> uppercase tracking-wide border">
                          <?php echo $lap['status']; ?>
                      </span>
                      <span class="text-xs text-muted"><?php echo date('d M Y', strtotime($lap['tanggal'])); ?></span>
                  </div>
                  <h4 class="font-bold text-dark text-sm mb-1"><?php echo htmlspecialchars($lap['judul']); ?></h4>
                  <p class="text-xs text-muted mb-3"><?php echo htmlspecialchars($lap['kategori']); ?></p>
                  <div class="bg-slate-50 rounded-lg p-3 mb-3 border border-slate-100 space-y-2">
                      <div class="flex items-start text-sm">
                          <i data-lucide="map-pin" class="w-4 h-4 text-slate-400 mr-2 mt-0.5 shrink-0"></i>
                          <span class="text-dark font-medium"><?php echo htmlspecialchars($lap['kelurahan']); ?>, Kec. <?php echo htmlspecialchars($lap['kecamatan']); ?></span>
                      </div>
                      <div class="flex items-start text-sm">
                          <i data-lucide="clock" class="w-4 h-4 text-slate-400 mr-2 mt-0.5 shrink-0"></i>
                          <span class="text-muted"><?php echo date('d M Y, H:i', strtotime($lap['tanggal'])); ?> WITA</span>
                      </div>
                  </div>
                  <?php if ($lap['status'] === 'Menunggu'): ?>
                  <button onclick="openModal(<?php echo $lap['id']; ?>, 'Menunggu', '<?php echo addslashes($lap['judul']); ?>')"
                      class="w-full bg-white border border-primary text-primary hover:bg-primary/5 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                      Mulai Tangani
                  </button>
                  <?php else: ?>
                  <button onclick="openModal(<?php echo $lap['id']; ?>, 'Diproses', '<?php echo addslashes($lap['judul']); ?>')"
                      class="w-full bg-primary hover:bg-primary-dark text-white py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                      Update Status
                  </button>
                  <?php endif; ?>
              </div>
              <?php
                      endwhile;
                  else:
              ?>
              <div class="p-10 text-center text-muted">
                  <div class="flex flex-col items-center">
                      <div class="w-14 h-14 rounded-full bg-secondary/10 flex items-center justify-center mb-3">
                          <i data-lucide="check-circle" class="w-7 h-7 text-secondary"></i>
                      </div>
                      <p class="font-semibold text-dark">Tidak ada tugas aktif</p>
                      <p class="text-sm mt-1">Semua laporan telah ditangani.</p>
                  </div>
              </div>
              <?php
                  endif;
              }
              ?>
          </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- ===== MODAL UPDATE STATUS ===== -->
    <div
      id="statusModal"
      class="modal-hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-dark/60"
    >
      <div id="modalBox" class="bg-white rounded-xl shadow-2xl w-full max-w-md">
        <!-- Header -->
        <div
          class="flex items-center justify-between px-6 py-4 border-b border-slate-200"
        >
          <div class="flex items-center">
            <div
              class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center mr-3"
            >
              <i data-lucide="refresh-cw" class="w-5 h-5 text-primary"></i>
            </div>
            <div>
              <h3 class="font-bold text-dark text-base">
                Update Status Laporan
              </h3>
              <p class="text-xs text-muted" id="modalLaporanId"></p>
            </div>
          </div>
          <button
            onclick="closeModal()"
            class="p-1.5 rounded-lg text-muted hover:text-danger hover:bg-red-50 transition-colors"
          >
            <i data-lucide="x" class="w-5 h-5"></i>
          </button>
        </div>

        <!-- Body -->
        <div class="px-6 py-5 space-y-5">
          <!-- Info -->
          <div class="bg-slate-50 rounded-lg p-3 border border-slate-200">
            <p class="text-xs text-muted mb-0.5">Judul Laporan</p>
            <p class="font-semibold text-dark text-sm" id="modalJudul">—</p>
            <div class="mt-2 flex items-center gap-2">
              <span class="text-xs text-muted">Status saat ini:</span>
              <span id="modalStatusSekarang"></span>
            </div>
          </div>

          <!-- Pilih Status -->
          <div>
            <label class="block text-sm font-semibold text-dark mb-3"
              >Ubah status menjadi:</label
            >
            <div class="grid grid-cols-2 gap-2" id="statusOptions"></div>
          </div>

          <!-- Catatan -->
          <div>
            <label
              class="block text-sm font-semibold text-dark mb-1.5"
              for="catatanPetugas"
            >
              Catatan Penanganan
              <span class="text-muted font-normal">(opsional)</span>
            </label>
            <textarea
              id="catatanPetugas"
              rows="3"
              class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm text-dark placeholder-muted focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-colors resize-none"
              placeholder="Tuliskan keterangan penanganan, kendala, atau alasan penolakan..."
            ></textarea>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-slate-200 flex gap-3 justify-end">
          <button
            onclick="closeModal()"
            class="px-4 py-2 text-sm font-medium text-muted bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors"
          >
            Batal
          </button>
          <button
            id="btnSimpanStatus"
            onclick="simpanStatus()"
            class="px-5 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-lg transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            disabled
          >
            <i data-lucide="save" class="w-4 h-4"></i>
            Simpan
          </button>
        </div>
      </div>
    </div>

    <!-- ===== TOAST ===== -->
    <div
      id="toast"
      class="fixed bottom-6 right-6 z-[200] flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-xl text-white text-sm font-medium opacity-0 translate-y-6 pointer-events-none"
      style="min-width: 260px"
    >
      <i id="toastIcon" class="w-5 h-5 shrink-0"></i>
      <span id="toastMsg"></span>
    </div>

    <script>
      lucide.createIcons();

      /* ---- Sidebar ---- */
      function toggleSidebar() {
        const sidebar = document.getElementById("sidebar");
        const overlay = document.getElementById("sidebarOverlay");
        if (sidebar.classList.contains("-translate-x-full")) {
          sidebar.classList.remove("-translate-x-full");
          overlay.classList.remove("hidden");
          document.body.style.overflow = "hidden";
        } else {
          sidebar.classList.add("-translate-x-full");
          overlay.classList.add("hidden");
          document.body.style.overflow = "auto";
        }
      }

      /* ---- Konfigurasi Status ---- */
      const STATUS_CFG = {
        Menunggu: {
          label: "Menunggu",
          bg: "bg-warning/10",
          text: "text-warning",
          border: "border-warning/20",
          icon: "clock",
        },
        Diproses: {
          label: "Diproses",
          bg: "bg-info/10",
          text: "text-info",
          border: "border-info/20",
          icon: "settings",
        },
        Selesai: {
          label: "Selesai",
          bg: "bg-secondary/10",
          text: "text-secondary",
          border: "border-secondary/20",
          icon: "check-circle",
        },
        Ditolak: {
          label: "Ditolak",
          bg: "bg-danger/10",
          text: "text-danger",
          border: "border-danger/20",
          icon: "x-circle",
        },
      };

      /* Alur transisi yang diperbolehkan */
      const TRANSISI = {
        Menunggu: ["Diproses", "Ditolak"],
        Diproses: ["Selesai", "Ditolak"],
      };

      let activeLaporanId = null;
      let activeStatusBaru = null;

      /* ---- Buka Modal ---- */
      function openModal(id, statusSekarang, judul) {
        activeLaporanId = id;
        activeStatusBaru = null;

        document.getElementById("modalJudul").textContent = judul;
        document.getElementById("modalLaporanId").textContent =
          "#LP-" + String(id).padStart(8, "0");

        /* Badge status sekarang */
        const c = STATUS_CFG[statusSekarang];
        document.getElementById("modalStatusSekarang").innerHTML =
          `<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold ${c.bg} ${c.text} border ${c.border}">${c.label.toUpperCase()}</span>`;

        /* Tombol pilihan status */
        const wrap = document.getElementById("statusOptions");
        wrap.innerHTML = "";
        (TRANSISI[statusSekarang] || []).forEach((s) => {
          const cfg = STATUS_CFG[s];
          const btn = document.createElement("button");
          btn.type = "button";
          btn.dataset.status = s;
          btn.className =
            "status-opt flex items-center justify-center gap-2 px-3 py-3 rounded-lg border-2 border-slate-200 text-sm font-semibold text-muted transition-all hover:opacity-80 focus:outline-none";
          btn.innerHTML = `<i data-lucide="${cfg.icon}" class="w-4 h-4"></i>${cfg.label}`;
          btn.addEventListener("click", () => pilihStatus(s, btn, cfg));
          wrap.appendChild(btn);
        });

        lucide.createIcons();
        document.getElementById("catatanPetugas").value = "";
        document.getElementById("btnSimpanStatus").disabled = true;

        const modal = document.getElementById("statusModal");
        modal.classList.remove("modal-hidden");
      }

      /* ---- Pilih Status ---- */
      function pilihStatus(status, btnEl, cfg) {
        activeStatusBaru = status;
        document.querySelectorAll(".status-opt").forEach((b) => {
          b.className =
            "status-opt flex items-center justify-center gap-2 px-3 py-3 rounded-lg border-2 border-slate-200 text-sm font-semibold text-muted transition-all hover:opacity-80 focus:outline-none";
        });
        btnEl.classList.remove("border-slate-200", "text-muted");
        btnEl.classList.add(
          "border-2",
          cfg.border.replace("/20", ""),
          cfg.text,
          cfg.bg,
        );
        document.getElementById("btnSimpanStatus").disabled = false;
      }

      /* ---- Tutup Modal ---- */
      function closeModal() {
        document.getElementById("statusModal").classList.add("modal-hidden");
        activeLaporanId = null;
        activeStatusBaru = null;
      }

      document
        .getElementById("statusModal")
        .addEventListener("click", function (e) {
          if (e.target === this) closeModal();
        });

      /* ---- Simpan Status (nanti dihubungkan ke AJAX/PHP) ---- */
        function simpanStatus() {
            if (!activeLaporanId || !activeStatusBaru) return;

            const catatan = document.getElementById('catatanPetugas').value;
            const formData = new FormData();
            formData.append('id_laporan', activeLaporanId);
            formData.append('status_baru', activeStatusBaru);
            formData.append('catatan', catatan);

            fetch('update_status.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    updateRowUI(activeLaporanId, activeStatusBaru);
                    showToast('success', 'Status diperbarui menjadi ' + activeStatusBaru + '!');
                    closeModal();
                } else {
                    showToast('error', 'Gagal: ' + data.message);
                }
            })
            .catch(() => showToast('error', 'Terjadi kesalahan!'));
        }

      /* ---- Update Tampilan Baris Setelah Simpan ---- */
      function updateRowUI(id, statusBaru) {
        const row = document.getElementById("row-" + id);
        if (!row) return;

        if (statusBaru === "Selesai" || statusBaru === "Ditolak") {
          /* Hapus baris dengan animasi */
          row.style.transition = "opacity 0.4s ease";
          row.style.opacity = "0";
          setTimeout(() => {
            row.remove();
            const tbody = document.getElementById("taskTableBody");
            if (tbody && tbody.querySelectorAll("tr").length === 0) {
              tbody.innerHTML = `
                            <tr><td colspan="5" class="py-12 text-center text-muted">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 rounded-full bg-secondary/10 flex items-center justify-center mb-3">
                                        <i data-lucide="check-circle" class="w-7 h-7 text-secondary"></i>
                                    </div>
                                    <p class="font-semibold text-dark">Tidak ada tugas aktif</p>
                                    <p class="text-sm mt-1">Semua tugas telah selesai ditangani.</p>
                                </div>
                            </td></tr>`;
              lucide.createIcons();
            }
          }, 400);
        } else {
          /* Update badge status */
          const badge = document.getElementById("status-badge-" + id);
          const cfg = STATUS_CFG[statusBaru];
          if (badge) {
            badge.innerHTML = `<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold ${cfg.bg} ${cfg.text} border ${cfg.border}">${cfg.label.toUpperCase()}</span>`;
          }
          /* Update tombol aksi */
          const actionCell = row.querySelector("td:last-child");
          const judul =
            row.querySelector(".font-bold.text-dark")?.textContent || "";
          if (actionCell) {
            actionCell.innerHTML = `
                        <button onclick="openModal(${id}, '${statusBaru}', '${judul.replace(/'/g, "\\'")}')"
                            class="bg-primary hover:bg-primary-dark text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors w-full shadow-sm">
                            Update Status
                        </button>`;
          }
        }
      }

      /* ---- Toast ---- */
      let toastTimer;
      function showToast(type, msg) {
        clearTimeout(toastTimer);
        const toast = document.getElementById("toast");
        const icon = document.getElementById("toastIcon");
        document.getElementById("toastMsg").textContent = msg;

        toast.className = toast.className.replace(/bg-\S+/g, "").trim();
        icon.setAttribute(
          "data-lucide",
          type === "success" ? "check-circle" : "x-circle",
        );
        toast.classList.add(type === "success" ? "bg-secondary" : "bg-danger");
        lucide.createIcons();

        toast.classList.remove(
          "opacity-0",
          "translate-y-6",
          "pointer-events-none",
        );
        toast.classList.add("opacity-100", "translate-y-0");

        toastTimer = setTimeout(() => {
          toast.classList.add(
            "opacity-0",
            "translate-y-6",
            "pointer-events-none",
          );
          toast.classList.remove("opacity-100", "translate-y-0");
        }, 3500);
      }
      /* ---- Dropdown Kelola Pengaduan ---- */
      function toggleDropdown() {
        const menu = document.getElementById("dropdownMenu");
        const icon = document.getElementById("dropdownIcon");
        const btn = document.getElementById("dropdownBtn");

        menu.classList.toggle("hidden");

        if (!menu.classList.contains("hidden")) {
          icon.style.transform = "rotate(180deg)";
          btn.classList.add("text-primary", "bg-primary/5");
          btn.classList.remove("text-muted");
        } else {
          icon.style.transform = "rotate(0deg)";
          btn.classList.remove("text-primary", "bg-primary/5");
          btn.classList.add("text-muted");
        }
      }
    </script>
  </body>
</html>
