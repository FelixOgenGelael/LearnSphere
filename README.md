# LearnSphere — Interactive Learning & Quiz Platform

## 1. Deskripsi Proyek
**LearnSphere** adalah aplikasi web pembelajaran terintegrasi yang menyediakan distribusi materi ajar multimedia (teks, foto, video), bank contoh soal beserta pembahasan mandiri, serta sistem evaluasi quiz pilihan ganda dengan penilaian otomatis.

## 2. Latar Belakang dan Tujuan (Keterkaitan SDGs)
Proyek ini diangkat berdasarkan **Sustainable Development Goals (SDGs) Nomor 4: Quality Education (Pendidikan Berkualitas)**. Akses terhadap bahan ajar terstruktur dan sarana latihan evaluasi mandiri masih belum merata. LearnSphere bertujuan menyediakan wadah digital terbuka bagi pengajar untuk mempublikasikan modul ajar dan bagi peserta didik untuk belajar serta mengukur pemahamannya secara mandiri.

## 3. Daftar Fitur & Modul yang Diimplementasikan (10 Modul)
1. **Modul Autentikasi & Sesi (`login.php`, `register.php`, `logout.php`)**: Registrasi multi-peran (Pengajar/Siswa) dan manajemen sesi berbasis PHP Session.
2. **Modul Dashboard & Statistik (`index.php`)**: Ringkasan progres belajar, jumlah materi, modul, quiz, dan rata-rata nilai.
3. **Modul Manajemen Topik Materi (`materi.php`, `materi_form.php`)**: CRUD mata pelajaran/topik beserta upload thumbnail.
4. **Modul Sub-Modul Materi Multimedia (`modul_form.php`, `modul_detail.php`)**: Pengelolaan bab materi dengan dukungan upload foto (opsional) dan upload video MP4 atau tautan YouTube (opsional).
5. **Modul Contoh Soal & Pembahasan Interaktif (`contoh_soal_form.php`)**: Penyisipan contoh soal pada modul materi dengan fitur toggle kunci & pembahasan.
6. **Modul Manajemen Paket Quiz (`quiz.php`, `quiz_form.php`)**: Pembuatan paket quiz dengan pengaturan durasi waktu.
7. **Modul Bank Soal Pilihan Ganda (`soal_form.php`)**: Pengelolaan butir soal pilihan ganda (Opsi A–D) dan kunci jawaban.
8. **Modul Pengerjaan Quiz & Penilaian Otomatis (`kerjakan_quiz.php`)**: Antarmuka pengerjaan soal pilihan ganda dilengkapi countdown timer JavaScript dan skoring otomatis PHP.
9. **Modul Riwayat Nilai & Papan Peringkat (`hasil_quiz.php`)**: Rekapitulasi skor, konversi predikat nilai (A–E), dan leaderboard peserta.
10. **Modul Diskusi Modul (`modul_detail.php`)**: Kolom diskusi interaktif pada setiap modul dengan penghitung batas karakter (Character Counter).

## 4. Teknologi yang Digunakan
- **Frontend**: HTML5 (Semantic Elements), CSS3 (External Stylesheet), JavaScript (DOM Manipulation, Form Validation, Timer)
- **Backend**: PHP 8+ (Native + Session Management)
- **Database**: MySQL / MariaDB
- **Web Server**: Apache (XAMPP)

## 5. Panduan Instalasi dan Konfigurasi
1. Unduh atau clone repository ini ke dalam folder `htdocs` pada direktori instalasi XAMPP Anda (`C:\xampp\htdocs\learnsphere`).
2. Jalankan layanan **Apache** dan **MySQL** melalui XAMPP Control Panel.
3. Buka `http://localhost/phpmyadmin`, buat database baru bernama `db_learnsphere`, lalu impor berkas `database.sql` yang tersedia di root proyek.
4. Pastikan konfigurasi koneksi pada `config/koneksi.php` sesuai dengan pengaturan MySQL lokal Anda (`host: localhost`, `user: root`, `password: ""`).

## 6. Panduan Menjalankan Aplikasi
1. Buka browser dan akses alamat `http://localhost/learnsphere/`.
2. Login menggunakan akun demo yang telah tersedia di database:
   - **Akun Pengajar**: Username `pengajar1` | Password `123456`
   - **Akun Siswa**: Username `siswa1` | Password `123456`
3. Atau klik **Buat akun baru** pada halaman Login untuk mendaftarkan pengguna baru.