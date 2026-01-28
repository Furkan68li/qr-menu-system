<?php
require_once '../config/baglan.php';
require_once '../auth/auth.php';

$pageTitle = 'QR Kod Oluşturucu';
$basePath = '../';
require_once '../includes/header.php';

// Menü URL'sini otomatik belirle
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$script = dirname(dirname($_SERVER['SCRIPT_NAME']));
$menuUrl = $protocol . "://" . $host . $script . "/index.php?src=qr";

// Temiz URL (çift slash sorununu önle)
$menuUrl = str_replace('\\', '/', $menuUrl);
$menuUrl = str_replace('//index.php', '/index.php', $menuUrl);
// url parametrelerini koru
if (strpos($menuUrl, '?') === false) {
    $menuUrl .= "?src=qr";
}
// zaten eklendiği için tekrar eklemeye gerek yok ama logic doğru olsun

?>

<div class="glass-panel p-8 rounded-2xl max-w-4xl mx-auto shadow-2xl">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

        <!-- Sol Taraf: Bilgi ve Ayarlar -->
        <div>
            <h2 class="text-2xl font-bold text-white mb-4">Menü QR Kodunuz</h2>
            <p class="text-gray-400 mb-6 leading-relaxed">
                Bu QR kodu, müşterilerinizi doğrudan dijital menünüze yönlendirir.
                İndirip masalarınıza yapıştırabilir, sosyal medyada paylaşabilirsiniz.
            </p>

            <div class="bg-blue-500/10 border border-blue-500/20 p-4 rounded-xl mb-8">
                <p class="text-xs text-blue-300 uppercase font-semibold mb-1">Hedef URL</p>
                <div class="flex items-center gap-2">
                    <code class="text-blue-100 text-sm truncate flex-1" id="urlInput"><?= $menuUrl ?></code>
                    <button onclick="copyUrl()" class="text-blue-400 hover:text-white transition-colors"
                        title="Kopyala">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </button>
                    <span id="copyMsg" class="text-xs text-green-400 font-medium hidden">Kopyalandı!</span>
                </div>
            </div>

            <button onclick="downloadQR()"
                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-blue-500/20 flex items-center justify-center gap-3 group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 group-hover:-translate-y-1 transition-transform"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                QR Kodu İndir (PNG)
            </button>
        </div>

        <!-- Sağ Taraf: QR Önizleme -->
        <div class="flex flex-col items-center justify-center">
            <div class="bg-white p-6 rounded-3xl shadow-2xl relative group">
                <!-- QR Canvas buraya gelecek -->
                <div id="qrcode"></div>

                <!-- Logo Overlay (Opsiyonel) -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-20">
                    <!-- İsterseniz buraya logo ekleyebilirsiniz -->
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-4">Otomatik oluşturuldu ve kullanıma hazır.</p>
        </div>
    </div>
</div>

<!-- QR Kütüphanesi -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    const menuUrl = "<?= $menuUrl ?>";

    // QR Kodu Oluştur
    const qrContainer = document.getElementById("qrcode");
    const qrcode = new QRCode(qrContainer, {
        text: menuUrl,
        width: 256,
        height: 256,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });

    // İndirme Fonksiyonu
    function downloadQR() {
        const canvas = qrContainer.querySelector('canvas');
        if (canvas) {
            const image = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");
            const link = document.createElement('a');
            link.download = 'qr-menu.png';
            link.href = image;
            link.click();
        } else {
            // Fallback for img tag fallback
            const img = qrContainer.querySelector('img');
            if (img) {
                const link = document.createElement('a');
                link.download = 'qr-menu.png';
                link.href = img.src;
                link.click();
            }
        }
    }

    // URL Kopyalama
    function copyUrl() {
        const urlText = document.getElementById('urlInput').innerText;
        navigator.clipboard.writeText(urlText).then(() => {
            const msg = document.getElementById('copyMsg');
            msg.classList.remove('hidden');
            setTimeout(() => {
                msg.classList.add('hidden');
            }, 2000);
        });
    }
</script>

<?php require_once '../includes/footer.php'; ?>
