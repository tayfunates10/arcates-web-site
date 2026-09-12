<?php
/**
 * Projeler, surec ve "Neden ARCATES" — referans olculeri. Referans B5.
 *
 * Olculer `public/assets/css/reference-lower.css` basindaki yorumda.
 * DOCS.md 5 (bolum 5)
 */

declare(strict_types=1);

test('F-LW-a', 'Alt bölüm stili sayfa stillerinin sonunda yüklenir', function (): void {
    $src = (string) file_get_contents(ARC_ROOT . '/app/Controllers/Front/HomeController.php');

    if (preg_match("~'styles'\s*=>\s*\[(.*?)\]~s", $src, $m) !== 1) {
        assertTrue(false, 'Stil listesi bulunmalı');
        return;
    }

    $sections = strpos($m[1], 'css/reference-sections.css');
    $lower    = strpos($m[1], 'css/reference-lower.css');

    assertTrue($lower !== false, 'Alt bölüm stili listede olmalı');
    assertTrue($sections !== false && $lower > $sections, 'Alt bölüm stili bölüm stilinden sonra gelmeli');
});

test('F-LW-b', 'Süreç adımları arasına ok başı basılır', function (): void {
    arc_need_db();
    Arcates\Core\Lang::use('tr');

    $body = (new Arcates\Controllers\Front\HomeController())
        ->index(Arcates\Core\Request::make('GET', '/'), [])->body();

    if (preg_match('~<ol class="ref-process__list">.*?</ol>~s', $body, $m) !== 1) {
        assertTrue(false, 'Süreç listesi basılmalı');
        return;
    }

    $adim = substr_count($m[0], '<li>');
    $ok   = substr_count($m[0], 'class="ref-process__link"');

    assertTrue($adim >= 2, 'En az iki adım olmalı');

    // Referansta her bosluk bir ok basiyla bitiyor; son adimdan sonra
    // baglayici yok. Yani ok sayisi adim sayisinin bir eksigi.
    assertSame($adim - 1, $ok, 'Bağlayıcı son adımdan sonra basılmamalı');

    assertContains('aria-hidden="true"', $m[0], 'Bağlayıcı dekoratif olmalı');
});

test('F-LW-c', 'Neden ARCATES madde ikonu dolu mavi zemin taşır', function (): void {
    $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/reference-lower.css');

    // Referansta ikon dolu mavi bir dairenin icinde beyaz duruyor. Bizde
    // daire koyu lacivert bir gradyandi (#0b2c4c -> #07192b) ve zeminden
    // neredeyse ayirt edilemiyordu.
    if (preg_match('~\.ref-why__body li > span\s*\{([^}]*)\}~s', $css, $m) !== 1) {
        assertTrue(false, 'Madde ikonu kuralı bulunmalı');
        return;
    }

    assertContains('#0F63E0', $m[1], 'Ölçülen mavi zemin kullanılmalı');
    assertContains('color: #fff', $m[1], 'Glif beyaz olmalı');
    assertNotContains('#0b2c4c', $m[1], 'Eski koyu gradyan kalmamalı');
});

test('F-LW-d', 'Bağlayıcı dar ekranda gizlenir', function (): void {
    $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/reference-lower.css');

    // 940 px altinda liste dikey akiyor ve dikey cizgiyi listenin kendi
    // `::before`'u ciziyor; yatay baglayici orada anlamsiz.
    assertContains('@media (max-width: 940px)', $css, 'Dar ekran kuralı olmalı');

    $dar = substr($css, strpos($css, '@media (max-width: 940px)'));
    assertContains('.ref-process__link { display: none; }', $dar, 'Bağlayıcı dar ekranda gizlenmeli');
});
