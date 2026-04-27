<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Saya - LaporIn Mataram</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind Config to match LaporIn Design System -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            DEFAULT: '#3A5A40',
                            dark: '#2B4330',
                        },
                        accent: {
                            DEFAULT: '#A3B18A',
                            dark: '#8b9a70',
                        },
                        secondary: '#588157',
                        warning: '#D97706',
                        danger: '#DC2626',
                        info: '#0284C7',
                        dark: '#1E293B',
                        light: '#F8FAFC',
                        muted: '#94A3B8',
                    }
                }
            }
        }
    </script>
    <style>
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #F8FAFC;
        }

        ::-webkit-scrollbar-thumb {
            background: #A3B18A;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #3A5A40;
        }
    </style>
</head>

<body class="bg-light text-dark font-sans h-screen flex overflow-hidden">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-dark/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
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
            <a href="beranda.html"
                class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"></i>
                Beranda
            </a>
            <a href="buatLaporan.html"
                class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="plus-circle" class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"></i>
                Buat Laporan
            </a>
            <a href="#" class="flex items-center px-3 py-2.5 bg-primary/10 text-primary rounded-lg font-medium group">
                <i data-lucide="file-text" class="w-5 h-5 mr-3"></i>
                Laporan Saya
            </a>
            <a href="#"
                class="flex items-center px-3 py-2.5 text-muted hover:text-dark hover:bg-slate-50 rounded-lg font-medium transition-colors group">
                <i data-lucide="user" class="w-5 h-5 mr-3 group-hover:text-primary transition-colors"></i>
                Profil
            </a>
        </nav>

        <!-- User Info -->
        <div class="p-4 border-t border-slate-200">
            <a href="#" class="flex items-center group">
                <div
                    class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-primary font-bold overflow-hidden border border-slate-200">
                    <img src="https://ui-avatars.com/api/?name=Pak+Andi&background=A3B18A&color=ffffff" alt="Avatar"
                        class="w-full h-full object-cover">
                </div>
                <div class="ml-3">
                    <p class="text-sm font-semibold text-dark group-hover:text-primary transition-colors">Pak Andi</p>
                    <p class="text-xs text-muted">Masyarakat</p>
                </div>
            </a>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">

        <!-- Top Navbar -->
        <header
            class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-30 shrink-0">
            <button
                class="lg:hidden text-muted hover:text-dark p-2 -ml-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                onclick="toggleSidebar()">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <div class="hidden sm:block">
                <nav class="flex text-sm text-muted font-medium">
                    <a href="beranda.html" class="hover:text-dark">Dashboard</a>
                    <span class="mx-2">/</span>
                    <span class="text-dark">Daftar Laporan</span>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-light p-4 sm:p-6 lg:p-8">
            <div class="max-w-6xl mx-auto flex flex-col h-full space-y-6">

                <!-- Page Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-dark tracking-tight">Daftar Laporan Saya</h2>
                        <p class="text-muted mt-1 text-sm">Riwayat dan status seluruh laporan infrastruktur yang pernah
                            Anda buat.</p>
                    </div>
                    <a href="buatLaporan.html"
                        class="bg-primary hover:bg-primary-dark text-white px-4 py-2.5 rounded-lg font-semibold flex items-center justify-center transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Buat Laporan
                    </a>
                </div>

                <!-- Filters & Search -->
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex flex-col lg:flex-row gap-4">
                    <!-- Search Input -->
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-5 h-5 text-slate-400"></i>
                        </div>
                        <input type="text" placeholder="Cari judul laporan..."
                            class="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm">
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="relative w-full sm:w-48">
                            <select
                                class="w-full pl-4 pr-10 py-2 appearance-none rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm bg-white">
                                <option value="all">Semua Kategori</option>
                                <option value="1">Jalan Rusak</option>
                                <option value="2">Pohon Tumbang</option>
                                <option value="3">Lampu Jalan Mati</option>
                                <option value="4">Saluran Air</option>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <div class="relative w-full sm:w-48">
                            <select
                                class="w-full pl-4 pr-10 py-2 appearance-none rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm bg-white">
                                <option value="all">Semua Status</option>
                                <option value="menunggu">Menunggu</option>
                                <option value="diproses">Diproses</option>
                                <option value="selesai">Selesai</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reports List (Table for Desktop, Cards for Mobile) -->
                <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">

                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-sm">
                                    <th class="py-3 px-4 font-semibold text-dark">Laporan</th>
                                    <th class="py-3 px-4 font-semibold text-dark">Kategori</th>
                                    <th class="py-3 px-4 font-semibold text-dark">Lokasi</th>
                                    <th class="py-3 px-4 font-semibold text-dark">Tanggal</th>
                                    <th class="py-3 px-4 font-semibold text-dark">Status</th>
                                    <th class="py-3 px-4 text-center font-semibold text-dark">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-slate-100">
                                <!-- Row 1: Menunggu -->
                                <tr class="hover:bg-slate-50 transition-colors group cursor-pointer" onclick="window.location='detailLaporan.html'">
                                    <td class="py-4 px-4 align-top">
                                        <p class="font-bold text-dark mb-1 line-clamp-1 group-hover:text-primary transition-colors">Jalan Berlubang Dalam di
                                            Simpang Empat</p>
                                        <p class="text-xs text-muted line-clamp-1">Terdapat lubang cukup besar
                                            membahayakan pengendara motor.</p>
                                    </td>
                                    <td class="py-4 px-4 align-top">
                                        <div class="flex items-center text-dark">
                                            <i data-lucide="road" class="w-4 h-4 mr-2 text-slate-400"></i>
                                            Jalan Rusak
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 align-top text-muted">Kekalik Jaya, Sekarbela</td>
                                    <td class="py-4 px-4 align-top text-muted">10 Apr 2026</td>
                                    <td class="py-4 px-4 align-top">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-warning/10 text-warning border border-warning/20">
                                            MENUNGGU
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 align-top text-center">
                                        <button
                                            class="text-primary hover:text-primary-dark p-1.5 rounded-lg hover:bg-primary/10 transition-colors"
                                            title="Lihat Detail">
                                            <i data-lucide="eye" class="w-5 h-5"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Row 2: Diproses -->
                                <tr class="hover:bg-slate-50 transition-colors group cursor-pointer" onclick="window.location='detailLaporan.html'">
                                    <td class="py-4 px-4 align-top">
                                        <p class="font-bold text-dark mb-1 line-clamp-1 group-hover:text-primary transition-colors">Pohon Tumbang Menutup Jalan</p>
                                        <p class="text-xs text-muted line-clamp-1">Pohon beringin tumbang akibat hujan
                                            angin semalam.</p>
                                    </td>
                                    <td class="py-4 px-4 align-top">
                                        <div class="flex items-center text-dark">
                                            <i data-lucide="tree-deciduous" class="w-4 h-4 mr-2 text-slate-400"></i>
                                            Pohon Tumbang
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 align-top text-muted">Taman Sangkareang, Mataram</td>
                                    <td class="py-4 px-4 align-top text-muted">08 Apr 2026</td>
                                    <td class="py-4 px-4 align-top">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-info/10 text-info border border-info/20">
                                            DIPROSES
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 align-top text-center">
                                        <button
                                            class="text-primary hover:text-primary-dark p-1.5 rounded-lg hover:bg-primary/10 transition-colors"
                                            title="Lihat Detail">
                                            <i data-lucide="eye" class="w-5 h-5"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Row 3: Selesai -->
                                <tr class="hover:bg-slate-50 transition-colors group cursor-pointer" onclick="window.location='detailLaporan.html'">
                                    <td class="py-4 px-4 align-top">
                                        <p class="font-bold text-dark mb-1 line-clamp-1 group-hover:text-primary transition-colors">Lampu PJU Padam</p>
                                        <p class="text-xs text-muted line-clamp-1">Mati total sepanjang 50 meter area
                                            perempatan.</p>
                                    </td>
                                    <td class="py-4 px-4 align-top">
                                        <div class="flex items-center text-dark">
                                            <i data-lucide="lightbulb-off" class="w-4 h-4 mr-2 text-slate-400"></i>
                                            Lampu Jalan
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 align-top text-muted">Jl. Udayana, Selaparang</td>
                                    <td class="py-4 px-4 align-top text-muted">01 Apr 2026</td>
                                    <td class="py-4 px-4 align-top">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-secondary/10 text-secondary border border-secondary/20">
                                            SELESAI
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 align-top text-center">
                                        <button
                                            class="text-primary hover:text-primary-dark p-1.5 rounded-lg hover:bg-primary/10 transition-colors"
                                            title="Lihat Detail">
                                            <i data-lucide="eye" class="w-5 h-5"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Row 4: Ditolak -->
                                <tr class="hover:bg-slate-50 transition-colors group opacity-80 cursor-pointer" onclick="window.location='detailLaporan.html'">
                                    <td class="py-4 px-4 align-top">
                                        <p class="font-bold text-dark mb-1 line-clamp-1 text-slate-500 group-hover:text-primary transition-colors">Saluran Air
                                            Mampet (Duplikat)</p>
                                        <p class="text-xs text-muted line-clamp-1">Air meluap ke jalan saat hujan deras
                                            kemarin.</p>
                                    </td>
                                    <td class="py-4 px-4 align-top">
                                        <div class="flex items-center text-slate-500">
                                            <i data-lucide="droplets" class="w-4 h-4 mr-2 text-slate-400"></i>
                                            Saluran Air
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 align-top text-muted">Ampenan Tengah, Ampenan</td>
                                    <td class="py-4 px-4 align-top text-muted">25 Mar 2026</td>
                                    <td class="py-4 px-4 align-top">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-danger/10 text-danger border border-danger/20">
                                            DITOLAK
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 align-top text-center">
                                        <button
                                            class="text-primary hover:text-primary-dark p-1.5 rounded-lg hover:bg-primary/10 transition-colors"
                                            title="Lihat Detail">
                                            <i data-lucide="eye" class="w-5 h-5"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="md:hidden flex flex-col divide-y divide-slate-100">
                        <!-- Card 1 -->
                        <div class="p-4 hover:bg-slate-50 transition-colors relative cursor-pointer group" onclick="window.location='detailLaporan.html'">
                            <div class="flex justify-between items-start mb-3">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-warning/10 text-warning uppercase tracking-wide">
                                    Menunggu
                                </span>
                                <span class="text-xs text-muted">10 Apr 2026</span>
                            </div>
                            <h4
                                class="font-bold text-dark text-sm mb-1 group-hover:text-primary transition-colors pr-6">
                                Jalan Berlubang Dalam di Simpang Empat</h4>
                            <div class="flex flex-wrap gap-y-2 text-xs text-muted mt-2">
                                <div class="flex items-center w-full sm:w-1/2">
                                    <i data-lucide="road" class="w-3.5 h-3.5 mr-1.5"></i> Jalan Rusak
                                </div>
                                <div class="flex items-center w-full sm:w-1/2">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 mr-1.5"></i> Kekalik Jaya, Sekarbela
                                </div>
                            </div>
                            <i data-lucide="chevron-right"
                                class="w-5 h-5 text-slate-300 absolute right-4 top-1/2 transform -translate-y-1/2 group-hover:text-primary"></i>
                        </div>

                        <!-- Card 2 -->
                        <div class="p-4 hover:bg-slate-50 transition-colors relative cursor-pointer group" onclick="window.location='detailLaporan.html'">
                            <div class="flex justify-between items-start mb-3">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-info/10 text-info uppercase tracking-wide">
                                    Diproses
                                </span>
                                <span class="text-xs text-muted">08 Apr 2026</span>
                            </div>
                            <h4
                                class="font-bold text-dark text-sm mb-1 group-hover:text-primary transition-colors pr-6">
                                Pohon Tumbang Menutup Jalan</h4>
                            <div class="flex flex-wrap gap-y-2 text-xs text-muted mt-2">
                                <div class="flex items-center w-full sm:w-1/2">
                                    <i data-lucide="tree-deciduous" class="w-3.5 h-3.5 mr-1.5"></i> Pohon Tumbang
                                </div>
                                <div class="flex items-center w-full sm:w-1/2">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 mr-1.5"></i> Taman Sangkareang, Mataram
                                </div>
                            </div>
                            <i data-lucide="chevron-right"
                                class="w-5 h-5 text-slate-300 absolute right-4 top-1/2 transform -translate-y-1/2 group-hover:text-primary"></i>
                        </div>

                        <!-- Card 3 -->
                        <div class="p-4 hover:bg-slate-50 transition-colors relative cursor-pointer group" onclick="window.location='detailLaporan.html'">
                            <div class="flex justify-between items-start mb-3">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-secondary/10 text-secondary uppercase tracking-wide">
                                    Selesai
                                </span>
                                <span class="text-xs text-muted">01 Apr 2026</span>
                            </div>
                            <h4
                                class="font-bold text-dark text-sm mb-1 group-hover:text-primary transition-colors pr-6">
                                Lampu PJU Padam</h4>
                            <div class="flex flex-wrap gap-y-2 text-xs text-muted mt-2">
                                <div class="flex items-center w-full sm:w-1/2">
                                    <i data-lucide="lightbulb-off" class="w-3.5 h-3.5 mr-1.5"></i> Lampu Jalan Mati
                                </div>
                                <div class="flex items-center w-full sm:w-1/2">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 mr-1.5"></i> Jl. Udayana, Selaparang
                                </div>
                            </div>
                            <i data-lucide="chevron-right"
                                class="w-5 h-5 text-slate-300 absolute right-4 top-1/2 transform -translate-y-1/2 group-hover:text-primary"></i>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div
                        class="border-t border-slate-200 bg-slate-50 px-4 py-3 flex items-center justify-between sm:px-6">
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-muted">
                                    Menampilkan <span class="font-medium text-dark">1</span> sampai <span
                                        class="font-medium text-dark">4</span> dari <span
                                        class="font-medium text-dark">4</span> laporan
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                    aria-label="Pagination">
                                    <a href="#"
                                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-slate-300 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50">
                                        <span class="sr-only">Previous</span>
                                        <i data-lucide="chevron-left" class="h-4 w-4"></i>
                                    </a>
                                    <a href="#" aria-current="page"
                                        class="z-10 bg-primary/10 border-primary text-primary relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                        1
                                    </a>
                                    <a href="#"
                                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-slate-300 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50">
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

        // Sidebar Toggle Logic for Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }
    </script>
</body>

</html>