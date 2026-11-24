<?php
require_once 'config/koneksi.php';
require_once 'config/functions.php';
wajibPengajar();

$modul_id = isset($_GET['modul_id']) ? (int)$_GET['modul_id'] : 0;
if ($modul_id <= 0) {
    header("Location: materi.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori_soal = in_array($_POST['kategori_soal'], ['Konsep Dasar', 'Hitungan', 'Pemecahan Masalah']) ? $_POST['kategori_soal'] : 'Konsep Dasar';
    $pertanyaan    = mysqli_real_escape_string($conn, trim($_POST['pertanyaan']));
    $pilihan_a     = mysqli_real_escape_string($conn, trim($_POST['pilihan_a']));
    $pilihan_b     = mysqli_real_escape_string($conn, trim($_POST['pilihan_b']));
    $pilihan_c     = mysqli_real_escape_string($conn, trim($_POST['pilihan_c']));
    $pilihan_d     = mysqli_real_escape_string($conn, trim($_POST['pilihan_d']));
    $jawaban_benar = mysqli_real_escape_string($conn, $_POST['jawaban_benar']);
    $pembahasan    = mysqli_real_escape_string($conn, trim($_POST['pembahasan']));

    $sql = "INSERT INTO contoh_soal (modul_id, kategori_soal, pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar, pembahasan) 
            VALUES ($modul_id, '$kategori_soal', '$pertanyaan', '$pilihan_a', '$pilihan_b', '$pilihan_c', '$pilihan_d', '$jawaban_benar', '$pembahasan')";
    mysqli_query($conn, $sql);
    header("Location: modul_detail.php?id=$modul_id#latihan-soal");
    exit;
}

include 'includes/header.php';
?>

<section class="card" style="max-width: 720px; margin: 0 auto;">
    <h2>Tambah Contoh Soal Matematika</h2>
    <form method="POST" style="margin-top:14px;">
        <div class="form-group">
            <label for="kategori_soal">Kategori Tipe Soal</label>
            <select id="kategori_soal" name="kategori_soal">
                <option value="Konsep Dasar">Konsep Dasar</option>
                <option value="Hitungan">Hitungan</option>
                <option value="Pemecahan Masalah">Pemecahan Masalah (Soal Cerita / Aplikasi)</option>
            </select>
        </div>
        <div class="form-group">
            <label for="pertanyaan">Pertanyaan Matematika</label>
            <textarea id="pertanyaan" name="pertanyaan" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="pilihan_a">Pilihan A</label>
            <input type="text" id="pilihan_a" name="pilihan_a" required>
        </div>
        <div class="form-group">
            <label for="pilihan_b">Pilihan B</label>
            <input type="text" id="pilihan_b" name="pilihan_b" required>
        </div>
        <div class="form-group">
            <label for="pilihan_c">Pilihan C</label>
            <input type="text" id="pilihan_c" name="pilihan_c" required>
        </div>
        <div class="form-group">
            <label for="pilihan_d">Pilihan D</label>
            <input type="text" id="pilihan_d" name="pilihan_d" required>
        </div>
        <div class="form-group">
            <label for="jawaban_benar">Kunci Jawaban Benar</label>
            <select id="jawaban_benar" name="jawaban_benar">
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>
        </div>
        <div class="form-group">
            <label for="pembahasan">Langkah Penyelesaian / Pembahasan</label>
            <textarea id="pembahasan" name="pembahasan" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Contoh Soal</button>
        <a href="modul_detail.php?id=<?= $modul_id ?>" class="btn btn-secondary">Batal</a>
    </form>
</section>

<?php include 'includes/footer.php'; ?>