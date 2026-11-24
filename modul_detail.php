<?php
require_once 'config/koneksi.php';
require_once 'config/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$modul = mysqli_fetch_assoc(mysqli_query($conn, "SELECT mm.*, m.judul AS judul_materi, k.nama_kategori FROM modul_materi mm JOIN materi m ON mm.materi_id = m.id JOIN kategori k ON m.kategori_id = k.id WHERE mm.id = $id"));

if (!$modul) {
    header("Location: materi.php");
    exit;
}

if (isset($_GET['hapus_contoh']) && isset($_SESSION['role']) && $_SESSION['role'] === 'pengajar') {
    $id_cs = (int)$_GET['hapus_contoh'];
    mysqli_query($conn, "DELETE FROM contoh_soal WHERE id = $id_cs AND modul_id = $id");
    header("Location: modul_detail.php?id=$id");
    exit;
}

// Tambah komentar diskusi (mendukung user login maupun tamu publik)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kirim_komentar'])) {
    $komentar = mysqli_real_escape_string($conn, trim($_POST['komentar']));
    $uid      = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : "NULL";
    $pengirim = isset($_SESSION['nama_lengkap'])
                ? mysqli_real_escape_string($conn, $_SESSION['nama_lengkap'] . " (" . ucfirst($_SESSION['role']) . ")")
                : mysqli_real_escape_string($conn, trim($_POST['nama_pengirim'] ?? 'Pengunjung Publik') . " (Publik)");

    if (!empty($komentar)) {
        mysqli_query($conn, "INSERT INTO diskusi (modul_id, user_id, nama_pengirim, komentar) VALUES ($id, $uid, '$pengirim', '$komentar')");
        header("Location: modul_detail.php?id=$id#diskusi");
        exit;
    }
}

$filter_kat_soal = isset($_GET['kat_soal']) ? trim($_GET['kat_soal']) : '';
$where_cs = "WHERE modul_id = $id";
if (in_array($filter_kat_soal, ['Konsep Dasar', 'Hitungan', 'Pemecahan Masalah'])) {
    $ks_esc = mysqli_real_escape_string($conn, $filter_kat_soal);
    $where_cs .= " AND kategori_soal = '$ks_esc'";
}

$contoh_list  = mysqli_query($conn, "SELECT * FROM contoh_soal $where_cs ORDER BY id ASC");
$diskusi_list = mysqli_query($conn, "SELECT * FROM diskusi WHERE modul_id = $id ORDER BY created_at DESC");

include 'includes/header.php';
?>

