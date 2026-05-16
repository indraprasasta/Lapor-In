# 🌿 Lapor-In
### Sistem Pelaporan Masalah Lingkungan & Kota *(Smart City Report)*

Platform pelaporan infrastruktur berbasis web yang memungkinkan warga Kota Mataram untuk melaporkan kerusakan lingkungan dan fasilitas kota secara cepat, mudah, dan transparan.

---

## 📋 Deskripsi

**Lapor-In** adalah sistem pelaporan masalah lingkungan dan infrastruktur kota yang dirancang untuk mewujudkan konsep *Smart City* di Kota Mataram. Warga dapat melaporkan berbagai masalah seperti jalan rusak, lampu jalan mati, pohon tumbang, hingga kerusakan saluran air hanya dengan mengunggah foto dan detail lokasi kejadian.

Setiap laporan akan diproses oleh admin dan diteruskan kepada petugas lapangan yang bertanggung jawab, sehingga penanganan masalah menjadi lebih terstruktur dan akuntabel. Platform ini juga dilengkapi fitur berita/pengumuman dari dinas terkait untuk meningkatkan transparansi informasi kepada masyarakat.

---

## 🗺️ Menu Utama

```
USER
  - Landing Page
  - Sign Up
  - Login
      - Membuat Laporan
      - Melihat Status Laporan
      - Edit & Hapus Laporan
      - Mengelola Profil

ADMIN
  - Landing Page
  - Login
      - Dashboard Statistik Laporan
      - Manajemen Laporan (Assign Petugas)
      - Manajemen Data Petugas
      - Publikasi Berita & Pengumuman
      - Manajemen Kategori Laporan

PETUGAS
  - Login
      - Dashboard Tugas
      - Daftar Tugas yang Diterima
      - Update Status Penanganan
      - Riwayat Penanganan
```

---

## 💻 Teknologi

`HTML` `CSS` `JavaScript` `PHP` `MySQL` `Apache`

---

## 🗂️ Struktur Proyek

```
lapor-in/
├── index.php                  # Landing page publik
├── login.php                  # Login warga
├── SignUp.php                 # Registrasi warga
│
├── user/                      # Halaman khusus warga
│   ├── beranda.php
│   ├── buatLaporan.php
│   ├── daftarLaporan.php
│   ├── detailLaporan.php
│   ├── editLaporan.php
│   ├── profile.php
│   ├── proses_profil.php
│   └── logout.php
│
├── admin/                     # Halaman khusus admin
│   ├── login.php
│   ├── beranda.php
│   ├── buatBerita.php
│   ├── daftarBerita.php
│   ├── manajemenLaporan.php
│   ├── dataPetugas.php
│   ├── kategoriLaporan.php
│   └── logout.php
│
├── petugas/                   # Halaman khusus petugas
│   ├── beranda.php
│   ├── daftarTugas.php
│   └── detailTugas.php
│
├── database/
│   └── conection.php          # Konfigurasi koneksi database
│
└── uploads/                   # Penyimpanan file upload
    ├── foto_laporan/
    ├── foto_berita/
    └── foto_profil/
```

---

## 🌐 Alamat Website

```
http://localhost/lapor-in/
```

---

*© 2026 Lapor-In · Wujudkan Mataram yang Lebih Nyaman 🌿*
