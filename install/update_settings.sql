
-- Ayarlar tablosu
CREATE TABLE IF NOT EXISTS ayarlar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    site_baslik VARCHAR(100) DEFAULT 'QR Menü',
    slogan VARCHAR(255) DEFAULT 'Lezzet Dünyası',
    telefon VARCHAR(20),
    adres TEXT,
    wifi_sifre VARCHAR(50),
    instagram VARCHAR(50),
    google_maps TEXT,
    guncelleme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan Ayarları Ekle (Eğer boşsa)
INSERT INTO ayarlar (site_baslik, slogan, telefon, adres, wifi_sifre, instagram, google_maps) 
SELECT 'QR Lezzet Durağı', 'Lezzetin Tek Adresi', '+90 555 123 45 67', 'Bağdat Cd. No:123, İstanbul', 'lezzet123', 'qrmenu', 'https://maps.google.com'
WHERE NOT EXISTS (SELECT * FROM ayarlar);
