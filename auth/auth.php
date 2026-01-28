<?php
// Oturum başlatılmamışsa başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// GÜVENLİK: Tarayıcı önbelleklemesini engelle (Çıkış yaptıktan sonra geri tuşuyla sayfayı görmeyi engeller)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Tarih geçmişte

// Kullanıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['kullanici_id'])) {
    // Çağrılan dosyanın konumuna göre doğru login yolunu bul
    $redirectPath = 'auth/login.php';
    if (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false) {
        $redirectPath = '../auth/login.php';
    } elseif (strpos($_SERVER['SCRIPT_NAME'], '/auth/') !== false) {
        $redirectPath = 'login.php';
    }

    header("Location: " . $redirectPath);
    exit;
}
?>