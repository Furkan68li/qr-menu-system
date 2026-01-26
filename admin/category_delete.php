<?php
require_once '../baglan.php';
require_once '../auth.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    if ($id <= 0) {
        header("Location: categories.php?err=Geçersiz ID");
        exit;
    }

    // Kategorinin varlığını kontrol et
    $check = $db->prepare("SELECT id FROM kategoriler WHERE id = ?");
    $check->execute([$id]);
    
    if ($check->fetch()) {
        // Eğer veritabanında CASCADE DELETE ayarlıysa, kategori silinince ürünler de silinir.
        // Ayarlı değilse önce ürünleri silmek gerekebilir.
        // Burada CASCADE olduğunu varsayıyoruz (önceki koddan çıkarımla), ancak hata yönetimi ekliyoruz.
        $stmt = $db->prepare("DELETE FROM kategoriler WHERE id = ?");
        
        try {
            if ($stmt->execute([$id])) {
                header("Location: categories.php?msg=Kategori başarıyla silindi");
            } else {
                header("Location: categories.php?err=Kategori silinemedi");
            }
        } catch (PDOException $e) {
            // Muhtemel FK kısıtlaması hatası
            header("Location: categories.php?err=Kategori silinemedi: Bu kategoriye bağlı ürünler olabilir ve otomatik silme kapalı olabilir.");
        }
    } else {
        header("Location: categories.php?err=Kategori bulunamadı");
    }
} else {
    header("Location: categories.php");
}
exit;
