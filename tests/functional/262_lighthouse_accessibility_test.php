<?php
/** Lighthouse production accessibility regressions must stay closed. */
declare(strict_types=1);

test('F-LH-01', 'Marka ana sayfa baglantisinin erisilebilir adi vardir', function (): void {
    $header = (string) file_get_contents(__DIR__ . '/../../views/front/partials/header.php');
    assertContains('aria-label="<?= Security::e($brandLabel) ?>"', $header);
    assertContains("$brandLabel      = trim((string) (\$_site['name'] ?? '')) ?: 'Arcates Yazılım';", $header);
});

test('F-LH-02', 'Footer telefon ve e-posta hedefleri en az 24px dokunma alanina sahiptir', function (): void {
    $head = (string) file_get_contents(__DIR__ . '/../../views/front/partials/head.php');
    assertContains('.nap > a.nap__line', $head);
    assertContains('min-block-size: 28px;', $head);
});
