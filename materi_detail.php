<?php
require_once 'config/koneksi.php';
require_once 'config/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$materi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT m.*, k.nama_kategori, u.nama_lengkap FROM materi m JOIN kategori k ON m.kategori_id = k.id JOIN users u ON m.user_id = u.id WHERE m.id = $id"));

if (!$materi) {
    header("Location: materi.php");
    exit;
}

if (isset($_GET['hapus_modul']) && isset($_SESSION['role']) && $_SESSION['role'] === 'pengajar') {
    $id_mod = (int)$_GET['hapus_modul'];
    mysqli_query($conn, "DELETE FROM modul_materi WHERE id = $id_mod AND materi_id = $id");
    header("Location: materi_detail.php?id=$id&pesan=modul_dihapus");
    exit;
}

$modul_list = mysqli_query($conn, "SELECT mm.*, (SELECT COUNT(*) FROM contoh_soal cs WHERE cs.modul_id = mm.id) AS jml_contoh FROM modul_materi mm WHERE mm.materi_id = $id ORDER BY mm.urutan ASC, mm.id ASC");
$quiz_list  = mysqli_query($conn, "SELECT * FROM quiz WHERE materi_id = $id");

include 'includes/header.php';
?>

<section class="card card-accent">
    <a href="materi.php" style="font-size:14px;">&larr; Kembali ke Katalog Materi</a>
    <div style="margin-top:10px;">
        <span class="badge badge-accent"><?= htmlspecialchars($materi['nama_kategori']) ?></span>
        <span class="badge"><?= htmlspecialchars($materi['tingkat']) ?></span>
        <h1 style="margin-top:8px;"><?= htmlspecialchars($materi['judul']) ?></h1>
        <p style="font-size:13.5px; color:var(--text-muted);">Disusun oleh: <b><?= htmlspecialchars($materi['nama_lengkap']) ?></b></p>
    </div>
    <p style="margin-top:12px; color:var(--text-secondary);"><?= nl2br(htmlspecialchars($materi['deskripsi'])) ?></p>
</section>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
    <h3>Daftar Modul Pembelajaran</h3>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'pengajar'): ?>
        <a href="modul_form.php?materi_id=<?= $id ?>" class="btn btn-primary">+ Tambah Modul Materi</a>
    <?php endif; ?>
</div>

<?php if (mysqli_num_rows($modul_list) === 0): ?>
    <div class="card"><p style="color:var(--text-muted);">Belum ada modul materi pada topik ini.</p></div>
<?php else: ?>
    <?php while ($mod = mysqli_fetch_assoc($modul_list)): ?>
        <div class="card card-hover" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
                <span class="badge badge-accent">Bab <?= $mod['urutan'] ?></span>
                <h4 style="margin-top:6px;"><?= htmlspecialchars($mod['judul_modul']) ?></h4>
                <small style="color:var(--text-muted);">
                    Media: 
                    <?= !empty($mod['gambar']) ? '<b>[Foto]</b> ' : '' ?>
                    <?= (!empty($mod['video_file']) || !empty($mod['video_url'])) ? '<b>[Video]</b> ' : '' ?>
                    <?= (empty($mod['gambar']) && empty($mod['video_file']) && empty($mod['video_url'])) ? 'Teks Terstruktur' : '' ?>
                    &bull; Latihan Contoh Soal: <b><?= $mod['jml_contoh'] ?> Soal</b>
                </small>
            </div>
            <div style="display:flex; gap:8px;">
                <a href="modul_detail.php?id=<?= $mod['id'] ?>" class="btn btn-success">Pelajari & Latihan Soal</a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'pengajar'): ?>
                    <a href="modul_form.php?id=<?= $mod['id'] ?>&materi_id=<?= $id ?>" class="btn btn-secondary">Edit</a>
                    <a href="materi_detail.php?id=<?= $id ?>&hapus_modul=<?= $mod['id'] ?>" class="btn btn-danger" onclick="return confirm('Hapus modul ini?')">Hapus</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
<?php endif; ?>

<section class="card" style="margin-top:20px;">
    <h3>Quiz Evaluasi Terkait</h3>
    <?php if (mysqli_num_rows($quiz_list) === 0): ?>
        <p><small style="color:var(--text-muted);">Belum ada paket quiz untuk materi ini.</small></p>
    <?php else: ?>
        <ul style="margin-left: 20px; margin-top: 8px;">
            <?php while ($qz = mysqli_fetch_assoc($quiz_list)): ?>
                <li style="margin-bottom: 8px;">
                    <b><?= htmlspecialchars($qz['judul_quiz']) ?></b> (Durasi: <?= $qz['durasi_menit'] ?> menit) — 
                    <a href="kerjakan_quiz.php?id=<?= $qz['id'] ?>"><b>Kerjakan Quiz &rarr;</b></a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>