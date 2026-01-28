<?php
require_once '../config/baglan.php';
require_once '../auth/auth.php';

$pageTitle = 'Profil Ayarları';
$basePath = '../';
require_once '../includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "Lütfen tüm alanları doldurun.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Yeni şifreler birbiriyle eşleşmiyor.";
    } else {
        // Mevcut şifreyi kontrol et
        $stmt = $db->prepare("SELECT sifre FROM kullanicilar WHERE id = ?");
        $stmt->execute([$_SESSION['kullanici_id']]);
        $user = $stmt->fetch();

        if ($user && password_verify($current_password, $user['sifre'])) {
            // Şifreyi güncelle
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $updateStmt = $db->prepare("UPDATE kullanicilar SET sifre = ? WHERE id = ?");
            if ($updateStmt->execute([$new_hash, $_SESSION['kullanici_id']])) {
                $success = "Şifreniz başarıyla güncellendi.";
            } else {
                $error = "Bir hata oluştu.";
            }
        } else {
            $error = "Mevcut şifreniz hatalı.";
        }
    }
}
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
        class="animate-fade-in bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-xl mb-6 flex items-center gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="glass-panel p-8 rounded-2xl max-w-xl mx-auto shadow-2xl">
    <div class="text-center mb-8">
        <div
            class="w-16 h-16 bg-gradient-to-tr from-gray-700 to-gray-600 rounded-full flex items-center justify-center mx-auto mb-4 text-white text-2xl font-bold shadow-lg shadow-gray-700/30">
            <?= strtoupper(substr($_SESSION['kullanici_adi'], 0, 1)) ?>
        </div>
        <h2 class="text-xl font-bold text-white">
            <?= htmlspecialchars($_SESSION['kullanici_adi']) ?>
        </h2>
        <p class="text-gray-400 text-sm">Şifrenizi buradan değiştirebilirsiniz.</p>
    </div>

    <form method="POST" action="" class="space-y-6">
        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-400 mb-2">Mevcut Şifre</label>
            <input type="password" id="current_password" name="current_password" required
                class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 outline-none transition-colors"
                placeholder="••••••••">
        </div>

        <div>
            <label for="new_password" class="block text-sm font-medium text-gray-400 mb-2">Yeni Şifre</label>
            <input type="password" id="new_password" name="new_password" required
                class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 outline-none transition-colors"
                placeholder="••••••••">
        </div>

        <div>
            <label for="confirm_password" class="block text-sm font-medium text-gray-400 mb-2">Yeni Şifre
                (Tekrar)</label>
            <input type="password" id="confirm_password" name="confirm_password" required
                class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 outline-none transition-colors"
                placeholder="••••••••">
        </div>

        <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg shadow-blue-500/20">
            Şifreyi Güncelle
        </button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
