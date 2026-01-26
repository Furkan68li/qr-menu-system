<?php
require_once '../baglan.php';
require_once 'auth.php';

if (!isset($_GET['id'])) {
    header("Location: categories.php");
    exit;
}

$id = (int) $_GET['id'];
$stmt = $db->prepare("SELECT * FROM kategoriler WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) {
    header("Location: categories.php?err=Kategori bulunamadı");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori_adi = trim($_POST['kategori_adi']);
    $sira = (int) $_POST['sira'];

    if (empty($kategori_adi)) {
        $error = "Kategori adı boş olamaz.";
    } else {
        $stmt = $db->prepare("UPDATE kategoriler SET kategori_adi = ?, sira = ? WHERE id = ?");
        if ($stmt->execute([$kategori_adi, $sira, $id])) {
            header("Location: categories.php?msg=Kategori güncellendi");
            exit;
        } else {
            $error = "Bir hata oluştu.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Düzenle - QR Menü</title>
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
    </style>
</head>

<body class="bg-antigravity-dark text-slate-200 min-h-screen relative selection:bg-antigravity-accent selection:text-white">

    <!-- Ambient Background -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-purple-600/10 rounded-full blur-[100px]"></div>
    </div>

    <div class="max-w-xl mx-auto px-4 py-12 animate-fade-in-up">
        <div class="mb-8 flex items-center justify-between">
            <div>
                 <h1 class="text-2xl font-bold text-white tracking-tight">Kategori Düzenle</h1>
                 <p class="text-gray-400 text-sm mt-1">"<?= htmlspecialchars($category['kategori_adi']) ?>" kategorisini düzenliyorsunuz.</p>
            </div>
             <a href="categories.php" class="text-gray-400 hover:text-white transition-colors flex items-center gap-1 text-sm bg-white/5 px-3 py-2 rounded-lg hover:bg-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                İptal
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="glass-card border-red-500/20 bg-red-500/5 text-red-400 p-4 rounded-xl mb-6 flex items-center gap-3">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="glass-card rounded-2xl p-8 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Kategori Adı</label>
                <input type="text" name="kategori_adi" required
                    value="<?= htmlspecialchars($category['kategori_adi']) ?>"
                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-antigravity-accent focus:ring-1 focus:ring-antigravity-accent transition-all">
            </div>

            <div>
                 <label class="block text-sm font-medium text-gray-300 mb-2">Sıralama</label>
                <input type="number" name="sira" value="<?= htmlspecialchars($category['sira']) ?>"
                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-antigravity-accent focus:ring-1 focus:ring-antigravity-accent transition-all">
                <p class="text-xs text-gray-500 mt-2">Düşük rakamlar menüde daha üstte gösterilir.</p>
            </div>

            <div class="flex items-center justify-end pt-4 border-t border-white/5">
                <button type="submit"
                    class="bg-antigravity-accent hover:bg-antigravity-accent-hover text-white font-semibold py-3 px-8 rounded-xl transition-all shadow-lg shadow-indigo-500/20 active:scale-[0.98] flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Güncelle</span>
                </button>
            </div>
        </form>
    </div>

</body>

</html>