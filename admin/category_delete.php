<?php
require_once '../config/baglan.php';
require_once '../auth/auth.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Güvenlik: Kategoriye bağlı ürün varsa silmeyi engelleme veya uyarı verme stratejisi izleyebiliriz.
    // Şimdilik CASCADE yapısı veritabanında varsa direkt silinir, yoksa hata verebilir.
    // Ancak veritabanı şemasında ON DELETE CASCADE tanımlı:
    // FOREIGN KEY (kategori_id) REFERENCES kategoriler(id) ON DELETE CASCADE
    // Bu yüzden kategoriyi silince ürünler de silinecek.

    $stmt = $db->prepare("DELETE FROM kategoriler WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: categories.php?msg=Kategori silindi");
    } else {
        header("Location: categories.php?err=Silme işlemi başarısız");
    }
} else {
    header("Location: categories.php");
}
exit;
