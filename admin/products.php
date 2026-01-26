<?php
require_once '../baglan.php';
require_once 'auth.php';

// Ürünleri ve kategori bilgilerini çek
$stmt = $db->query("
    SELECT u.*, k.kategori_adi 
    FROM urunler u 
    LEFT JOIN kategoriler k ON u.kategori_id = k.id 
    ORDER BY u.id DESC
");
$urunler = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün Yönetimi - QR Menü</title>
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
                    <span class="text-xl font-bold text-white tracking-wide border-l border-white/10 pl-4">Ürün
                        Yönetimi</span>
                </div>
                <div>
                    <a href="product_add.php"
                        class="bg-antigravity-accent text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors text-sm font-medium">Yeni
                        Ürün Ekle</a>
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
                        <th class="px-6 py-4 font-medium">Görsel</th>
                        <th class="px-6 py-4 font-medium">Ürün Adı</th>
                        <th class="px-6 py-4 font-medium">Kategori</th>
                        <th class="px-6 py-4 font-medium">Fiyat</th>
                        <th class="px-6 py-4 font-medium">Durum</th>
                        <th class="px-6 py-4 font-medium text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php foreach ($urunler as $urun): ?>
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <?php if ($urun['gorsel_yolu']): ?>
                                    <img src="../assets/images/<?= htmlspecialchars($urun['gorsel_yolu']) ?>" alt=""
                                        class="w-12 h-12 rounded object-cover">
                                <?php else: ?>
                                    <div
                                        class="w-12 h-12 bg-white/10 rounded flex items-center justify-center text-xs text-gray-500">
                                        Yok</div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 font-medium text-white">
                                <?= htmlspecialchars($urun['urun_adi']) ?>
                                <div class="text-xs text-gray-500 truncate max-w-xs">
                                    <?= htmlspecialchars($urun['aciklama']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-400">
                                <?= htmlspecialchars($urun['kategori_adi']) ?>
                            </td>
                            <td class="px-6 py-4 font-medium text-antigravity-accent">
                                <?= number_format($urun['fiyat'], 2) ?> ₺
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($urun['aktif']): ?>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/10 text-green-500">Aktif</span>
                                <?php else: ?>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/10 text-red-500">Pasif</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="product_edit.php?id=<?= $urun['id'] ?>"
                                    class="text-blue-400 hover:text-blue-300 text-sm font-medium">Düzenle</a>
                                <a href="product_delete.php?id=<?= $urun['id'] ?>"
                                    onclick="return confirm('Bu ürünü silmek istediğinize emin misiniz?')"
                                    class="text-red-400 hover:text-red-300 text-sm font-medium">Sil</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($urunler)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Henüz ürün bulunmuyor.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>