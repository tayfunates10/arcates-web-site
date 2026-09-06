<?php
/**
 * Sunucu yapilandirmasi ve dosya erisimi.
 * DOCS.md 10.6, 10.7, 10.10 — testler S-05, S-07, S-11, S-12, S-13
 */

declare(strict_types=1);

use Arcates\Core\Config;

test('S-05', 'Yukleme uzanti beyaz listesi PHP dosyalarini kabul etmez', function (): void {
    arc_test_config();

    $allowed = (array) Config::get('upload.allowed_ext', []);

    foreach (['php', 'phtml', 'phar', 'html', 'js', 'htaccess', 'exe', 'sh'] as $bad) {
        assertFalse(in_array($bad, $allowed, true), "Uzanti reddedilmeli: {$bad}");
    }
    foreach (['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'pdf'] as $good) {
        assertTrue(in_array($good, $allowed, true), "Uzanti kabul edilmeli: {$good}");
    }
});

test('S-07', 'uploads klasorunde PHP calistirilmasi engellenir', function (): void {
    $file = ARC_ROOT . '/public/uploads/.htaccess';
    assertTrue(is_file($file), 'uploads/.htaccess bulunmali');

    $body = (string) file_get_contents($file);
    assertContains('php_flag engine off', $body, 'PHP motoru kapatilmali');
    assertContains('Require all denied', $body, 'PHP uzantilarina erisim reddedilmeli');
    assertContains('phtml', $body, 'phtml de engellenmeli');
    assertContains('Options -Indexes', $body, 'Klasor listelemesi kapali olmali');
});

test('S-11', 'Yapilandirma ve kaynak klasorleri web kokunun disindadir', function (): void {
    // Alan adi document root olarak public/ klasorunu gosterir. DOCS.md 13
    foreach (['config', 'app', 'storage', 'views', 'db', 'tests', 'tools', 'lang'] as $dir) {
        assertFalse(
            is_dir(ARC_ROOT . '/public/' . $dir),
            "{$dir} klasoru public/ altinda olmamali"
        );

        // Hosting web kokunu disari alamiyorsa ikinci kat koruma bulunur.
        assertTrue(
            is_file(ARC_ROOT . '/' . $dir . '/.htaccess'),
            "{$dir}/.htaccess ikinci kat koruma olarak bulunmali"
        );
    }

    $guard = (string) file_get_contents(ARC_ROOT . '/config/.htaccess');
    assertContains('Require all denied', $guard, 'Erisim reddedilmeli');
});

test('S-11b', 'public/.htaccess hassas uzantilari kapatir ve https zorlar', function (): void {
    $body = (string) file_get_contents(ARC_ROOT . '/public/.htaccess');

    assertContains('RewriteCond %{HTTPS} off', $body, 'https yonlendirmesi bulunmali');
    assertContains('R=301', $body, 'Kalici yonlendirme olmali');
    assertContains('Options -Indexes', $body, 'Klasor listeleme kapali olmali');
    assertContains('env|ini|log|sql|md', $body, 'Hassas uzantilar reddedilmeli');
    assertContains('X-Content-Type-Options', $body, 'Guvenlik basliklari bulunmali');
});

test('S-12', 'Surum kontrolu klasoru web kokunun disinda', function (): void {
    assertFalse(is_dir(ARC_ROOT . '/public/.git'), '.git public/ altinda olmamali');
    assertTrue(is_dir(ARC_ROOT . '/.git') || getenv('CI') !== false, 'Depo kokunde .git beklenir');
});

test('S-13', 'Kurulum tamamlandiktan sonra /install kapanir', function (): void {
    arc_test_config();

    $lock       = ARC_ROOT . '/storage/installed.lock';
    $hadLock    = is_file($lock);
    $controller = new Arcates\Controllers\Front\InstallController();

    if (!$hadLock) {
        // Kilit dosyasi kurulum tamamlandiginda yazilir. DOCS.md 13 adim 4
        skip('installed.lock yok; kurulum akisi henuz calistirilmadi.');
    }

    $response = $controller->index(Arcates\Core\Request::make('GET', '/install'), []);
    assertSame(404, $response->status(), 'Kurulum kapali olmali');
});

test('S-13b', 'Kurulum kilidi olmadan panel kurulum sihirbazina yonlenir', function (): void {
    arc_test_config();

    if (Arcates\Core\App::isInstalled()) {
        skip('Bu ortamda kurulum tamamlanmis.');
    }

    $controller = new Arcates\Controllers\Front\InstallController();
    $response   = $controller->index(Arcates\Core\Request::make('GET', '/install'), []);

    assertSame(200, $response->status(), 'Kurulum ekrani acik olmali');
    assertContains('Kurulum', $response->body(), 'Kurulum basligi gorunmeli');
});
