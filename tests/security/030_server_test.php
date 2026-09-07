<?php
/**
 * Sunucu yapilandirmasi ve dosya erisimi.
 * DOCS.md 10.6, 10.7, 10.10 — testler S-05, S-07, S-11, S-12, S-13
 */

declare(strict_types=1);

use Arcates\Core\Config;

test('S-05', 'Yükleme uzantı beyaz listesi PHP dosyalarını kabul etmez', function (): void {
    arc_test_config();

    $allowed = (array) Config::get('upload.allowed_ext', []);

    foreach (['php', 'phtml', 'phar', 'html', 'js', 'htaccess', 'exe', 'sh'] as $bad) {
        assertFalse(in_array($bad, $allowed, true), "Uzantı reddedilmeli: {$bad}");
    }
    foreach (['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'pdf'] as $good) {
        assertTrue(in_array($good, $allowed, true), "Uzantı kabul edilmeli: {$good}");
    }
});

test('S-07', 'uploads klasöründe PHP çalıştırılması engellenir', function (): void {
    $file = ARC_ROOT . '/public/uploads/.htaccess';
    assertTrue(is_file($file), 'uploads/.htaccess bulunmalı');

    $body = (string) file_get_contents($file);
    assertContains('php_flag engine off', $body, 'PHP motoru kapatılmalı');
    assertContains('Require all denied', $body, 'PHP uzantılarına erişim reddedilmeli');
    assertContains('phtml', $body, 'phtml de engellenmeli');
    assertContains('Options -Indexes', $body, 'Klasör listelemesi kapalı olmalı');
});

test('S-11', 'Yapılandırma ve kaynak klasörleri web kökünün dışındadır', function (): void {
    foreach (['config', 'app', 'storage', 'views', 'db', 'tests', 'tools', 'lang'] as $dir) {
        assertFalse(
            is_dir(ARC_ROOT . '/public/' . $dir),
            "{$dir} klasörü public/ altında olmamalı"
        );

        assertTrue(
            is_file(ARC_ROOT . '/' . $dir . '/.htaccess'),
            "{$dir}/.htaccess ikinci kat koruma olarak bulunmalı"
        );
    }

    $guard = (string) file_get_contents(ARC_ROOT . '/config/.htaccess');
    assertContains('Require all denied', $guard, 'Erişim reddedilmeli');
});

test('S-11b', 'public/.htaccess hassas uzantıları kapatır ve https zorlar', function (): void {
    $body = (string) file_get_contents(ARC_ROOT . '/public/.htaccess');

    assertContains('RewriteCond %{HTTPS} off', $body, 'https yönlendirmesi bulunmalı');
    assertContains('R=301', $body, 'Kalıcı yönlendirme olmalı');
    assertContains('Options -Indexes', $body, 'Klasör listeleme kapalı olmalı');
    assertContains('env|ini|log|sql|md', $body, 'Hassas uzantılar reddedilmeli');
    assertContains('X-Content-Type-Options', $body, 'Güvenlik başlıkları bulunmalı');
});

test('S-12', 'Sürüm kontrolü klasörü web kökünün dışında', function (): void {
    assertFalse(is_dir(ARC_ROOT . '/public/.git'), '.git public/ altında olmamalı');
    assertTrue(is_dir(ARC_ROOT . '/.git') || getenv('CI') !== false, 'Depo kökünde .git beklenir');
});

/**
 * Kurulum durum testleri icin config.php ve installed.lock dosyalarini gecici
 * olarak istenen duruma getirir; test sonunda calisma alanini aynen geri kurar.
 */
function arc_with_install_state(bool $installed, callable $fn): void
{
    $config      = ARC_ROOT . '/config/config.php';
    $configSeed  = ARC_ROOT . '/config/config.example.php';
    $lock        = ARC_ROOT . '/storage/installed.lock';
    $hadConfig   = is_file($config);
    $configBody  = $hadConfig ? (string) file_get_contents($config) : null;
    $hadLock     = is_file($lock);
    $lockBody    = $hadLock ? (string) file_get_contents($lock) : null;

    if (!$hadConfig) {
        copy($configSeed, $config);
    }

    if ($installed) {
        file_put_contents($lock, "test-installed\n");
    } else {
        @unlink($lock);
    }

    try {
        $fn();
    } finally {
        if ($hadLock) {
            file_put_contents($lock, (string) $lockBody);
        } else {
            @unlink($lock);
        }

        if ($hadConfig) {
            file_put_contents($config, (string) $configBody);
        } else {
            @unlink($config);
        }
    }
}

test('S-13', 'Kurulum tamamlandıktan sonra /install kapanır', function (): void {
    arc_test_config();

    arc_with_install_state(true, function (): void {
        assertTrue(Arcates\Core\App::isInstalled(), 'Config ve kilit varken uygulama kurulmuş sayılmalı');

        $controller = new Arcates\Controllers\Front\InstallController();
        $response   = $controller->index(Arcates\Core\Request::make('GET', '/install'), []);
        assertSame(404, $response->status(), 'Kurulum kapalı olmalı');
    });
});

test('S-13b', 'Kurulum kilidi olmadan panel kurulum sihirbazına yönlenir', function (): void {
    arc_test_config();

    arc_with_install_state(false, function (): void {
        assertFalse(Arcates\Core\App::isInstalled(), 'Kilit yokken uygulama kurulmuş sayılmamalı');

        $controller = new Arcates\Controllers\Front\InstallController();
        $response   = $controller->index(Arcates\Core\Request::make('GET', '/install'), []);

        assertSame(200, $response->status(), 'Kurulum ekranı açık olmalı');
        assertContains('Kurulum', $response->body(), 'Kurulum başlığı görünmeli');
    });
});
