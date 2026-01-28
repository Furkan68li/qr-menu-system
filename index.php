<?php
// Hata raporlamayı aç
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/baglan.php';

session_start();

// Dil Kontrolü
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Varsayılan Dil
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'tr';
}

$lang = $_SESSION['lang'];

// Sabit Metinler (Dil desteği için)
$texts = [
    'tr' => [
        'title' => 'QR Menü',
        'slogan' => 'Lezzet Dünyası',
        'search_placeholder' => 'Canınız ne çekiyor?',
        'no_results' => 'Aradığınız ürün bulunamadı.',
        'currency' => '₺',
        'about' => 'Hakkımızda',
        'address' => 'Adres',
        'phone' => 'Telefon',
        'hours' => 'Çalışma Saatleri',
        'get_directions' => 'Yol Tarifi',
        'call_now' => 'Hemen Ara'
    ],
    'en' => [
        'title' => 'QR Menu',
        'slogan' => 'World of Flavors',
        'search_placeholder' => 'What are you craving?',
        'no_results' => 'No products found.',
        'currency' => '$',
        'about' => 'About Us',
        'address' => 'Address',
        'phone' => 'Phone',
        'hours' => 'Opening Hours',
        'get_directions' => 'Get Directions',
        'call_now' => 'Call Now'
    ]
];

try {
    // Ayarları Çek
    $ayarSorgu = $db->query("SELECT * FROM ayarlar LIMIT 1");
    $ayar = $ayarSorgu->fetch(PDO::FETCH_ASSOC);

    // Eğer ayar yoksa varsayılanları kullan (Hata önleme)
    if (!$ayar) {
        $ayar = [
            'site_baslik' => 'QR Menü',
            'slogan' => 'Lezzet Dünyası',
            'telefon' => '',
            'adres' => '',
            'wifi_sifre' => '',
            'instagram' => '',
            'google_maps' => ''
        ];
    }
} catch (PDOException $e) {
    die("Ayarlar yüklenemedi: " . $e->getMessage());
}

// Restoran Bilgileri (Veritabanından)
$restaurant_info = [
    'name' => $ayar['site_baslik'],
    'address' => $ayar['adres'],
    'phone' => $ayar['telefon'],
    'hours' => '09:00 - 23:00', // Bu da veritabanına eklenebilir istendiğinde
    'maps_link' => $ayar['google_maps'],
    'instagram' => '@' . str_replace('@', '', $ayar['instagram']),
    'wifi_pass' => $ayar['wifi_sifre']
];

$t = $texts[$lang];

try {
    // Kategorileri çek (Seçilen dile göre)
    $katCol = ($lang == 'en') ? 'kategori_adi_en AS kategori_adi' : 'kategori_adi';
    $kategoriSorgu = $db->query("SELECT id, $katCol FROM kategoriler ORDER BY sira ASC");
    $kategoriler = $kategoriSorgu->fetchAll(PDO::FETCH_ASSOC);

    // Ürünleri çek (Seçilen dile göre)
    $urunAdCol = ($lang == 'en') ? 'urun_adi_en AS urun_adi' : 'urun_adi';
    $aciklamaCol = ($lang == 'en') ? 'aciklama_en AS aciklama' : 'aciklama';

    $urunSorgu = $db->prepare("SELECT id, kategori_id, fiyat, gorsel_yolu, $urunAdCol, $aciklamaCol FROM urunler WHERE aktif = 1 ORDER BY kategori_id ASC, id ASC");
    $urunSorgu->execute();
    $tumUrunler = $urunSorgu->fetchAll(PDO::FETCH_ASSOC);

    // Ürünleri kategori ID'sine göre grupla
    $urunlerByKategori = [];
    foreach ($tumUrunler as $urun) {
        $urunlerByKategori[$urun['kategori_id']][] = $urun;
    }
} catch (PDOException $e) {
    die("Veritabanı Hatası: " . $e->getMessage());
} catch (PDOException $e) {
    die("Veritabanı Hatası: " . $e->getMessage());
}

