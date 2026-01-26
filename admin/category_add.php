<?php
require_once '../baglan.php';
require_once '../auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori_adi = trim($_POST['kategori_adi']);
    $sira = (int) $_POST['sira'];

    if (empty($kategori_adi)) {
        $error = "Kategori adı boş olamaz.";
    } else {
        $stmt = $db->prepare("INSERT INTO kategoriler (kategori_adi, sira) VALUES (?, ?)");
        if ($stmt->execute([$kategori_adi, $sira])) {
            header("Location: categories.php?msg=Kategori başarıyla eklendi");
            exit;
        } else {
            $error = "Bir hata oluştu.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Ekle - QR Menü</title>
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

    <div class="max-w-xl mx-auto px-4 py-12">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">Yeni Kategori Ekle</h1>
            <a href="categories.php" class="text-gray-400 hover:text-white transition-colors">İptal</a>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-lg mb-6">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="bg-antigravity-card border border-white/5 rounded-xl p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Kategori Adı</label>
                <input type="text" name="kategori_adi" required
                    class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-antigravity-accent transition-colors placeholder-gray-600"
                    placeholder="Örn: Tatlılar">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Sıralama</label>
                <input type="number" name="sira" value="0"
                    class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-antigravity-accent transition-colors">
                <p class="text-xs text-gray-600 mt-1">Düşük rakamlar önce gösterilir.</p>
            </div>

            <button type="submit"
                class="w-full bg-antigravity-accent text-white font-medium py-3 rounded-lg hover:bg-blue-600 transition-colors">
                Kategoriyi Kaydet
            </button>
        </form>
    </div>

</body>

</html>