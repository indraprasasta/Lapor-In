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

## BUG LOG

```
##### BUG 1
1) Gejala             : Bisa masuk dengan URL tanpa login
2) Langkah reproduksi : Menganalisa lokasi kesalahan
3) Hipotesis penyebab : Kurang fungsi backend
4) Fix                : if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }
5) Bukti              : 4515c20

##### BUG 2
1) Gejala             : Tidak ada transparansi kerja petugas (bisa asal isi finish pejerkaan)
2) Langkah reproduksi : Menganalisa lokasi kesalahan
3) Hipotesis penyebab : Fitur Kurang jadi menambahkan fitur rating pada user
4) Fix                : Memperbaiki database user (menambahkan rating), memperbaiki file folder user
5) Bukti              : 578ddcd

##### BUG 3
1) Gejala             : Tidak ada Minimal password ketika SIGN UP
2) Langkah reproduksi : Menganalisa lokasi kesalahan
3) Hipotesis penyebab : Kurang Kondisi
4) Fix                : if (strlen($password) < 6) {$pesan_error = 'Password minimal 6 karakter.';}
5) Bukti              : 067cb7
```

---
## AI Usage Statement

```
##### 1
1) Tool                           : Claude
2) Untuk apa                      : Brainstorming
3) 2-3 prompt utama               : Jelaskan saya perbedaan PDO dengan mysqli
4) Bagian output AI yang dipakai  : -
5) Bagian yang saya ubah + alasan : -

##### 2
1) Tool                           : Claude
2) Untuk apa                      : Membantu efisiensi CSS
3) 2-3 prompt utama               : Dari file CSS ini buatkan saya versi tailwind untuk file...
4) Bagian output AI yang dipakai  : Pada bagian beranda admin
5) Bagian yang saya ubah + alasan : warna, variable, font, untuk menyesuaikan UI dengan keinginan

##### 3
1) Tool                           : Claude
2) Untuk apa                      : Mencari Bug
3) 2-3 prompt utama               : Dari Project WEB saya yang sudah jadi, adakah bug / keanehan yang anda temukan?
4) Bagian output AI yang dipakai  : Pada bagian petugas, petugas dapat input status tanpa transparansi apakah pekerjaannya sudah selesai atau belum
5) Bagian yang saya ubah + alasan : Update bagian user dan menambahkan data pada tabel user untuk rating kinerja petugas pada laporan yang sudah kita berikan
```

---

## Pertanyaan Presentasi

```
##### Pertanyaan 1
1) Nama       : Azizurrifki
2) NIM        : F1D02410037
3) Pertanyaan : Pesan Berhasil atau kesalahan input tidak semua ada
4) Tanggapan  : Kelolosan (diperbaiki belakangan)
5) Status     : DONE (Success)

##### Pertanyaan 2
1) Nama       : Salsabila Nailafahdi
2) NIM        : F1D02410135
3) Pertanyaan : Minimal Karakter Password saat sign up tidak ada
4) Tanggapan  : Live Coding Memperbaiki Kesalahan
5) Status     : DONE (Success)

##### Pertanyaan 3
1) Nama       : Royana Afwani, S.T., M.T. (Dosen Pengampu PEMWEB)
2) Pertanyaan : Perbedaan Kegunaan Session pada file admin/beranda.php dan login.php
3) Tanggapan  : admin/beranda.php (untuk menjaga url dan bekas login admin), login.php (untuk bekas login admin dan memvalidasi usn & pw)
4) Status     : DONE (Success)

##### Pertanyaan 4
1) Nama       : Royana Afwani, S.T., M.T. (Dosen Pengampu PEMWEB)
2) Pertanyaan : WEB real-time (update informasi tanpa refresh)
3) Tanggapan  : Belum eksplor sejauh itu karena sedikit yang kami tau WEB berbeda dengan Mobile untuk update secara real-time
4) Status     : DONE (Success)

##### Pertanyaan 5
1) Nama       : Royana Afwani, S.T., M.T. (Dosen Pengampu PEMWEB)
2) Pertanyaan : Penjelasan tentang tailwind dan css
3) Tanggapan  : Kita menggunakan tailwind untuk memudahkan dan efisiensi kode, karena ketika kita menggunakan .css untuk 1 halaman saja memakan 1k line
4) Status     : DONE (Success)

##### Pertanyaan 6
1) Nama       : Royana Afwani, S.T., M.T. (Dosen Pengampu PEMWEB)
2) Pertanyaan : Penjelasan tentang PDO & Query
3) Tanggapan  : PDO (Php Data Object) berfungsi untuk mengambil data dari database, join digunakan untuk menggabungkan dua tabel berdasarkan kolom yang sama
4) Status     : DONE (Success)

##### Pertanyaan 7
1) Nama       : Royana Afwani, S.T., M.T. (Dosen Pengampu PEMWEB)
2) Pertanyaan : Penjelasan tentang interaksi antara frontend dan backend pada JS (const kecamatanLabels = <?php echo json_encode($kecamatan_labels); ?>;)
3) Tanggapan  : untuk mengubah array PHP menjadi format yang bisa digunakan di JavaScript. Fungsi json_encode() akan mengonversi array PHP menjadi string JSON, yang kemudian dapat diakses sebagai array JavaScript.
4) Status     : DONE (Success)

##### Pertanyaan 8
1) Nama       : Royana Afwani, S.T., M.T. (Dosen Pengampu PEMWEB)
2) Pertanyaan : Menunjukkan korelasi kode JavaScript
3) Tanggapan  : Menjelaskan kode dan menunjukkan korelasi dari kode kode yang di tunjukkan
4) Status     : DONE (Success)
```

---

## 🌐 Alamat Website

```
http://localhost/lapor-in/
```

---

*© 2026 Lapor-In · Wujudkan Mataram yang Lebih Nyaman 🌿*
