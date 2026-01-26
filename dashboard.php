<?php
require_once 'baglan.php';
require_once 'auth.php';

// İstatistikleri çek
$catCount = $db->query("SELECT COUNT(*) FROM kategoriler")->fetchColumn();
$prodCount = $db->query("SELECT COUNT(*) FROM urunler")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - QR Menü</title>
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

    <!-- Top Navigation -->
    <nav class="bg-antigravity-card border-b border-white/5 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4">
                    <span class="text-xl font-bold text-white tracking-wide">Yönetim Paneli</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-400">Hoşgeldin,
                        <?= htmlspecialchars($_SESSION['kullanici_adi']) ?>
                    </span>
                    <a href="logout.php"
                        class="text-sm text-red-500 hover:text-red-400 font-medium transition-colors">Çıkış Yap</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Welcome Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-white">Genel Bakış</h2>
            <p class="text-gray-400 mt-1">İşletmenizin durumu hakkında özet bilgiler.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Stat Card 1 -->
            <div class="bg-antigravity-card border border-white/5 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400">Toplam Kategori</p>
                        <p class="text-3xl font-bold text-white mt-2">
                            <?= $catCount ?>
                        </p>
                    </div>
                    <div class="bg-blue-500/10 p-3 rounded-lg text-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Stat Card 2 -->
            <div class="bg-antigravity-card border border-white/5 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400">Toplam Ürün</p>
                        <p class="text-3xl font-bold text-white mt-2">
                            <?= $prodCount ?>
                        </p>
                    </div>
                    <div class="bg-emerald-500/10 p-3 rounded-lg text-emerald-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="admin/categories.php"
                class="group block p-6 bg-antigravity-card border border-white/5 rounded-xl hover:border-white/20 transition-all">
                <h3 class="text-lg font-semibold text-white group-hover:text-antigravity-accent transition-colors">
                    Kategorileri Yönet &rarr;</h3>
                <p class="text-gray-400 text-sm mt-2">Menü kategorilerini ekleyin, düzenleyin veya sıralayın.</p>
            </a>

            <a href="admin/products.php"
                class="group block p-6 bg-antigravity-card border border-white/5 rounded-xl hover:border-white/20 transition-all">
                <h3 class="text-lg font-semibold text-white group-hover:text-antigravity-accent transition-colors">
                    Ürünleri Yönet &rarr;</h3>
                <p class="text-gray-400 text-sm mt-2">Menüdeki ürünleri, fiyatları ve görselleri güncelleyin.</p>
            </a>
        </div>

    </div>
</body>

</html>