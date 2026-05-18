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

### 📊 Spesifikasi Tabel

#### Tabel `users`
Menyimpan data akun warga yang terdaftar.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT, AUTO_INCREMENT, PK | ID unik user |
| `nama` | VARCHAR(100) | Nama lengkap user |
| `nik` | VARCHAR(16), UNIQUE | Nomor Induk Kependudukan (16 digit) |
| `username` | VARCHAR(50), UNIQUE | Username untuk login |
| `password` | VARCHAR(255) | Password akun |
| `gender` | ENUM('Laki-laki','Perempuan') | Jenis kelamin |
| `alamat` | TEXT | Alamat tempat tinggal |
| `foto` | VARCHAR(255) | Nama file foto profil (disimpan di `uploads/foto_profil/`) |

---

#### Tabel `admin`
Menyimpan data akun administrator.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT, AUTO_INCREMENT, PK | ID unik admin |
| `nama` | VARCHAR(100) | Nama lengkap admin |
| `username` | VARCHAR(50), UNIQUE | Username untuk login |
| `password` | VARCHAR(255) | Password akun |

---

#### Tabel `dinas`
Menyimpan data instansi/dinas terkait.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT, AUTO_INCREMENT, PK | ID unik dinas |
| `kode_dinas` | VARCHAR(20) | Kode singkat dinas (cth: PUPR) |
| `nama_dinas` | VARCHAR(100) | Nama lengkap dinas |

---

#### Tabel `petugas`
Menyimpan data akun petugas lapangan.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT, AUTO_INCREMENT, PK | ID unik petugas |
| `nama` | VARCHAR(100) | Nama lengkap petugas |
| `nip` | VARCHAR(30), UNIQUE | Nomor Induk Pegawai |
| `username` | VARCHAR(50), UNIQUE | Username untuk login |
| `password` | VARCHAR(255) | Password akun |
| `jabatan` | VARCHAR(100) | Jabatan petugas |
| `dinas_id` | INT, FK → `dinas.id` | Referensi instansi petugas |

---

#### Tabel `laporan`
Menyimpan data laporan yang dikirim oleh warga.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT, AUTO_INCREMENT, PK | ID unik laporan |
| `user_id` | INT, FK → `users.id` | Pemilik laporan |
| `judul` | VARCHAR(100) | Judul laporan |
| `kategori` | VARCHAR(50) | Kategori masalah (Jalan Rusak, Pohon Tumbang, dll.) |
| `deskripsi` | TEXT | Uraian detail masalah |
| `foto` | VARCHAR(255) | Nama file foto bukti (disimpan di `uploads/foto_laporan/`) |
| `alamat` | TEXT | Alamat lokasi kejadian |
| `kecamatan` | VARCHAR(100) | Kecamatan lokasi |
| `kelurahan` | VARCHAR(100) | Kelurahan lokasi |
| `status` | ENUM('Menunggu','Diproses','Selesai','Ditolak') | Status penanganan laporan |
| `petugas_id` | INT, FK → `petugas.id`, NULLABLE | Petugas yang ditugaskan (diisi oleh admin) |
| `tanggal` | DATETIME / TIMESTAMP | Waktu laporan dibuat |

---

#### Tabel `berita`
Menyimpan data berita/pengumuman yang dipublikasikan admin.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT, AUTO_INCREMENT, PK | ID unik berita |
| `judul` | VARCHAR(200) | Judul berita |
| `isi` | TEXT | Isi konten berita |
| `foto` | VARCHAR(255) | Nama file foto berita (disimpan di `uploads/foto_berita/`) |
| `kategori` | VARCHAR(50) | Kategori berita (Infrastruktur, Lingkungan, dll.) |
| `tanggal` | DATETIME / TIMESTAMP | Waktu berita dipublikasikan |

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
