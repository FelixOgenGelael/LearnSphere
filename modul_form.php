<?php
require_once 'config/koneksi.php';
require_once 'config/functions.php';
wajibPengajar();

$id        = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$materi_id = isset($_GET['materi_id']) ? (int)$_GET['materi_id'] : 0;

$data = [
    'urutan' => 1,
    'judul_modul' => '',
    'konten' => '',
    'gambar' => '',
    'video_file' => '',
    'video_url' => ''
];

if ($id > 0) {
    $res = mysqli_query($conn, "SELECT * FROM modul_materi WHERE id = $id");
    if ($row = mysqli_fetch_assoc($res)) {
        $data = $row;
        $materi_id = $row['materi_id'];
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $urutan      = (int)$_POST['urutan'];
    $judul_modul = mysqli_real_escape_string($conn, trim($_POST['judul_modul']));
    $konten      = mysqli_real_escape_string($conn, trim($_POST['konten']));
    $video_url   = mysqli_real_escape_string($conn, trim($_POST['video_url']));

    $gambarPath = $data['gambar'];
    $videoPath  = $data['video_file'];

    // Upload Foto (Opsional)
    $upGambar = uploadMedia($_FILES['gambar'], 'uploads/gambar', ['jpg', 'jpeg', 'png', 'webp', 'gif']);
    if ($upGambar === false) {
        $error = "Format foto tidak didukung (gunakan JPG, PNG, atau WEBP).";
    } elseif ($upGambar !== null) {
        $gambarPath = $upGambar;
    }

    // Upload Video Lokal (Opsional)
    $upVideo = uploadMedia($_FILES['video_file'], 'uploads/video', ['mp4', 'webm', 'ogg']);
    if ($upVideo === false) {
        $error = "Format video tidak didukung (gunakan MP4 atau WEBM).";
    } elseif ($upVideo !== null) {
        $videoPath = $upVideo;
    }

    if (empty($error)) {
        if ($id > 0) {
            $sql = "UPDATE modul_materi SET urutan=$urutan, judul_modul='$judul_modul', konten='$konten', 
                    gambar='$gambarPath', video_file='$videoPath', video_url='$video_url' WHERE id=$id";
        } else {
            $sql = "INSERT INTO modul_materi (materi_id, urutan, judul_modul, konten, gambar, video_file, video_url) 
                    VALUES ($materi_id, $urutan, '$judul_modul', '$konten', '$gambarPath', '$videoPath', '$video_url')";
        }
        mysqli_query($conn, $sql);
        header("Location: materi_detail.php?id=$materi_id&pesan=modul_tersimpan");
        exit;
    }
}

include 'includes/header.php';
?>

<section class="card" style="max-width: 760px; margin: 0 auto;">
    <h2><?= $id > 0 ? 'Edit Modul Materi' : 'Tambah Modul Materi Baru' ?></h2>
    <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="urutan">Nomor Urut Bab / Modul</label>
            <input type="number" id="urutan" name="urutan" value="<?= (int)$data['urutan'] ?>" min="1" required>
        </div>
        <div class="form-group">
            <label for="judul_modul">Judul Modul Materi</label>
            <input type="text" id="judul_modul" name="judul_modul" value="<?= htmlspecialchars($data['judul_modul']) ?>" required>
        </div>
        <div class="form-group">
            <label for="konten">Isi Penjelasan Materi</label>
            <textarea id="konten" name="konten" rows="8" required><?= htmlspecialchars($data['konten']) ?></textarea>
        </div>
        <div class="form-group">
            <label for="gambar">Upload Foto / Ilustrasi Pendukung (Opsional)</label>
            <input type="file" id="gambar" name="gambar" accept="image/*">
            <?php if (!empty($data['gambar'])): ?>
                <small>File saat ini: <?= htmlspecialchars($data['gambar']) ?></small>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="video_file">Upload Video Pembelajaran Lokal MP4/WEBM (Opsional)</label>
            <input type="file" id="video_file" name="video_file" accept="video/mp4,video/webm">
            <?php if (!empty($data['video_file'])): ?>
                <small>Video saat ini: <?= htmlspecialchars($data['video_file']) ?></small>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="video_url">Atau Tautan Video YouTube (Opsional)</label>
            <input type="url" id="video_url" name="video_url" value="<?= htmlspecialchars($data['video_url']) ?>" placeholder="https://www.youtube.com/watch?v=...">
        </div>

        <button type="submit" class="btn btn-primary">Simpan Modul</button>
        <a href="materi_detail.php?id=<?= $materi_id ?>" class="btn btn-secondary">Batal</a>
    </form>
</section>

<?php include 'includes/footer.php'; ?>