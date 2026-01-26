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
        .glass-nav {
             background: rgba(9, 9, 11, 0.8);
             backdrop-filter: blur(16px);
             -webkit-backdrop-filter: blur(16px);
             border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>

<body class="bg-antigravity-dark text-slate-200 min-h-screen selection:bg-antigravity-accent selection:text-white relative overflow-x-hidden">

    <!-- Ambient Background -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-purple-600/20 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-blue-600/20 rounded-full blur-[100px]"></div>
    </div>

    <!-- Top Navigation -->
    <nav class="glass-nav sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between h-16">
                <!-- Brand -->
                <div class="flex items-center gap-3">
                     <div class="bg-gradient-to-tr from-antigravity-accent to-purple-500 p-2 rounded-lg shadow-lg shadow-indigo-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                     </div>
                    <span class="text-xl font-bold text-white tracking-wide">Yönetim Paneli</span>
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-2 sm:gap-4 ml-auto">
                    <!-- Menu Link -->
                    <a href="index.php" target="_blank"
                       class="hidden sm:inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-antigravity-accent to-indigo-600 hover:from-indigo-500 hover:to-indigo-500 text-white text-sm font-medium rounded-lg transition-all shadow-lg shadow-indigo-500/20 hover:scale-105 active:scale-95 group">
                        <span>Menüye Git</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/80 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                    
                    <div class="h-6 w-px bg-white/10 hidden sm:block"></div>

                    <div class="flex items-center gap-3">
                        <div class="hidden md:flex flex-col items-end">
                            <span class="text-xs text-gray-400">Hoşgeldin,</span>
                            <span class="text-sm font-medium text-white leading-none"><?= htmlspecialchars($_SESSION['kullanici_adi']) ?></span>
                        </div>
                        <a href="logout.php"
                           class="flex items-center justify-center w-9 h-9 sm:w-auto sm:h-auto sm:px-3 sm:py-2 text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg transition-all" title="Çıkış Yap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span class="hidden sm:inline text-sm font-medium">Çıkış</span>
                        </a>
                    </div>
                </div>
            </div>
             <!-- Mobile Menu Button Row -->
             <div class="sm:hidden pb-3 border-t border-white/5 pt-3 flex justify-between items-center">
                 <span class="text-sm text-gray-400">Kullanıcı: <span class="text-white"><?= htmlspecialchars($_SESSION['kullanici_adi']) ?></span></span>
                  <a href="index.php" target="_blank" class="text-xs bg-antigravity-accent px-3 py-1.5 rounded-md text-white flex items-center gap-1 shadow-lg shadow-indigo-500/20">
                      Menüyü Gör <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                  </a>
             </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in-up">

        <!-- Welcome Section -->
        <div class="mb-10 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                 <h2 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-400">Genel Bakış</h2>
                 <p class="text-gray-400 mt-2">İşletme istatistikleriniz ve hızlı yönetim araçlarınız.</p>
            </div>
            <div class="text-sm text-gray-500 font-mono bg-white/5 py-1 px-3 rounded-full border border-white/5 shadow-inner">
                <?= date('d.m.Y') ?>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
            <!-- Stat Card 1 -->
            <div class="glass-card rounded-2xl p-6 relative group overflow-hidden transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/10 hover:-translate-y-1">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-blue-500 transform rotate-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div class="flex items-center gap-4 relative z-10">
                     <div class="p-3 bg-blue-500/20 text-blue-400 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                     </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Toplam Kategori</p>
                        <p class="text-4xl font-bold text-white mt-1 group-hover:scale-110 origin-left transition-transform duration-300"><?= $catCount ?></p>
                    </div>
                </div>
            </div>

            <!-- Stat Card 2 -->
            <div class="glass-card rounded-2xl p-6 relative group overflow-hidden transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/10 hover:-translate-y-1">
                 <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-emerald-500 transform -rotate-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="flex items-center gap-4 relative z-10">
                     <div class="p-3 bg-emerald-500/20 text-emerald-400 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                     </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Toplam Ürün</p>
                        <p class="text-4xl font-bold text-white mt-1 group-hover:scale-110 origin-left transition-transform duration-300"><?= $prodCount ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Title -->
        <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
            <span class="w-1 h-6 bg-antigravity-accent rounded-full"></span>
            Hızlı İşlemler
        </h3>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="admin/categories.php"
                class="group relative glass-card p-8 rounded-2xl hover:border-antigravity-accent/50 transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-antigravity-accent/0 to-antigravity-accent/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative z-10 flex items-start justify-between">
                     <div>
                        <h3 class="text-xl font-bold text-white group-hover:text-antigravity-accent transition-colors mb-2">
                            Kategorileri Yönet</h3>
                        <p class="text-gray-400 text-sm max-w-sm">Menü kategorilerini ekleyin, düzenleyin veya sıralamasını değiştirin.</p>
                     </div>
                     <div class="bg-white/5 p-3 rounded-lg group-hover:bg-antigravity-accent group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                     </div>
                </div>
            </a>

            <a href="admin/products.php"
                class="group relative glass-card p-8 rounded-2xl hover:border-emerald-500/50 transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/0 to-emerald-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative z-10 flex items-start justify-between">
                     <div>
                        <h3 class="text-xl font-bold text-white group-hover:text-emerald-400 transition-colors mb-2">
                            Ürünleri Yönet</h3>
                        <p class="text-gray-400 text-sm max-w-sm">Menünüzdeki ürünleri, fiyatları, görselleri ve durumlarını güncelleyin.</p>
                     </div>
                     <div class="bg-white/5 p-3 rounded-lg group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                     </div>
                </div>
            </a>
        </div>

    </div>
</body>
</html>