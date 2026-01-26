<?php
// Oturum başlatılmamışsa başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kullanıcı giriş yapmış mı kontrol et
// 'kullanici_id' session değişkeni giriş yapıldığında oluşturulacak
if (!isset($_SESSION['kullanici_id'])) {
    // Giriş yapılmamışsa login sayfasına yönlendir
    header("Location: login.php");
    exit;
}
?>