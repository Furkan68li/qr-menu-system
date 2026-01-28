<?php
require 'config/baglan.php';
try {
    $db->exec("TRUNCATE TABLE ziyaretler");
    echo "Ziyaretçi verileri başarıyla sıfırlandı.";
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}
?>