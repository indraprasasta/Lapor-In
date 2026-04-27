<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan - LaporIn Mataram</title>
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

        /* Timeline specific styles */
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 11px;
            top: 28px;
            bottom: -8px;
            width: 2px;
            background-color: #E2E8F0;
            /* slate-200 */
        }

        .timeline-item:last-child::before {
            display: none;
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
            <a href="daftarLaporan.html" class="flex items-center px-3 py-2.5 bg-primary/10 text-primary rounded-lg font-medium group">
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
            class="h-16 bg-accent border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-30 shrink-0">
            <button
                class="lg:hidden text-muted hover:text-dark p-2 -ml-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                onclick="toggleSidebar()">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <div class="hidden sm:block">
                <nav class="flex text-white text-muted font-medium">
                    <a href="beranda.html" class="hover:text-dark">Dashboard</a>
                    <span class="mx-2">/</span>
                    <a href="daftarLaporan.html" class="hover:text-dark">Laporan Saya</a>
                    <span class="mx-2">/</span>
                    <span class="text-dark">Detail Laporan</span>
                </nav>
            </div>
            <div class="flex items-center ml-auto">
                <button
                    class="relative p-2 text-muted hover:text-dark rounded-full hover:bg-slate-50 transition-colors">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-primary p-4 sm:p-6 lg:p-8">
            <div class="max-w-5xl mx-auto space-y-6">

                <!-- Page Header & Actions -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-center">
                        <button
                            class="mr-4 p-2 rounded-lg hover:bg-slate-200 transition-colors text-slate-500 hover:text-dark focus:outline-none focus:ring-2 focus:ring-primary"
                            title="Kembali" onclick="history.back()">
                            <i data-lucide="arrow-left" class="w-5 h-5"></i>
                        </button>
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <h2 class="text-2xl font-bold text-white tracking-tight">Detail Laporan #LP-20260408</h2>
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-accent text-white border border-info/20 uppercase">
                                    Diproses
                                </span>
                            </div>
                            <p class="text-muted text-sm">Dilaporkan pada 08 April 2026, 09:15 WITA</p>
                        </div>
                    </div>

                    <!-- Tombol Edit/Hapus hanya muncul jika status MENUNGGU (Disembunyikan di contoh ini karena status DIPROSES) -->
                    <!-- 
                    <div class="flex items-center gap-2">
                        <button class="px-4 py-2 bg-white border border-slate-300 text-dark font-medium rounded-lg hover:bg-slate-50 transition-colors flex items-center text-sm shadow-sm">
                            <i data-lucide="edit" class="w-4 h-4 mr-2"></i> Edit
                        </button>
                        <button class="px-4 py-2 bg-white border border-danger text-danger font-medium rounded-lg hover:bg-danger/5 transition-colors flex items-center text-sm shadow-sm">
                            <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i> Hapus
                        </button>
                    </div>
                    -->
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Left Column: Informasi Laporan (2/3 width on LG) -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Card Informasi Utama -->
                        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-dark mb-4">Pohon Tumbang Menutup Jalan</h3>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 mb-6">
                                    <div>
                                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">
                                            Kategori</p>
                                        <div class="flex items-center text-dark font-medium">
                                            <div
                                                class="w-8 h-8 rounded bg-slate-100 flex items-center justify-center mr-2">
                                                <i data-lucide="tree-deciduous" class="w-4 h-4 text-slate-600"></i>
                                            </div>
                                            Pohon Tumbang
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">
                                            Lokasi Kecamatan</p>
                                        <p class="text-dark font-medium">Mataram (Taman Sangkareang)</p>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                                        Deskripsi Kerusakan</p>
                                    <p
                                        class="text-dark text-sm leading-relaxed bg-slate-50 p-4 rounded-lg border border-slate-100">
                                        Terdapat pohon beringin tua yang tumbang akibat hujan angin lebat semalam.
                                        Batang pohon cukup besar dan menutupi hampir seluruh akses jalan masuk menuju
                                        area utara Taman Sangkareang. Mohon segera ditangani karena membahayakan
                                        pengguna jalan dan merusak pagar taman.
                                    </p>
                                </div>

                                <div class="mt-6">
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Alamat
                                        Lengkap</p>
                                    <div class="flex items-start">
                                        <i data-lucide="map-pin" class="w-5 h-5 text-primary mt-0.5 mr-2 shrink-0"></i>
                                        <p class="text-dark text-sm">Jl. Pejanggik, Taman Sangkareang Pintu Utara,
                                            Mataram Barat, Kec. Mataram, Kota Mataram, Nusa Tenggara Bar.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Foto Bukti -->
                        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                            <div
                                class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                                <h3 class="font-bold text-dark">Foto Bukti Kerusakan</h3>
                                <span
                                    class="text-xs font-medium text-slate-500 bg-white px-2 py-1 rounded border border-slate-200">2
                                    Foto</span>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Dummy Images using Unsplash -->
                                    <div
                                        class="aspect-video bg-slate-100 rounded-lg overflow-hidden border border-slate-200 cursor-pointer hover:opacity-90 transition-opacity group relative">
                                        <img src="https://images.unsplash.com/photo-1582298538104-efa9cb1023c4?q=80&w=600&auto=format&fit=crop"
                                            alt="Foto Pohon Tumbang 1" class="w-full h-full object-cover">
                                        <div
                                            class="absolute inset-0 bg-dark/0 group-hover:bg-dark/20 flex items-center justify-center transition-colors">
                                            <i data-lucide="zoom-in"
                                                class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                        </div>
                                    </div>
                                    <div
                                        class="aspect-video bg-slate-100 rounded-lg overflow-hidden border border-slate-200 cursor-pointer hover:opacity-90 transition-opacity group relative">
                                        <img src="https://images.unsplash.com/photo-1545610842-45e05417ab75?q=80&w=600&auto=format&fit=crop"
                                            alt="Foto Pohon Tumbang 2" class="w-full h-full object-cover">
                                        <div
                                            class="absolute inset-0 bg-dark/0 group-hover:bg-dark/20 flex items-center justify-center transition-colors">
                                            <i data-lucide="zoom-in"
                                                class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Timeline & Progress (1/3 width on LG) -->
                    <div class="space-y-6">

                        <!-- Card Timeline Status -->
                        <div
                            class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden sticky top-24">
                            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                                <h3 class="font-bold text-dark">Progress Penanganan</h3>
                            </div>
                            <div class="p-6">
                                <div class="relative">

                                    <!-- Item 3: Current Status (Diproses) -->
                                    <div class="timeline-item relative pb-6 pl-8">
                                        <div
                                            class="absolute left-0 top-1 w-6 h-6 rounded-full bg-accent border-2 border-primary flex items-center justify-center z-10">
                                            <div class="w-2 h-2 rounded-full bg-primary"></div>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-dark">Sedang Diproses</h4>
                                            <p class="text-xs text-muted mb-2">08 Apr 2026, 14:30 WITA</p>
                                            <div
                                                class="bg-info/5 border border-info/10 rounded-lg p-3 text-xs text-dark mt-2">
                                                <p class="font-semibold text-primary mb-1"><i data-lucide="user"
                                                        class="w-3 h-3 inline mr-1"></i> Pak Budi (Petugas Dinas Taman)
                                                </p>
                                                "Petugas sedang menuju lokasi dengan membawa alat pemotong (chainsaw)
                                                dan truk pengangkut."
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Item 2: Assign Petugas -->
                                    <div class="timeline-item relative pb-6 pl-8">
                                        <div
                                            class="absolute left-0 top-1 w-6 h-6 rounded-full bg-white border-2 border-slate-300 flex items-center justify-center z-10">
                                            <i data-lucide="check" class="w-3 h-3 text-slate-400"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-slate-600">Laporan Diterima &
                                                Ditugaskan</h4>
                                            <p class="text-xs text-muted">08 Apr 2026, 10:00 WITA</p>
                                            <p class="text-xs text-slate-500 mt-1">Admin telah memverifikasi laporan dan
                                                menugaskannya ke Dinas Pertamanan kota.</p>
                                        </div>
                                    </div>

                                    <!-- Item 1: Laporan Dibuat -->
                                    <div class="timeline-item relative pl-8">
                                        <div
                                            class="absolute left-0 top-1 w-6 h-6 rounded-full bg-white border-2 border-slate-300 flex items-center justify-center z-10">
                                            <i data-lucide="check" class="w-3 h-3 text-slate-400"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-slate-600">Laporan Dibuat</h4>
                                            <p class="text-xs text-muted">08 Apr 2026, 09:15 WITA</p>
                                        </div>
                                    </div>

                                </div>
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