// Ziyaret Takibi (Gerçek)
try {
    // Kaynak belirle (qr veya direct)
    $source = isset($_GET['src']) && $_GET['src'] == 'qr' ? 'qr' : 'direct';

    // IP adresini anonimleştir (GDPR uyumlu olması için hash'le)
    $ip = $_SERVER['REMOTE_ADDR'];
    $ipHash = hash('sha256', $ip . date('Y-m-d')); // Günlük unique ziyaretçi için

    // Aynı IP'den bugün bu kaynaktan giriş var mı? (Tekrar saymayı engellemek için basit kontrol)
    // Gerçek bir senaryoda session veya cookie de kullanılabilir ama IP hash yeterli.
    $checkStmt = $db->prepare("SELECT COUNT(*) FROM ziyaretler WHERE ip_hash = ? AND DATE(tarih) = CURDATE() AND kaynak = ?");
    $checkStmt->execute([$ipHash, $source]);
    $exists = $checkStmt->fetchColumn();

    if ($exists == 0) {
        $logStmt = $db->prepare("INSERT INTO ziyaretler (ip_hash, kaynak) VALUES (?, ?)");
        $logStmt->execute([$ipHash, $source]);
    }
} catch (Exception $e) {
    // Loglama hatası kullanıcı deneyimini bozmamalı
}
?>
<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Menü</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Inter"', 'sans-serif'],
                        display: ['"Space Grotesk"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            dark: '#0a0a0a',
                            card: '#171717',
                            primary: '#ca8a04',
                            secondary: '#eab308',
                            accent: '#f59e0b',
                            text: '#f3f4f6',
                            muted: '#a3a3a3'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #0a0a0a;
            color: #f3f4f6;
        }

        .nav-pill.active {
            background: #ca8a04;
            color: #000;
        }

        .glass-card {
            background: #171717;
            border: 1px solid #333;
        }
    </style>
</head>

