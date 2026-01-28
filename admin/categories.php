<?php
require_once '../config/baglan.php';
require_once '../auth/auth.php';

// Kategorileri ve ürün sayılarını çek
$stmt = $db->query("
    SELECT k.*, COUNT(u.id) as urun_sayisi 
    FROM kategoriler k 
    LEFT JOIN urunler u ON k.id = u.kategori_id 
    GROUP BY k.id 
    ORDER BY k.sira ASC
");
$kategoriler = $stmt->fetchAll();

$pageTitle = 'Kategori Yönetimi';
$basePath = '../';
require_once '../includes/header.php';
?>

<?php if (isset($_GET['msg'])): ?>
    <div
        class="animate-fade-in bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl mb-6 flex items-center gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <?= htmlspecialchars($_GET['msg']) ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['err'])): ?>
    <div
        class="animate-fade-in bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl mb-6 flex items-center gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <?= htmlspecialchars($_GET['err']) ?>
    </div>
<?php endif; ?>

<!-- Action Bar -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div class="relative max-w-xs w-full">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <input type="text" placeholder="Kategori ara..."
            class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 transition-colors text-sm">
    </div>

    <a href="category_add.php"
        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl transition-all shadow-lg shadow-blue-500/25 text-sm font-medium group">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:rotate-90 transition-transform" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Yeni Kategori Ekle
    </a>
</div>

<!-- Categories Table -->
<div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-white/5 bg-white/5">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Sıra</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Kategori Adı</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Ürün Sayısı</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">
                        İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php foreach ($kategoriler as $kat): ?>
                    <tr class="group hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white/5 text-gray-400 font-medium text-xs border border-white/10">
                                <?= $kat['sira'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-white font-medium"><?= htmlspecialchars($kat['kategori_adi']) ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/10">
                                <?= $kat['urun_sayisi'] ?> Ürün
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="category_edit.php?id=<?= $kat['id'] ?>"
                                class="text-blue-400 hover:text-blue-300 transition-colors p-2 inline-block hover:bg-blue-500/10 rounded-lg"
                                title="Düzenle">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </a>
                            <a href="category_delete.php?id=<?= $kat['id'] ?>"
                                onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')"
                                class="text-red-400 hover:text-red-300 transition-colors p-2 inline-block hover:bg-red-500/10 rounded-lg"
                                title="Sil">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($kategoriler)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-600 mb-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                <p class="text-lg font-medium text-gray-400">Henüz kategori bulunmuyor</p>
                                <p class="text-sm mt-1">Yeni bir kategori ekleyerek başlayın.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
