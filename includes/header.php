<?php
require_once __DIR__ . '/../config/functions.php';

if (isset($conn)) {
    syncSessionUser($conn);
}
$halaman_aktif = basename($_SERVER['PHP_SELF']);

$jumlah_pending_admin = 0;
if (isset($conn) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $res_pend = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM users WHERE status_pengajar = 'pending'");
    if ($res_pend) {
        $jumlah_pending_admin = (int)mysqli_fetch_assoc($res_pend)['jml'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LearnSphere - Portal Belajar & Quiz Matematika Terbuka</title>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body>
    <header id="main-header">
        <div class="container header-flex">
            <div class="brand">
                <a href="index.php">
                    <span class="brand-icon">&Sigma;</span>
                    <div>
                        <strong>LearnSphere</strong>
                        <small style="display:block; line-height:1.1;">Open Mathematics Hub</small>
                    </div>
                </a>
            </div>

            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                <nav id="main-nav">
                    <ul>
                        <li><a href="index.php" class="<?= $halaman_aktif === 'index.php' ? 'active' : '' ?>">Beranda</a></li>
                        <li><a href="materi.php" class="<?= in_array($halaman_aktif, ['materi.php', 'materi_detail.php', 'modul_detail.php', 'materi_form.php', 'modul_form.php', 'contoh_soal_form.php']) ? 'active' : '' ?>">Materi</a></li>
                        <li><a href="quiz.php" class="<?= in_array($halaman_aktif, ['quiz.php', 'quiz_form.php', 'soal_form.php', 'kerjakan_quiz.php']) ? 'active' : '' ?>">Quiz</a></li>
                        <li><a href="cari.php" class="<?= $halaman_aktif === 'cari.php' ? 'active' : '' ?>">Cari</a></li>
                        <li><a href="hasil_quiz.php" class="<?= $halaman_aktif === 'hasil_quiz.php' ? 'active' : '' ?>">Peringkat</a></li>

                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>
                            <li>
                                <a href="ajukan_pengajar.php" class="<?= $halaman_aktif === 'ajukan_pengajar.php' ? 'active' : '' ?>">
                                    <?= ($_SESSION['status_pengajar'] ?? '') === 'pending' ? 'Status Pengajar (Pending)' : 'Ajukan Pengajar' ?>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <li>
                                <a href="admin_users.php" class="<?= $halaman_aktif === 'admin_users.php' ? 'active' : '' ?>">
                                    Panel Admin<?= $jumlah_pending_admin > 0 ? ' (' . $jumlah_pending_admin . ')' : '' ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>

                <div class="nav-actions">
                    <button type="button" id="btn-theme-toggle" class="btn-theme" onclick="toggleTheme()">Mode Gelap</button>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span class="user-pill"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?> (<?= ucfirst($_SESSION['role']) ?>)</span>
                        <a href="logout.php" class="btn btn-danger" onclick="return confirm('Keluar dari akun?')">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-secondary" style="padding:6px 12px;">Login</a>
                        <a href="register.php" class="btn btn-primary" style="padding:6px 12px;">Daftar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    <main class="container main-content">