<?php
/**
 * "Ana Sayfa" menu ogesi gocu.  DOCS.md 8.6
 *
 * Goc kurulmus sitelere yeni menu ogesini tasir. `Seeder::menus()` yalnizca
 * menu tamamen bos oldugunda calistigi icin bu isi tohumlama yapamaz.
 *
 * Aranan davranis: ikinci kez calistiginda yeni satir yazmamak.
 */

declare(strict_types=1);

use Arcates\Core\Database;
use Arcates\Core\Migrator;

test('F-P18-a', 'Göç dosyası beklenen adla durur', function (): void {
    $yol = ARC_ROOT . '/db/migrations/2026_09_11_0003_ana_sayfa_menu_ogesi.sql';
    assertTrue(is_file($yol), 'Göç dosyası bulunmalı');

    $sql = (string) file_get_contents($yol);
    assertContains('INSERT INTO menu_items', $sql, 'Menü öğesi eklenmeli');
    assertContains('INSERT IGNORE INTO menu_item_translations', $sql, 'Etiket koşullu eklenmeli');
    assertContains('NOT EXISTS', $sql, 'Öğe koşullu eklenmeli');
});

test('F-P18-b', 'Göç iki kez çalışsa da tek öğe bırakır', function (): void {
    arc_need_db();

    $db  = Database::instance();
    $sql = (string) file_get_contents(
        ARC_ROOT . '/db/migrations/2026_09_11_0003_ana_sayfa_menu_ogesi.sql'
    );

    $once = (int) $db->count('menu_items', ['menu_key' => 'main', 'url' => '/']);

    // Kurulu veritabaninda goc zaten uygulanmis olabilir; iki kez daha
    // calistirip satir sayisinin artmadigini dogruluyoruz.
    (new Migrator($db))->runSqlScript($sql);
    (new Migrator($db))->runSqlScript($sql);

    $sonra = (int) $db->count('menu_items', ['menu_key' => 'main', 'url' => '/']);

    assertSame(1, $sonra, 'Anasayfa öğesi tam olarak bir kez bulunmalı');
    assertTrue($sonra >= $once, 'Göç var olan öğeyi silmemeli');
});

test('F-P18-c', 'Göç Türkçe etiketi yazar', function (): void {
    arc_need_db();

    $db    = Database::instance();
    $etiket = $db->value(
        'SELECT t.label
           FROM menu_items mi
           JOIN menu_item_translations t ON t.menu_item_id = mi.id
          WHERE mi.menu_key = :k AND mi.url = :u AND t.lang = :l',
        ['k' => 'main', 'u' => '/', 'l' => 'tr']
    );

    assertSame('Ana Sayfa', $etiket, 'Anasayfa öğesinin etiketi yazılmalı');
});
