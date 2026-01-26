-- Veritabanı oluşturma (Eğer yoksa)
CREATE DATABASE IF NOT EXISTS qr_menu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qr_menu;

-- Kullanıcılar tablosu (Admin girişi için)
CREATE TABLE IF NOT EXISTS kullanicilar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kullanici_adi VARCHAR(50) NOT NULL UNIQUE,
    sifre VARCHAR(255) NOT NULL, -- password_hash ile hashlenmiş şifreler için
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kategoriler tablosu
CREATE TABLE IF NOT EXISTS kategoriler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_adi VARCHAR(100) NOT NULL,
    sira INT DEFAULT 0, -- Menüdeki sıralama için
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sira (sira) -- Sıralama performansı için index
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ürünler tablosu
CREATE TABLE IF NOT EXISTS urunler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT NOT NULL,
    urun_adi VARCHAR(150) NOT NULL,
    aciklama TEXT,
    fiyat DECIMAL(10, 2) NOT NULL,
    gorsel_yolu VARCHAR(255),
    aktif TINYINT(1) DEFAULT 1, -- Ürünün görünürlülüğü için
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategoriler(id) ON DELETE CASCADE,
    INDEX idx_kategori_id (kategori_id) -- Kategoriye göre listeleme performansı için
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan Admin Kullanıcısı (Kullanıcı adı: admin, Şifre: 123)
-- Hash: 123 için oluşturulmuştur
INSERT INTO kullanicilar (kullanici_adi, sifre) VALUES ('admin', '$2y$10$ese.aN9L5d6fDt7uZbaeGOToy6oWohd5mEZk8xRCb6jm2eB9TjX6W');
