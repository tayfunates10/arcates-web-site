<?php
declare(strict_types=1);

test('F-R3-08', 'Final CTA panel ve NAP verisini koruyup dekoratif baglanti yayi kullanir', function (): void {
    $cta = (string) file_get_contents(ARC_ROOT . '/views/front/partials/cta.php');
    assertContains("Settings::get('cta_title'", $cta, 'CTA basligi panel/ayar kaynagindan gelmeli');
    assertContains("Settings::get('nap_phone'", $cta, 'WhatsApp telefonu tek NAP kaynagindan gelmeli');
    assertContains('cta__visual', $cta, 'CTA baglanti gorseli bulunmali');
    assertContains('aria-hidden="true"', $cta, 'CTA gorseli dekoratif olmali');
    assertContains('cta-arc--bright', $cta, 'CTA vurgulu baglanti yayi bulunmali');
});

test('F-R3-10', 'Bos basari ve hata durumlari ortak Arcates isaret ailesini kullanir', function (): void {
    $thanks = (string) file_get_contents(ARC_ROOT . '/views/front/thanks.php');
    assertContains('state-mark--success', $thanks, 'Tesekkur sayfasi basari isareti kullanmali');
    assertContains('aria-hidden="true"', $thanks, 'Basari isareti dekoratif olmali');

    foreach (['projects.php', 'posts.php', 'faqs.php'] as $file) {
        $template = (string) file_get_contents(ARC_ROOT . '/views/front/' . $file);
        assertContains('empty-state', $template, $file . ' bos durum yuzeyi kullanmali');
        assertContains('state-mark--empty', $template, $file . ' bos durum isareti kullanmali');
        assertContains("__('no_results')", $template, $file . ' mevcut ceviri metnini korumali');
    }

    foreach (['403.php', '404.php', '419.php', '500.php'] as $file) {
        $template = (string) file_get_contents(ARC_ROOT . '/views/errors/' . $file);
        assertContains('state-mark--error', $template, $file . ' ortak hata isareti kullanmali');
        assertContains('css/r3-cta-status.css', $template, $file . ' ortak durum stilini yuklemeli');
        assertContains('noindex', $template, $file . ' indekslenmemeli');
    }

    $maintenance = (string) file_get_contents(ARC_ROOT . '/views/errors/503.php');
    assertContains('state-mark--empty', $maintenance, '503 bakim durumu hata yerine bekleme isareti kullanmali');
    assertContains('noindex', $maintenance, '503 indekslenmemeli');
});

test('F-R3-0810', 'G-08 G-10 stili hafif global ve harici kaynaksizdir', function (): void {
    $cssPath = ARC_ROOT . '/public/assets/css/r3-cta-status.css';
    assertTrue(is_file($cssPath), 'R3 CTA/durum CSS dosyasi bulunmali');
    $bytes = filesize($cssPath);
    assertTrue($bytes !== false && $bytes < 16 * 1024, 'R3 CTA/durum CSS 16 KB altinda olmali');

    $head = (string) file_get_contents(ARC_ROOT . '/views/front/partials/head.php');
    assertContains('css/r3-cta-status.css', $head, 'Ortak front-end durum stili head tarafindan yuklenmeli');

    $css = strtolower((string) file_get_contents($cssPath));
    assertNotContains('url(http', $css, 'R3 CTA/durum stili harici kaynak kullanmamali');
    assertNotContains('!important', $css, 'R3 CTA/durum stili zorlayici important kullanmamali');
    assertContains('@media (prefers-reduced-motion: reduce)', $css, 'Reduced-motion davranisi bulunmali');
});
