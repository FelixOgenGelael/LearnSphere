<?php
// Mengakhiri sesi pengguna (Modul 7 Percobaan 2)
session_start();
session_unset();
session_destroy();
header("Location: login.php");
exit;
?>