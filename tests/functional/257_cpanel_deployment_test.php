<?php
declare(strict_types=1);

test('F-CPANEL-01', 'cPanel pull deployment manifesti guvenli hedef ve proje agacini kullanir', function (): void {
    $path = ARC_ROOT . '/.cpanel.yml';
    assertTrue(is_file($path), '.cpanel.yml repo kokunde bulunmali');

    $yaml = (string) file_get_contents($path);
    assertContains('deployment:', $yaml, 'deployment anahtari bulunmali');
    assertContains('tasks:', $yaml, 'deployment gorevleri bulunmali');
    assertContains('DEPLOYPATH=/home/locqyjadry1t/public_html/arcatesyazilim.com/', $yaml, 'cPanel canli uygulama koku dogru olmali');

    foreach (['app', 'config', 'db', 'lang', 'public', 'storage', 'tools', 'views'] as $dir) {
        assertContains('/bin/cp -R ' . $dir . ' $DEPLOYPATH', $yaml, $dir . ' deploy edilmelidir');
    }

    assertNotContains('*', $yaml, 'Wildcard ile tum repo deploy edilmemeli');
    assertNotContains('/bin/rm', $yaml, 'Deployment mevcut canli dosyalari silmemeli');
    assertNotContains('--delete', $yaml, 'Runtime config/upload/storage verileri silinmemeli');
});

test('F-CPANEL-02', 'public kok istegi index.php ile acilir', function (): void {
    $path = ARC_ROOT . '/public/.htaccess';
    assertTrue(is_file($path), 'public/.htaccess bulunmali');

    $htaccess = (string) file_get_contents($path);
    assertContains('DirectoryIndex index.php', $htaccess, 'Apache kok isteginde index.php baslangic dosyasi olmali');
    assertContains('Options -Indexes', $htaccess, 'Dizin listeleme kapali kalmali');
});
