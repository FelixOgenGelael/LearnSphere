<?php
// Proteksi khusus halaman manajemen milik Pengajar
function wajibPengajar() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pengajar') {
        header("Location: login.php?pesan=khusus_pengajar");
        exit;
    }
}

// Konversi skor ke huruf mutu (Modul 4 & 5)
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