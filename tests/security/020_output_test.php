<?php
/**
 * Cikti kacirma ve zararli icerik.  DOCS.md 10.2, 10.6, testler S-02, S-17
 */

declare(strict_types=1);

use Arcates\Core\Security;

test('S-02', 'Sayfa basligindaki script metin olarak gorunur, calismaz', function (): void {
    $payload = '<script>alert(1)</script>';
    $escaped = Security::e($payload);

    assertNotContains('<script>', $escaped, 'Ham etiket ciktiya girmemeli');
    assertContains('&lt;script&gt;', $escaped, 'Etiket kacirilmis olmali');
    assertContains('alert(1)', $escaped, 'Metin okunur kalmali');
});

test('S-02b', 'Nitelik baglaminda tirnak kacisi kirilmaz', function (): void {
    $payload = '" onmouseover="alert(1)';
    $escaped = Security::attr($payload);

    assertNotContains('" onmouseover="', $escaped, 'Nitelik kirilmamali');
    assertContains('&quot;', $escaped, 'Cift tirnak kacirilmali');
});

test('S-17', 'Zararli SVG yuklemesinde script ve on* nitelikleri temizlenir', function (): void {
    $svg = '<?xml version="1.0"?>'
        . '<!DOCTYPE svg [<!ENTITY xxe SYSTEM "file:///etc/passwd">]>'
        . '<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)" viewBox="0 0 10 10">'
        . '<script>alert(2)</script>'
        . '<a xlink:href="javascript:alert(3)"><rect width="10" height="10"/></a>'
        . '<use xlink:href="#kotu"/>'
        . '<circle cx="5" cy="5" r="4" fill="#0B4FA8" onclick="alert(4)"/>'
        . '</svg>';

    $clean = Security::sanitizeSvg($svg);

    assertNotContains('<script', $clean, 'script bloklari kaldirilmali');
    assertNotContains('onload', $clean, 'onload niteligi kaldirilmali');
    assertNotContains('onclick', $clean, 'onclick niteligi kaldirilmali');
    assertNotContains('xlink:href', $clean, 'xlink:href kaldirilmali');
    assertNotContains('javascript:', $clean, 'javascript: semasi kaldirilmali');
    assertNotContains('<!DOCTYPE', $clean, 'DOCTYPE kaldirilmali (XXE)');
    assertNotContains('<!ENTITY', $clean, 'ENTITY bildirimi kaldirilmali');
    assertContains('<circle', $clean, 'Zararsiz sekiller korunmali');
});

test('S-02c', 'Icerik guvenlik politikasi panelde satir ici scripte izin vermez', function (): void {
    arc_test_config();

    $adminCsp = Security::csp(true);
    assertContains("script-src 'self'", $adminCsp, 'Panel yalnizca kendi kaynagindan script yuklemeli');
    assertNotContains('unsafe-inline', $adminCsp, 'unsafe-inline bulunmamali');
    assertNotContains('unsafe-eval', $adminCsp, 'unsafe-eval bulunmamali');

    $frontCsp = Security::csp(false);
    assertNotContains('unsafe-inline', $frontCsp, 'On yuzde de unsafe-inline bulunmamali');
    assertContains("object-src 'none'", $frontCsp);
    assertContains("base-uri 'self'", $frontCsp);
    assertContains('https://fonts.googleapis.com', $frontCsp, 'Yalnizca Google Fonts stil kaynagi');
});
