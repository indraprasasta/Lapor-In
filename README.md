# 🌿 Lapor-In
### Sistem Pelaporan Masalah Lingkungan & Kota *(Smart City Report)*

Platform pelaporan infrastruktur berbasis web yang memungkinkan warga Kota Mataram untuk melaporkan kerusakan lingkungan dan fasilitas kota secara cepat, mudah, dan transparan.

---

## 📋 Deskripsi

**Lapor-In** dalah sistem pelaporan masalah lingkungan dan infrastruktur kota yang dirancang untuk mewujudkan konsep Smart City di Kota Mataram. Warga dapat melaporkan berbagai masalah seperti jalan rusak, lampu jalan mati, pohon tumbang, sampah menumpuk, hingga kerusakan fasilitas umum dengan mengunggah foto dan detail lokasi kejadian.

Setiap laporan akan ditinjau oleh admin yang kemudian mengubah status penanganan laporan (Menunggu → Diproses → Selesai / Ditolak). Petugas lapangan yang terdaftar pada dinas terkait dapat memantau dan memperbarui status laporan secara langsung melalui dashboard petugas. Warga juga dapat memberikan rating dan ulasan setelah laporan selesai ditangani. Platform ini juga dilengkapi fitur berita dan pengumuman untuk meningkatkan transparansi informasi kepada masyarakat.

---

## 🗺️ Menu Utama

```
USER
  - Landing Page (index.php)
  - Sign Up
  - Login
      - Beranda / Dashboard
      - Membuat Laporan
      - Melihat & Menelusuri Daftar Laporan
      - Detail, Edit & Hapus Laporan (hanya status Menunggu)
      - Rating & Ulasan Laporan (status Selesai)
      - Mengelola Profil (foto, nama, alamat, username)

ADMIN
  - Login
      - Dashboard Statistik Laporan
      - Manajemen Laporan (Assign Petugas ke Laporan)
      - Data Petugas (Tambah, Edit, Hapus)
      - Data User (Lihat, Hapus beserta laporannya)
      - Publikasi Berita (Buat, Edit, Hapus)
      - Manajemen Kategori Laporan (Tambah, Toggle Aktif/Nonaktif, Hapus)

PETUGAS
  - Login
      - Dashboard Tugas (statistik total, diproses, selesai)
      - Kelola Pengaduan (Masuk, Proses, Ditolak, Selesai)
      - Detail Laporan per Pengaduan
      - Update Status Penanganan (Diproses / Selesai / Ditolak)
```
---

## 👥 Tim Pengembang

| Nama | NIM | Role & Tanggung Jawab |
|------|-----|----------------------|
| **I Wayan Girindra Prasasta** | F1D02410009 | **Fullstack Lead** — Desain halaman dengan (Tailwind CSS), landing page, & halaman user, responsivitas, Arsitektur sistem, backend PHP (autentikasi, manajemen laporan), koneksi MySQL |
| **Mochammad Gaza Hadi Rabbani** | F1D02410121 | **Fullstack Lead** — Desain halaman dengan (Tailwind CSS) admin, integrasi form Arsitektur sistem, backend PHP (autentikasi, logika petugas), desain database, koneksi MySQL |

---

## 💻 Tech Stack

| Kategori | Teknologi |
|----------|-----------|
| **Frontend** | HTML5, Tailwind CSS (CDN), JavaScript (Vanilla) |
| **Ikon** | Lucide Icons, Font Awesome 6 |
| **Tipografi** | Google Fonts — Poppins |
| **Backend** | PHP 8 (Native, tanpa framework) |
| **Database** | MySQL |
| **Server** | Apache (XAMPP / LAMP) |
| **Upload Handler** | PHP `move_uploaded_file()` |
| **Session** | PHP Native Session |
| **Alert & Dialog** | SweetAlert2 |

---

## 🗄️ DBMS — Konfigurasi & Spesifikasi Tabel

### ⚙️ Konfigurasi Koneksi
```
Host     : localhost
User     : root
Password : (kosong)
Database : laporin
File     : /database/conection.php
```

---

## 💻 Teknologi

`HTML` `CSS` `JavaScript` `PHP` `MySQL` `Apache`

---

## 🗂️ SITE MAP

```
lapor-in/
├── index.php                   # Landing page publik
├── login.php                   # Halaman login (user, admin, petugas)
├── SignUp.php                  # Registrasi warga
├── semuaBerita.php             # Halaman publik semua berita
├── semuaLaporan.php            # Halaman publik laporan terselesaikan
├── lupaPassword.php            # Halaman lupa password
├── fix_pass.php                # Utilitas: hash ulang password lama
├── force_logout.php            # Utilitas: paksa logout semua sesi
│
├── user/                       # Halaman khusus warga (session: user_id)
│   ├── beranda.php
│   ├── buatLaporan.php
│   ├── daftarLaporan.php
│   ├── detailLaporan.php       # Termasuk fitur rating & ulasan
│   ├── editLaporan.php         # Hanya laporan berstatus Menunggu
│   ├── proses_delete_laporan.php
│   ├── profile.php
│   ├── proses_profil.php
│   ├── sidebar.php
│   └── logout.php
│
├── admin/                      # Halaman khusus admin (session: admin_id)
│   ├── beranda.php
│   ├── dataLaporan.php         # Manajemen laporan & assign petugas
│   ├── detailLaporan.php
│   ├── buatBerita.php
│   ├── daftarBerita.php
│   ├── editBerita.php
│   ├── dataPetugas.php
│   ├── tambahpetugas.php
│   ├── editPetugas.php
│   ├── dataUser.php
│   ├── kategoriLaporan.php
│   ├── sidebar.php
│   └── logout.php
│
├── petugas/                    # Halaman khusus petugas (session: petugas_id)
│   ├── beranda.php
│   ├── pengaduan.php           # Kelola pengaduan per status
│   ├── detailLaporan.php
│   ├── update_status.php       # API update status laporan (POST/JSON)
│   ├── sidebar.php
│   └── logout.php
│
├── database/
│   └── conection.php           # Konfigurasi koneksi PDO MySQL
│
├── src/
│   └── style.css               # CSS kustom untuk landing page
│
└── uploads/                    # Penyimpanan file upload
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
