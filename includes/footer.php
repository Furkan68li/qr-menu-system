<!-- End Main Content -->
</div>

<footer class="mt-auto pt-8 border-t border-white/5">
    <div class="flex items-center justify-between text-xs text-slate-500">
        <p>&copy;
            <?= date('Y') ?> QR Menü Sistemi. Tüm hakları saklıdır.
        </p>
        <p>v1.0.0 Premium</p>
    </div>
</footer>

</div>
</main>

</body>

<script>
    // GÜVENLİK: Sayfa önbellekten yüklense bile oturumun geçerliliğini kontrol et
    document.addEventListener('DOMContentLoaded', function () {
        // Sadece admin sayfalarında çalışsın (URL kontrolü)
        if (window.location.href.indexOf('/admin/') > -1 || window.location.href.indexOf('dashboard.php') > -1) {
            // Base path hesapla
            const basePath = '<?= isset($basePath) ? $basePath : '' ?>';

            fetch(basePath + 'auth/check_session.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'inactive') {
                        // Oturum düşmüş, yönlendir
                        window.location.href = basePath + 'auth/login.php';
                    }
                })
                .catch(err => {
                    // Hata durumunda güvenli tarafta kalıp login'e atabiliriz veya sessiz kalabiliriz
                    // console.error('Session check failed', err);
                });
        }
    });
</script>

</html>