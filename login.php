<?php
require_once 'config/koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            // Simpan data user ke dalam $_SESSION (Modul 7)
            $_SESSION['user_id']      = $row['id'];
            $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
            $_SESSION['username']     = $row['username'];
            $_SESSION['role']         = $row['role'];

            header("Location: index.php");
            exit;
        } else {
            $error = "Password yang Anda masukkan salah!";
        }
    } else {
        $error = "Username tidak ditemukan dalam sistem!";
    }
}

include 'includes/header.php';
?>

<section class="auth-wrapper">
    <div class="card card-highlight">
        <h2>Login ke LearnSphere</h2>
        <p><small>Masuk untuk mengakses materi pembelajaran dan quiz pilihan ganda.</small></p>
        <hr style="margin: 12px 0;">

        <?php if (isset($_GET['pesan'])): ?>
            <?php if ($_GET['pesan'] === 'registrasi_sukses'): ?>
                <div class="alert alert-success">Registrasi berhasil! Silakan login dengan akun Anda.</div>
            <?php elseif ($_GET['pesan'] === 'wajib_login'): ?>
                <div class="alert alert-error">Sesi tidak ditemukan. Silakan login terlebih dahulu.</div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="" onsubmit="return validasiLogin()">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Login</button>
        </form>

        <p style="margin-top: 14px; text-align: center;">
            Belum punya akun? <a href="register.php"><b>Buat akun baru</b></a>
        </p>

        <div style="margin-top: 16px; padding: 10px; background: #f1f5f9; border-radius: 6px; font-size: 13px;">
            <b>Akun Demo Pengujian:</b><br>
            • Pengajar: <code>pengajar1</code> / Pass: <code>123456</code><br>
            • Siswa: <code>siswa1</code> / Pass: <code>123456</code>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>