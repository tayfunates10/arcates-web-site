<?php
declare(strict_types=1);

test('F-LIVE-06', 'Lighthouse erisilebilirlik duzeltmeleri kaynakta korunur', function (): void {
    $root = dirname(__DIR__, 2);
    $header = (string) file_get_contents($root . '/views/front/partials/header.php');
    $css = (string) file_get_contents($root . '/public/assets/css/inner-performance.css');

    assertContains('class="brand"', $header);
    assertContains('aria-label="<?= Security::e((string) ($_site[\'name\'] ?? \'Arcates Yazilim\')) ?>"', $header,
        'Marka ana sayfa linki ekran okuyucu icin adlandirilmali');

    assertContains('.site-foot .nap > a.nap__line', $css);
    assertContains('min-block-size: 1.5rem', $css,
        'Telefon ve e-posta hedefleri en az 24px yuksekligi korumali');
});
