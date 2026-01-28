<?php
require_once '../config/baglan.php';

$sql = "
CREATE TABLE IF NOT EXISTS ziyaretler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_hash VARCHAR(64) NOT NULL,
    tarih DATETIME DEFAULT CURRENT_TIMESTAMP,
    kaynak ENUM('direct', 'qr') DEFAULT 'direct',
    INDEX idx_tarih (tarih),
    INDEX idx_kaynak (kaynak)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

try {
    $db->exec($sql);
    echo "Tablo oluşturuldu.<br>";

    // Demo verisi ekle (Eğer tablo boşsa)
    $count = $db->query("SELECT COUNT(*) FROM ziyaretler")->fetchColumn();
    if ($count == 0) {
        echo "Demo verileri ekleniyor...<br>";

        // Son 7 gün için rastgele veri
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));

            // Her gün için rastgele 10-50 ziyaret
            $dailyVisits = rand(10, 50);

            for ($j = 0; $j < $dailyVisits; $j++) {
                // Rastgele saat
                $hour = rand(9, 23);
                $minute = rand(0, 59);
                $seconds = rand(0, 59);
                $fullDate = "$date $hour:$minute:$seconds";

                // %30 ihtimalle QR, %70 direct
                $source = (rand(1, 100) <= 30) ? 'qr' : 'direct';
                $ip = md5(uniqid()); // Fake IP hash

                $db->exec("INSERT INTO ziyaretler (ip_hash, tarih, kaynak) VALUES ('$ip', '$fullDate', '$source')");
            }
        }
        echo "Demo verileri eklendi.";
    }

} catch (PDOException $e) {
    echo "Hata: " . $e->getMessage();
}
?>