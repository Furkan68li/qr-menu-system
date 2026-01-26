<?php
require_once '../baglan.php';
require_once '../auth.php';

// Kategorileri çek (Dropdown için)
$kategoriler = $db->query("SELECT * FROM kategoriler ORDER BY sira ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori_id = (int) $_POST['kategori_id'];
    $urun_adi = trim($_POST['urun_adi']);
    $aciklama = trim($_POST['aciklama']);
    $fiyat = (float) $_POST['fiyat'];
    $aktif = isset($_POST['aktif']) ? 1 : 0;

    $gorsel_yolu = null;
    $error = null;

    if (empty($urun_adi) || $fiyat <= 0) {
        $error = "Ürün adı ve geçerli bir fiyat girilmelidir.";
    }
    // Görsel yükleme işlemi
    elseif (isset($_FILES['gorsel']) && $_FILES['gorsel']['error'] === UPLOAD_ERR_OK) {
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $fileInfo = pathinfo($_FILES['gorsel']['name']);
        $extension = strtolower($fileInfo['extension']);

        if (!in_array($extension, $allowedExtensions)) {
            $error = "Sadece JPG ve PNG formatında dosyalar yüklenebilir.";
        } else {
            // Benzersiz isim oluştur
            $newFileName = uniqid() . '.' . $extension;
            $uploadDir = '../assets/images/';

            if (move_uploaded_file($_FILES['gorsel']['tmp_name'], $uploadDir . $newFileName)) {
                $gorsel_yolu = $newFileName;
            } else {
                $error = "Görsel yüklenirken bir hata oluştu.";
            }
        }
    }

    if (!$error) {
        $stmt = $db->prepare("INSERT INTO urunler (kategori_id, urun_adi, aciklama, fiyat, gorsel_yolu, aktif) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$kategori_id, $urun_adi, $aciklama, $fiyat, $gorsel_yolu, $aktif])) {
            header("Location: products.php?msg=Ürün başarıyla eklendi");
            exit;
        } else {
            $error = "Veritabanı hatası.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün Ekle - QR Menü</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        'antigravity-dark': '#0f0f11',
                        'antigravity-card': '#18181b',
                        'antigravity-accent': '#3b82f6',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-antigravity-dark text-slate-200 min-h-screen">

    <div class="max-w-2xl mx-auto px-4 py-12">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">Yeni Ürün Ekle</h1>
            <a href="products.php" class="text-gray-400 hover:text-white transition-colors">İptal</a>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-lg mb-6">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data"
            class="bg-antigravity-card border border-white/5 rounded-xl p-6 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Ürün Adı</label>
                    <input type="text" name="urun_adi" required
                        class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-antigravity-accent transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Kategori</label>
                    <select name="kategori_id" required
                        class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-antigravity-accent transition-colors">
                        <?php foreach ($kategoriler as $kat): ?>
                            <option value="<?= $kat['id'] ?>" class="bg-antigravity-card">
                                <?= htmlspecialchars($kat['kategori_adi']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Fiyat (TL)</label>
                    <input type="number" step="0.01" name="fiyat" required
                        class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-antigravity-accent transition-colors">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Açıklama</label>
                    <textarea name="aciklama" rows="3"
                        class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-antigravity-accent transition-colors"></textarea>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Ürün Görseli</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="dropzone-file"
                            class="flex flex-col items-center justify-center w-full h-32 border-2 border-white/10 border-dashed rounded-lg cursor-pointer hover:bg-white/5 transition-colors">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                </svg>
                                <p class="text-sm text-gray-400"><span class="font-semibold">Yüklemek için
                                        tıklayın</span> veya sürükleyin</p>
                                <p class="text-xs text-gray-500">JPG veya PNG (Maks. 2MB)</p>
                            </div>
                            <input id="dropzone-file" name="gorsel" type="file" class="hidden"
                                accept=".jpg,.jpeg,.png" />
                        </label>
                    </div>
                </div>

                <div class="col-span-2 flex items-center">
                    <input type="checkbox" name="aktif" id="aktif" checked
                        class="w-4 h-4 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-600 focus:ring-2">
                    <label for="aktif" class="ml-2 text-sm font-medium text-gray-300">Ürün satışta (Aktif)</label>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-antigravity-accent text-white font-medium py-3 rounded-lg hover:bg-blue-600 transition-colors">
                Ürünü Kaydet
            </button>
        </form>
    </div>

</body>

</html>