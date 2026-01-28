<?php
require_once '../config/baglan.php';
require_once '../auth/auth.php';

// Ayarları çek
$stmt = $db->query("SELECT * FROM ayarlar LIMIT 1");
$ayar = $stmt->fetch(PDO::FETCH_ASSOC);

// Eğer ayar yoksa varsayılan oluştur (Güvenlik önlemi)
if (!$ayar) {
    $db->exec("INSERT INTO ayarlar (site_baslik) VALUES ('QR Menü')");
    $ayar = $db->query("SELECT * FROM ayarlar LIMIT 1")->fetch(PDO::FETCH_ASSOC);
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_baslik = trim($_POST['site_baslik']);
    $slogan = trim($_POST['slogan']);
    $telefon = trim($_POST['telefon']);
    $adres = trim($_POST['adres']);
    $wifi_sifre = trim($_POST['wifi_sifre']);
    $instagram = trim($_POST['instagram']);
    $google_maps = trim($_POST['google_maps']);

    try {
        $updateStmt = $db->prepare("UPDATE ayarlar SET site_baslik = ?, slogan = ?, telefon = ?, adres = ?, wifi_sifre = ?, instagram = ?, google_maps = ? WHERE id = ?");
        $updateStmt->execute([$site_baslik, $slogan, $telefon, $adres, $wifi_sifre, $instagram, $google_maps, $ayar['id']]);

        $success = 'Ayarlar başarıyla güncellendi.';
        // Güncel veriyi tekrar çek
        $ayar = $db->query("SELECT * FROM ayarlar LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = 'Güncelleme hatası: ' . $e->getMessage();
    }
}

$pageTitle = 'Site Ayarları';
$basePath = '../';
require_once '../includes/header.php';
?>

<?php if ($success): ?>
    <div
        class="animate-fade-in bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl mb-6 flex items-center gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div
        class="animate-fade-in bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl mb-6 flex items-center gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="glass-panel p-8 rounded-2xl max-w-4xl mx-auto shadow-2xl">
    <form method="POST" action="" class="space-y-8">

        <!-- Genel Bilgiler -->
        <div class="space-y-6">
            <h3 class="text-lg font-semibold text-white border-b border-white/10 pb-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Genel Bilgiler
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Restoran Adı</label>
                    <input type="text" name="site_baslik" value="<?= htmlspecialchars($ayar['site_baslik']) ?>"
                        class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Slogan</label>
                    <input type="text" name="slogan" value="<?= htmlspecialchars($ayar['slogan']) ?>"
                        class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 outline-none transition-colors">
                </div>
            </div>
        </div>

        <!-- İletişim Bilgileri -->
        <div class="space-y-6">
            <h3 class="text-lg font-semibold text-white border-b border-white/10 pb-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                İletişim & Konum
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Telefon Numarası</label>
                    <input type="text" name="telefon" value="<?= htmlspecialchars($ayar['telefon']) ?>"
                        class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/50 outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Instagram (Kullanıcı Adı)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500">@</span>
                        <input type="text" name="instagram" value="<?= htmlspecialchars($ayar['instagram']) ?>"
                            class="w-full bg-black/20 border border-white/10 rounded-xl pl-8 pr-4 py-3 text-white focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/50 outline-none transition-colors">
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Açık Adres</label>
                    <textarea name="adres" rows="2"
                        class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/50 outline-none transition-colors"><?= htmlspecialchars($ayar['adres']) ?></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Google Maps Linki</label>
                    <input type="text" name="google_maps" value="<?= htmlspecialchars($ayar['google_maps']) ?>"
                        class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/50 outline-none transition-colors">
                </div>
            </div>
        </div>

        <!-- Diğer -->
        <div class="space-y-6">
            <h3 class="text-lg font-semibold text-white border-b border-white/10 pb-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                </svg>
                Diğer
            </h3>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Wi-Fi Şifresi</label>
                <input type="text" name="wifi_sifre" value="<?= htmlspecialchars($ayar['wifi_sifre']) ?>"
                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 outline-none transition-colors">
            </div>
        </div>

        <div class="pt-4 flex justify-end">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Ayarları Kaydet
            </button>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
