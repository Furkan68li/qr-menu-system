<?php
// Veritabanı bağlantı bilgileri
$host = 'localhost';
$dbname = 'qr_menu';
$username = 'root'; // Varsayılan XAMPP/WAMP kullanıcısı
$password = '';     // Varsayılan şifre genellikle boştur

try {
    // PDO ile veritabanına bağlanma
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    // Hata modunu exception olarak ayarlama
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Varsayılan fetch modunu associative array olarak ayarlama
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // echo "Bağlantı başarılı"; // Test için açılabilir
} catch (PDOException $e) {
    // Bağlantı hatası durumunda kullanıcıya gösterilecek mesaj
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>