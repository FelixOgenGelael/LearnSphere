<?php
require_once 'config/koneksi.php';
require_once 'config/functions.php';

$terbaru = null;
if (isset($_GET['terbaru'])) {
    $id_terbaru = (int)$_GET['terbaru'];
    $terbaru = mysqli_fetch_assoc(mysqli_query($conn, "SELECT h.*, q.judul_quiz FROM hasil_quiz h JOIN quiz q ON h.quiz_id = q.id WHERE h.id = $id_terbaru"));
}

$riwayat = mysqli_query($conn, "SELECT h.*, q.judul_quiz, k.nama_kategori 
                                FROM hasil_quiz h 
                                JOIN quiz q ON h.quiz_id = q.id 
                                JOIN kategori k ON q.kategori_id = k.id
                                ORDER BY h.skor DESC, h.waktu_selesai DESC");

include 'includes/header.php';
?>

<?php if ($terbaru): ?>
    <section class="card card-accent" style="text-align:center; max-width:640px; margin:0 auto 24px;">
        <span class="badge badge-accent">Hasil Evaluasi: <?= htmlspecialchars($terbaru['nama_peserta']) ?></span>
        <h2 style="margin-top:8px;"><?= htmlspecialchars($terbaru['judul_quiz']) ?></h2>
        <div style="font-size:3.2rem; font-weight:800; color:var(--accent); margin:10px 0;">
            <?= number_format($terbaru['skor'], 0) ?> <span style="font-size:1.3rem;">(Grade <?= $terbaru['grade'] ?>)</span>
        </div>
        <p style="color:var(--text-secondary);">Jawaban benar: <b><?= $terbaru['jumlah_benar'] ?></b> dari <b><?= $terbaru['total_soal'] ?></b> soal.</p>
        <div style="margin-top:16px;">
            <a href="quiz.php" class="btn btn-primary">Kerjakan Quiz Lainnya</a>
        </div>
    </section>
<?php endif; ?>

<section class="card">
    <h2>Papan Peringkat & Riwayat Evaluasi Matematika</h2>
    <p style="color:var(--text-muted); font-size:14px;">Menampilkan capaian skor seluruh peserta (akun terdaftar maupun pengunjung publik).</p>

    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th rowspan="2">Peringkat</th>
                    <th rowspan="2">Nama Peserta</th>
                    <th rowspan="2">Paket Quiz & Kategori</th>
                    <th colspan="3" style="text-align:center;">Hasil Evaluasi</th>
                    <th rowspan="2">Waktu Selesai</th>
                </tr>
                <tr>
                    <th>Benar / Total</th>
                    <th>Skor</th>
                    <th>Predikat</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($riwayat) === 0): ?>
                    <tr>
                        <td colspan="7" style="text-align:center; color:var(--text-muted);">Belum ada riwayat pengerjaan quiz.</td>
                    </tr>
                <?php else: ?>
                    <?php $rank = 1; while ($r = mysqli_fetch_assoc($riwayat)): ?>
                        <tr>
                            <td><b>#<?= $rank++ ?></b></td>
                            <td><?= htmlspecialchars($r['nama_peserta']) ?></td>
                            <td>
                                <b><?= htmlspecialchars($r['judul_quiz']) ?></b><br>
                                <span class="badge"><?= htmlspecialchars($r['nama_kategori']) ?></span>
                            </td>
                            <td><?= $r['jumlah_benar'] ?> / <?= $r['total_soal'] ?></td>
                            <td><b><?= number_format($r['skor'], 1) ?></b></td>
                            <td><span class="badge badge-accent"><?= $r['grade'] ?></span></td>
                            <td><small style="color:var(--text-muted);"><?= $r['waktu_selesai'] ?></small></td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include 'includes/footer.php'; ?>