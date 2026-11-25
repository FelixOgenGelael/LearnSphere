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
            $_SESSION['user_id']         = $row['id'];
            $_SESSION['nama_lengkap']    = $row['nama_lengkap'];
            $_SESSION['username']        = $row['username'];
            $_SESSION['role']            = $row['role'];
            $_SESSION['status_pengajar'] = $row['status_pengajar'];

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
    <div class="card card-accent">
        <h2>Login ke LearnSphere</h2>
        <p style="font-size:14px; color:var(--text-secondary);">Masuk sebagai User, Pengajar, atau Admin.</p>
        <hr style="border:none; border-top:1px solid var(--border-color); margin: 14px 0;">

        <?php if (isset($_GET['pesan'])): ?>
            <?php if ($_GET['pesan'] === 'registrasi_sukses'): ?>
                <div class="alert alert-success">Registrasi berhasil! Silakan login dengan akun Anda.</div>
            <?php elseif ($_GET['pesan'] === 'registrasi_pengajar_pending'): ?>
                <div class="alert alert-success">
                    Pendaftaran berhasil! Akun Anda aktif sebagai <b>User</b> sambil menunggu persetujuan <b>Admin</b> untuk mendapatkan hak akses <b>Pengajar</b>.
                </div>
            <?php elseif ($_GET['pesan'] === 'wajib_login_quiz'): ?>
                <div class="alert alert-error">
                    <b>Login Diperlukan:</b> Silakan masuk terlebih dahulu untuk mengerjakan Quiz. Tanpa login, Anda tetap dapat membaca materi dan mengerjakan <b>Contoh Soal</b>.
                </div>
            <?php elseif ($_GET['pesan'] === 'khusus_pengajar'): ?>
                <div class="alert alert-error">Halaman tersebut hanya dapat diakses oleh <b>Pengajar</b> yang telah disetujui Admin atau oleh <b>Admin</b>.</div>
            <?php elseif ($_GET['pesan'] === 'khusus_admin'): ?>
                <div class="alert alert-error">Halaman tersebut hanya dapat diakses oleh <b>Admin</b>.</div>
            <?php else: ?>
                <div class="alert alert-error">Silakan login terlebih dahulu untuk melanjutkan.</div>
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

        <p style="margin-top: 14px; text-align: center; font-size:14.5px;">
            Belum punya akun? <a href="register.php"><b>Daftar gratis di sini</b></a>
        </p>

        <div style="margin-top: 16px; padding: 12px; background: var(--bg-subtle); border-radius: 8px; font-size: 13px; color:var(--text-secondary);">
            <b>Akun Demo Pengujian (Password: <code>123456</code>):</b><br>
            &bull; Admin: <code>admin1</code><br>
            &bull; Pengajar (Disetujui): <code>pengajar1</code><br>
            &bull; User Biasa: <code>user1</code><br>
            &bull; User (Menunggu Persetujuan Pengajar): <code>user2</code>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>