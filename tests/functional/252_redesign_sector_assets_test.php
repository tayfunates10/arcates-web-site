<?php
declare(strict_types=1);

test('F-R3-03', 'Alti sektor gorseli 4:3, guvenli ve butce icindedir', function (): void {
    $slugs = [
        'otel-pansiyon-web-sitesi',
        'zeytinyagi-e-ticaret-sitesi',
        'restoran-kafe-qr-menu',
        'emlak-web-sitesi',
        'nakliyat-web-sitesi',
        'tabela-matbaa-web-sitesi',
    ];

    $total = 0;
    foreach ($slugs as $slug) {
        $path = ARC_ROOT . '/public/assets/img/redesign/sector-' . $slug . '.svg';
        assertTrue(is_file($path), 'Sektor gorseli mevcut olmali: ' . $slug);
        $svg = (string) file_get_contents($path);
        $bytes = filesize($path);
        assertTrue($bytes !== false && $bytes < 8 * 1024, 'Tek sektor gorseli 8 KB altinda olmali: ' . $slug);
        $total += (int) $bytes;
        assertContains('viewBox="0 0 480 360"', $svg, 'Sektor gorseli 4:3 olmali: ' . $slug);
        assertNotContains('<text', strtolower($svg), 'Sektor gorselinde sahte metin olmamali: ' . $slug);
        assertNotContains('<script', strtolower($svg), 'Sektor gorselinde script olmamali: ' . $slug);
        assertTrue(
            preg_match('/(?:xlink:)?href\s*=\s*["\'][^#]/i', $svg) !== 1,
            'Sektor gorseli harici href kaynagi kullanmamali: ' . $slug
        );
    }

    assertTrue($total < 40 * 1024, 'Alti sektor gorselinin toplam butcesi 40 KB altinda olmali');

    $template = (string) file_get_contents(ARC_ROOT . '/views/front/sector.php');
    assertContains('sector-hero__image', $template);
    assertContains('loading="eager"', $template);
    assertContains('alt=""', $template);
    assertContains('aria-hidden="true"', $template);

    $controller = (string) file_get_contents(ARC_ROOT . '/app/Controllers/Front/PageController.php');
    assertContains("'styles'      => \$page['type'] === 'sector' ? ['css/r3-sector-illustrations.css'] : []", $controller);
});
