<?php
require_once '../baglan.php';
require_once 'auth.php';

// Kategorileri ve ürün sayılarını çek
$stmt = $db->query("
    SELECT k.*, COUNT(u.id) as urun_sayisi 
    FROM kategoriler k 
    LEFT JOIN urunler u ON k.id = u.kategori_id 
    GROUP BY k.id 
    ORDER BY k.sira ASC
");
$kategoriler = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Yönetimi - QR Menü</title>
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

    <nav class="bg-antigravity-card border-b border-white/5 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="../dashboard.php" class="text-white hover:text-gray-300">&larr; Dashboard</a>
                    <span class="text-xl font-bold text-white tracking-wide border-l border-white/10 pl-4">Kategori
                        Yönetimi</span>
                </div>
                <div>
                    <a href="category_add.php"
                        class="bg-antigravity-accent text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors text-sm font-medium">Yeni
                        Kategori Ekle</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <?php if (isset($_GET['msg'])): ?>
            <div class="bg-green-500/10 border border-green-500/20 text-green-500 p-4 rounded-lg mb-6">
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['err'])): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-lg mb-6">
                <?= htmlspecialchars($_GET['err']) ?>
            </div>
        <?php endif; ?>

        <div class="bg-antigravity-card border border-white/5 rounded-xl overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-black/20 text-gray-400 text-sm uppercase">
                    <tr>
                        <th class="px-6 py-4 font-medium">Sıra</th>
                        <th class="px-6 py-4 font-medium">Kategori Adı</th>
                        <th class="px-6 py-4 font-medium">Ürün Sayısı</th>
                        <th class="px-6 py-4 font-medium text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php foreach ($kategoriler as $kat): ?>
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 text-gray-400">
                                <?= $kat['sira'] ?>
                            </td>
                            <td class="px-6 py-4 font-medium text-white">
                                <?= htmlspecialchars($kat['kategori_adi']) ?>
                            </td>
                            <td class="px-6 py-4 text-gray-400">
                                <?= $kat['urun_sayisi'] ?>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="category_edit.php?id=<?= $kat['id'] ?>"
                                    class="text-blue-400 hover:text-blue-300 text-sm font-medium">Düzenle</a>
                                <a href="category_delete.php?id=<?= $kat['id'] ?>"
                                    onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')"
                                    class="text-red-400 hover:text-red-300 text-sm font-medium">Sil</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($kategoriler)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Henüz kategori bulunmuyor.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>