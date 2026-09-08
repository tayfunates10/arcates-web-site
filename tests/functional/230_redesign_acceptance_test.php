<?php
/** R8 final redesign acceptance contract. */
declare(strict_types=1);

function arc_r8_file(string $path): string
{
    return (string) file_get_contents(ARC_ROOT . '/' . $path);
}

test('F-R8-01', 'R8 Chromium kabul paketi mevcut browser zincirinin sonunda calisir', function (): void {
    $ci = arc_r8_file('.github/workflows/ci.yml');
    $admin = strpos($ci, 'node tools/browser/admin-check.mjs');
    $r8 = strpos($ci, 'node tools/browser/acceptance-check.mjs');
    assertTrue($admin !== false && $r8 !== false && $r8 > $admin, 'R8 kabul paketi admin-check sonrasinda calismali');
    assertContains("UPDATE languages SET is_active=1 WHERE code='ar'", $ci, 'R8 RTL kabulunden once Arapca yalniz CI veritabaninda etkinlestirilmeli');
});

test('F-R8-02', 'R8 responsive matrisi 320 ile 1920 arasindaki ana kirilimlari kapsar', function (): void {
    $browser = arc_r8_file('tools/browser/acceptance-check.mjs');
    foreach (['320', '360', '390', '768', '1024', '1366', '1440', '1920'] as $width) {
        assertContains('width: ' . $width, $browser, 'Eksik R8 viewport: ' . $width);
    }
    foreach (['/web-tasarim', '/edremit-web-tasarim', '/blog', '/referanslar', '/iletisim', '/sss', '/tesekkurler'] as $route) {
        assertContains("'" . $route . "'", $browser, 'Eksik R8 temsilci rota: ' . $route);
    }
});

test('F-R8-03', 'R8 yuzde 200 kontrolu metodunu acikca etkili reflow olarak tanimlar', function (): void {
    $browser = arc_r8_file('tools/browser/acceptance-check.mjs');
    assertContains('deviceScaleFactor: 2', $browser);
    assertContains('width: 640', $browser);
    assertContains('1280 physical px / deviceScaleFactor 2 => 640 CSS px layout viewport', $browser);
    assertContains('%200 etkili reflow', $browser);
});

test('F-R8-04', 'R8 klavye kabulunde skip-link ve mobil menu davranisi kilitlidir', function (): void {
    $browser = arc_r8_file('tools/browser/acceptance-check.mjs');
    foreach (['skip-link', "keyboard.press('Tab')", "keyboard.press('Enter')", "keyboard.press('Escape')", "'#icerik'", 'aria-expanded'] as $needle) {
        assertContains($needle, $browser, 'Eksik R8 klavye kriteri: ' . $needle);
    }
});

test('F-R8-05', 'R8 gercek Arapca rota ile RTL yonunu dar ve genis ekranda denetler', function (): void {
    $browser = arc_r8_file('tools/browser/acceptance-check.mjs');
    assertContains("BASE + '/ar/'", $browser);
    assertContains("rtl.lang === 'ar'", $browser);
    assertContains("rtl.dir === 'rtl'", $browser);
    assertContains("rtl.computed === 'rtl'", $browser);
});

test('F-R8-06', 'R8 reduced-motion on yuz ve panel sistem ailelerini birlikte kapsar', function (): void {
    $browser = arc_r8_file('tools/browser/acceptance-check.mjs');
    assertContains("reducedMotion: 'reduce'", $browser);
    assertContains('[data-reveal]', $browser);
    assertContains("BASE + '/panel/giris'", $browser);
    assertContains('maxDuration <= 0.01', $browser);
});

test('F-R8-07', 'R8 teslim kaydi kapsam metod sinir ve performans kapisini belgeler', function (): void {
    $doc = arc_r8_file('REDESIGN-R8-ACCEPTANCE.md');
    foreach ([
        '233d71aa9a0a88738d7976cec0cf0577c3583ba4',
        '320',
        '1920',
        '%200',
        '1280 fiziksel / 640 CSS',
        'native tarayıcı zoom',
        'Klavye',
        'RTL',
        'reduced-motion',
        'A-11',
        '60.0 fps',
        'canlı hosting',
    ] as $needle) {
        assertContains($needle, $doc, 'Eksik R8 teslim kaydi: ' . $needle);
    }
});
