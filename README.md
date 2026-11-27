# LearnSphere - Portal Belajar & Quiz Matematika Terbuka

**Proyek Akhir Responsi 2 — Praktikum Pemrograman Web I**  
Jurusan Informatika, Fakultas Teknik, Universitas Jenderal Soedirman (2026)

---

## 1. Nama dan Deskripsi Proyek

**LearnSphere** (*Open Mathematics Hub*) adalah aplikasi web edukasi berbasis akses terbuka yang dirancang untuk memfasilitasi pembelajaran matematika secara terstruktur, mandiri, dan interaktif. 

Platform ini memadukan akses terbuka bagi masyarakat umum dengan sistem evaluasi terukur bagi pengguna terdaftar. Pengunjung publik dapat langsung menjelajahi materi matematika berdasarkan bidang keilmuan, mempelajari modul multimedia (teks, foto ilustrasi, dan video pembelajaran), mengerjakan **latihan contoh soal interaktif** dengan koreksi serta pembahasan instan, hingga menelusuri bank soal. Sementara itu, pengguna yang terdaftar dan masuk ke sistem dapat mengikuti **quiz evaluasi berwaktu**, mencatat riwayat capaian, bersaing di **papan peringkat teragregasi**, serta mengajukan diri menjadi **Pengajar** dengan verifikasi **Admin**.

---

## 2. Latar Belakang, Tujuan, dan Keterkaitan SDGs

### Latar Belakang
Pembelajaran matematika sering kali terkendala oleh sumber belajar yang tersebar, minimnya latihan soal dengan langkah pembahasan langsung pada tiap sub-bab, serta kurangnya sarana evaluasi mandiri yang terukur. Pelajar membutuhkan ruang belajar digital yang nyaman dibaca dalam waktu lama, mudah dicari berdasarkan topik maupun tipe soal, dan memungkinkan pengujian pemahaman secara bertahap.

### Tujuan dan Manfaat Proyek
1. **Menyediakan Akses Belajar Terbuka:** Memudahkan masyarakat dan pelajar mengakses materi matematika serta berlatih contoh soal secara gratis tanpa hambatan registrasi awal.
2. **Meningkatkan Pemahaman Bertahap:** Mengintegrasikan penjelasan konsep, ilustrasi visual/video, latihan contoh soal interaktif, dan diskusi modul dalam satu alur belajar.
3. **Menyediakan Evaluasi Terukur & Terverifikasi:** Mengukur capaian belajar pengguna terdaftar melalui quiz pilihan ganda berwaktu dengan pemeringkatan objektif, serta menjaga kualitas konten melalui mekanisme persetujuan Pengajar oleh Admin.

### Keterkaitan dengan Sustainable Development Goals (SDGs)
Proyek ini berlandaskan pada **SDGs Nomor 4: Quality Education (Pendidikan Berkualitas)**, khususnya dalam mendukung pemerataan akses sumber belajar digital yang inklusif, bermutu, dan mendorong kesempatan belajar sepanjang hayat (*lifelong learning*) bagi seluruh kalangan pelajar.

---

## 3. Profil Kelompok & Pembagian Tugas (3 Anggota)

Sesuai ketentuan pengerjaan proyek akhir, pengembangan **LearnSphere** dilaksanakan oleh tim yang beranggotakan **3 (tiga) orang** dengan pembagian peran dan tanggung jawab yang saling terintegrasi:

| No | Nama Lengkap | NIM | Peran & Tanggung Jawab Utama |
| :--- | :--- | :--- | :--- |
| 1 | **Felix Ogen Gelael** | `H1D025123` | **Database Architect & Backend Core:** Perancangan skema 9 tabel relasional (`db_learnsphere`), koneksi database, sistem autentikasi & otorisasi berbasis role (`pengajar` & `siswa`), serta modul manajemen topik materi dan sub-modul multimedia. |
| 2 | **Nurhafidz Firmansyah** | `H1D025110` | **Quality Assurance (QA), Software Tester:** Melakukan skenario pengujian, memverifikasi pembatasan hak akses publik vs akun login, menguji akurasi kalkulasi skor/grade & konsistensi peringkat tanpa duplikasi. |
| 3 | **Adha Japari Munggaran** | `H1D025143` | **UI/UX & Interactive Learning:** Perancangan antarmuka minimalis ramah mata (*Light/Dark Mode*), fitur pencarian terpadu materi & bank soal, kategorisasi bidang & tipe soal. |

