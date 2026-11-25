<?php
require_once 'config/koneksi.php';
require_once 'config/functions.php';
wajibAdmin();

$admin_id = (int)$_SESSION['user_id'];
$notif    = '';

// Proses Persetujuan / Penolakan Pengajar
if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $target_id = (int)$_GET['id'];
    $aksi      = $_GET['aksi'];

    if ($target_id !== $admin_id) {
        if ($aksi === 'setujui') {
            mysqli_query($conn, "UPDATE users SET role = 'pengajar', status_pengajar = 'approved' WHERE id = $target_id");
            $notif = "Permohonan disetujui! Pengguna kini memiliki hak akses Pengajar.";
        } elseif ($aksi === 'tolak') {
            mysqli_query($conn, "UPDATE users SET role = 'user', status_pengajar = 'rejected' WHERE id = $target_id");
            $notif = "Permohonan pengajar telah ditolak.";
        } elseif ($aksi === 'hapus') {
            mysqli_query($conn, "DELETE FROM users WHERE id = $target_id");
            $notif = "Akun pengguna berhasil dihapus.";
        }
    }
}

// Ubah Role Secara Langsung oleh Admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ubah_role'])) {
    $target_id = (int)$_POST['user_id'];
    $role_baru = in_array($_POST['role_baru'], ['user', 'pengajar', 'admin']) ? $_POST['role_baru'] : 'user';

    if ($target_id !== $admin_id) {
        $status_baru = ($role_baru === 'pengajar' || $role_baru === 'admin') ? 'approved' : 'none';
        mysqli_query($conn, "UPDATE users SET role = '$role_baru', status_pengajar = '$status_baru' WHERE id = $target_id");
        $notif = "Perubahan role pengguna berhasil disimpan.";
    }
}

$pending_list = mysqli_query($conn, "SELECT * FROM users WHERE status_pengajar = 'pending' ORDER BY created_at ASC");
$all_users    = mysqli_query($conn, "SELECT * FROM users ORDER BY FIELD(role, 'admin', 'pengajar', 'user'), created_at DESC");

include 'includes/header.php';
?>

<section class="card card-accent">
    <span class="badge badge-accent">Administrator Control</span>
    <h1 style="margin-top:8px;">Panel Admin - Persetujuan Pengajar & Manajemen Role</h1>
    <p style="color:var(--text-secondary);">Verifikasi calon pengajar sebelum memberikan izin pengelolaan materi dan quiz, serta kelola hak akses pengguna.</p>
</section>

<?php if ($notif): ?>
    <div class="alert alert-success"><?= htmlspecialchars($notif) ?></div>
<?php endif; ?>

<!-- DAFTAR PERMOHONAN PENGAJAR MENUNGGU PERSETUJUAN -->
<section class="card">
    <h2>Permohonan Pengajar Menunggu Persetujuan (<?= mysqli_num_rows($pending_list) ?>)</h2>
    <?php if (mysqli_num_rows($pending_list) === 0): ?>
        <p style="color:var(--text-muted); margin-top:8px; font-size:14.5px;">Tidak ada permohonan pengajar yang tertunda saat ini.</p>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>Username & Email</th>
                        <th>Bidang Keahlian / Keterangan</th>
                        <th>Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($p = mysqli_fetch_assoc($pending_list)): ?>
                        <tr>
                            <td><b><?= htmlspecialchars($p['nama_lengkap']) ?></b></td>
                            <td>
                                <code><?= htmlspecialchars($p['username']) ?></code><br>
                                <small style="color:var(--text-muted);"><?= htmlspecialchars($p['email']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($p['bidang_keahlian'] ?? '-') ?></td>
                            <td>
                                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                    <a href="admin_users.php?aksi=setujui&id=<?= $p['id'] ?>" class="btn btn-success" onclick="return confirm('Setujui <?= htmlspecialchars($p['nama_lengkap']) ?> menjadi Pengajar?')">
                                        Setujui Pengajar
                                    </a>
                                    <a href="admin_users.php?aksi=tolak&id=<?= $p['id'] ?>" class="btn btn-danger" onclick="return confirm('Tolak permohonan ini?')">
                                        Tolak
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<!-- DAFTAR SELURUH PENGGUNA TERDAFTAR -->
<section class="card">
    <h2>Daftar Seluruh Pengguna Terdaftar</h2>
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Role Aktif</th>
                    <th>Status Pengajar</th>
                    <th>Ubah Role & Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($u = mysqli_fetch_assoc($all_users)): ?>
                    <tr>
                        <td>
                            <b><?= htmlspecialchars($u['nama_lengkap']) ?></b>
                            <?php if ((int)$u['id'] === $admin_id): ?>
                                <span class="badge badge-accent" style="margin-left:4px;">Anda</span>
                            <?php endif; ?>
                        </td>
                        <td><code><?= htmlspecialchars($u['username']) ?></code></td>
                        <td><span class="badge badge-accent"><?= strtoupper($u['role']) ?></span></td>
                        <td>
                            <?php
                            $st = $u['status_pengajar'];
                            if ($st === 'pending') echo '<span class="badge" style="background:var(--warning-bg);">Pending</span>';
                            elseif ($st === 'approved') echo '<span class="badge badge-accent">Disetujui</span>';
                            elseif ($st === 'rejected') echo '<span class="badge">Ditolak</span>';
                            else echo '<small style="color:var(--text-muted);">-</small>';
                            ?>
                        </td>
                        <td>
                            <?php if ((int)$u['id'] === $admin_id): ?>
                                <small style="color:var(--text-muted);">Akun Admin Aktif</small>
                            <?php else: ?>
                                <form method="POST" action="" style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <select name="role_baru" style="width:auto; padding:5px 10px; font-size:13px;">
                                        <option value="user" <?= $u['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                        <option value="pengajar" <?= $u['role'] === 'pengajar' ? 'selected' : '' ?>>Pengajar</option>
                                        <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <button type="submit" name="ubah_role" class="btn btn-secondary" style="padding:5px 10px; font-size:13px;">Simpan</button>
                                    <a href="admin_users.php?aksi=hapus&id=<?= $u['id'] ?>" class="btn btn-danger" onclick="return confirm('Hapus akun pengguna ini?')">Hapus</a>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include 'includes/footer.php'; ?>