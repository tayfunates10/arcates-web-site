<?php
/**
 * Terminal bulunmayan paylasimli hostingler icin panelden eksik icerik tamamlama.
 */
declare(strict_types=1);

use Arcates\Core\ContentSeeder;

test('F-PROD-03', 'Eksik baslangic icerigi veritabanina idempotent yazilir', function (): void {
    $db = arc_need_db();
    $seeder = new ContentSeeder($db);

    $seeder->runMissing();

    foreach (['iletisim', 'fiyatlar', 'web-tasarim', 'edremit-web-tasarim'] as $slug) {
        $id = $db->value(
            'SELECT pt.page_id FROM page_translations pt JOIN pages p ON p.id = pt.page_id
              WHERE pt.lang = :lang AND pt.slug = :slug AND p.status = :status LIMIT 1',
            [':lang' => 'tr', ':slug' => $slug, ':status' => 'published']
        );
        assertTrue($id !== null, "Eksik canlı URL tamamlanmalı: /{$slug}");
    }

    assertSame(0, $seeder->missingCoreCount(), 'Dört temel canlı URL tamamlandıktan sonra eksik sayılmamalı');

    $second = $seeder->runMissing();
    assertSame(0, $second['pages'], 'İkinci çalıştırma sayfa çoğaltmamalı');
    assertSame(0, $second['projects'], 'İkinci çalıştırma örnek site çoğaltmamalı');
    assertSame(0, $second['posts'], 'İkinci çalıştırma blog çoğaltmamalı');
    assertSame(0, $second['faqs'], 'İkinci çalıştırma SSS çoğaltmamalı');
});

test('F-PROD-04', 'Panel tamamlama işlemi admin ve CSRF korumalıdır', function (): void {
    $settings = (string) file_get_contents(ARC_ROOT . '/app/Controllers/Admin/SettingController.php');
    $dashboard = (string) file_get_contents(ARC_ROOT . '/views/admin/dashboard.php');
    $controller = (string) file_get_contents(ARC_ROOT . '/app/Controllers/Admin/DashboardController.php');

    assertContains("guardAdmin()", $settings, 'İçerik yükleme admin korumasını kullanmalı');
    assertContains("verifyCsrf", $settings, 'İçerik yükleme CSRF doğrulamalı');
    assertContains("_action') === 'seed_content'", $settings, 'Ayar güncellemesinden ayrı açık eylem olmalı');
    assertContains('runMissing()', $settings, 'Panel ContentSeeder çalıştırmalı');
    assertContains('csrf_field()', $dashboard, 'Panel düğmesi CSRF alanı taşımalı');
    assertContains('Eksik içerikleri yükle', $dashboard, 'Kullanıcıya açık tek tıklama eylemi bulunmalı');
    assertContains('missingCoreCount()', $controller, 'Pano düğmeyi yalnız eksik içerik varsa göstermeli');
});

test('F-PROD-05', 'Yeni kurulum tam başlangıç içeriğini otomatik yükler', function (): void {
    $installer = (string) file_get_contents(ARC_ROOT . '/app/Controllers/Front/InstallController.php');

    assertContains('use Arcates\\Core\\ContentSeeder;', $installer, 'Installer ContentSeeder kullanmalı');
    assertContains('(new ContentSeeder($db))->runMissing();', $installer, 'Şema sonrası tam içerik seed edilmeli');
    assertNotContains('(new Seeder($db))->run();', $installer, 'Eksik temel seeder tek başına kullanılmamalı');
});
