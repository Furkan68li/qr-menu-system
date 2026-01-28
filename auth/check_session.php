<?php
// Session'ı başlat ama yeni session oluşturma (zaten varsa kullan)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Cache'i engelle
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Oturum kontrolü
$response = ['status' => 'inactive'];
if (isset($_SESSION['kullanici_id'])) {
    $response['status'] = 'active';
}

echo json_encode($response);
?>