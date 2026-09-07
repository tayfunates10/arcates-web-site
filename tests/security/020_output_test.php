<?php
/**
 * Cikti kacirma ve zararli icerik.  DOCS.md 10.2, 10.6, testler S-02, S-17
 */

declare(strict_types=1);

use Arcates\Core\Security;

test('S-02', 'Sayfa başlığındaki script metin olarak görünür, çalışmaz', function (): void {
    $payload = '<script>alert(1)</script>';
    $escaped = Security::e($payload);

    assertNotContains('<script>', $escaped, 'Ham etiket çıktıya girmemeli');
    assertContains('&lt;script&gt;', $escaped, 'Etiket kaçırılmış olmalı');
    assertContains('alert(1)', $escaped, 'Metin okunur kalmalı');
});

test('S-02b', 'Nitelik bağlamında tırnak kaçışı kırılmaz', function (): void {
    $payload = '" onmouseover="alert(1)';
    $escaped = Security::attr($payload);

    assertNotContains('" onmouseover="', $escaped, 'Nitelik kırılmamalı');
    assertContains('&quot;', $escaped, 'Çift tırnak kaçırılmalı');
});

test('S-17', 'Zararlı SVG yüklemesinde script ve on* nitelikleri temizlenir', function (): void {
    $svg = '<?xml version="1.0"?>'
        . '<!DOCTYPE svg [<!ENTITY xxe SYSTEM "file:///etc/passwd">]>'
        . '<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)" viewBox="0 0 10 10">'
        . '<script>alert(2)</script>'
        . '<a xlink:href="javascript:alert(3)"><rect width="10" height="10"/></a>'
        . '<use xlink:href="#kotu"/>'
        . '<circle cx="5" cy="5" r="4" fill="#0B4FA8" onclick="alert(4)"/>'
        . '</svg>';

    $clean = Security::sanitizeSvg($svg);

    assertNotContains('<script', $clean, 'script blokları kaldırılmalı');
    assertNotContains('onload', $clean, 'onload niteliği kaldırılmalı');
    assertNotContains('onclick', $clean, 'onclick niteliği kaldırılmalı');
    assertNotContains('xlink:href', $clean, 'xlink:href kaldırılmalı');
    assertNotContains('javascript:', $clean, 'javascript: şeması kaldırılmalı');
    assertNotContains('<!DOCTYPE', $clean, 'DOCTYPE kaldırılmalı (XXE)');
    assertNotContains('<!ENTITY', $clean, 'ENTITY bildirimi kaldırılmalı');
    assertContains('<circle', $clean, 'Zararsız şekiller korunmalı');
});

test('S-02c', 'İçerik güvenlik politikası panelde satır içi scripte izin vermez', function (): void {
    arc_test_config();

    $adminCsp = Security::csp(true);
    assertContains("script-src 'self'", $adminCsp, 'Panel yalnızca kendi kaynağından script yüklemeli');
    assertNotContains('unsafe-inline', $adminCsp, 'unsafe-inline bulunmamalı');
    assertNotContains('unsafe-eval', $adminCsp, 'unsafe-eval bulunmamalı');

    $frontCsp = Security::csp(false);
    assertNotContains('unsafe-inline', $frontCsp, 'On yüzde de unsafe-inline bulunmamalı');
    assertContains("object-src 'none'", $frontCsp);
    assertContains("base-uri 'self'", $frontCsp);
    assertContains('https://fonts.googleapis.com', $frontCsp, 'Yalnızca Google Fonts stil kaynağı');
});
