<?php
/**
 * Iskelet dogrulamasi — depo yapisi ve calistiricinin kendisi.
 * DOCS.md 3, 14.1
 */

declare(strict_types=1);

test('X-01', 'Depo iskeleti beklenen klasorleri icerir', function (): void {
    foreach (
        [
            'app/Core', 'app/Models', 'app/Controllers/Front', 'app/Controllers/Admin',
            'views/front/partials', 'views/admin', 'config', 'db/migrations',
            'lang', 'public/assets/css', 'public/assets/js', 'tools',
            'tests/unit', 'tests/security', 'tests/functional',
        ] as $dir
    ) {
        assertTrue(is_dir(ARC_ROOT . '/' . $dir), "Klasor bulunmali: {$dir}");
    }
});

test('X-02', 'Zorunlu kok dosyalari mevcut', function (): void {
    foreach (['CLAUDE.md', 'DOCS.md', 'CHANGELOG.md', 'README.md', 'VERSION', '.gitignore'] as $file) {
        assertTrue(is_file(ARC_ROOT . '/' . $file), "Dosya bulunmali: {$file}");
    }
});

test('X-03', 'Yukleme klasoru PHP calistirmaya kapali', function (): void {
    $htaccess = ARC_ROOT . '/public/uploads/.htaccess';
    assertTrue(is_file($htaccess), 'uploads/.htaccess bulunmali');
    $body = (string) file_get_contents($htaccess);
    assertContains('php_flag engine off', $body, 'PHP motoru kapatilmali');
    assertContains('Require all denied', $body, 'PHP uzantilari reddedilmeli');
});

test('X-04', 'Yapilandirma ornegi gecerli bir dizi dondurur', function (): void {
    $config = require ARC_ROOT . '/config/config.example.php';
    assertTrue(is_array($config), 'config.example.php dizi dondurmeli');
    foreach (['app', 'db', 'lang', 'mail', 'security', 'upload'] as $key) {
        assertTrue(isset($config[$key]), "Yapilandirma anahtari bulunmali: {$key}");
    }
    assertSame('Europe/Istanbul', $config['app']['timezone'], 'Zaman dilimi Europe/Istanbul olmali');
    assertFalse($config['app']['debug'], 'Canlida debug kapali olmali');
});
