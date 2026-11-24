<?php
require_once 'config/koneksi.php';
require_once 'config/functions.php';

if (isset($_GET['hapus']) && isset($_SESSION['role']) && $_SESSION['role'] === 'pengajar') {
    $id_hapus = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM quiz WHERE id = $id_hapus");
    header("Location: quiz.php?pesan=dihapus");
    exit;
}

$kategori_aktif = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$cari           = isset($_GET['q']) ? trim($_GET['q']) : '';

$where = [];
if ($kategori_aktif > 0) {
    $where[] = "q.kategori_id = $kategori_aktif";
}
if ($cari !== '') {
    $c_esc = mysqli_real_escape_string($conn, $cari);
    $where[] = "(q.judul_quiz LIKE '%$c_esc%' OR q.deskripsi LIKE '%$c_esc%' OR m.judul LIKE '%$c_esc%')";
}
$where_sql = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

$query = "SELECT q.*, k.nama_kategori, m.judul AS nama_materi, 
          (SELECT COUNT(*) FROM soal_quiz sq WHERE sq.quiz_id = q.id) AS total_soal 
          FROM quiz q 
          JOIN kategori k ON q.kategori_id = k.id
          JOIN materi m ON q.materi_id = m.id 
          $where_sql
          ORDER BY q.created_at DESC";
$result = mysqli_query($conn, $query);
$kategori_list = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id ASC");

include 'includes/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:16px;">
    <div>
        <h1>Quiz Evaluasi Matematika</h1>
        <p style="color:var(--text-secondary); font-size:14.5px;">Uji kemampuan matematika Anda secara langsung dan lihat skor otomatis.</p>
    </div>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'pengajar'): ?>
        <a href="quiz_form.php" class="btn btn-primary">+ Tambah Paket Quiz</a>
    <?php endif; ?>
</div>

<div class="filter-pills">
    <a href="quiz.php" class="pill-link <?= $kategori_aktif === 0 ? 'active' : '' ?>">Semua Kategori</a>
    <?php while ($kat = mysqli_fetch_assoc($kategori_list)): ?>
        <a href="quiz.php?kategori=<?= $kat['id'] ?>" class="pill-link <?= $kategori_aktif === (int)$kat['id'] ? 'active' : '' ?>">
            <?= htmlspecialchars($kat['nama_kategori']) ?>
        </a>
    <?php endwhile; ?>
</div>

<form method="GET" action="quiz.php" class="search-bar-box">
    <input type="hidden" name="kategori" value="<?= $kategori_aktif ?>">
    <input type="text" name="q" value="<?= htmlspecialchars($cari) ?>" placeholder="Cari judul quiz atau topik materi...">
    <button type="submit" class="btn btn-primary">Cari Quiz</button>
    <?php if ($cari !== '' || $kategori_aktif > 0): ?>
        <a href="quiz.php" class="btn btn-secondary">Reset</a>
    <?php endif; ?>
</form>

<section class="grid-3">
    <?php while ($qz = mysqli_fetch_assoc($result)): ?>
        <div class="card card-hover" style="display:flex; flex-direction:column; justify-content:space-between;">
            <div>
                <span class="badge badge-accent"><?= htmlspecialchars($qz['nama_kategori']) ?></span>
                <span class="badge"><?= $qz['total_soal'] ?> Soal</span>
                <h3 style="margin-top:10px;"><?= htmlspecialchars($qz['judul_quiz']) ?></h3>
                <p style="font-size:13px; color:var(--text-muted); margin-bottom:6px;">Materi: <?= htmlspecialchars($qz['nama_materi']) ?></p>
                <p style="font-size:14px; color:var(--text-secondary); margin-bottom:8px;"><?= htmlspecialchars($qz['deskripsi']) ?></p>
                <p><small style="color:var(--text-muted);">Waktu: <b><?= $qz['durasi_menit'] ?> Menit</b></small></p>
            </div>
            <div style="margin-top:14px; display:flex; gap:8px; flex-wrap:wrap;">
                <?php if ($qz['total_soal'] > 0): ?>
                    <a href="kerjakan_quiz.php?id=<?= $qz['id'] ?>" class="btn btn-success">Mulai Quiz</a>
                <?php else: ?>
                    <span class="badge">Soal Belum Tersedia</span>
                <?php endif; ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'pengajar'): ?>
                    <a href="soal_form.php?quiz_id=<?= $qz['id'] ?>" class="btn btn-secondary">Kelola Soal (<?= $qz['total_soal'] ?>)</a>
                    <a href="quiz.php?hapus=<?= $qz['id'] ?>" class="btn btn-danger" onclick="return confirm('Hapus paket quiz ini?')">Hapus</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
</section>

<?php include 'includes/footer.php'; ?>