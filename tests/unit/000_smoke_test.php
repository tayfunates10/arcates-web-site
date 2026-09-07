<?php
/**
 * Iskelet dogrulamasi — depo yapisi ve calistiricinin kendisi.
 * DOCS.md 3, 14.1
 */

declare(strict_types=1);

test('X-01', 'Depo iskeleti beklenen klasörleri içerir', function (): void {
    foreach (
        [
            'app/Core', 'app/Models', 'app/Controllers/Front', 'app/Controllers/Admin',
            'views/front/partials', 'views/admin', 'config', 'db/migrations',
            'lang', 'public/assets/css', 'public/assets/js', 'tools',
            'tests/unit', 'tests/security', 'tests/functional',
        ] as $dir
    ) {
        assertTrue(is_dir(ARC_ROOT . '/' . $dir), "Klasör bulunmalı: {$dir}");
    }
});

test('X-02', 'Zorunlu kök dosyaları mevcut', function (): void {
    foreach (['CLAUDE.md', 'DOCS.md', 'CHANGELOG.md', 'README.md', 'VERSION', '.gitignore'] as $file) {
        assertTrue(is_file(ARC_ROOT . '/' . $file), "Dosya bulunmalı: {$file}");
    }
});

test('X-03', 'Yükleme klasörü PHP çalıştırmaya kapalı', function (): void {
    $htaccess = ARC_ROOT . '/public/uploads/.htaccess';
    assertTrue(is_file($htaccess), 'uploads/.htaccess bulunmalı');
    $body = (string) file_get_contents($htaccess);
    assertContains('php_flag engine off', $body, 'PHP motoru kapatılmalı');
    assertContains('Require all denied', $body, 'PHP uzantıları reddedilmeli');
});

test('X-04', 'Yapılandırma örneği geçerli bir dizi döndürür', function (): void {
    $config = require ARC_ROOT . '/config/config.example.php';
    assertTrue(is_array($config), 'config.example.php dizi döndürmeli');
    foreach (['app', 'db', 'lang', 'mail', 'security', 'upload'] as $key) {
        assertTrue(isset($config[$key]), "Yapılandırma anahtarı bulunmalı: {$key}");
    }
    assertSame('Europe/Istanbul', $config['app']['timezone'], 'Zaman dilimi Europe/Istanbul olmalı');
    assertFalse($config['app']['debug'], 'Canlıda debug kapalı olmalı');
});
