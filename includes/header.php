<?php
if (!isset($basePath)) {
    $basePath = '';
}
if (!isset($pageTitle)) {
    $pageTitle = 'Yönetim Paneli';
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= htmlspecialchars($pageTitle) ?> - QR Menü
    </title>
    <!-- Anti-Cache Meta Tags -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        'glass-border': 'rgba(255, 255, 255, 0.08)',
                    },
                    animation: {
                        'blob': 'blob 10s infinite',
                        'fade-in': 'fadeIn 0.5s ease-out forwards',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #020617;
            color: #e2e8f0;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 15% 50%, rgba(59, 130, 246, 0.08), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(147, 51, 234, 0.08), transparent 25%);
            z-index: -1;
            pointer-events: none;
        }

        .glass-panel {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-link {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
            border-radius: 0 4px 4px 0;
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .sidebar-link:hover::before,
        .sidebar-link.active::before {
            transform: scaleY(1);
        }

        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.03);
            color: white;
        }
    </style>
</head>

<body class="flex h-screen overflow-hidden antialiased selection:bg-blue-500/30">

    <!-- Sidebar -->
    <aside class="w-64 glass-panel border-r border-white/5 hidden md:flex flex-col z-20">
        <!-- Logo -->
        <div class="h-20 flex items-center px-8 border-b border-white/5">
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <span class="text-lg font-bold tracking-tight text-white">QR Menü</span>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 px-4">Yönetim</div>

            <a href="<?= $basePath ?>dashboard.php"
                class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-400 rounded-xl hover:bg-white/5 hover:text-white group <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:text-blue-400 transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                Genel Bakış
            </a>

            <a href="<?= $basePath ?>admin/categories.php"
                class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-400 rounded-xl hover:bg-white/5 hover:text-white group <?= strpos($_SERVER['PHP_SELF'], 'categories.php') !== false ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:text-purple-400 transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Kategoriler
            </a>

            <a href="<?= $basePath ?>admin/products.php"
                class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-400 rounded-xl hover:bg-white/5 hover:text-white group <?= strpos($_SERVER['PHP_SELF'], 'products.php') !== false ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:text-emerald-400 transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Ürünler
            </a>
            <!-- Divider & Settings -->
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 px-4 mt-8">Araçlar & Ayarlar
            </div>

            <a href="<?= $basePath ?>admin/qr_builder.php"
                class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-400 rounded-xl hover:bg-white/5 hover:text-white group <?= strpos($_SERVER['PHP_SELF'], 'qr_builder.php') !== false ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:text-pink-400 transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                QR Kod Oluştur
            </a>

            <a href="<?= $basePath ?>admin/settings.php"
                class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-400 rounded-xl hover:bg-white/5 hover:text-white group <?= strpos($_SERVER['PHP_SELF'], 'settings.php') !== false ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:text-amber-400 transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Site Ayarları
            </a>

            <a href="<?= $basePath ?>admin/profile.php"
                class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-400 rounded-xl hover:bg-white/5 hover:text-white group <?= strpos($_SERVER['PHP_SELF'], 'profile.php') !== false ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:text-cyan-400 transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Profil Ayarları
            </a>
        </nav>

        <!-- User Profile -->
        <div class="p-4 border-t border-white/5">
            <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/5">
                <div
                    class="w-10 h-10 rounded-full bg-gradient-to-tr from-gray-700 to-gray-600 flex items-center justify-center text-white font-bold text-sm">
                    <?= strtoupper(substr($_SESSION['kullanici_adi'] ?? 'A', 0, 1)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">
                        <?= htmlspecialchars($_SESSION['kullanici_adi'] ?? 'Admin') ?>
                    </p>
                    <a href="<?= $basePath ?>auth/logout.php"
                        class="text-xs text-red-400 hover:text-red-300 transition-colors">Çıkış Yap</a>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto relative z-10">
        <!-- Mobile Header -->
        <header
            class="md:hidden h-16 glass-panel border-b border-white/5 flex items-center justify-between px-4 sticky top-0 z-30">
            <span class="font-bold text-white">QR Menü</span>
            <button class="text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </header>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 min-h-full flex flex-col">
            <!-- Header Section -->
            <div class="flex items-center justify-between mb-8 animate-fade-in">
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-tight">
                        <?= htmlspecialchars($pageTitle) ?>
                    </h1>
                    <p class="text-gray-400 text-sm mt-1">Sistemi buradan yönetebilirsiniz.</p>
                </div>
                <a href="<?= $basePath ?>index.php" target="_blank"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600/10 text-blue-400 hover:bg-blue-600/20 hover:text-blue-300 transition-all border border-blue-500/20 text-sm font-medium">
                    <span>Menüyü Gör</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>

            <div class="animate-fade-in" style="animation-delay: 0.1s;">