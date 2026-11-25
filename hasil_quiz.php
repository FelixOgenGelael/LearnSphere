<?php
require_once 'config/koneksi.php';
require_once 'config/functions.php';

// Mode pengurutan peringkat: 'total_skor' (default), 'akurasi', atau 'quiz_selesai'
$opsi_urut = ['total_skor', 'akurasi', 'quiz_selesai'];
$urut = (isset($_GET['urut']) && in_array($_GET['urut'], $opsi_urut)) ? $_GET['urut'] : 'total_skor';

// Kartu hasil terbaru jika pengguna baru saja menyelesaikan quiz
$terbaru = null;
if (isset($_GET['terbaru']) && isset($_SESSION['user_id'])) {
    $id_terbaru = (int)$_GET['terbaru'];
    $uid        = (int)$_SESSION['user_id'];
    $terbaru    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT h.*, q.judul_quiz FROM hasil_quiz h JOIN quiz q ON h.quiz_id = q.id WHERE h.id = $id_terbaru AND h.user_id = $uid"));
}

// Query Peringkat Unik (GROUP BY u.id sehingga tidak ada orang muncul 2 kali)
if ($urut === 'akurasi') {
    $order_sql = "ORDER BY akurasi DESC, total_skor DESC, total_quiz DESC";
} elseif ($urut === 'quiz_selesai') {
    $order_sql = "ORDER BY total_quiz DESC, total_skor DESC, akurasi DESC";
} else {
    $order_sql = "ORDER BY total_skor DESC, akurasi DESC, total_quiz DESC";
}

$sql_peringkat = "SELECT 
                    u.id AS user_id,
                    u.nama_lengkap,
                    COUNT(h.id) AS total_quiz,
                    SUM(h.skor) AS total_skor,
                    ROUND((SUM(h.jumlah_benar) / NULLIF(SUM(h.total_soal), 0)) * 100, 1) AS akurasi
                  FROM hasil_quiz h
                  JOIN users u ON h.user_id = u.id
                  GROUP BY u.id, u.nama_lengkap
                  $order_sql";
$peringkat_res = mysqli_query($conn, $sql_peringkat);

// Riwayat pribadi pengguna yang sedang login
$riwayat_pribadi = null;
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $riwayat_pribadi = mysqli_query($conn, "SELECT h.*, q.judul_quiz, k.nama_kategori 
                                             FROM hasil_quiz h 
                                             JOIN quiz q ON h.quiz_id = q.id 
                                             JOIN kategori k ON q.kategori_id = k.id
                                             WHERE h.user_id = $uid 
                                             ORDER BY h.waktu_selesai DESC");
}

include 'includes/header.php';
?>

<?php if ($terbaru): ?>
    <section class="card card-accent" style="text-align:center; max-width:640px; margin:0 auto 24px;">
        <span class="badge badge-accent">Evaluasi Quiz Selesai</span>
        <h2 style="margin-top:8px;"><?= htmlspecialchars($terbaru['judul_quiz']) ?></h2>
        <div style="font-size:3.2rem; font-weight:800; color:var(--accent); margin:10px 0;">
            <?= number_format($terbaru['skor'], 0) ?> <span style="font-size:1.3rem;">(Grade <?= $terbaru['grade'] ?>)</span>
        </div>
        <p style="color:var(--text-secondary);">Anda menjawab benar <b><?= $terbaru['jumlah_benar'] ?></b> dari <b><?= $terbaru['total_soal'] ?></b> soal.</p>
        <div style="margin-top:16px;">
            <a href="quiz.php" class="btn btn-primary">Kerjakan Quiz Lainnya</a>
        </div>
    </section>
<?php endif; ?>

