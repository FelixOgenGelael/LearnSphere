DROP DATABASE IF EXISTS db_learnsphere;
CREATE DATABASE db_learnsphere CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE db_learnsphere;

-- 1. Tabel Users (Untuk Pengajar & Siswa Terdaftar)
CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('pengajar', 'siswa') NOT NULL DEFAULT 'siswa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabel Kategori Matematika (Kategorisasi Materi & Quiz)
CREATE TABLE kategori (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(80) NOT NULL UNIQUE,
    deskripsi VARCHAR(255) NOT NULL
);

-- 3. Tabel Materi (Topik Utama Matematika)
CREATE TABLE materi (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    judul VARCHAR(150) NOT NULL,
    tingkat ENUM('Dasar', 'Menengah', 'Lanjut') NOT NULL DEFAULT 'Dasar',
    deskripsi TEXT NOT NULL,
    thumbnail VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 4. Tabel Modul Materi (Sub-Bab + Foto & Video Opsional)
CREATE TABLE modul_materi (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    materi_id INT(11) NOT NULL,
    urutan INT(11) NOT NULL DEFAULT 1,
    judul_modul VARCHAR(150) NOT NULL,
    konten TEXT NOT NULL,
    gambar VARCHAR(255) DEFAULT NULL,
    video_file VARCHAR(255) DEFAULT NULL,
    video_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (materi_id) REFERENCES materi(id) ON DELETE CASCADE
);

-- 5. Tabel Contoh Soal pada Modul Materi (Terkategori & Interaktif)
CREATE TABLE contoh_soal (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    modul_id INT(11) NOT NULL,
    kategori_soal ENUM('Konsep Dasar', 'Hitungan', 'Pemecahan Masalah') NOT NULL DEFAULT 'Konsep Dasar',
    pertanyaan TEXT NOT NULL,
    pilihan_a VARCHAR(255) NOT NULL,
    pilihan_b VARCHAR(255) NOT NULL,
    pilihan_c VARCHAR(255) NOT NULL,
    pilihan_d VARCHAR(255) NOT NULL,
    jawaban_benar ENUM('A', 'B', 'C', 'D') NOT NULL,
    pembahasan TEXT NOT NULL,
    FOREIGN KEY (modul_id) REFERENCES modul_materi(id) ON DELETE CASCADE
);

-- 6. Tabel Paket Quiz Matematika
CREATE TABLE quiz (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT(11) NOT NULL,
    materi_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    judul_quiz VARCHAR(150) NOT NULL,
    deskripsi TEXT NOT NULL,
    durasi_menit INT(11) NOT NULL DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE RESTRICT,
    FOREIGN KEY (materi_id) REFERENCES materi(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 7. Tabel Soal Quiz Pilihan Ganda (Terkategori)
CREATE TABLE soal_quiz (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT(11) NOT NULL,
    kategori_soal ENUM('Konsep Dasar', 'Hitungan', 'Pemecahan Masalah') NOT NULL DEFAULT 'Hitungan',
    pertanyaan TEXT NOT NULL,
    opsi_a VARCHAR(255) NOT NULL,
    opsi_b VARCHAR(255) NOT NULL,
    opsi_c VARCHAR(255) NOT NULL,
    opsi_d VARCHAR(255) NOT NULL,
    kunci_jawaban ENUM('A', 'B', 'C', 'D') NOT NULL,
    FOREIGN KEY (quiz_id) REFERENCES quiz(id) ON DELETE CASCADE
);

-- 8. Tabel Hasil Quiz (Mendukung User Terdaftar & Pengunjung Publik)
CREATE TABLE hasil_quiz (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT(11) NOT NULL,
    user_id INT(11) DEFAULT NULL,
    nama_peserta VARCHAR(100) NOT NULL,
    jumlah_benar INT(11) NOT NULL,
    total_soal INT(11) NOT NULL,
    skor DECIMAL(5,2) NOT NULL,
    grade CHAR(1) NOT NULL,
    waktu_selesai TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quiz(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 9. Tabel Diskusi Modul (Mendukung Publik & Member)
CREATE TABLE diskusi (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    modul_id INT(11) NOT NULL,
    user_id INT(11) DEFAULT NULL,
    nama_pengirim VARCHAR(100) NOT NULL,
    komentar TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (modul_id) REFERENCES modul_materi(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ==========================================================
-- DATA AWAL (SEED DATA) BERTEMA MATEMATIKA
-- Password akun demo: 123456
-- ==========================================================
INSERT INTO users (nama_lengkap, username, email, password, role) VALUES
('Dr. Hendra Wijaya, M.Si.', 'pengajar1', 'hendra@learnsphere.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengajar'),
('Nadia Putri', 'siswa1', 'nadia@learnsphere.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'siswa');

INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Aljabar', 'Persamaan linear, pertidaksamaan, fungsi kuadrat, matriks, dan sistem persamaan.'),
('Kalkulus', 'Limit fungsi, turunan (diferensial), serta konsep dasar integral dan aplikasinya.'),
('Geometri & Trigonometri', 'Bangun datar, bangun ruang, perbandingan trigonometri, dan identitas sudut.'),
('Statistika & Peluang', 'Ukuran pemusatan data, penyebaran data, kaidah pencacahan, dan probabilitas.');

INSERT INTO materi (kategori_id, user_id, judul, tingkat, deskripsi) VALUES
(1, 1, 'Sistem Persamaan Linear & Fungsi Kuadrat', 'Dasar', 'Mempelajari penyelesaian sistem persamaan linear dua variabel (SPLDV) menggunakan metode substitusi dan eliminasi, serta analisis grafik fungsi kuadrat.'),
(2, 1, 'Limit Fungsi Aljabar & Turunan Pertama', 'Menengah', 'Memahami konsep pendekatan nilai limit fungsi aljabar, sifat-sifat limit, hingga aturan dasar turunan fungsi polinomial untuk menentukan gradien garis singgung.'),
(3, 1, 'Perbandingan Trigonometri pada Segitiga Siku-Siku', 'Dasar', 'Mengenal rasio sinus, kosinus, dan tangen beserta nilai sudut istimewa untuk menyelesaikan masalah pengukuran jarak dan ketinggian.'),
(4, 1, 'Ukuran Pemusatan Data & Peluang Kejadian', 'Dasar', 'Menghitung rata-rata (mean), median, modus data tunggal maupun berkelompok, serta peluang empirik dan teoretik.');

INSERT INTO modul_materi (materi_id, urutan, judul_modul, konten, video_url) VALUES
(1, 1, 'Metode Eliminasi dan Substitusi pada SPLDV', 'Sistem Persamaan Linear Dua Variabel (SPLDV) memiliki bentuk umum ax + by = c dan dx + ey = f. Untuk menemukan himpunan penyelesaian (x, y), kita dapat menggunakan:\n\n1. Metode Eliminasi: Menyamakan koefisien salah satu variabel lalu menjumlahkan atau mengurangkan kedua persamaan.\n2. Metode Substitusi: Menyatakan satu variabel dalam variabel lainnya, lalu menggantikannya ke persamaan kedua.\n3. Metode Gabungan: Mengeliminasi satu variabel terlebih dahulu untuk memperoleh nilai variabel pertama, lalu mensubstitusikannya untuk mendapatkan variabel kedua.', 'https://www.youtube.com/embed/nok99JOhcjo'),
(1, 2, 'Diskriminan dan Titik Puncak Fungsi Kuadrat', 'Fungsi kuadrat berbentuk f(x) = ax^2 + bx + c dengan a tidak sama dengan 0. Nilai diskriminan dirumuskan sebagai D = b^2 - 4ac:\n- Jika D > 0, grafik memotong sumbu-X di dua titik berbeda.\n- Jika D = 0, grafik menyinggung sumbu-X di satu titik.\n- Jika D < 0, grafik tidak memotong sumbu-X.\n\nKoordinat titik puncak parabola adalah (-b / 2a, -D / 4a).', NULL),
(2, 1, 'Aturan Dasar Turunan Fungsi Aljabar', 'Turunan pertama dari fungsi f(x) dinotasikan sebagai f''(x) atau dy/dx. Aturan pangkat menyatakan bahwa jika f(x) = a * x^n, maka turunannya adalah f''(x) = a * n * x^(n-1). Turunan dari konstanta selalu bernilai 0.', NULL),
(3, 1, 'Sinus, Kosinus, Tangen dan Sudut Istimewa', 'Pada segitiga siku-siku dengan sudut lancip theta:\n- sin(theta) = Sisi Depan / Sisi Miring\n- cos(theta) = Sisi Samping / Sisi Miring\n- tan(theta) = Sisi Depan / Sisi Samping\n\nSudut istimewa meliputi 0°, 30°, 45°, 60°, dan 90° yang nilainya sering digunakan dalam perhitungan geometri.', NULL);

INSERT INTO contoh_soal (modul_id, kategori_soal, pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar, pembahasan) VALUES
(1, 'Hitungan', 'Diketahui sistem persamaan linear: 2x + y = 11 dan x - y = 1. Nilai dari x + 2y adalah...', '8', '10', '12', '14', 'B', 'Jumlahkan kedua persamaan untuk mengeliminasi y: (2x + y) + (x - y) = 11 + 1 => 3x = 12 => x = 4. Substitusi x = 4 ke x - y = 1 menghasilkan 4 - y = 1 => y = 3. Maka x + 2y = 4 + 2(3) = 10.'),
(1, 'Pemecahan Masalah', 'Harga 3 buku tulis dan 2 pensil adalah Rp17.000, sedangkan harga 2 buku tulis dan 2 pensil adalah Rp14.000. Berapakah harga 1 buku tulis?', 'Rp2.500', 'Rp3.000', 'Rp3.500', 'Rp4.000', 'B', 'Misalkan buku = b dan pensil = p. Persamaan (1): 3b + 2p = 17.000; Persamaan (2): 2b + 2p = 14.000. Kurangkan (1) dengan (2) sehingga variabel p tereliminasi: b = 3.000. Jadi harga 1 buku tulis adalah Rp3.000.'),
(2, 'Konsep Dasar', 'Jika grafik fungsi kuadrat f(x) = x^2 - 6x + c menyinggung sumbu-X di satu titik, maka nilai c adalah...', '6', '8', '9', '12', 'C', 'Syarat grafik menyinggung sumbu-X adalah D = 0. Diketahui a = 1, b = -6. Maka b^2 - 4ac = 0 => (-6)^2 - 4(1)(c) = 0 => 36 - 4c = 0 => c = 9.'),
(3, 'Hitungan', 'Turunan pertama dari fungsi f(x) = 4x^3 - 5x^2 + 7x - 9 adalah...', '12x^2 - 10x + 7', '12x^2 - 5x + 7', '4x^2 - 10x + 7', '12x^3 - 10x^2 + 7', 'A', 'Gunakan aturan pangkat f''(x) = a*n*x^(n-1) pada tiap suku: turunan 4x^3 adalah 12x^2, turunan -5x^2 adalah -10x, turunan 7x adalah 7, dan turunan konstanta -9 adalah 0.'),
(4, 'Hitungan', 'Nilai dari sin 30° + cos 60° adalah...', '0', '0,5', '1', '1,5', 'C', 'Berdasarkan tabel sudut istimewa trigonometri, sin 30° = 1/2 dan cos 60° = 1/2. Jadi 1/2 + 1/2 = 1.');

INSERT INTO quiz (kategori_id, materi_id, user_id, judul_quiz, deskripsi, durasi_menit) VALUES
(1, 1, 1, 'Quiz Aljabar: SPLDV & Fungsi Kuadrat', 'Evaluasi pemahaman konsep dan hitungan mengenai sistem persamaan linear dua variabel serta sifat grafik fungsi kuadrat.', 10),
(2, 2, 1, 'Quiz Kalkulus: Turunan Fungsi Polinomial', 'Uji kecepatan dan ketepatan Anda dalam menentukan turunan pertama fungsi aljabar.', 8);

INSERT INTO soal_quiz (quiz_id, kategori_soal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, kunci_jawaban) VALUES
(1, 'Hitungan', 'Himpunan penyelesaian dari 3x + 2y = 12 dan x + 2y = 8 adalah...', '(2, 3)', '(3, 2)', '(4, 0)', '(1, 4)', 'A'),
(1, 'Konsep Dasar', 'Sumbu simetri dari grafik fungsi kuadrat f(x) = 2x^2 - 8x + 5 adalah...', 'x = -2', 'x = 2', 'x = 4', 'x = -4', 'B'),
(1, 'Pemecahan Masalah', 'Keliling sebuah persegi panjang adalah 34 cm. Jika selisih panjang dan lebarnya adalah 5 cm, luas persegi panjang tersebut adalah...', '60 cm²', '66 cm²', '70 cm²', '72 cm²', 'B'),
(2, 'Hitungan', 'Jika f(x) = 2x^4 - 3x^2 + 6, maka nilai dari f''(1) adalah...', '2', '4', '6', '8', 'A'),
(2, 'Konsep Dasar', 'Turunan pertama dari fungsi konstan f(x) = 2026 adalah...', '2026', '1', '0', '2026x', 'C');

INSERT INTO hasil_quiz (quiz_id, user_id, nama_peserta, jumlah_benar, total_soal, skor, grade) VALUES
(1, 2, 'Nadia Putri', 3, 3, 100.00, 'A'),
(1, NULL, 'Raka Pratama (Tamu)', 2, 3, 66.67, 'B');