<?php
require 'config/baglan.php';
try {
    $stmt = $db->query("SELECT tarih, kaynak FROM ziyaretler WHERE kaynak='qr' ORDER BY tarih DESC LIMIT 10");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($results as $row) {
        echo $row['tarih'] . " - " . $row['kaynak'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>