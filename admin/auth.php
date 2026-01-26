<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kullanıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['kullanici_id'])) {
    // Admin klasöründen bir üst dizindeki login.php'ye yönlendir
    header("Location: ../login.php");
    exit;
}
?>
