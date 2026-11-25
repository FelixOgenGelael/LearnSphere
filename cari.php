<?php
require_once 'config/koneksi.php';
require_once 'config/functions.php';

$keyword     = isset($_GET['q']) ? trim($_GET['q']) : '';
$kategori_id = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$jenis       = isset($_GET['jenis']) ? $_GET['jenis'] : 'semua'; // semua | materi | soal

$kategori_list = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

$hasil_materi = [];
$hasil_modul  = [];
$hasil_contoh = [];
$hasil_soal_q = [];

$kw_esc = mysqli_real_escape_string($conn, $keyword);
$filter_kat_materi = $kategori_id > 0 ? " AND m.kategori_id = $kategori_id " : "";
$filter_kat_quiz   = $kategori_id > 0 ? " AND q.kategori_id = $kategori_id " : "";

if ($keyword !== '' || $kategori_id > 0) {
    // 1. Cari Topik Materi & Sub-Modul
    if ($jenis === 'semua' || $jenis === 'materi') {
        $sql_m = "SELECT m.*, k.nama_kategori FROM materi m 
                  JOIN kategori k ON m.kategori_id = k.id 
                  WHERE (m.judul LIKE '%$kw_esc%' OR m.deskripsi LIKE '%$kw_esc%') $filter_kat_materi
                  ORDER BY m.id DESC";
        $res_m = mysqli_query($conn, $sql_m);
        while ($row = mysqli_fetch_assoc($res_m)) $hasil_materi[] = $row;

        $sql_mod = "SELECT mm.*, m.judul AS nama_materi, k.nama_kategori FROM modul_materi mm 
                    JOIN materi m ON mm.materi_id = m.id 
                    JOIN kategori k ON m.kategori_id = k.id 
                    WHERE (mm.judul_modul LIKE '%$kw_esc%' OR mm.konten LIKE '%$kw_esc%') $filter_kat_materi
                    ORDER BY mm.id DESC";
        $res_mod = mysqli_query($conn, $sql_mod);
        while ($row = mysqli_fetch_assoc($res_mod)) $hasil_modul[] = $row;
    }

    // 2. Cari Contoh Soal & Soal Quiz Pilihan Ganda
    if ($jenis === 'semua' || $jenis === 'soal') {
        $sql_cs = "SELECT cs.*, mm.judul_modul, mm.id AS modul_id, k.nama_kategori FROM contoh_soal cs 
                   JOIN modul_materi mm ON cs.modul_id = mm.id 
                   JOIN materi m ON mm.materi_id = m.id 
                   JOIN kategori k ON m.kategori_id = k.id 
                   WHERE (cs.pertanyaan LIKE '%$kw_esc%' OR cs.pembahasan LIKE '%$kw_esc%') $filter_kat_materi
                   ORDER BY cs.id DESC";
        $res_cs = mysqli_query($conn, $sql_cs);
        while ($row = mysqli_fetch_assoc($res_cs)) $hasil_contoh[] = $row;

        $sql_sq = "SELECT sq.*, q.judul_quiz, q.id AS quiz_id, k.nama_kategori FROM soal_quiz sq 
                   JOIN quiz q ON sq.quiz_id = q.id 
                   JOIN kategori k ON q.kategori_id = k.id 
                   WHERE sq.pertanyaan LIKE '%$kw_esc%' $filter_kat_quiz
                   ORDER BY sq.id DESC";
        $res_sq = mysqli_query($conn, $sql_sq);
        while ($row = mysqli_fetch_assoc($res_sq)) $hasil_soal_q[] = $row;
    }
}

include 'includes/header.php';
?>

