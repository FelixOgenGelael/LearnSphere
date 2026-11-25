<?php
require_once 'config/koneksi.php';
require_once 'config/functions.php';
wajibLogin();

$uid  = (int)$_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $uid"));

if ($user['role'] !== 'user') {
    header("Location: index.php");
    exit;
}

$pesan_sukses = '';
$error        = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajukan'])) {
    $bidang_keahlian = mysqli_real_escape_string($conn, trim($_POST['bidang_keahlian']));
    if (empty($bidang_keahlian)) {
        $error = "Harap isi bidang keahlian atau pengalaman mengajar matematika Anda.";
    } else {
        mysqli_query($conn, "UPDATE users SET status_pengajar = 'pending', bidang_keahlian = '$bidang_keahlian' WHERE id = $uid");
        $_SESSION['status_pengajar'] = 'pending';
        $user['status_pengajar']     = 'pending';
        $user['bidang_keahlian']     = $_POST['bidang_keahlian'];
        $pesan_sukses                = "Permohonan menjadi Pengajar telah dikirim dan sedang menunggu persetujuan Admin.";
    }
}

include 'includes/header.php';
?>

<section class="reading-container">
    <div class="card card-accent">
        <span class="badge badge-accent">Pengembangan Kontribusi</span>
        <h1 style="margin-top:8px;">Ajukan Permohonan Menjadi Pengajar</h1>
        <p style="color:var(--text-secondary); margin-top:6px;">
            Sebagai <b>Pengajar</b> di LearnSphere, Anda dapat menyusun topik materi matematika, mengunggah modul multimedia, menambahkan contoh soal interaktif, dan membuat paket quiz evaluasi.
        </p>
        <hr style="border:none; border-top:1px solid var(--border-color); margin: 16px 0;">

        <?php if ($pesan_sukses): ?>
            <div class="alert alert-success"><?= htmlspecialchars($pesan_sukses) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($user['status_pengajar'] === 'pending'): ?>
            <div style="background:var(--warning-bg); border-left:4px solid var(--warning-border); padding:16px; border-radius:8px;">
                <h3>Status Pengajuan: Menunggu Persetujuan Admin</h3>
                <p style="margin-top:6px; font-size:14.5px;">
                    Permohonan Anda dengan bidang keahlian <b>"<?= htmlspecialchars($user['bidang_keahlian']) ?>"</b> sedang ditinjau oleh Admin. Selama masa peninjauan, Anda tetap dapat menggunakan seluruh fitur belajar dan quiz sebagai <b>User</b>.
                </p>
            </div>
        <?php else: ?>
            <?php if ($user['status_pengajar'] === 'rejected'): ?>
                <div class="alert alert-error">
                    Permohonan pengajar Anda sebelumnya belum disetujui oleh Admin. Anda dapat memperbarui keterangan keahlian dan mengajukan ulang di bawah ini.
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="nama">Nama Pemohon</label>
                    <input type="text" id="nama" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="bidang_keahlian">Bidang Keahlian / Latar Belakang Mengajar Matematika</label>
                    <textarea id="bidang_keahlian" name="bidang_keahlian" rows="3" placeholder="Jelaskan secara singkat latar belakang pendidikan atau bidang matematika yang ingin Anda ajarkan (misal: Aljabar Linear, Kalkulus Dasar, Olimpiade Matematika)..." required><?= htmlspecialchars($user['bidang_keahlian'] ?? '') ?></textarea>
                </div>
                <button type="submit" name="ajukan" class="btn btn-primary">Kirim Permohonan Pengajar</button>
                <a href="index.php" class="btn btn-secondary">Kembali ke Beranda</a>
            </form>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>