<?php
require_once 'config/koneksi.php';
require_once 'config/functions.php';
wajibPengajar();

$quiz_id = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
$quiz = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM quiz WHERE id = $quiz_id"));

if (!$quiz) {
    header("Location: quiz.php");
    exit;
}

if (isset($_GET['hapus_soal'])) {
    $id_soal = (int)$_GET['hapus_soal'];
    mysqli_query($conn, "DELETE FROM soal_quiz WHERE id = $id_soal AND quiz_id = $quiz_id");
    header("Location: soal_form.php?quiz_id=$quiz_id");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori_soal = in_array($_POST['kategori_soal'], ['Konsep Dasar', 'Hitungan', 'Pemecahan Masalah']) ? $_POST['kategori_soal'] : 'Hitungan';
    $pertanyaan    = mysqli_real_escape_string($conn, trim($_POST['pertanyaan']));
    $opsi_a        = mysqli_real_escape_string($conn, trim($_POST['opsi_a']));
    $opsi_b        = mysqli_real_escape_string($conn, trim($_POST['opsi_b']));
    $opsi_c        = mysqli_real_escape_string($conn, trim($_POST['opsi_c']));
    $opsi_d        = mysqli_real_escape_string($conn, trim($_POST['opsi_d']));
    $kunci_jawaban = mysqli_real_escape_string($conn, $_POST['kunci_jawaban']);

    $sql = "INSERT INTO soal_quiz (quiz_id, kategori_soal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, kunci_jawaban) 
            VALUES ($quiz_id, '$kategori_soal', '$pertanyaan', '$opsi_a', '$opsi_b', '$opsi_c', '$opsi_d', '$kunci_jawaban')";
    mysqli_query($conn, $sql);
    header("Location: soal_form.php?quiz_id=$quiz_id&pesan=soal_ditambah");
    exit;
}

$soal_list = mysqli_query($conn, "SELECT * FROM soal_quiz WHERE quiz_id = $quiz_id ORDER BY id ASC");

include 'includes/header.php';
?>

<section class="card card-accent">
    <a href="quiz.php" style="font-size:14px;">&larr; Kembali ke Daftar Quiz</a>
    <h2 style="margin-top:8px;">Kelola Soal Pilihan Ganda: <?= htmlspecialchars($quiz['judul_quiz']) ?></h2>
</section>

<section class="card">
    <h3>Tambah Butir Soal Matematika</h3>
    <form method="POST" style="margin-top:12px;">
        <div class="form-group">
            <label for="kategori_soal">Kategori Tipe Soal</label>
            <select id="kategori_soal" name="kategori_soal">
                <option value="Konsep Dasar">Konsep Dasar</option>
                <option value="Hitungan">Hitungan</option>
                <option value="Pemecahan Masalah">Pemecahan Masalah</option>
            </select>
        </div>
        <div class="form-group">
            <label for="pertanyaan">Teks Pertanyaan</label>
            <textarea id="pertanyaan" name="pertanyaan" rows="3" required></textarea>
        </div>
        <div class="grid-3" style="grid-template-columns: 1fr 1fr;">
            <div class="form-group">
                <label for="opsi_a">Opsi A</label>
                <input type="text" id="opsi_a" name="opsi_a" required>
            </div>
            <div class="form-group">
                <label for="opsi_b">Opsi B</label>
                <input type="text" id="opsi_b" name="opsi_b" required>
            </div>
            <div class="form-group">
                <label for="opsi_c">Opsi C</label>
                <input type="text" id="opsi_c" name="opsi_c" required>
            </div>
            <div class="form-group">
                <label for="opsi_d">Opsi D</label>
                <input type="text" id="opsi_d" name="opsi_d" required>
            </div>
        </div>
        <div class="form-group">
            <label for="kunci_jawaban">Kunci Jawaban Benar</label>
            <select id="kunci_jawaban" name="kunci_jawaban">
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">+ Simpan Soal Pilihan Ganda</button>
    </form>
</section>

<section class="card">
    <h3>Daftar Soal Terdaftar (<?= mysqli_num_rows($soal_list) ?> Soal)</h3>
    <?php $no = 1; while ($s = mysqli_fetch_assoc($soal_list)): ?>
        <div style="border-top:1px solid var(--border-color); padding:14px 0;">
            <span class="badge"><?= htmlspecialchars($s['kategori_soal']) ?></span>
            <p style="margin-top:6px;"><b><?= $no++ ?>. <?= htmlspecialchars($s['pertanyaan']) ?></b></p>
            <ul style="margin-left:20px; margin-top:6px;">
                <li>A. <?= htmlspecialchars($s['opsi_a']) ?></li>
                <li>B. <?= htmlspecialchars($s['opsi_b']) ?></li>
                <li>C. <?= htmlspecialchars($s['opsi_c']) ?></li>
                <li>D. <?= htmlspecialchars($s['opsi_d']) ?></li>
            </ul>
            <p style="margin-top:8px;">
                <span class="badge badge-accent">Kunci: <?= $s['kunci_jawaban'] ?></span>
                <a href="soal_form.php?quiz_id=<?= $quiz_id ?>&hapus_soal=<?= $s['id'] ?>" style="color:#dc2626; margin-left:10px; font-size:13px;" onclick="return confirm('Hapus soal ini?')">[Hapus Soal]</a>
            </p>
        </div>
    <?php endwhile; ?>
</section>

<?php include 'includes/footer.php'; ?>