---

## 4. Daftar Fitur dan Modul yang Diimplementasikan (11 Modul)

Aplikasi ini mengimplementasikan **11 modul utama** yang saling terintegrasi untuk mendukung alur pembelajaran, evaluasi, dan tata kelola pengguna:

1. **Modul Beranda Publik & Navigasi Kategori (`index.php`, `includes/header.php`):** Menampilkan statistik pembelajaran, kartu jelajah berdasarkan 4 bidang matematika (*Aljabar*, *Kalkulus*, *Geometri & Trigonometri*, *Statistika & Peluang*), materi terbaru, notifikasi status pengajar/admin, serta bilah pencarian cepat.
2. **Modul Autentikasi & Manajemen 3 Role (`login.php`, `register.php`, `logout.php`, `config/functions.php`):** Pendaftaran akun baru (sebagai *User* atau calon *Pengajar*), validasi form JavaScript/PHP, enkripsi password (`password_hash`), serta sinkronisasi hak akses *real-time* antara **User**, **Pengajar**, dan **Admin**.
3. **Modul Pengajuan Pengajar & Panel Verifikasi Admin (`ajukan_pengajar.php`, `admin_users.php`):** Alur pengajuan kenaikan peran dari `User` menjadi `Pengajar` beserta halaman khusus **Admin** untuk menyetujui, menolak, atau mengubah role seluruh pengguna terdaftar.
4. **Modul Katalog & Kategorisasi Materi (`materi.php`):** Daftar topik matematika terbuka yang dilengkapi filter kategori bidang, filter tingkat kesulitan (*Dasar*, *Menengah*, *Lanjut*), serta pencarian kata kunci.
5. **Modul Manajemen Topik Materi (`materi_form.php`, `materi_detail.php`):** Fitur pengelolaan (CRUD) topik materi beserta unggah gambar sampul opsional bagi Pengajar/Admin dan tampilan silabus modul bagi pengunjung publik.
6. **Modul Sub-Modul Pembelajaran Multimedia (`modul_form.php`, `modul_detail.php`):** Penyajian materi per bab yang mendukung kombinasi teks terstruktur, unggah foto ilustrasi, unggah video lokal (MP4/WEBM), maupun sematan (*embed*) video YouTube.
7. **Modul Latihan Contoh Soal Interaktif (`contoh_soal_form.php`, `modul_detail.php`):** Latihan soal pilihan ganda di dalam modul dengan filter tipe soal (*Konsep Dasar*, *Hitungan*, *Pemecahan Masalah*) yang dapat dikerjakan langsung oleh pengunjung publik maupun pengguna login dengan koreksi instan, tombol **Coba Lagi**, dan pembahasan otomatis.
8. **Modul Pencarian Terpadu Materi & Bank Soal (`cari.php`):** Mesin pencarian lintas tabel yang menelusuri kata kunci pada judul/deskripsi materi, isi modul, pertanyaan contoh soal, hingga butir soal quiz.
9. **Modul Forum Diskusi Modul (`modul_detail.php`):** Ruang tanya jawab pada setiap modul yang dapat digunakan oleh pengunjung publik maupun pengguna terdaftar, dilengkapi penghitung sisa karakter (*character counter*) berbasis JavaScript.
10. **Modul Manajemen Paket Quiz & Bank Soal (`quiz.php`, `quiz_form.php`, `soal_form.php`):** Pengelolaan paket quiz berdurasi beserta penyusunan butir soal pilihan ganda (Opsi A–D) terkategori oleh Pengajar terverifikasi atau Admin.
11. **Modul Pengerjaan Quiz & Papan Peringkat Teragregasi (`kerjakan_quiz.php`, `hasil_quiz.php`):** Halaman ujian khusus pengguna login dengan *countdown timer* otomatis dan pelacak progres soal terjawab, serta papan peringkat unik per peserta (tanpa duplikasi nama) yang dapat diurutkan berdasarkan **Total Skor Keseluruhan**, **Akurasi (%)**, maupun **Jumlah Quiz Selesai**.

---

## 5. Teknologi yang Digunakan