<section class="card card-accent">
    <h1>Pencarian Terpadu Materi & Bank Soal</h1>
    <p style="color:var(--text-secondary); margin-bottom:14px;">Temukan topik matematika, sub-modul penjelasan, maupun butir soal latihan berdasarkan kata kunci dan kategori.</p>

    <form method="GET" action="cari.php" class="search-bar-box" style="margin-bottom:0; padding:0; border:none; box-shadow:none;">
        <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>" placeholder="Masukkan kata kunci (contoh: eliminasi, turunan, segitiga, peluang)...">
        <select name="kategori">
            <option value="0">Semua Kategori Matematika</option>
            <?php while ($k = mysqli_fetch_assoc($kategori_list)): ?>
                <option value="<?= $k['id'] ?>" <?= $kategori_id === (int)$k['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($k['nama_kategori']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <select name="jenis">
            <option value="semua" <?= $jenis === 'semua' ? 'selected' : '' ?>>Materi & Soal</option>
            <option value="materi" <?= $jenis === 'materi' ? 'selected' : '' ?>>Hanya Materi & Modul</option>
            <option value="soal" <?= $jenis === 'soal' ? 'selected' : '' ?>>Hanya Bank Soal</option>
        </select>
        <button type="submit" class="btn btn-primary">Cari</button>
    </form>
</section>

<?php if ($keyword === '' && $kategori_id === 0): ?>
    <div class="card" style="text-align:center; color:var(--text-muted);">
        Silakan masukkan kata kunci atau pilih kategori matematika di atas untuk mulai mencari.
    </div>
<?php else: ?>
    <?php $total_temuan = count($hasil_materi) + count($hasil_modul) + count($hasil_contoh) + count($hasil_soal_q); ?>
    <p style="margin-bottom:16px; color:var(--text-secondary);">
        Ditemukan <b><?= $total_temuan ?></b> hasil relevan
        <?= $keyword !== '' ? 'untuk kata kunci <b>"' . htmlspecialchars($keyword) . '"</b>' : '' ?>.
    </p>

    <!-- HASIL TOPIK MATERI & MODUL -->
    <?php if (count($hasil_materi) > 0 || count($hasil_modul) > 0): ?>
        <section class="card">
            <h3>Hasil Topik Materi & Modul (<?= count($hasil_materi) + count($hasil_modul) ?>)</h3>
            <?php foreach ($hasil_materi as $hm): ?>
                <div style="border-top:1px solid var(--border-color); padding:12px 0;">
                    <span class="badge badge-accent">Topik Materi</span>
                    <span class="badge"><?= htmlspecialchars($hm['nama_kategori']) ?></span>
                    <h4 style="margin-top:6px;"><a href="materi_detail.php?id=<?= $hm['id'] ?>"><?= htmlspecialchars($hm['judul']) ?></a></h4>
                    <p style="font-size:14px; color:var(--text-secondary);"><?= htmlspecialchars($hm['deskripsi']) ?></p>
                </div>
            <?php endforeach; ?>

            <?php foreach ($hasil_modul as $hmod): ?>
                <div style="border-top:1px solid var(--border-color); padding:12px 0;">
                    <span class="badge">Modul Belajar</span>
                    <span class="badge"><?= htmlspecialchars($hmod['nama_kategori']) ?></span>
                    <h4 style="margin-top:6px;"><a href="modul_detail.php?id=<?= $hmod['id'] ?>"><?= htmlspecialchars($hmod['judul_modul']) ?></a></h4>
                    <p style="font-size:13.5px; color:var(--text-muted);">Bagian dari materi: <?= htmlspecialchars($hmod['nama_materi']) ?></p>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <!-- HASIL BANK CONTOH SOAL & SOAL QUIZ -->
    <?php if (count($hasil_contoh) > 0 || count($hasil_soal_q) > 0): ?>
        <section class="card">
            <h3>Hasil Bank Soal Matematika (<?= count($hasil_contoh) + count($hasil_soal_q) ?>)</h3>
            <?php foreach ($hasil_contoh as $hcs): ?>
                <div style="border-top:1px solid var(--border-color); padding:12px 0;">
                    <span class="badge badge-accent">Contoh Soal Interaktif</span>
                    <span class="badge"><?= htmlspecialchars($hcs['nama_kategori']) ?></span>
                    <span class="badge"><?= htmlspecialchars($hcs['kategori_soal']) ?></span>
                    <p style="margin-top:8px; font-weight:600;"><?= htmlspecialchars($hcs['pertanyaan']) ?></p>
                    <a href="modul_detail.php?id=<?= $hcs['modul_id'] ?>" style="font-size:13.5px;">Kerjakan di Modul: <?= htmlspecialchars($hcs['judul_modul']) ?> &rarr;</a>
                </div>
            <?php endforeach; ?>

            <?php foreach ($hasil_soal_q as $hsq): ?>
                <div style="border-top:1px solid var(--border-color); padding:12px 0;">
                    <span class="badge">Soal Quiz</span>
                    <span class="badge"><?= htmlspecialchars($hsq['nama_kategori']) ?></span>
                    <span class="badge"><?= htmlspecialchars($hsq['kategori_soal']) ?></span>
                    <p style="margin-top:8px; font-weight:600;"><?= htmlspecialchars($hsq['pertanyaan']) ?></p>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="kerjakan_quiz.php?id=<?= $hsq['quiz_id'] ?>" style="font-size:13.5px;">Ikuti Paket Quiz: <?= htmlspecialchars($hsq['judul_quiz']) ?> &rarr;</a>
                    <?php else: ?>
                        <a href="login.php?pesan=wajib_login_quiz" style="font-size:13.5px;">Login untuk Ikuti Paket Quiz: <?= htmlspecialchars($hsq['judul_quiz']) ?> &rarr;</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>