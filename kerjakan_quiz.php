<?php
require_once 'config/koneksi.php';
require_once 'config/functions.php';

// Pengunjung yang tidak login tidak dapat mengerjakan quiz
wajibLogin('wajib_login_quiz');

$quiz_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$quiz = mysqli_fetch_assoc(mysqli_query($conn, "SELECT q.*, k.nama_kategori, m.judul AS nama_materi FROM quiz q JOIN kategori k ON q.kategori_id = k.id JOIN materi m ON q.materi_id = m.id WHERE q.id = $quiz_id"));

if (!$quiz) {
    header("Location: quiz.php");
    exit;
}

$soal_res = mysqli_query($conn, "SELECT * FROM soal_quiz WHERE quiz_id = $quiz_id ORDER BY id ASC");
$daftar_soal = [];
while ($row = mysqli_fetch_assoc($soal_res)) {
    $daftar_soal[] = $row;
}

if (count($daftar_soal) === 0) {
    header("Location: quiz.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jawaban_user = $_POST['jawaban'] ?? [];
    $jumlah_benar = 0;
    $total_soal   = count($daftar_soal);

    foreach ($daftar_soal as $soal) {
        $id_soal = $soal['id'];
        if (isset($jawaban_user[$id_soal]) && $jawaban_user[$id_soal] === $soal['kunci_jawaban']) {
            $jumlah_benar++;
        }
    }

    $skor         = ($jumlah_benar / $total_soal) * 100;
    $grade        = hitungGrade($skor);
    $user_id      = (int)$_SESSION['user_id'];
    $nama_peserta = mysqli_real_escape_string($conn, $_SESSION['nama_lengkap']);

    $sql = "INSERT INTO hasil_quiz (quiz_id, user_id, nama_peserta, jumlah_benar, total_soal, skor, grade) 
            VALUES ($quiz_id, $user_id, '$nama_peserta', $jumlah_benar, $total_soal, $skor, '$grade')";
    mysqli_query($conn, $sql);
    $hasil_id = mysqli_insert_id($conn);

    header("Location: hasil_quiz.php?terbaru=$hasil_id");
    exit;
}

$total_pertanyaan = count($daftar_soal);
include 'includes/header.php';
?>

<div class="reading-container">
    <div id="quiz-timer-bar">
        <div>
            <strong><?= htmlspecialchars($quiz['judul_quiz']) ?></strong>
            <span style="display:block; font-size:13px; color:var(--text-muted);"><?= htmlspecialchars($quiz['nama_kategori']) ?> &bull; Peserta: <?= htmlspecialchars($_SESSION['nama_lengkap']) ?></span>
        </div>
        <div style="display:flex; align-items:center; gap:10px;">
            <span id="progres-terjawab" class="badge">0 / <?= $total_pertanyaan ?> Terjawab</span>
            <span class="timer-badge" id="waktu-tersisa"><?= str_pad($quiz['durasi_menit'], 2, '0', STR_PAD_LEFT) ?>:00</span>
        </div>
    </div>

    <form id="form-kerjakan-quiz" method="POST" action="">
        <?php foreach ($daftar_soal as $index => $soal): ?>
            <div class="card">
                <p style="margin-bottom:14px;">
                    <span class="badge badge-accent" style="margin-right:4px;">Soal <?= $index + 1 ?> / <?= $total_pertanyaan ?></span>
                    <span class="badge" style="margin-right:6px;"><?= htmlspecialchars($soal['kategori_soal']) ?></span>
                    <div style="margin-top:8px; font-weight:600;"><?= nl2br(htmlspecialchars($soal['pertanyaan'])) ?></div>
                </p>

                <?php
                $opsi_array = [
                    'A' => $soal['opsi_a'],
                    'B' => $soal['opsi_b'],
                    'C' => $soal['opsi_c'],
                    'D' => $soal['opsi_d']
                ];
                ?>
                <div class="practice-options" style="margin-bottom:0;">
                    <?php foreach ($opsi_array as $huruf => $teks_opsi): ?>
                        <label class="practice-option">
                            <input type="radio" name="jawaban[<?= $soal['id'] ?>]" value="<?= $huruf ?>" onchange="updateProgresQuiz(<?= $total_pertanyaan ?>)" required>
                            <span><b><?= $huruf ?>.</b> <?= htmlspecialchars($teks_opsi) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px;">
            <a href="quiz.php" class="btn btn-secondary" onclick="return confirm('Batalkan pengerjaan quiz?')">Batal</a>
            <button type="submit" class="btn btn-primary" style="padding:12px 24px; font-size:15px;" onclick="return confirm('Kumpulkan semua jawaban quiz sekarang?')">
                Kumpulkan Jawaban & Lihat Skor
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        mulaiTimerQuiz(<?= (int)$quiz['durasi_menit'] ?>);
    });
</script>

<?php include 'includes/footer.php'; ?>