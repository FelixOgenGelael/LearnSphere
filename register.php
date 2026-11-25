<?php
require_once 'config/koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap    = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
    $username        = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email           = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password        = $_POST['password'];
    $konfirmasi      = $_POST['konfirmasi_password'];
    $pilihan_daftar  = ($_POST['pilihan_daftar'] === 'pengajar') ? 'pengajar' : 'user';
    $bidang_keahlian = mysqli_real_escape_string($conn, trim($_POST['bidang_keahlian'] ?? ''));

    if ($password !== $konfirmasi) {
        $error = "Konfirmasi password tidak cocok!";
    } elseif ($pilihan_daftar === 'pengajar' && empty($bidang_keahlian)) {
        $error = "Harap isi bidang keahlian atau latar belakang mengajar Anda.";
    } else {
        $cek = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username' OR email = '$email'");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Username atau Email sudah terdaftar. Gunakan yang lain!";
        } else {
            $hash            = password_hash($password, PASSWORD_DEFAULT);
            // Role awal tetap 'user' sampai Admin menyetujui permohonan pengajar
            $role_awal       = 'user';
            $status_pengajar = ($pilihan_daftar === 'pengajar') ? 'pending' : 'none';
            $keahlian_sql    = ($pilihan_daftar === 'pengajar') ? "'$bidang_keahlian'" : "NULL";

            $sql = "INSERT INTO users (nama_lengkap, username, email, password, role, status_pengajar, bidang_keahlian) 
                    VALUES ('$nama_lengkap', '$username', '$email', '$hash', '$role_awal', '$status_pengajar', $keahlian_sql)";
            if (mysqli_query($conn, $sql)) {
                $kode_pesan = ($pilihan_daftar === 'pengajar') ? 'registrasi_pengajar_pending' : 'registrasi_sukses';
                header("Location: login.php?pesan=" . $kode_pesan);
                exit;
            } else {
                $error = "Terjadi kesalahan saat menyimpan data: " . mysqli_error($conn);
            }
        }
    }
}

include 'includes/header.php';
?>

<section class="auth-wrapper" style="max-width: 500px;">
    <div class="card card-accent">
        <h2>Registrasi Akun LearnSphere</h2>
        <p style="font-size:14px; color:var(--text-secondary);">Buat akun untuk mengerjakan quiz evaluasi atau mengajukan diri sebagai pengajar matematika.</p>
        <hr style="border:none; border-top:1px solid var(--border-color); margin: 14px 0;">

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="" onsubmit="return validasiRegister()">
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Contoh: Budi Santoso" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Huruf dan angka tanpa spasi" required>
            </div>
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" id="email" name="email" placeholder="nama@email.com" required>
            </div>
            <div class="form-group">
                <label for="pilihan_daftar">Tujuan Pendaftaran</label>
                <select id="pilihan_daftar" name="pilihan_daftar" onchange="toggleFormKeahlian()">
                    <option value="user">User (Peserta Belajar & Quiz)</option>
                    <option value="pengajar">Pengajar (Memerlukan Persetujuan Admin)</option>
                </select>
            </div>

            <div class="form-group" id="grup-keahlian" style="display:none; background:var(--bg-subtle); padding:12px; border-radius:8px; border:1px solid var(--border-color);">
                <label for="bidang_keahlian">Bidang Keahlian / Latar Belakang Mengajar</label>
                <input type="text" id="bidang_keahlian" name="bidang_keahlian" placeholder="Contoh: Mahasiswa Pendidikan Matematika / Guru Aljabar">
                <small style="color:var(--text-muted); display:block; margin-top:6px;">
                    Catatan: Akun Anda akan langsung aktif sebagai <b>User</b> sambil menunggu persetujuan <b>Admin</b> untuk membuka fitur kelola materi & quiz.
                </small>
            </div>

            <div class="form-group">
                <label for="password">Password (Minimal 6 Karakter)</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="konfirmasi_password">Konfirmasi Password</label>
                <input type="password" id="konfirmasi_password" name="konfirmasi_password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Daftar Sekarang</button>
        </form>

        <p style="margin-top: 14px; text-align: center; font-size:14.5px;">
            Sudah memiliki akun? <a href="login.php"><b>Login di sini</b></a>
        </p>
    </div>
</section>

<script>
function toggleFormKeahlian() {
    const pilih = document.getElementById('pilihan_daftar').value;
    const grup  = document.getElementById('grup-keahlian');
    const input = document.getElementById('bidang_keahlian');
    if (pilih === 'pengajar') {
        grup.style.display = 'block';
        input.required = true;
    } else {
        grup.style.display = 'none';
        input.required = false;
    }
}
</script>

<?php include 'includes/footer.php'; ?>