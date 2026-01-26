<?php
require_once '../baglan.php';
require_once '../auth.php';

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$id = (int) $_GET['id'];
$stmt = $db->prepare("SELECT * FROM urunler WHERE id = ?");
$stmt->execute([$id]);
$urun = $stmt->fetch();

if (!$urun) {
    header("Location: products.php?err=Ürün bulunamadı");
    exit;
}

// Kategorileri çek
$kategoriler = $db->query("SELECT * FROM kategoriler ORDER BY sira ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori_id = (int) $_POST['kategori_id'];
    $urun_adi = trim($_POST['urun_adi']);
    $aciklama = trim($_POST['aciklama']);
    $fiyat = (float) $_POST['fiyat'];
    $aktif = isset($_POST['aktif']) ? 1 : 0;

    $error = null;
    $gorsel_guncellendi = false;
    $yeni_gorsel_adi = $urun['gorsel_yolu']; // Varsayılan eski görsel

    if (empty($urun_adi) || $fiyat <= 0) {
        $error = "Ürün adı ve geçerli bir fiyat girilmelidir.";
    }
    // Yeni görsel yüklendiyse
    elseif (isset($_FILES['gorsel']) && $_FILES['gorsel']['error'] === UPLOAD_ERR_OK) {
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $fileInfo = pathinfo($_FILES['gorsel']['name']);
        $extension = strtolower($fileInfo['extension']);

        if (!in_array($extension, $allowedExtensions)) {
            $error = "Sadece JPG ve PNG formatında dosyalar yüklenebilir.";
        } else {
            // Yeni dosya adı
            $newFileName = uniqid() . '.' . $extension;
            $uploadDir = '../assets/images/';

            if (move_uploaded_file($_FILES['gorsel']['tmp_name'], $uploadDir . $newFileName)) {
                $yeni_gorsel_adi = $newFileName;
                $gorsel_guncellendi = true;

                // Eski görseli sil (Opsiyonel ama temizlik için iyi)
                if ($urun['gorsel_yolu'] && file_exists($uploadDir . $urun['gorsel_yolu'])) {
                    unlink($uploadDir . $urun['gorsel_yolu']);
                }
            } else {
                $error = "Görsel yüklenirken bir hata oluştu.";
            }
        }
    }

    if (!$error) {
        $sql = "UPDATE urunler SET kategori_id=?, urun_adi=?, aciklama=?, fiyat=?, aktif=?, gorsel_yolu=? WHERE id=?";
        $stmt = $db->prepare($sql);
        if ($stmt->execute([$kategori_id, $urun_adi, $aciklama, $fiyat, $aktif, $yeni_gorsel_adi, $id])) {
            header("Location: products.php?msg=Ürün başarıyla güncellendi");
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
    <title>Ürün Düzenle - QR Menü</title>
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
            <h1 class="text-2xl font-bold text-white">Ürün Düzenle</h1>
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
                    <input type="text" name="urun_adi" required value="<?= htmlspecialchars($urun['urun_adi']) ?>"
                        class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-antigravity-accent transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Kategori</label>
                    <select name="kategori_id" required
                        class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-antigravity-accent transition-colors">
                        <?php foreach ($kategoriler as $kat): ?>
                            <option value="<?= $kat['id'] ?>" <?= $kat['id'] == $urun['kategori_id'] ? 'selected' : '' ?>
                                class="bg-antigravity-card">
                                <?= htmlspecialchars($kat['kategori_adi']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Fiyat (TL)</label>
                    <input type="number" step="0.01" name="fiyat" required value="<?= $urun['fiyat'] ?>"
                        class="w-full bg-black/20 border border-blue-500/50 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-antigravity-accent transition-colors bg-blue-500/5">
                    <p class="text-xs text-blue-400 mt-1">Hızlı fiyat güncelleme alanı</p>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Açıklama</label>
                    <textarea name="aciklama" rows="3"
                        class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-antigravity-accent transition-colors"><?= htmlspecialchars($urun['aciklama']) ?></textarea>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Ürün Görseli</label>
                    <div class="flex items-start gap-4">
                        <?php if ($urun['gorsel_yolu']): ?>
                            <div class="w-24 h-24 shrink-0 rounded-lg border border-white/10 overflow-hidden">
                                <img src="../assets/images/<?= htmlspecialchars($urun['gorsel_yolu']) ?>"
                                    class="w-full h-full object-cover">
                            </div>
                        <?php endif; ?>

                        <div class="flex-1">
                            <input type="file" name="gorsel" accept=".jpg,.jpeg,.png" class="block w-full text-sm text-gray-400
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-antigravity-accent file:text-white
                                      hover:file:bg-blue-600">
                            <p class="text-xs text-gray-500 mt-2">Değiştirmek istemiyorsanız boş bırakın.</p>
                        </div>
                    </div>
                </div>

                <div class="col-span-2 flex items-center">
                    <input type="checkbox" name="aktif" id="aktif" <?= $urun['aktif'] ? 'checked' : '' ?> class="w-4 h-4
                    text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-600 focus:ring-2">
                    <label for="aktif" class="ml-2 text-sm font-medium text-gray-300">Ürün satışta (Aktif)</label>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-antigravity-accent text-white font-medium py-3 rounded-lg hover:bg-blue-600 transition-colors">
                Değişiklikleri Kaydet
            </button>
        </form>
    </div>

</body>

</html>