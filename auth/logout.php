<?php
session_start();

// Kullanıcı ID'sini özellikle sil
unset($_SESSION['kullanici_id']);
unset($_SESSION['kullanici_adi']);

// Tüm session değişkenlerini sil
$_SESSION = array();

// Session cookie'sini de sil (Tam güvenlik için)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Session'ı yok et
session_destroy();
header("Location: login.php");
exit;
?>