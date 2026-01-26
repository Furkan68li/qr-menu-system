-- Kategoriler tablosuna İngilizce ad sütunu ekle
ALTER TABLE kategoriler ADD COLUMN IF NOT EXISTS kategori_adi_en VARCHAR(100);

-- Ürünler tablosuna İngilizce ad ve açıklama sütunları ekle
ALTER TABLE urunler ADD COLUMN IF NOT EXISTS urun_adi_en VARCHAR(150);
ALTER TABLE urunler ADD COLUMN IF NOT EXISTS aciklama_en TEXT;

-- Mevcut verileri kopyala (Hata almamak için)
UPDATE kategoriler SET kategori_adi_en = kategori_adi WHERE kategori_adi_en IS NULL;
UPDATE urunler SET urun_adi_en = urun_adi, aciklama_en = aciklama WHERE urun_adi_en IS NULL;
