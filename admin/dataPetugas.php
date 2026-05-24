<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

require __DIR__ . '/../database/conection.php';

$admin_nama = $_SESSION['admin_nama'];

// Hapus data petugas
if (isset($_GET['hapus'])) {
    $id_hapus = (int) $_GET['hapus'];
    $stmt_hapus = $pdo->prepare("DELETE FROM petugas WHERE id = :id");
    $stmt_hapus->execute([':id' => $id_hapus]);
    header("Location: datapetugas.php?deleted=1");
    exit();
}

// Search & Filter
$search       = isset($_GET['search'])  ? trim($_GET['search'])  : '';
$filter_dinas = isset($_GET['dinas_id']) ? (int) $_GET['dinas_id'] : 0;

$where = "WHERE 1=1";
$params = [];
if ($search != '') {
    $where .= " AND (p.nama LIKE :search OR p.nip LIKE :search)";
    $params[':search'] = "%$search%";
}
if ($filter_dinas > 0) {
    $where .= " AND p.dinas_id = :dinas_id";
    $params[':dinas_id'] = $filter_dinas;
}

// Pagination
$limit = 4;
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Ambil total data untuk pagination (dengan filter)
$stmt_total = $pdo->prepare("
    SELECT COUNT(*) as t
    FROM petugas p
    JOIN dinas d ON p.dinas_id = d.id
    $where
");
$stmt_total->execute($params);
$total_petugas_filter = $stmt_total->fetch()['t'];
$total_pages = ceil($total_petugas_filter / $limit);

// Tambahkan Limit ke params
$params[':limit'] = $limit;
$params[':offset'] = $offset;

// Ambil data petugas join dengan dinas
$stmt_petugas = $pdo->prepare("
    SELECT p.*, d.nama_dinas, d.kode_dinas
    FROM petugas p
    JOIN dinas d ON p.dinas_id = d.id
    $where
    ORDER BY p.id DESC
    LIMIT :limit OFFSET :offset
");

// Bind semua parameter secara manual karena LIMIT/OFFSET butuh INT
foreach ($params as $key => $val) {
    if ($key === ':limit' || $key === ':offset' || $key === ':dinas_id') {
        $stmt_petugas->bindValue($key, $val, PDO::PARAM_INT);
    } else {
        $stmt_petugas->bindValue($key, $val, PDO::PARAM_STR);
    }
}
$stmt_petugas->execute();
$query_petugas = $stmt_petugas->fetchAll();
$total_petugas = count($query_petugas); // jumlah di page ini

// Statistik
$total         = $pdo->query("SELECT COUNT(*) as t FROM petugas")->fetch()['t'];
$diproses   = $pdo->query("SELECT COUNT(*) as total FROM laporan WHERE status = 'Diproses'")->fetch()['total'];

// Ambil data dinas untuk filter dropdown
$query_dinas_stmt = $pdo->query("SELECT * FROM dinas ORDER BY nama_dinas ASC");
$query_dinas = $query_dinas_stmt->fetchAll();
?>

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
    <?php include 'sidebar.php'; ?>

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
                <!-- Total Petugas -->
                 <p class="text-xs font-semibold text-slate-500 uppercase">
                  Total Petugas
                </p>
                <div class="p-1.5 bg-slate-100 rounded text-slate-600">
                  <i data-lucide="users" class="w-4 h-4"></i>
                </div>
              </div>
              <p class="text-2xl font-bold text-dark"><?php echo $total; ?></p>
              <p class="text-xs text-muted font-medium mt-1">
                Terdaftar dalam sistem
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
              <p class="text-2xl font-bold text-info"><?php echo $diproses; ?></p>
              <p class="text-xs text-info/80 font-medium mt-1">
                Menangani laporan aktif
              </p>
            </div>
          </div>

          <!-- Filters & Search Toolbar -->
          <form method="GET" class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
              <div class="flex flex-col lg:flex-row gap-4 items-end lg:items-center">
                  <!-- Search -->
                  <div class="flex-1 w-full relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <i data-lucide="search" class="w-5 h-5 text-slate-400"></i>
                      </div>
                      <input type="text" name="search" value="<?php echo $search; ?>"
                          placeholder="Cari nama petugas atau NIP..."
                          class="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm">
                  </div>

                  <div class="flex flex-wrap sm:flex-nowrap gap-3 w-full lg:w-auto">
                      <!-- Filter Dinas -->
                      <div class="relative w-full sm:w-48">
                          <select name="dinas_id"
                              class="w-full pl-3 pr-8 py-2 appearance-none rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-white text-slate-700">
                              <option value="0">Semua Dinas</option>
                              <?php
                              foreach($query_dinas as $d):
                              ?>
                              <option value="<?php echo $d['id']; ?>" <?php echo $filter_dinas == $d['id'] ? 'selected' : ''; ?>>
                                  <?php echo $d['nama_dinas']; ?>
                              </option>
                              <?php endforeach; ?>
                          </select>
                          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                              <i data-lucide="chevron-down" class="w-4 h-4"></i>
                          </div>
                      </div>

                      <button type="submit"
                          class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-primary-dark transition-colors">
                          Cari
                      </button>

                      <?php if($search || $filter_dinas > 0): ?>
                      <a href="datapetugas.php"
                          class="bg-slate-100 text-dark px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-200 transition-colors text-center">
                          Reset
                      </a>
                      <?php endif; ?>
                  </div>
              </div>
          </form>

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
                <?php if($total_petugas > 0): ?>
                    <?php foreach($query_petugas as $petugas): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <!-- Profil -->
                        <td class="py-4 px-6">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full overflow-hidden mr-3 shrink-0 border border-slate-200">
                                    <?php if(!empty($petugas['foto'])): ?>
                                    <img src="../uploads/foto_petugas/<?php echo $petugas['foto']; ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($petugas['nama']); ?>&background=A3B18A&color=ffffff" class="w-full h-full object-cover">
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="font-bold text-dark"><?php echo $petugas['nama']; ?></p>
                                    <p class="text-xs font-mono text-slate-500 mt-0.5">NIP: <?php echo $petugas['nip']; ?></p>
                                    <p class="text-xs text-muted mt-0.5">@<?php echo $petugas['username']; ?></p>
                                </div>
                            </div>
                        </td>

                        <!-- Dinas -->
                        <td class="py-4 px-6 align-middle">
                            <p class="text-dark font-medium"><?php echo $petugas['nama_dinas']; ?></p>
                            <p class="text-xs text-muted mt-1"><?php echo $petugas['jabatan']; ?></p>
                        </td>

                        <!-- Beban Saat Ini (laporan Diproses) -->
                        <?php
                        $stmt_beban = $pdo->prepare(
                            "SELECT COUNT(*) as t FROM laporan 
                            WHERE status = 'Diproses' 
                            AND kategori IN (
                                SELECT kategori FROM dinas_kategori WHERE dinas_id = :dinas_id
                            )"
                        );
                        $stmt_beban->execute([':dinas_id' => $petugas['dinas_id']]);
                        $beban = $stmt_beban->fetch()['t'];
                        ?>
                        <td class="py-4 px-6 text-center align-middle">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full <?php echo $beban > 0 ? 'bg-info/10 text-info border border-info/20' : 'bg-slate-100 text-muted'; ?> font-bold">
                                <?php echo $beban; ?>
                            </span>
                        </td>

                        <!-- Total Selesai -->
                        <?php
                        $stmt_selesai = $pdo->prepare(
                            "SELECT COUNT(*) as t FROM laporan 
                            WHERE status = 'Selesai'
                            AND kategori IN (
                                SELECT kategori FROM dinas_kategori WHERE dinas_id = :dinas_id
                            )"
                        );
                        $stmt_selesai->execute([':dinas_id' => $petugas['dinas_id']]);
                        $selesai = $stmt_selesai->fetch()['t'];
                        ?>
                        <td class="py-4 px-6 text-center align-middle">
                            <p class="text-lg font-bold text-slate-700"><?php echo $selesai; ?></p>
                        </td>

                        <!-- Ketersediaan -->
                        <td class="py-4 px-6 align-middle">
                            <?php if($beban > 0): ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-info/10 text-info border border-info/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-info mr-1.5 animate-pulse"></span>
                                SEDANG BERTUGAS
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-secondary/10 text-secondary border border-secondary/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-secondary mr-1.5"></span>
                                TERSEDIA
                            </span>
                            <?php endif; ?>
                        </td>

                        <!-- Aksi -->
                        <td class="py-4 px-6 text-center align-middle">
                            <div class="flex items-center justify-center gap-2">
                                <a href="editPetugas.php?id=<?php echo $petugas['id']; ?>" class="p-1.5 text-warning hover:bg-warning/10 rounded-lg transition-colors" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                <button onclick="if(confirm('Yakin ingin menghapus petugas <?php echo addslashes($petugas['nama']); ?>?')) window.location.href='datapetugas.php?hapus=<?php echo $petugas['id']; ?>';"
                                    class="p-1.5 text-danger hover:bg-danger/10 rounded-lg transition-colors" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="py-12 text-center text-muted">
                            <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                            <p class="font-medium">Tidak ada petugas ditemukan</p>
                            <a href="tambahpetugas.php" class="text-primary text-sm hover:underline mt-1 inline-block">Tambah petugas baru</a>
                        </td>
                    </tr>
                <?php endif; ?>
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
                      Menampilkan <span class="font-medium text-dark"><?php echo $total_petugas; ?></span>
                      dari <span class="font-medium text-dark"><?php echo $total_petugas_filter; ?></span> petugas
                  </p>
                </div>
                <div>
                  <?php if($total_pages > 1): ?>
                  <nav
                    class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                    aria-label="Pagination"
                  >
                    <!-- Tombol Prev -->
                    <?php if($page > 1): ?>
                    <a
                      href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search='.$search : ''; ?><?php echo $filter_dinas ? '&dinas_id='.$filter_dinas : ''; ?>"
                      class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-slate-300 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50"
                    >
                      <span class="sr-only">Previous</span>
                      <i data-lucide="chevron-left" class="h-4 w-4"></i>
                    </a>
                    <?php else: ?>
                    <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-slate-300 bg-white text-sm font-medium text-slate-400 cursor-not-allowed">
                      <i data-lucide="chevron-left" class="h-4 w-4"></i>
                    </span>
                    <?php endif; ?>

                    <!-- Nomor Halaman -->
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if($i == $page): ?>
                        <span class="z-10 bg-primary border-primary text-white relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            <?php echo $i; ?>
                        </span>
                        <?php else: ?>
                        <a href="?page=<?php echo $i; ?><?php echo $search ? '&search='.$search : ''; ?><?php echo $filter_dinas ? '&dinas_id='.$filter_dinas : ''; ?>" 
                           class="bg-white border-slate-300 text-slate-500 hover:bg-slate-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            <?php echo $i; ?>
                        </a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <!-- Tombol Next -->
                    <?php if($page < $total_pages): ?>
                    <a
                      href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search='.$search : ''; ?><?php echo $filter_dinas ? '&dinas_id='.$filter_dinas : ''; ?>"
                      class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-slate-300 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50"
                    >
                      <span class="sr-only">Next</span>
                      <i data-lucide="chevron-right" class="h-4 w-4"></i>
                    </a>
                    <?php else: ?>
                    <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-slate-300 bg-white text-sm font-medium text-slate-400 cursor-not-allowed">
                      <i data-lucide="chevron-right" class="h-4 w-4"></i>
                    </span>
                    <?php endif; ?>
                  </nav>
                  <?php endif; ?>
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
