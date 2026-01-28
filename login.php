<?php
require_once 'baglan.php';

session_start();

// Eğer kullanıcı zaten giriş yapmışsa dashboard'a yönlendir
if (isset($_SESSION['kullanici_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Lütfen tüm alanları doldurun.";
    } else {
        $stmt = $db->prepare("SELECT id, kullanici_adi, sifre FROM kullanicilar WHERE kullanici_adi = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['sifre'])) {
            // Giriş başarılı
            $_SESSION['kullanici_id'] = $user['id'];
            $_SESSION['kullanici_adi'] = $user['kullanici_adi'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Kullanıcı adı veya şifre hatalı.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Giriş</title>
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

<body class="bg-antigravity-dark text-slate-200 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-sm bg-antigravity-card border border-white/10 rounded-2xl shadow-2xl overflow-hidden p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight mb-2">Yönetim Paneli</h1>
            <p class="text-gray-400 text-sm">Devam etmek için giriş yapın</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-500 text-sm p-3 rounded-lg mb-6 text-center">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-400 mb-2">Kullanıcı Adı</label>
                <input type="text" id="username" name="username"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-antigravity-accent focus:ring-1 focus:ring-antigravity-accent transition-colors"
                    placeholder="admin" required>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-400 mb-2">Şifre</label>
                <input type="password" id="password" name="password"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-antigravity-accent focus:ring-1 focus:ring-antigravity-accent transition-colors"
                    placeholder="••••••••" required>
            </div>

            <button type="submit"
                class="w-full bg-antigravity-accent hover:bg-blue-600 text-white font-semibold py-3 rounded-lg transition-colors duration-200 shadow-lg shadow-blue-500/20">
                Giriş Yap
            </button>
        </form>
    </div>

</body>

</html>