<body class="selection:bg-brand-primary selection:text-white pb-20">

    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay"
        class="fixed inset-0 bg-black/80 z-[60] hidden transition-opacity duration-300 opacity-0 backdrop-blur-sm">
    </div>

    <!-- Sidebar -->
    <aside id="sidebar"
        class="fixed top-0 left-0 h-full w-[85%] max-w-sm bg-[#111] z-[70] transform -translate-x-full transition-transform duration-300 ease-out border-r border-white/10 shadow-2xl overflow-y-auto">
        <div class="p-6 relative">
            <!-- Close Button -->
            <button id="closeSidebar"
                class="absolute top-6 right-6 p-2 text-gray-400 hover:text-white bg-white/5 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Sidebar Header -->
            <div class="mb-10 mt-2">
                <div
                    class="w-16 h-16 bg-gradient-to-tr from-brand-primary to-brand-secondary rounded-2xl flex items-center justify-center mb-4 shadow-lg shadow-brand-primary/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-black" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white"><?= $restaurant_info['name'] ?></h2>
                <p class="text-sm text-gray-400 mt-1"><?= $t['about'] ?></p>
            </div>

            <!-- Info Sections -->
            <div class="space-y-8">
                <!-- Hours -->
                <div class="flex gap-4">
                    <div
                        class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center shrink-0 text-brand-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white mb-1"><?= $t['hours'] ?></h3>
                        <p class="text-gray-400 text-sm"><?= $restaurant_info['hours'] ?></p>
                        <p class="text-xs text-brand-primary mt-1">Şu an Açık</p>
                    </div>
                </div>

                <!-- Address -->
                <div class="flex gap-4">
                    <div
                        class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center shrink-0 text-brand-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white mb-1"><?= $t['address'] ?></h3>
                        <p class="text-gray-400 text-sm leading-relaxed mb-2"><?= $restaurant_info['address'] ?></p>
                        <a href="<?= $restaurant_info['maps_link'] ?>" target="_blank"
                            class="inline-flex items-center text-xs font-bold text-brand-primary hover:text-brand-secondary">
                            <?= $t['get_directions'] ?> &rarr;
                        </a>
                    </div>
                </div>

                <!-- Phone -->
                <div class="flex gap-4">
                    <div
                        class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center shrink-0 text-brand-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white mb-1"><?= $t['phone'] ?></h3>
                        <p class="text-gray-400 text-sm mb-2"><?= $restaurant_info['phone'] ?></p>
                        <a href="tel:<?= $restaurant_info['phone'] ?>"
                            class="inline-flex items-center px-3 py-1.5 rounded-lg bg-white/10 text-xs font-bold text-white hover:bg-brand-primary hover:text-black transition-colors">
                            <?= $t['call_now'] ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="mt-12 pt-6 border-t border-white/10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">WiFi Password</p>
                        <p class="text-sm font-mono text-brand-accent"><?= $restaurant_info['wifi_pass'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Hero -->
    <header class="pt-8 pb-6 px-6 text-center relative">
        <!-- Sidebar Toggle -->
        <button id="openSidebar"
            class="absolute top-6 left-6 p-2 bg-white/5 border border-white/10 rounded-full text-white hover:bg-white/10 active:scale-95 transition-all z-20">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
            </svg>
        </button>

        <!-- Language Switcher -->
        <div class="absolute top-6 right-6 flex gap-2 z-20">
            <a href="?lang=tr"
                class="flex items-center justify-center w-8 h-8 rounded-full border border-white/20 transition-all <?= $lang == 'tr' ? 'bg-brand-primary text-black font-bold' : 'bg-white/5 text-gray-400 hover:bg-white/10' ?>">
                TR
            </a>
            <a href="?lang=en"
                class="flex items-center justify-center w-8 h-8 rounded-full border border-white/20 transition-all <?= $lang == 'en' ? 'bg-brand-primary text-black font-bold' : 'bg-white/5 text-gray-400 hover:bg-white/10' ?>">
                EN
            </a>
        </div>

        <h1 class="text-4xl font-display font-bold text-white mb-2">
            <?= htmlspecialchars($ayar['site_baslik'] ?? $t['title']) ?>
        </h1>
        <p class="text-brand-muted text-sm mb-6"><?= htmlspecialchars($ayar['slogan'] ?? $t['slogan']) ?></p>

        <!-- Search Box -->
        <div class="relative max-w-md mx-auto">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-brand-primary" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <input type="text" id="searchInput" placeholder="<?= $t['search_placeholder'] ?>"
                class="block w-full pl-10 pr-3 py-3 border border-white/10 rounded-xl leading-5 bg-white/5 text-gray-300 placeholder-gray-500 focus:outline-none focus:bg-white/10 focus:ring-1 focus:ring-brand-primary focus:border-brand-primary sm:text-sm transition-all duration-200">
        </div>
    </header>

    <!-- Nav -->
    <nav class="sticky top-0 z-50 bg-brand-dark/95 backdrop-blur-md py-3 px-4 border-b border-white/10 shadow-lg">
        <div class="flex overflow-x-auto gap-2 snap-x pb-2 hide-scrollbar" id="categoryNav">
            <?php foreach ($kategoriler as $index => $kategori): ?>
                <a href="#kategori-<?= $kategori['id'] ?>"
                    class="nav-pill snap-start shrink-0 px-4 py-2 rounded-full text-xs font-bold uppercase transition-all border border-white/10 bg-white/5 text-gray-400 whitespace-nowrap">
                    <?= htmlspecialchars($kategori['kategori_adi']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>

    <!-- Content -->
    <main class="px-4 py-8 space-y-10 max-w-2xl mx-auto min-h-screen" id="menuContent">
        <div id="noResults" class="hidden text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <p class="text-gray-400 text-lg">Aradığınız ürün bulunamadı.</p>
        </div>

        <?php foreach ($kategoriler as $kategori): ?>
            <?php
            $katId = $kategori['id'];
            if (!isset($urunlerByKategori[$katId]))
                continue;
            ?>

            <section id="kategori-<?= $katId ?>" class="menu-section scroll-mt-32">
                <h2
                    class="section-title text-xl font-display font-bold text-white mb-4 pl-3 border-l-4 border-brand-primary">
                    <?= htmlspecialchars($kategori['kategori_adi']) ?>
                </h2>

                <div class="grid gap-4">
                    <?php foreach ($urunlerByKategori[$katId] as $urun): ?>
                        <div class="glass-card product-card rounded-2xl p-3 flex gap-4 overflow-hidden group">
                            <!-- Image -->
                            <div class="w-24 h-24 shrink-0 rounded-xl bg-gray-800 overflow-hidden relative">
                                <?php if (!empty($urun['gorsel_yolu'])): ?>
                                    <img src="assets/images/<?= htmlspecialchars($urun['gorsel_yolu']) ?>"
                                        class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                                <?php else: ?>
                                    <div class="flex items-center justify-center w-full h-full bg-gray-900 text-white/10">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Text -->
                            <div class="flex flex-col flex-1 justify-between py-1">
                                <div>
                                    <h3 class="product-name font-bold text-white text-lg leading-tight mb-1">
                                        <?= htmlspecialchars($urun['urun_adi']) ?>
                                    </h3>
                                    <p class="product-desc text-xs text-gray-400 line-clamp-2">
                                        <?= htmlspecialchars($urun['aciklama']) ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="text-brand-secondary font-bold text-lg drop-shadow-sm"><?= number_format($urun['fiyat'], 2) ?>
                                        ₺</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </main>

    <script>
        // Sidebar Logic
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const openSidebarBtn = document.getElementById('openSidebar');
        const closeSidebarBtn = document.getElementById('closeSidebar');

        function toggleSidebar(show) {
            if (show) {
                sidebarOverlay.classList.remove('hidden');
                // Force triggering reflow
                void sidebarOverlay.offsetWidth;
                sidebarOverlay.classList.remove('opacity-0');
                sidebar.classList.remove('-translate-x-full');
                document.body.style.overflow = 'hidden'; // Prevent scrolling
            } else {
                sidebarOverlay.classList.add('opacity-0');
                sidebar.classList.add('-translate-x-full');
                document.body.style.overflow = '';

                setTimeout(() => {
                    sidebarOverlay.classList.add('hidden');
                }, 300);
            }
        }

        openSidebarBtn.addEventListener('click', () => toggleSidebar(true));
        closeSidebarBtn.addEventListener('click', () => toggleSidebar(false));
        sidebarOverlay.addEventListener('click', () => toggleSidebar(false));

        // Smooth Scroll Fix
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const target = document.getElementById(targetId);
                if (target) {
                    const headerOffset = 80;
                    const elementPosition = target.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                    window.scrollTo({ top: offsetPosition, behavior: "smooth" });

                    document.querySelectorAll('.nav-pill').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });

        // Search Functionality
        const searchInput = document.getElementById('searchInput');
        const sections = document.querySelectorAll('.menu-section');
        const noResults = document.getElementById('noResults');

        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase().trim();
            let hasGlobalResults = false;

            sections.forEach(section => {
                const cards = section.querySelectorAll('.product-card');
                let hasVisibleCards = false;

                cards.forEach(card => {
                    const name = card.querySelector('.product-name').textContent.toLowerCase();
                    const desc = card.querySelector('.product-desc').textContent.toLowerCase();

                    if (name.includes(searchTerm) || desc.includes(searchTerm)) {
                        card.style.display = 'flex';
                        hasVisibleCards = true;
                        hasGlobalResults = true;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Toggle Section Visibility
                if (hasVisibleCards) {
                    section.classList.remove('hidden');
                } else {
                    section.classList.add('hidden');
                }
            });

            // Toggle No Results Message
            if (!hasGlobalResults) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        });
    </script>
</body>

</html>