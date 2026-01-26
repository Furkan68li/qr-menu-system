<?php
require_once '../baglan.php';
require_once '../auth.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Önce görsel ismini al
    $stmt = $db->prepare("SELECT gorsel_yolu FROM urunler WHERE id = ?");
    $stmt->execute([$id]);
    $urun = $stmt->fetch();

    $stmt = $db->prepare("DELETE FROM urunler WHERE id = ?");
    if ($stmt->execute([$id])) {
        // Veritabanından silindiyse görseli de diskten sil
        if ($urun && $urun['gorsel_yolu']) {
            $filePath = '../assets/images/' . $urun['gorsel_yolu'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        header("Location: products.php?msg=Ürün silindi");
    } else {
        header("Location: products.php?err=Silme işlemi başarısız");
    }
} else {
    header("Location: products.php");
}
exit;
