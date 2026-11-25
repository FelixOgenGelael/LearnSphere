<?php
require_once 'config/koneksi.php';
require_once 'config/functions.php';

$jml_materi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM materi"))['total'];
$jml_modul  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM modul_materi"))['total'];
$jml_soal   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM contoh_soal"))['total']
            + mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM soal_quiz"))['total'];
$jml_quiz   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM quiz"))['total'];

$kategori_res = mysqli_query($conn, "SELECT k.*, (SELECT COUNT(*) FROM materi m WHERE m.kategori_id = k.id) AS jml_materi FROM kategori k ORDER BY k.id ASC");
$materi_terbaru = mysqli_query($conn, "SELECT m.*, k.nama_kategori, u.nama_lengkap AS pembuat FROM materi m JOIN kategori k ON m.kategori_id = k.id JOIN users u ON m.user_id = u.id ORDER BY m.created_at DESC LIMIT 3");

include 'includes/header.php';
?>

<!-- HERO BERANDA PUBLIK + PENCARIAN CEPAT -->
<section class="card card-accent">
    <span class="badge badge-accent">Akses Terbuka &bull; Matematika Interaktif</span>
    <h1 style="margin-top:10px;">
        <?= isset($_SESSION['user_id']) ? 'Selamat Datang Kembali, ' . htmlspecialchars($_SESSION['nama_lengkap']) : 'Belajar Matematika Lebih Terstruktur & Interaktif' ?>
    </h1>
    <p style="color:var(--text-secondary); margin-top:6px; max-width:740px;">
        Jelajahi materi matematika berdasarkan kategori, pelajari modul multimedia, kerjakan latihan contoh soal dengan pembahasan instan, serta uji pemahaman melalui quiz pilihan ganda.
    </p>

    <!-- Form Pencarian Cepat ke cari.php -->
    <form action="cari.php" method="GET" style="display:flex; gap:10px; flex-wrap:wrap; margin-top:18px;">
        <input type="text" name="q" placeholder="Cari topik materi, rumus, atau soal (misal: SPLDV, turunan, sin 30)..." style="flex:1; min-width:250px;" required>
        <button type="submit" class="btn btn-primary">Cari Materi & Soal</button>
        <a href="materi.php" class="btn btn-secondary">Semua Materi</a>
    </form>
</section>

<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>
    <?php if (($_SESSION['status_pengajar'] ?? '') === 'pending'): ?>
        <div class="card" style="border-left: 4px solid var(--warning-border); background: var(--warning-bg);">
            <b>Permohonan Pengajar Sedang Ditinjau:</b> Akun Anda saat ini berstatus <b>User</b> sambil menunggu persetujuan <b>Admin</b> untuk mendapatkan hak akses <b>Pengajar</b>.
        </div>
    <?php else: ?>
        <div class="card" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
                <b>Ingin berkontribusi membuat materi dan soal matematika?</b>
                <p style="font-size:14px; color:var(--text-secondary);">Ajukan peningkatan akun Anda dari <b>User</b> menjadi <b>Pengajar</b> (memerlukan verifikasi Admin).</p>
            </div>
            <a href="ajukan_pengajar.php" class="btn btn-secondary">Ajukan Sebagai Pengajar</a>
        </div>
    <?php endif; ?>
<?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && $jumlah_pending_admin > 0): ?>
    <div class="card" style="border-left: 4px solid var(--accent); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <b>Terdapat <?= $jumlah_pending_admin ?> Permohonan Pengajar Baru!</b>
            <p style="font-size:14px; color:var(--text-secondary);">Tinjau dan setujui permohonan agar calon pengajar dapat mulai menyusun materi.</p>
        </div>
        <a href="admin_users.php" class="btn btn-primary">Buka Panel Admin</a>
    </div>
<?php endif; ?>

<!-- STATISTIK PLATFORM -->
<section class="grid-4" style="margin-bottom: 24px;">
    <div class="stat-box">
        <div class="stat-label">Topik Matematika</div>
        <div class="stat-value"><?= $jml_materi ?></div>
        <a href="materi.php" style="font-size:13.5px;">Lihat katalog &rarr;</a>
    </div>
    <div class="stat-box">
        <div class="stat-label">Modul Belajar</div>
        <div class="stat-value"><?= $jml_modul ?></div>
        <span style="font-size:13.5px; color:var(--text-muted);">Penjelasan, foto & video</span>
    </div>
    <div class="stat-box">
        <div class="stat-label">Bank Contoh & Quiz</div>
        <div class="stat-value"><?= $jml_soal ?></div>
        <a href="cari.php" style="font-size:13.5px;">Telusuri bank soal &rarr;</a>
    </div>
    <div class="stat-box">
        <div class="stat-label">Paket Evaluasi</div>
        <div class="stat-value"><?= $jml_quiz ?></div>
        <a href="quiz.php" style="font-size:13.5px;">Kerjakan quiz &rarr;</a>
    </div>
</section>

<!-- JELAJAH BERDASARKAN KATEGORI MATEMATIKA -->
<section style="margin-bottom: 26px;">
    <h2 style="margin-bottom:14px;">Kategori Bidang Matematika</h2>
    <div class="grid-4">
        <?php while ($kat = mysqli_fetch_assoc($kategori_res)): ?>
            <a href="materi.php?kategori=<?= $kat['id'] ?>" class="card card-hover" style="text-decoration:none; display:block; margin-bottom:0;">
                <span class="badge badge-accent"><?= $kat['jml_materi'] ?> Materi</span>
                <h3 style="margin-top:10px;"><?= htmlspecialchars($kat['nama_kategori']) ?></h3>
                <p style="font-size:13.5px; color:var(--text-secondary); margin-top:6px;">
                    <?= htmlspecialchars($kat['deskripsi']) ?>
                </p>
            </a>
        <?php endwhile; ?>
    </div>
</section>

<!-- MATERI TERBARU -->
<section>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <h2>Materi Matematika Terbaru</h2>
        <a href="materi.php" style="font-size:14px; font-weight:600;">Lihat Semua &rarr;</a>
    </div>
    <div class="grid-3">
        <?php while ($m = mysqli_fetch_assoc($materi_terbaru)): ?>
            <div class="card card-hover" style="display:flex; flex-direction:column; justify-content:space-between;">
                <div>
                    <span class="badge badge-accent"><?= htmlspecialchars($m['nama_kategori']) ?></span>
                    <span class="badge"><?= htmlspecialchars($m['tingkat']) ?></span>
                    <h3 style="margin-top:10px;"><?= htmlspecialchars($m['judul']) ?></h3>
                    <p style="font-size:14px; color:var(--text-secondary); margin-top:6px;">
                        <?= htmlspecialchars(substr($m['deskripsi'], 0, 115)) ?>...
                    </p>
                </div>
                <div style="margin-top:14px;">
                    <a href="materi_detail.php?id=<?= $m['id'] ?>" class="btn btn-primary" style="width:100%;">Pelajari Materi</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>