<div class="reading-container">
    <section class="card card-accent">
        <a href="materi_detail.php?id=<?= $modul['materi_id'] ?>" style="font-size:14px;">&larr; Kembali ke Materi: <b><?= htmlspecialchars($modul['judul_materi']) ?></b></a>
        <div style="margin-top:12px;">
            <span class="badge badge-accent"><?= htmlspecialchars($modul['nama_kategori']) ?></span>
            <span class="badge">Modul <?= $modul['urutan'] ?></span>
            <h1 style="margin-top:8px;"><?= htmlspecialchars($modul['judul_modul']) ?></h1>
        </div>
        <hr style="border:none; border-top:1px solid var(--border-color); margin: 16px 0;">

        <?php if (!empty($modul['gambar']) && file_exists($modul['gambar'])): ?>
            <div style="text-align:center;">
                <img src="<?= htmlspecialchars($modul['gambar']) ?>" alt="Ilustrasi Modul" class="media-preview">
            </div>
        <?php endif; ?>

        <?php if (!empty($modul['video_file']) && file_exists($modul['video_file'])): ?>
            <video controls class="media-preview" style="width:100%;">
                <source src="<?= htmlspecialchars($modul['video_file']) ?>" type="video/mp4">
            </video>
        <?php endif; ?>

        <?php if (!empty($modul['video_url'])): ?>
            <div class="video-container">
                <iframe src="<?= formatYoutubeEmbed($modul['video_url']) ?>" frameborder="0" allowfullscreen></iframe>
            </div>
        <?php endif; ?>

        <div class="module-prose">
            <?= nl2br(htmlspecialchars($modul['konten'])) ?>
        </div>
    </section>

    <!-- LATIHAN CONTOH SOAL INTERAKTIF + FILTER KATEGORI SOAL -->
    <section class="card" id="latihan-soal">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:14px;">
            <div>
                <h2>Latihan Contoh Soal Matematika</h2>
                <p style="font-size:14px; color:var(--text-muted);">Kerjakan soal di bawah ini dan klik <b>Periksa Jawaban</b> untuk melihat pembahasan.</p>
            </div>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'pengajar'): ?>
                <a href="contoh_soal_form.php?modul_id=<?= $id ?>" class="btn btn-primary">+ Tambah Contoh Soal</a>
            <?php endif; ?>
        </div>

        <!-- Filter Kategori Soal -->
        <div class="filter-pills" style="margin-bottom:16px;">
            <a href="modul_detail.php?id=<?= $id ?>#latihan-soal" class="pill-link <?= $filter_kat_soal === '' ? 'active' : '' ?>">Semua Tipe</a>
            <a href="modul_detail.php?id=<?= $id ?>&kat_soal=Konsep+Dasar#latihan-soal" class="pill-link <?= $filter_kat_soal === 'Konsep Dasar' ? 'active' : '' ?>">Konsep Dasar</a>
            <a href="modul_detail.php?id=<?= $id ?>&kat_soal=Hitungan#latihan-soal" class="pill-link <?= $filter_kat_soal === 'Hitungan' ? 'active' : '' ?>">Hitungan</a>
            <a href="modul_detail.php?id=<?= $id ?>&kat_soal=Pemecahan+Masalah#latihan-soal" class="pill-link <?= $filter_kat_soal === 'Pemecahan Masalah' ? 'active' : '' ?>">Pemecahan Masalah</a>
        </div>

        <?php if (mysqli_num_rows($contoh_list) === 0): ?>
            <p style="color:var(--text-muted); font-size:14.5px;">Belum ada contoh soal untuk kategori ini.</p>
        <?php else: ?>
            <?php $no = 1; while ($cs = mysqli_fetch_assoc($contoh_list)): ?>
                <div class="practice-card">
                    <div class="practice-question">
                        <span class="badge badge-accent" style="margin-right:4px;">Soal <?= $no++ ?></span>
                        <span class="badge" style="margin-right:6px;"><?= htmlspecialchars($cs['kategori_soal']) ?></span>
                        <div style="margin-top:8px;"><?= nl2br(htmlspecialchars($cs['pertanyaan'])) ?></div>
                    </div>

                    <?php
                    $opsi_contoh = [
                        'A' => $cs['pilihan_a'],
                        'B' => $cs['pilihan_b'],
                        'C' => $cs['pilihan_c'],
                        'D' => $cs['pilihan_d']
                    ];
                    ?>
                    <div class="practice-options">
                        <?php foreach ($opsi_contoh as $huruf => $teks): ?>
                            <label class="practice-option" id="label-contoh-<?= $cs['id'] ?>-<?= $huruf ?>">
                                <input type="radio" name="contoh_opsi_<?= $cs['id'] ?>" value="<?= $huruf ?>">
                                <span><b><?= $huruf ?>.</b> <?= htmlspecialchars($teks) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                        <button type="button" class="btn btn-primary" onclick="periksaContohSoal(<?= $cs['id'] ?>, '<?= $cs['jawaban_benar'] ?>')">
                            Periksa Jawaban
                        </button>
                        <button type="button" id="btn-reset-<?= $cs['id'] ?>" class="btn btn-secondary" style="display:none;" onclick="resetContohSoal(<?= $cs['id'] ?>)">
                            Coba Lagi
                        </button>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'pengajar'): ?>
                            <a href="modul_detail.php?id=<?= $id ?>&hapus_contoh=<?= $cs['id'] ?>" class="btn btn-danger" onclick="return confirm('Hapus contoh soal ini?')">Hapus</a>
                        <?php endif; ?>
                    </div>

                    <div id="feedback-contoh-<?= $cs['id'] ?>" class="practice-feedback"></div>
                    <div id="pembahasan-<?= $cs['id'] ?>" class="pembahasan-box">
                        <p style="margin-bottom:4px;"><b>Pembahasan (Kunci: <?= $cs['jawaban_benar'] ?>):</b></p>
                        <p><?= nl2br(htmlspecialchars($cs['pembahasan'])) ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </section>

    <!-- DISKUSI MODUL (PUBLIK & MEMBER) -->
    <section class="card" id="diskusi">
        <h3>Diskusi & Tanya Jawab Modul</h3>
        <form method="POST" action="" style="margin-top:12px; margin-bottom:20px;">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="form-group">
                    <label for="nama_pengirim">Nama Anda</label>
                    <input type="text" id="nama_pengirim" name="nama_pengirim" placeholder="Masukkan nama Anda..." required>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <label for="komentar">Pertanyaan atau catatan langkah penyelesaian:</label>
                <textarea id="komentar" name="komentar" rows="3" onkeyup="hitungKarakter('komentar', 'sisa-karakter', 250)" required></textarea>
                <div style="text-align:right; margin-top:4px;">
                    <small style="color:var(--text-muted);">Sisa karakter: <input type="text" id="sisa-karakter" value="250" readonly style="width:52px; padding:2px 6px; display:inline-block; text-align:center;"></small>
                </div>
            </div>
            <button type="submit" name="kirim_komentar" class="btn btn-primary">Kirim Komentar</button>
        </form>

        <?php while ($dk = mysqli_fetch_assoc($diskusi_list)): ?>
            <div style="border-top:1px solid var(--border-color); padding:12px 0;">
                <p style="font-size:14px;">
                    <b><?= htmlspecialchars($dk['nama_pengirim']) ?></b>
                    <small style="color:var(--text-muted); margin-left:6px;"><?= $dk['created_at'] ?></small>
                </p>
                <p style="margin-top:5px; font-size:14.5px;"><?= nl2br(htmlspecialchars($dk['komentar'])) ?></p>
            </div>
        <?php endwhile; ?>
    </section>
</div>

<?php include 'includes/footer.php'; ?>