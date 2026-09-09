<?php
declare(strict_types=1);

test('F-R3-09', 'Sosyal kart ve favicon metadata zinciri G-09 kaynaklarini kullanir', function (): void {
    $head = (string) file_get_contents(ARC_ROOT . '/views/front/partials/head.php');

    assertContains("path_url('/assets/social-card.php')", $head, 'Varsayilan OG gorseli G-09 sosyal kart endpointi olmali');
    assertContains('og:image:type', $head, 'OG MIME tipi belirtilmeli');
    assertContains('content="1200"', $head, 'OG genisligi 1200 olmali');
    assertContains('content="630"', $head, 'OG yuksekligi 630 olmali');
    assertContains('twitter:image', $head, 'Twitter gorseli OG kaynagini kullanmali');
    assertContains('theme-color', $head, 'Tarayici tema rengi tanimli olmali');

    foreach (['16x16', '32x32', '48x48', '192x192', '180x180'] as $size) {
        assertContains('sizes="' . $size . '"', $head, $size . ' ikon hedefi head icinde tanimli olmali');
    }

    assertContains('/assets/brand-icon.php?size=16', $head, '16px favicon tam boyutta uretilmeli');
    assertContains('/assets/img/favicon-32.png', $head, 'Onayli 32px favicon korunmali');
    assertContains('/assets/brand-icon.php?size=48', $head, '48px favicon tam boyutta uretilmeli');
    assertContains('/assets/img/logo-mark.png', $head, '192px ana marka isareti korunmali');
    assertContains('/assets/img/apple-touch-icon.png', $head, 'Onayli 180px Apple ikonu korunmali');
});

test('F-R3-09Q', 'Marka masterlari ve G-09 raster ureticileri kalite sozlesmesini korur', function (): void {
    $expected = [
        'public/assets/img/favicon-32.png' => [32, 32],
        'public/assets/img/apple-touch-icon.png' => [180, 180],
        'public/assets/img/logo-mark.png' => [192, 192],
        'public/assets/img/logo-wordmark.png' => [560, 187],
        'public/assets/img/og-default.png' => [1200, 630],
    ];

    foreach ($expected as $relative => [$width, $height]) {
        $path = ARC_ROOT . '/' . $relative;
        assertTrue(is_file($path), $relative . ' bulunmali');
        $info = getimagesize($path);
        assertTrue(is_array($info), $relative . ' okunabilir raster olmali');
        assertSame($width, $info[0], $relative . ' genisligi korunmali');
        assertSame($height, $info[1], $relative . ' yuksekligi korunmali');
    }

    $icon = (string) file_get_contents(ARC_ROOT . '/public/assets/brand-icon.php');
    assertContains('$allowed = [16, 48]', $icon, 'Yalniz belgelenmis kucuk favicon boyutlari uretilmeli');
    assertContains('favicon-32.png', $icon, '16px hedefi mevcut kucuk favicon kaynagini kullanmali');
    assertContains('logo-mark.png', $icon, '48px hedefi onayli ana isareti kullanmali');
    assertContains('imagecopyresampled', $icon, 'Kucuk ikonlar yuksek kaliteli resample ile uretilmeli');

    $social = (string) file_get_contents(ARC_ROOT . '/public/assets/social-card.php');
    assertContains('$width = 1200', $social, 'Sosyal kart 1200px olmali');
    assertContains('$height = 630', $social, 'Sosyal kart 630px olmali');
    assertContains('logo-wordmark.png', $social, 'Sosyal kart onayli wordmark kaynagini kullanmali');
    assertContains('og-default.png', $social, 'GD yoksa mevcut statik OG fallback korunmali');
    assertContains('imagecopyresampled', $social, 'Wordmark kaliteli resample ile yerlestirilmeli');
    assertNotContains('imagestring(', strtolower($social), 'Sosyal karta dinamik metin gomulmemeli');
    assertNotContains('imagettftext(', strtolower($social), 'Sosyal karta dinamik metin gomulmemeli');
    assertNotContains('http://', strtolower($social), 'Sosyal kart harici HTTP kaynak kullanmamali');
    assertNotContains('https://', strtolower($social), 'Sosyal kart harici HTTPS kaynak kullanmamali');

    $bytes = filesize(ARC_ROOT . '/public/assets/social-card.php');
    assertTrue($bytes !== false && $bytes < 20 * 1024, 'Sosyal kart ureticisi 20 KB altinda olmali');
});
