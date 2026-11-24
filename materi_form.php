<?php
require_once 'config/koneksi.php';
require_once 'config/functions.php';
wajibPengajar();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$data = ['kategori_id' => 1, 'judul' => '', 'tingkat' => 'Dasar', 'deskripsi' => '', 'thumbnail' => ''];

if ($id > 0) {
    $res = mysqli_query($conn, "SELECT * FROM materi WHERE id = $id");
    if ($row = mysqli_fetch_assoc($res)) {
        $data = $row;
    }
}

$kategori_list = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id ASC");

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori_id = (int)$_POST['kategori_id'];
    $judul       = mysqli_real_escape_string($conn, trim($_POST['judul']));
    $tingkat     = in_array($_POST['tingkat'], ['Dasar', 'Menengah', 'Lanjut']) ? $_POST['tingkat'] : 'Dasar';
    $deskripsi   = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    $user_id     = $_SESSION['user_id'];

    $thumbnailPath = $data['thumbnail'];
    $upload = uploadMedia($_FILES['thumbnail'], 'uploads/gambar', ['jpg', 'jpeg', 'png', 'webp']);
    if ($upload === false) {
        $error = "Format gambar thumbnail harus JPG, JPEG, PNG, atau WEBP.";
    } elseif ($upload !== null) {
        $thumbnailPath = $upload;
    }

    if (empty($error)) {
        if ($id > 0) {
            $sql = "UPDATE materi SET kategori_id=$kategori_id, judul='$judul', tingkat='$tingkat', deskripsi='$deskripsi', thumbnail='$thumbnailPath' WHERE id=$id";
        } else {
            $sql = "INSERT INTO materi (kategori_id, user_id, judul, tingkat, deskripsi, thumbnail) VALUES ($kategori_id, $user_id, '$judul', '$tingkat', '$deskripsi', '$thumbnailPath')";
        }
        mysqli_query($conn, $sql);
        header("Location: materi.php?pesan=sukses");
        exit;
    }
}

include 'includes/header.php';
?>

<section class="card" style="max-width: 700px; margin: 0 auto;">
    <h2><?= $id > 0 ? 'Edit Topik Materi Matematika' : 'Tambah Topik Materi Matematika' ?></h2>
    <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data" style="margin-top:14px;">
        <div class="form-group">
            <label for="kategori_id">Kategori Bidang Matematika</label>
            <select id="kategori_id" name="kategori_id" required>
                <?php while ($kat = mysqli_fetch_assoc($kategori_list)): ?>
                    <option value="<?= $kat['id'] ?>" <?= (int)$data['kategori_id'] === (int)$kat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($kat['nama_kategori']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="tingkat">Tingkat Kesulitan</label>
            <select id="tingkat" name="tingkat">
                <option value="Dasar" <?= $data['tingkat'] === 'Dasar' ? 'selected' : '' ?>>Dasar</option>
                <option value="Menengah" <?= $data['tingkat'] === 'Menengah' ? 'selected' : '' ?>>Menengah</option>
                <option value="Lanjut" <?= $data['tingkat'] === 'Lanjut' ? 'selected' : '' ?>>Lanjut</option>
            </select>
        </div>
        <div class="form-group">
            <label for="judul">Judul Materi</label>
            <input type="text" id="judul" name="judul" value="<?= htmlspecialchars($data['judul']) ?>" placeholder="Contoh: Integral Tentu dan Luas Daerah" required>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi Materi</label>
            <textarea id="deskripsi" name="deskripsi" rows="4" required><?= htmlspecialchars($data['deskripsi']) ?></textarea>
        </div>
        <div class="form-group">
            <label for="thumbnail">Gambar Sampul / Ilustrasi (Opsional)</label>
            <input type="file" id="thumbnail" name="thumbnail" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Simpan Materi</button>
        <a href="materi.php" class="btn btn-secondary">Batal</a>
    </form>
</section>

<?php include 'includes/footer.php'; ?>