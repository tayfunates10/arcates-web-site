<?php
declare(strict_types=1);

test('F-R3-04', 'Eski surec partiali veri odakli ve erisilebilir kalir', function (): void {
    $template = (string) file_get_contents(ARC_ROOT . '/views/front/partials/steps.php');
    assertContains('step__visual', $template);
    assertContains('aria-hidden="true"', $template);
    assertContains('viewBox="0 0 48 48"', $template);
    assertContains("Security::e(\$step['title'] ?? '')", $template, 'Surec basligi panel verisinden gelmeli');
    assertContains("Security::e(\$step['text'])", $template, 'Surec metni panel verisinden gelmeli');
    assertNotContains('style="', $template, 'CSP nedeniyle satir ici stil olmamali');
});

test('F-R3-05', 'Eski bolge partiali dinamik ilce verisini ve erisilebilir baglantilari korur', function (): void {
    $template = (string) file_get_contents(ARC_ROOT . '/views/front/partials/coast.php');
    assertContains('coast__backdrop', $template);
    assertContains('coast__grid', $template);
    assertContains('coast__route-shadow', $template);
    assertContains('coast-route-gradient', $template);
    assertContains('data-coast', $template, 'Mevcut scroll cizim motoru korunmali');
    assertContains('coast__path', $template);
    assertContains("array_column(\$districts, 'name')", $template, 'Bolge adlari veri kaynagindan gelmeli');
    assertContains("Security::e(\$district['url'])", $template, 'Bolge URLleri veri kaynagindan gelmeli');
    assertContains('role="img"', $template);
    assertContains('coast__list', $template, 'SVG disinda erisilebilir HTML baglanti listesi korunmali');
    assertNotContains('style="', $template, 'CSP nedeniyle satir ici stil olmamali');
});

test('F-R3-045', 'Eski G-04 G-05 stili korunur fakat yeni referans anasayfaya yuklenmez', function (): void {
    $path = ARC_ROOT . '/public/assets/css/r3-process-region-visuals.css';
    assertTrue(is_file($path), 'G-04/G-05 CSS dosyasi mevcut olmali');
    $css = (string) file_get_contents($path);
    $bytes = filesize($path);
    assertTrue($bytes !== false && $bytes < 16 * 1024, 'G-04/G-05 CSS 16 KB altinda olmali');
    assertContains('.step__visual', $css);
    assertContains('.coast__route-shadow', $css);
    assertContains('@media (max-width: 58.75rem)', $css);
    assertContains('@media (max-width: 45rem)', $css);
    assertContains('@media (prefers-reduced-motion: reduce)', $css);
    assertNotContains('!important', $css);

    $controller = (string) file_get_contents(ARC_ROOT . '/app/Controllers/Front/HomeController.php');
    assertContains("'css/reference-home.css'", $controller);
    assertNotContains("'css/r3-process-region-visuals.css'", $controller, 'Referans anasayfa eski bolge/surec stilini yuklememeli');

    $pageController = (string) file_get_contents(ARC_ROOT . '/app/Controllers/Front/PageController.php');
    assertNotContains('r3-process-region-visuals.css', $pageController, 'Ek stil ic sayfalara tasinmamali');
});