<section class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:14px;">
        <div>
            <h2>Papan Peringkat Peserta</h2>
            <p style="color:var(--text-muted); font-size:14px;">
                Setiap peserta ditampilkan satu kali berdasarkan akumulasi seluruh quiz yang telah dikerjakan.
            </p>
        </div>

        <!-- Pilihan Dasar Pemeringkatan -->
        <div class="filter-pills" style="margin-bottom:0;">
            <a href="hasil_quiz.php?urut=total_skor" class="pill-link <?= $urut === 'total_skor' ? 'active' : '' ?>">
                Total Skor Keseluruhan
            </a>
            <a href="hasil_quiz.php?urut=akurasi" class="pill-link <?= $urut === 'akurasi' ? 'active' : '' ?>">
                Akurasi (%)
            </a>
            <a href="hasil_quiz.php?urut=quiz_selesai" class="pill-link <?= $urut === 'quiz_selesai' ? 'active' : '' ?>">
                Jumlah Quiz Selesai
            </a>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th rowspan="2">Peringkat</th>
                    <th rowspan="2">Nama Peserta</th>
                    <th colspan="3" style="text-align:center;">Akumulasi Capaian Evaluasi</th>
                </tr>
                <tr>
                    <th><?= $urut === 'quiz_selesai' ? 'Quiz Selesai &darr;' : 'Quiz Selesai' ?></th>
                    <th><?= $urut === 'akurasi' ? 'Akurasi (%) &darr;' : 'Akurasi (%)' ?></th>
                    <th><?= $urut === 'total_skor' ? 'Total Skor &darr;' : 'Total Skor' ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($peringkat_res) === 0): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; color:var(--text-muted);">Belum ada peserta yang menyelesaikan quiz.</td>
                    </tr>
                <?php else: ?>
                    <?php $rank = 1; while ($r = mysqli_fetch_assoc($peringkat_res)): ?>
                        <?php $is_me = (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$r['user_id']); ?>
                        <tr style="<?= $is_me ? 'background-color: var(--accent-soft);' : '' ?>">
                            <td><b>#<?= $rank++ ?></b></td>
                            <td>
                                <b><?= htmlspecialchars($r['nama_lengkap']) ?></b>
                                <?php if ($is_me): ?>
                                    <span class="badge badge-accent" style="margin-left:6px;">Anda</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($urut === 'quiz_selesai'): ?>
                                    <span class="badge badge-accent"><?= $r['total_quiz'] ?> Quiz</span>
                                <?php else: ?>
                                    <?= $r['total_quiz'] ?> Quiz
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($urut === 'akurasi'): ?>
                                    <span class="badge badge-accent"><?= number_format($r['akurasi'], 1) ?>%</span>
                                <?php else: ?>
                                    <?= number_format($r['akurasi'], 1) ?>%
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($urut === 'total_skor'): ?>
                                    <span class="badge badge-accent"><?= number_format($r['total_skor'], 1) ?></span>
                                <?php else: ?>
                                    <b><?= number_format($r['total_skor'], 1) ?></b>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php if ($riwayat_pribadi && mysqli_num_rows($riwayat_pribadi) > 0): ?>
<section class="card">
    <h3>Riwayat Pengerjaan Quiz Anda</h3>
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Paket Quiz</th>
                    <th>Kategori</th>
                    <th>Benar / Total</th>
                    <th>Skor</th>
                    <th>Predikat</th>
                    <th>Waktu Selesai</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($rp = mysqli_fetch_assoc($riwayat_pribadi)): ?>
                    <tr>
                        <td><b><?= htmlspecialchars($rp['judul_quiz']) ?></b></td>
                        <td><span class="badge"><?= htmlspecialchars($rp['nama_kategori']) ?></span></td>
                        <td><?= $rp['jumlah_benar'] ?> / <?= $rp['total_soal'] ?></td>
                        <td><b><?= number_format($rp['skor'], 1) ?></b></td>
                        <td><span class="badge badge-accent"><?= $rp['grade'] ?></span></td>
                        <td><small style="color:var(--text-muted);"><?= $rp['waktu_selesai'] ?></small></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>