<?php
require_once 'config/koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
    $username     = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email        = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password     = $_POST['password'];
    $konfirmasi   = $_POST['konfirmasi_password'];
    $role         = ($_POST['role'] === 'pengajar') ? 'pengajar' : 'siswa';

    if ($password !== $konfirmasi) {
        $error = "Konfirmasi password tidak sesuai!";
    } else {
        // Cek apakah username atau email sudah terdaftar (Modul 7)
        $cek = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username' OR email = '$email'");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Username atau Email sudah digunakan, silakan pilih yang lain!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (nama_lengkap, username, email, password, role) 
                      VALUES ('$nama_lengkap', '$username', '$email', '$hash', '$role')";
            if (mysqli_query($conn, $query)) {
                header("Location: login.php?pesan=registrasi_sukses");
                exit;
            } else {
                $error = "Gagal mendaftarkan akun: " . mysqli_error($conn);
            }
        }
    }
}

include 'includes/header.php';
?>

<section class="auth-wrapper">
    <div class="card card-highlight">
        <h2>Registrasi Akun LearnSphere</h2>
        <p><small>Bergabung untuk belajar materi terbuka dan mengikuti evaluasi interaktif.</small></p>
        <hr style="margin: 12px 0;">

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="" onsubmit="return validasiRegister()">
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Contoh: Siti Aminah" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Contoh: sitiaminah" required>
            </div>
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" id="email" name="email" placeholder="nama@email.com" required>
            </div>
            <div class="form-group">
                <label for="role">Daftar Sebagai</label>
                <select id="role" name="role">
                    <option value="siswa">Siswa / Peserta Belajar</option>
                    <option value="pengajar">Pengajar / Kontributor Materi & Quiz</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Password (minimal 6 karakter)</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="konfirmasi_password">Konfirmasi Password</label>
                <input type="password" id="konfirmasi_password" name="konfirmasi_password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Daftar Sekarang</button>
        </form>

        <p style="margin-top: 14px; text-align: center;">
            Sudah memiliki akun? <a href="login.php"><b>Login di sini</b></a>
        </p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>