<?php
require_once 'baglan.php';

// Kategorileri çek
$kategoriSorgu = $db->query("SELECT * FROM kategoriler ORDER BY sira ASC");
$kategoriler = $kategoriSorgu->fetchAll(PDO::FETCH_ASSOC);

// Ürünleri çek (Aktif olanlar)
$urunSorgu = $db->prepare("SELECT * FROM urunler WHERE aktif = 1 ORDER BY kategori_id ASC, id ASC");
$urunSorgu->execute();
$tumUrunler = $urunSorgu->fetchAll(PDO::FETCH_ASSOC);

// Ürünleri kategori ID'sine göre grupla
$urunlerByKategori = [];
foreach ($tumUrunler as $urun) {
    $urunlerByKategori[$urun['kategori_id']][] = $urun;
}
?>
<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'media',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        'antigravity-dark': '#0f0f11',
                        'antigravity-card': '#18181b',
                        'antigravity-accent': '#3b82f6',
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #0f0f11;
        }

        ::-webkit-scrollbar-thumb {
            background: #3f3f46;
            border-radius: 3px;
        }

        /* Hide scrollbar for category nav but keep functionality */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-antigravity-dark text-slate-200 antialiased min-h-screen pb-12">

    <!-- Header / Categories Sticky Nav -->
    <header class="sticky top-0 z-50 bg-antigravity-dark/95 backdrop-blur-md border-b border-white/5 shadow-lg">
        <div class="max-w-md mx-auto px-4 py-4">
            <h1 class="text-xl font-bold text-center tracking-wide text-white mb-4">M E N U</h1>

            <!-- Category Navigation -->
            <nav class="flex overflow-x-auto no-scrollbar gap-3 pb-2 snap-x">
                <?php foreach ($kategoriler as $kategori): ?>
                    <a href="#kategori-<?= $kategori['id'] ?>"
                        class="snap-start shrink-0 px-4 py-2 rounded-full bg-white/5 border border-white/10 text-sm font-medium hover:bg-white/10 hover:border-white/20 transition-all active:scale-95">
                        <?= htmlspecialchars($kategori['kategori_adi']) ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-md mx-auto px-4 py-6 space-y-10">

        <?php foreach ($kategoriler as $kategori): ?>
            <?php
            $katId = $kategori['id'];
            if (!isset($urunlerByKategori[$katId]))
                continue; // Bu kategoride ürün yoksa atla
            ?>

            <!-- Category Section -->
            <section id="kategori-<?= $katId ?>" class="scroll-mt-36">
                <div class="flex items-center gap-4 mb-6">
                    <h2 class="text-2xl font-bold text-white tracking-tight">
                        <?= htmlspecialchars($kategori['kategori_adi']) ?>
                    </h2>
                    <div class="h-px flex-1 bg-gradient-to-r from-white/20 to-transparent"></div>
                </div>

                <div class="grid gap-4">
                    <?php foreach ($urunlerByKategori[$katId] as $urun): ?>
                        <!-- Product Card -->
                        <div
                            class="group relative bg-antigravity-card rounded-2xl p-3 border border-white/5 hover:border-white/10 transition-all hover:bg-white/[0.02] flex gap-4 overflow-hidden">

                            <!-- Image -->
                            <div class="w-24 h-24 shrink-0 rounded-xl bg-white/5 overflow-hidden relative">
                                <?php if (!empty($urun['gorsel_yolu'])): ?>
                                    <img src="assets/images/<?= htmlspecialchars($urun['gorsel_yolu']) ?>"
                                        alt="<?= htmlspecialchars($urun['urun_adi']) ?>" loading="lazy"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                <?php else: ?>
                                    <!-- Placeholder Icon -->
                                    <div class="flex items-center justify-center w-full h-full text-white/20">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Content -->
                            <div class="flex flex-col justify-between flex-1 py-1">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-100 leading-tight mb-1">
                                        <?= htmlspecialchars($urun['urun_adi']) ?>
                                    </h3>
                                    <p class="text-sm text-gray-400 line-clamp-2 leading-snug">
                                        <?= htmlspecialchars($urun['aciklama']) ?>
                                    </p>
                                </div>
                                <div class="flex items-center justify-end">
                                    <span class="text-antigravity-accent font-semibold text-lg tracking-wide">
                                        <?= number_format($urun['fiyat'], 2) ?> ₺
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>

    </main>

    <!-- Footer -->
    <footer class="max-w-md mx-auto px-6 py-8 text-center border-t border-white/5 mt-8">
        <p class="text-xs text-gray-600 tracking-widest uppercase">Powered by Antigravity</p>
    </footer>

</body>

</html>