* **Frontend:** HTML5 (*Semantic Structure*), CSS3 (*Custom Properties / CSS Variables*, Responsive Grid, *Light & Dark Theme*), JavaScript Vanilla (*DOM Manipulation*, *Form Validation*, *Interactive Practice Checker*, *Countdown Timer*).
* **Backend:** PHP 8+ (Native Procedural dengan *Session Management* dan *File Upload Handling*).
* **Database:** MySQL / MariaDB (`db_learnsphere` dengan 9 tabel berelasi *Foreign Key*).
* **Web Server:** Apache (XAMPP).

---

## 6. Panduan Instalasi dan Konfigurasi

1. **Unduh / Clone Repository:**
   ```bash
   git clone https://github.com/FelixOgenGelael/LearnSphere.git
   ```
   Pindahkan folder `learnsphere` ke dalam direktori root web server Anda (misalnya `C:\xampp\htdocs\LearnSphere`).

2. **Jalankan Layanan Server:**
   Buka **XAMPP Control Panel**, lalu klik **Start** pada layanan **Apache** dan **MySQL**.

3. **Impor Database MySQL:**
   * Buka browser dan akses `http://localhost/phpmyadmin`.
   * Klik tab **Import** (atau **SQL**), pilih berkas `database.sql` dari folder utama proyek, lalu klik **Go / Kirim**.
   * Skrip akan membuat database `db_learnsphere`, seluruh struktur tabel, serta data awal materi dan soal matematika.

4. **Verifikasi Konfigurasi Koneksi:**
   Pastikan pengaturan pada `config/koneksi.php` sesuai dengan konfigurasi MySQL lokal Anda:
   ```php
   $host = "localhost";
   $user = "root";
   $pass = "";
   $db   = "db_learnsphere";
   ```

---

## 7. Panduan Menjalankan Aplikasi

1. **Akses Halaman Publik (Tanpa Login):**
   Kunjungi `http://localhost/LearnSphere/` di browser. Pengunjung dapat langsung membuka menu **Materi**, membaca modul, mengerjakan **Latihan Contoh Soal** di dalam modul, menggunakan fitur **Cari**, berdiskusi, serta melihat **Peringkat**.

2. **Mengerjakan Quiz & Mengajukan Diri Sebagai Pengajar (`User`):**
   * Klik **Daftar** untuk membuat akun baru atau **Login** menggunakan akun demo `user1`.
   * Buka menu **Quiz**, pilih paket evaluasi matematika, klik **Mulai Quiz**, dan selesaikan soal sebelum waktu habis.
   * Untuk menjadi kontributor materi, klik menu **Ajukan Pengajar** dan isi bidang keahlian matematika Anda.

3. **Persetujuan Pengajar (`Admin`) & Pengelolaan Materi (`Pengajar`):**
   * Login menggunakan akun `admin1`, buka menu **Panel Admin**, lalu klik **Setujui Pengajar** pada daftar permohonan yang masuk.
   * Setelah disetujui (atau dengan login menggunakan akun `pengajar1`), pengguna dapat mengakses fitur `+ Tambah Materi`, `+ Tambah Modul`, `+ Tambah Contoh Soal`, `+ Tambah Paket Quiz`, dan `Kelola Soal`.

### Akun Demo Pengujian
| Peran (Role) | Username | Password | Status & Hak Akses |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin1` | `123456` | Panel Admin (setujui/tolak pengajar, ubah role) & kelola konten |
| **Pengajar** | `pengajar1` | `123456` | Pengajar terverifikasi (kelola materi, modul, contoh soal & quiz) |
| **User** | `user1` | `123456` | User aktif (kerjakan quiz & dapat mengajukan diri sebagai pengajar) |
| **User (Pending)** | `user2` | `123456` | User dengan status pengajuan pengajar menunggu persetujuan Admin |

---

## 8. Tautan Publikasi & Dokumentasi Proyek

* **Video Presentasi Proyek (YouTube):** `https://youtu.be/KVP2_GniSgM`
* **Project Showcase (Anggota 1):** `https://www.instagram.com/p/DgPtueNmNNw/?knvt=NmJhbDV0OWxxOW1o`
* **Project Showcase (Anggota 2):** `https://www.instagram.com/p/DgPtueNmNNw/?jplx=MTBtcXFtYzdzd3U2ag==`
* **Project Showcase (Anggota 3):** `https://www.instagram.com/p/DgPtueNmNNw/?lqvp=OHpuMnRqN3QzcnB2`
