<?php
require_once 'config/koneksi.php';
require_once 'config/functions.php';
wajibPengajar();

$kategori_list = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id ASC");
$materi_list   = mysqli_query($conn, "SELECT id, judul FROM materi ORDER BY judul ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori_id  = (int)$_POST['kategori_id'];
    $materi_id    = (int)$_POST['materi_id'];
    $judul_quiz   = mysqli_real_escape_string($conn, trim($_POST['judul_quiz']));
    $deskripsi    = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    $durasi_menit = (int)$_POST['durasi_menit'];
    $user_id      = $_SESSION['user_id'];

    $sql = "INSERT INTO quiz (kategori_id, materi_id, user_id, judul_quiz, deskripsi, durasi_menit) 
            VALUES ($kategori_id, $materi_id, $user_id, '$judul_quiz', '$deskripsi', $durasi_menit)";
    mysqli_query($conn, $sql);
    $new_quiz_id = mysqli_insert_id($conn);

    header("Location: soal_form.php?quiz_id=$new_quiz_id");
    exit;
}

include 'includes/header.php';
?>

<section class="card" style="max-width: 680px; margin: 0 auto;">
    <h2>Buat Paket Quiz Matematika Baru</h2>
    <form method="POST" style="margin-top:14px;">
        <div class="form-group">
            <label for="kategori_id">Kategori Bidang Matematika</label>
            <select id="kategori_id" name="kategori_id" required>
                <?php while ($kat = mysqli_fetch_assoc($kategori_list)): ?>
                    <option value="<?= $kat['id'] ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="materi_id">Topik Materi Terkait</label>
            <select id="materi_id" name="materi_id" required>
                <?php while ($m = mysqli_fetch_assoc($materi_list)): ?>
                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['judul']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="judul_quiz">Judul Paket Quiz</label>
            <input type="text" id="judul_quiz" name="judul_quiz" placeholder="Contoh: Quiz Trigonometri Sudut Istimewa" required>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi / Petunjuk</label>
            <textarea id="deskripsi" name="deskripsi" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="durasi_menit">Durasi Waktu (Menit)</label>
            <input type="number" id="durasi_menit" name="durasi_menit" value="10" min="1" max="120" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan & Lanjut Tambah Soal</button>
        <a href="quiz.php" class="btn btn-secondary">Batal</a>
    </form>
</section>

<?php include 'includes/footer.php'; ?>