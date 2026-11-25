<?php
// Sinkronisasi role & status pengajar dari database secara real-time
function syncSessionUser($conn) {
    if (isset($_SESSION['user_id'])) {
        $uid = (int)$_SESSION['user_id'];
        $res = mysqli_query($conn, "SELECT nama_lengkap, role, status_pengajar FROM users WHERE id = $uid");
        if ($row = mysqli_fetch_assoc($res)) {
            $_SESSION['nama_lengkap']    = $row['nama_lengkap'];
            $_SESSION['role']            = $row['role'];
            $_SESSION['status_pengajar'] = $row['status_pengajar'];
        } else {
            session_destroy();
            header("Location: login.php");
            exit;
        }
    }
}

// Mengecek apakah user memiliki hak kelola konten (Pengajar terverifikasi atau Admin)
function isPengajarOrAdmin() {
    return isset($_SESSION['role']) && in_array($_SESSION['role'], ['pengajar', 'admin']);
}

// Proteksi halaman wajib login (misal: pengerjaan Quiz & pengajuan Pengajar)
function wajibLogin($pesan = 'wajib_login') {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php?pesan=" . urlencode($pesan));
        exit;
    }
}

// Proteksi khusus halaman manajemen materi & quiz (Pengajar disetujui atau Admin)
function wajibPengajar() {
    if (!isPengajarOrAdmin()) {
        header("Location: login.php?pesan=khusus_pengajar");
        exit;
    }
}

// Proteksi khusus halaman Panel Admin
function wajibAdmin() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: login.php?pesan=khusus_admin");
        exit;
    }
}

// Konversi skor ke huruf mutu
function hitungGrade($skor) {
    if ($skor >= 81) return 'A';
    if ($skor >= 61) return 'B';
    if ($skor >= 41) return 'C';
    if ($skor >= 21) return 'D';
    return 'E';
}

// Upload foto atau video opsional
function uploadMedia($fileInput, $folderTujuan, $ekstensiDiizinkan) {
    if (!isset($fileInput) || $fileInput['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($fileInput['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $ekstensi = strtolower(pathinfo($fileInput['name'], PATHINFO_EXTENSION));
    if (!in_array($ekstensi, $ekstensiDiizinkan)) {
        return false;
    }

    if (!is_dir($folderTujuan)) {
        mkdir($folderTujuan, 0777, true);
    }

    $namaBaru   = uniqid('math_') . '.' . $ekstensi;
    $pathTujuan = rtrim($folderTujuan, '/') . '/' . $namaBaru;

    return move_uploaded_file($fileInput['tmp_name'], $pathTujuan) ? $pathTujuan : false;
}

// Konversi link YouTube biasa ke embed
function formatYoutubeEmbed($url) {
    if (empty($url)) return '';
    if (strpos($url, 'watch?v=') !== false) {
        $parts = parse_url($url);
        parse_str($parts['query'] ?? '', $query);
        if (!empty($query['v'])) {
            return 'https://www.youtube.com/embed/' . htmlspecialchars($query['v']);
        }
    } elseif (strpos($url, 'youtu.be/') !== false) {
        $id = basename(parse_url($url, PHP_URL_PATH));
        return 'https://www.youtube.com/embed/' . htmlspecialchars($id);
    }
    return htmlspecialchars($url);
}
?>