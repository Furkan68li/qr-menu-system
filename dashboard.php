<?php
require_once 'config/baglan.php';
require_once 'auth/auth.php';

// İstatistikleri çek
// İstatistikleri çek
$catCount = $db->query("SELECT COUNT(*) FROM kategoriler")->fetchColumn();
$prodCount = $db->query("SELECT COUNT(*) FROM urunler")->fetchColumn();

// Ziyaret İstatistikleri
$totalViews = $db->query("SELECT COUNT(*) FROM ziyaretler")->fetchColumn();
$qrScans = $db->query("SELECT COUNT(*) FROM ziyaretler WHERE kaynak='qr'")->fetchColumn();

// Grafik Verileri (Son 7 Gün)
$days = [];
$visits = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dayName = strftime("%a", strtotime($date)); // Tr gün adı için setlocale gerekebilir
    // Alternatif Türkçe Günler
    $turkceGunler = ['Paz', 'Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt'];
    $dayIndex = date('w', strtotime($date));
    $days[] = $turkceGunler[$dayIndex];

    // Sadece QR kaynağından gelenleri say
    $count = $db->query("SELECT COUNT(*) FROM ziyaretler WHERE DATE(tarih) = '$date' AND kaynak='qr'")->fetchColumn();
    $visits[] = $count;
}

// Geçen haftaya göre değişim (Basit simülasyon veya gerçek hesap)
// Gerçek hesap için geçen hafta datası lazım, şimdilik statik veya basit oran
$growthRate = 12; // %12 (Örnek)

// Son 5 Ürünü Çek
$sonUrunler = $db->query("SELECT * FROM urunler ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Sayfa Başlığı ve Ayarlar
$pageTitle = 'Genel Bakış';
require_once 'includes/header.php';
?>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Stat Card 1 -->
    <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group hover:bg-white/5 transition-colors">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-blue-500 transform rotate-12 scale-150"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
        </div>
        <div class="relative z-10">
            <div
                class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500/20 to-blue-600/10 text-blue-400 flex items-center justify-center mb-4 border border-blue-500/20 shadow-lg shadow-blue-500/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-400">Toplam Kategori</p>
            <p class="text-3xl font-bold text-white mt-1"><?= $catCount ?></p>
            <div class="flex items-center gap-2 mt-4 text-xs font-medium text-blue-400/80">
                <span class="px-2 py-1 rounded-md bg-blue-500/10 border border-blue-500/20 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Aktif
                </span>
                <span class="text-gray-500">Kategoriler</span>
            </div>
        </div>
    </div>

    <!-- Stat Card 2 -->
    <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group hover:bg-white/5 transition-colors">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-emerald-500 transform rotate-12 scale-150"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
        </div>
        <div class="relative z-10">
            <div
                class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500/20 to-emerald-600/10 text-emerald-400 flex items-center justify-center mb-4 border border-emerald-500/20 shadow-lg shadow-emerald-500/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-400">Toplam Ürün</p>
            <p class="text-3xl font-bold text-white mt-1"><?= $prodCount ?></p>
            <div class="flex items-center gap-2 mt-4 text-xs font-medium text-emerald-400/80">
                <span
                    class="px-2 py-1 rounded-md bg-emerald-500/10 border border-emerald-500/20 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Yayında
                </span>
                <span class="text-gray-500">Ürünler</span>
            </div>
        </div>
    </div>

    <!-- Stat Card 3 (QR Tarama) -->
    <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group hover:bg-white/5 transition-colors">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-pink-500 transform rotate-12 scale-150"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
        </div>
        <div class="relative z-10">
            <div
                class="w-12 h-12 rounded-xl bg-gradient-to-br from-pink-500/20 to-pink-600/10 text-pink-400 flex items-center justify-center mb-4 border border-pink-500/20 shadow-lg shadow-pink-500/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-400">QR Okutma</p>
            <p class="text-3xl font-bold text-white mt-1"><?= number_format($qrScans) ?></p>
            <div class="flex items-center gap-2 mt-4 text-xs font-medium text-pink-400/80">
                <span class="px-2 py-1 rounded-md bg-pink-500/10 border border-pink-500/20 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    %<?= ($totalViews > 0 ? round(($qrScans / $totalViews) * 100) : 0) ?>
                </span>
                <span class="text-gray-500">Dönüşüm oranı</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

    <!-- Chart Section -->
    <div class="lg:col-span-2">
        <div class="glass-panel p-6 rounded-2xl h-full shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span class="w-1 h-5 bg-gradient-to-b from-blue-400 to-blue-600 rounded-full"></span>
                    Haftalık Ziyaretçi Analizi (QR)
                </h3>
                <select
                    class="bg-black/20 border border-white/10 text-xs text-gray-400 rounded-lg px-2 py-1 outline-none focus:border-blue-500/50">
                    <option>Son 7 Gün</option>
                    <option>Bu Ay</option>
                    <option>Bu Yıl</option>
                </select>
            </div>
            <div class="w-full h-[300px]">
                <canvas id="visitorsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Products -->
    <div class="lg:col-span-1">
        <div class="glass-panel p-6 rounded-2xl h-full shadow-2xl flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span class="w-1 h-5 bg-gradient-to-b from-emerald-400 to-emerald-600 rounded-full"></span>
                    Son Eklenenler
                </h3>
                <a href="admin/product_add.php"
                    class="text-xs font-medium text-emerald-400 hover:text-emerald-300 transition-colors bg-emerald-500/10 px-2 py-1 rounded-lg border border-emerald-500/20">
                    + Ekle
                </a>
            </div>

            <div class="flex-1 overflow-y-auto pr-1 custom-scrollbar">
                <div class="space-y-3">
                    <?php foreach ($sonUrunler as $urun): ?>
                        <div
                            class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-gray-800 overflow-hidden flex-shrink-0">
                                <?php if ($urun['gorsel_yolu']): ?>
                                    <img src="assets/images/<?= htmlspecialchars($urun['gorsel_yolu']) ?>"
                                        class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-gray-600">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-white truncate">
                                    <?= htmlspecialchars($urun['urun_adi']) ?>
                                </h4>
                                <p class="text-xs text-emerald-400 font-semibold"><?= number_format($urun['fiyat'], 2) ?> ₺
                                </p>
                            </div>
                            <a href="admin/product_edit.php?id=<?= $urun['id'] ?>"
                                class="text-gray-500 hover:text-white transition-colors p-1.5 rounded-lg hover:bg-white/10">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </a>
                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($sonUrunler)): ?>
                        <div class="text-center py-6 text-gray-500 text-sm">
                            Henüz ürün eklenmemiş.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-white/5 text-center">
                <a href="admin/products.php" class="text-xs text-gray-400 hover:text-white transition-colors">Tüm
                    Ürünleri Gör &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('visitorsChart').getContext('2d');

    // Gradient oluşturma
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)'); // Blue start
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)'); // Transparent end

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($days) ?>,
            datasets: [{
                label: 'Ziyaretçiler',
                data: <?= json_encode($visits) ?>,
                borderColor: '#3b82f6',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#1d4ed8',
                pointBorderColor: '#ffffff',
                pointHoverBackgroundColor: '#ffffff',
                pointHoverBorderColor: '#3b82f6',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#cbd5e1',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: false,
                    callbacks: {
                        label: function (context) {
                            return context.parsed.y + ' Ziyaretçi';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#94a3b8',
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#94a3b8',
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 11
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>