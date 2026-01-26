<?php
require_once '../baglan.php';
require_once 'auth.php';

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
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $fileInfo = pathinfo($_FILES['gorsel']['name']);
        $extension = strtolower($fileInfo['extension']);

        if (!in_array($extension, $allowedExtensions)) {
            $error = "Sadece JPG, PNG ve WEBP formatında dosyalar yüklenebilir.";
        } else {
            // Klasör kontrolü ve oluşturma
            $uploadDir = '../assets/images/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Benzersiz isim oluştur
            $newFileName = uniqid() . '.' . $extension;
            
            if (move_uploaded_file($_FILES['gorsel']['tmp_name'], $uploadDir . $newFileName)) {
                $gorsel_yolu = $newFileName;
            } else {
                $error = "Görsel yüklenirken bir hata oluştu: Klasör yazma iznini kontrol edin.";
            }
        }
    }

    if (!$error) {
        $stmt = $db->prepare("INSERT INTO urunler (kategori_id, urun_adi, aciklama, fiyat, gorsel_yolu, aktif) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$kategori_id, $urun_adi, $aciklama, $fiyat, $gorsel_yolu, $aktif])) {
            header("Location: products.php?msg=Ürün başarıyla eklendi");
            exit;
        } else {
            $error = "Veritabanı hatası: Kayıt oluşturulamadı.";
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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        'antigravity-dark': '#09090b',
                        'antigravity-card': '#18181b',
                        'antigravity-accent': '#6366f1',
                        'antigravity-accent-hover': '#4f46e5',
                    },
                    backgroundImage: {
                        'glass': 'linear-gradient(145deg, rgba(24, 24, 27, 0.6) 0%, rgba(24, 24, 27, 0.3) 100%)',
                    }
                }
            }
        }
    </script>
    <style>
        .glass-card {
            background: rgba(24, 24, 27, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>

<body class="bg-antigravity-dark text-slate-200 min-h-screen relative selection:bg-antigravity-accent selection:text-white">

    <!-- Ambient Background -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-purple-600/10 rounded-full blur-[100px]"></div>
    </div>

    <div class="max-w-2xl mx-auto px-4 py-12 animate-fade-in-up">
        <div class="mb-8 flex items-center justify-between">
            <div>
                 <h1 class="text-2xl font-bold text-white tracking-tight">Yeni Ürün Ekle</h1>
                 <p class="text-gray-400 text-sm mt-1">Yeni bir ürünü menüye ekleyin.</p>
            </div>
            <a href="products.php" class="text-gray-400 hover:text-white transition-colors flex items-center gap-1 text-sm bg-white/5 px-3 py-2 rounded-lg hover:bg-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                İptal
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="glass-card border-red-500/20 bg-red-500/5 text-red-400 p-4 rounded-xl mb-6 flex items-center gap-3">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="glass-card rounded-2xl p-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Ürün Adı</label>
                    <input type="text" name="urun_adi" required
                        class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-antigravity-accent focus:ring-1 focus:ring-antigravity-accent transition-all placeholder-gray-600"
                        placeholder="Örn: Hamburger">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Kategori</label>
                    <select name="kategori_id" required
                        class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-antigravity-accent focus:ring-1 focus:ring-antigravity-accent transition-all [&>option]:bg-antigravity-card">
                        <?php foreach ($kategoriler as $kat): ?>
                            <option value="<?= $kat['id'] ?>">
                                <?= htmlspecialchars($kat['kategori_adi']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Fiyat (TL)</label>
                    <input type="number" step="0.01" name="fiyat" required
                        class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-antigravity-accent focus:ring-1 focus:ring-antigravity-accent transition-all"
                        placeholder="0.00">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Açıklama</label>
                    <textarea name="aciklama" rows="3"
                        class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-antigravity-accent focus:ring-1 focus:ring-antigravity-accent transition-all placeholder-gray-600"
                        placeholder="Ürün içeriği ve detayları..."></textarea>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Ürün Görseli</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="dropzone-file"
                            class="flex flex-col items-center justify-center w-full h-32 border-2 border-white/10 border-dashed rounded-xl cursor-pointer hover:bg-white/5 hover:border-antigravity-accent/50 transition-all group">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-gray-500 group-hover:text-antigravity-accent transition-colors" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                </svg>
                                <p class="text-sm text-gray-400 group-hover:text-white transition-colors"><span class="font-semibold">Yüklemek için tıklayın</span> veya sürükleyin</p>
                                <p class="text-xs text-gray-500 mt-1">JPG, PNG, WEBP (Maks. 2MB)</p>
                            </div>
                            <input id="dropzone-file" name="gorsel" type="file" class="hidden"
                                accept=".jpg,.jpeg,.png,.webp" />
                        </label>
                    </div>
                </div>

                <div class="col-span-2">
                    <label class="flex items-center gap-3 cursor-pointer group p-2 rounded-lg hover:bg-white/5 transition-colors w-full sm:w-auto">
                        <div class="relative flex items-center">
                            <input type="checkbox" name="aktif" value="1" checked class="peer sr-only">
                            <div class="w-6 h-6 border-2 border-gray-600 rounded bg-transparent peer-checked:bg-antigravity-accent peer-checked:border-antigravity-accent transition-all flex items-center justify-center">
                                <svg class="w-4 h-4 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <span class="text-sm font-medium text-gray-300 group-hover:text-white transition-colors">Ürün Satışta (Aktif)</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end pt-4 border-t border-white/5">
                <button type="submit"
                    class="bg-antigravity-accent hover:bg-antigravity-accent-hover text-white font-semibold py-3 px-8 rounded-xl transition-all shadow-lg shadow-indigo-500/20 active:scale-[0.98] flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Ürünü Kaydet</span>
                </button>
            </div>
        </form>
    </div>

</body>

</html>