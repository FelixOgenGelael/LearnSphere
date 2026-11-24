<?php
require_once 'config/koneksi.php';
require_once 'config/functions.php';

if (isset($_GET['hapus']) && isset($_SESSION['role']) && $_SESSION['role'] === 'pengajar') {
    $id_hapus = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM materi WHERE id = $id_hapus");
    header("Location: materi.php?pesan=dihapus");
    exit;
}

$kategori_aktif = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$tingkat_aktif  = isset($_GET['tingkat']) ? trim($_GET['tingkat']) : '';
$cari           = isset($_GET['q']) ? trim($_GET['q']) : '';

$where = [];
if ($kategori_aktif > 0) {
    $where[] = "m.kategori_id = $kategori_aktif";
}
if (in_array($tingkat_aktif, ['Dasar', 'Menengah', 'Lanjut'])) {
    $t_esc = mysqli_real_escape_string($conn, $tingkat_aktif);
    $where[] = "m.tingkat = '$t_esc'";
}
if ($cari !== '') {
    $c_esc = mysqli_real_escape_string($conn, $cari);
    $where[] = "(m.judul LIKE '%$c_esc%' OR m.deskripsi LIKE '%$c_esc%')";
}

$where_sql = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

$query = "SELECT m.*, k.nama_kategori, u.nama_lengkap AS pembuat, 
          (SELECT COUNT(*) FROM modul_materi mm WHERE mm.materi_id = m.id) AS jumlah_modul 
          FROM materi m 
          JOIN kategori k ON m.kategori_id = k.id
          JOIN users u ON m.user_id = u.id 
          $where_sql
          ORDER BY m.created_at DESC";
$result = mysqli_query($conn, $query);
$kategori_list = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id ASC");

include 'includes/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:16px;">
    <div>
        <h1>Katalog Materi Matematika</h1>
        <p style="color:var(--text-secondary); font-size:14.5px;">Pilih kategori atau cari topik matematika yang ingin dipelajari.</p>
    </div>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'pengajar'): ?>
        <a href="materi_form.php" class="btn btn-primary">+ Tambah Materi Matematika</a>
    <?php endif; ?>
</div>

<!-- PILLS FILTER KATEGORI -->
<div class="filter-pills">
    <a href="materi.php" class="pill-link <?= $kategori_aktif === 0 ? 'active' : '' ?>">Semua Kategori</a>
    <?php while ($kat = mysqli_fetch_assoc($kategori_list)): ?>
        <a href="materi.php?kategori=<?= $kat['id'] ?>" class="pill-link <?= $kategori_aktif === (int)$kat['id'] ? 'active' : '' ?>">
            <?= htmlspecialchars($kat['nama_kategori']) ?>
        </a>
    <?php endwhile; ?>
</div>

<!-- BAR PENCARIAN & FILTER TINGKAT -->
<form method="GET" action="materi.php" class="search-bar-box">
    <input type="hidden" name="kategori" value="<?= $kategori_aktif ?>">
    <input type="text" name="q" value="<?= htmlspecialchars($cari) ?>" placeholder="Cari judul atau deskripsi materi...">
    <select name="tingkat">
        <option value="">Semua Tingkat</option>
        <option value="Dasar" <?= $tingkat_aktif === 'Dasar' ? 'selected' : '' ?>>Tingkat Dasar</option>
        <option value="Menengah" <?= $tingkat_aktif === 'Menengah' ? 'selected' : '' ?>>Tingkat Menengah</option>
        <option value="Lanjut" <?= $tingkat_aktif === 'Lanjut' ? 'selected' : '' ?>>Tingkat Lanjut</option>
    </select>
    <button type="submit" class="btn btn-primary">Filter & Cari</button>
    <?php if ($cari !== '' || $tingkat_aktif !== '' || $kategori_aktif > 0): ?>
        <a href="materi.php" class="btn btn-secondary">Reset</a>
    <?php endif; ?>
</form>

<?php if (isset($_GET['pesan'])): ?>
    <div class="alert alert-success">Data materi berhasil diperbarui!</div>
<?php endif; ?>

<?php if (mysqli_num_rows($result) === 0): ?>
    <div class="card" style="text-align:center; color:var(--text-muted);">
        Tidak ada materi matematika yang sesuai dengan kriteria pencarian Anda.
    </div>
<?php else: ?>
    <section class="grid-3">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="card card-hover" style="display:flex; flex-direction:column; justify-content:space-between;">
                <div>
                    <?php if (!empty($row['thumbnail']) && file_exists($row['thumbnail'])): ?>
                        <img src="<?= htmlspecialchars($row['thumbnail']) ?>" alt="Thumbnail" style="width:100%; height:160px; object-fit:cover; border-radius:8px; margin-bottom:12px;">
                    <?php endif; ?>
                    <span class="badge badge-accent"><?= htmlspecialchars($row['nama_kategori']) ?></span>
                    <span class="badge"><?= htmlspecialchars($row['tingkat']) ?></span>
                    <span class="badge"><?= $row['jumlah_modul'] ?> Modul</span>
                    <h3 style="margin-top:10px;"><?= htmlspecialchars($row['judul']) ?></h3>
                    <p style="font-size:14px; color:var(--text-secondary); margin:8px 0;">
                        <?= htmlspecialchars(substr($row['deskripsi'], 0, 120)) ?>...
                    </p>
                    <p><small style="color:var(--text-muted);">Penyusun: <b><?= htmlspecialchars($row['pembuat']) ?></b></small></p>
                </div>
                <div style="margin-top:16px; display:flex; gap:8px; flex-wrap:wrap;">
                    <a href="materi_detail.php?id=<?= $row['id'] ?>" class="btn btn-primary">Buka Materi</a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'pengajar'): ?>
                        <a href="materi_form.php?id=<?= $row['id'] ?>" class="btn btn-secondary">Edit</a>
                        <a href="materi.php?hapus=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Hapus materi ini beserta seluruh modulnya?')">Hapus</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>