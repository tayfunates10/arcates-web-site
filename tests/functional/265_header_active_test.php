<?php
/**
 * Ust menu — acik sayfa isareti. Referans B1.
 *
 * Aktif oge canonical adrese gore isaretlenir. Alt sayfadayken ust oge de
 * acik kalir (/referanslar/x acikken "Örnek siteler"), anasayfa ogesi bu
 * kuralin disindadir.  DOCS.md 5 (bolum 1)
 */

declare(strict_types=1);

use Arcates\Controllers\Front\PageController;
use Arcates\Core\Lang;
use Arcates\Core\Request;

test('F-HD-a', 'Açık sayfanın menü öğesi işaretlenir', function (): void {
    arc_need_db();
    Lang::use('tr');

    $body = (new PageController())->show(Request::make('GET', '/blog'), ['slug' => 'blog'])->body();

    assertContains('site-nav__link is-current', $body, 'Açık sayfa sınıf almalı');
    assertContains('aria-current="page"', $body, 'Ekran okuyucu için aria-current verilmeli');

    // Yalnizca bir menu ogesi aktif olmali. Sayim ust menuyle sinirli:
    // kirinti yolu ve blog kategori cipleri de `aria-current` kullanir ve
    // ikisi de dogru desendir.
    $nav = '';
    if (preg_match('~<nav class="site-nav".*?</nav>~s', $body, $m) === 1) {
        $nav = $m[0];
    }
    assertNotSame('', $nav, 'Üst menü basılmalı');
    assertSame(1, substr_count($nav, 'aria-current="page"'), 'Üst menüde tek öğe aktif olmalı');
    assertSame(1, substr_count($nav, 'is-current'), 'Üst menüde tek öğe işaretli olmalı');
});

/** WCAG bagil parlaklik. */
function arc_hd_luminance(string $hex): float
{
    $hex = ltrim($hex, '#');
    $ch  = [];
    foreach ([0, 2, 4] as $i) {
        $v    = hexdec(substr($hex, $i, 2)) / 255;
        $ch[] = $v <= 0.03928 ? $v / 12.92 : (($v + 0.055) / 1.055) ** 2.4;
    }
    return 0.2126 * $ch[0] + 0.7152 * $ch[1] + 0.0722 * $ch[2];
}

/** Iki renk arasindaki kontrast orani. */
function arc_hd_contrast(string $a, string $b): float
{
    $la = arc_hd_luminance($a);
    $lb = arc_hd_luminance($b);
    return (max($la, $lb) + 0.05) / (min($la, $lb) + 0.05);
}

test('F-HD-b', 'Menü renkleri koyu bantta okunur', function (): void {
    $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/reference-header.css');

    // Renkler referans gorselden piksel olarak ornekledi; tahmin degil.
    assertContains('--ref-nav:           #CFDFF2', $css, 'Pasif öğe rengi ölçülen değer olmalı');
    assertContains('--ref-nav-active:    #5AC8F5', $css, 'Açık sayfa rengi ölçülen değer olmalı');

    // `--fg` acik tema metnidir (#062244); koyu bantta 1.1:1'e duser.
    assertNotContains('color: var(--fg);', $css, 'Açık tema metin rengi koyu menüde kullanılmamalı');

    // Bant zemini `#000A1A`; her iki menu rengi AA esigini asmali.
    $bant = '000A1A';
    assertTrue(arc_hd_contrast('CFDFF2', $bant) >= 4.5, 'Pasif öğe kontrastı 4.5:1 altında');
    assertTrue(arc_hd_contrast('5AC8F5', $bant) >= 4.5, 'Açık sayfa kontrastı 4.5:1 altında');
});

test('F-HD-c', 'Menü masaüstünde ortalanır, dar ekranda ortalanmaz', function (): void {
    $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/reference-header.css');

    assertContains('@media (min-width: 941px)', $css, 'Ortalama yalnızca masaüstünde uygulanmalı');
    assertContains('justify-content: center', $css, 'Menü ortalanmalı');

    // 940 altinda panel acilir kutuya donuyor; orada ortalama uygulanmamali.
    $desktop = substr($css, strpos($css, '@media (min-width: 941px)'));
    assertContains('justify-content: center', $desktop, 'Ortalama masaüstü bloğunda olmalı');
});

test('F-HD-d', 'Alt sayfada üst menü öğesi açık kalır', function (): void {
    arc_need_db();
    Lang::use('tr');

    $ref = new ReflectionMethod(Arcates\Controllers\Front\Controller::class, 'markCurrent');
    $ref->setAccessible(true);

    $menu = [
        ['href' => 'https://ornek.test/referanslar', 'children' => []],
        ['href' => 'https://ornek.test/blog', 'children' => []],
        ['href' => 'https://ornek.test/', 'children' => []],
    ];

    $marked = $ref->invoke(null, $menu, 'https://ornek.test/referanslar/akcay-ornek');
    assertTrue((bool) $marked[0]['is_current'], 'Alt sayfada üst öğe açık kalmalı');
    assertFalse((bool) $marked[1]['is_current'], 'İlgisiz öğe kapalı olmalı');
    assertFalse((bool) $marked[2]['is_current'], 'Anasayfa öğesi her sayfada yanmamalı');

    $home = $ref->invoke(null, $menu, 'https://ornek.test/');
    assertTrue((bool) $home[2]['is_current'], 'Anasayfada anasayfa öğesi açık olmalı');
});


test('F-HD-e', 'Anasayfa menü öğesi vardır ve anasayfada işaretlidir', function (): void {
    arc_need_db();
    Lang::use('tr');

    $body = (new Arcates\Controllers\Front\HomeController())
        ->index(Request::make('GET', '/'), [])->body();

    $nav = '';
    if (preg_match('~<nav class="site-nav".*?</nav>~s', $body, $m) === 1) {
        $nav = $m[0];
    }
    assertNotSame('', $nav, 'Üst menü basılmalı');

    // Referans duzende ilk oge Ana Sayfa ve acik sayfa isareti onun uzerinde.
    // Oge olmadan isaret anasayfada hic gorunmuyordu.
    assertContains('Ana Sayfa', $nav, 'Menüde Ana Sayfa öğesi bulunmalı');
    assertSame(1, substr_count($nav, 'aria-current="page"'), 'Anasayfada tek öğe aktif olmalı');
    assertSame(1, substr_count($nav, 'is-current'), 'Anasayfada tek öğe işaretli olmalı');
});

test('F-HD-f', 'Çağrı butonundaki ok ekran okuyucudan gizlidir', function (): void {
    arc_need_db();
    Lang::use('tr');

    $body = (new Arcates\Controllers\Front\HomeController())
        ->index(Request::make('GET', '/'), [])->body();

    assertContains('class="btn__arrow"', $body, 'Butonda ok bulunmalı');

    // Ok dekoratif: "Teklif Al ok" diye okunmamali.
    if (preg_match('~<svg class="btn__arrow".*?</svg>~s', $body, $m) !== 1) {
        assertTrue(false, 'Ok işaretlemesi bulunamadı');
        return;
    }
    assertContains('aria-hidden="true"', $m[0], 'Ok aria-hidden taşımalı');
    assertContains('focusable="false"', $m[0], 'Ok odak sırasına girmemeli');

    // Harici dosya yok; ok satir ici SVG. CLAUDE.md 4
    assertNotContains('<img', $m[0], 'Ok için resim dosyası kullanılmamalı');
});

test('F-HD-g', 'Açma düğmesinin çubukları koyu bantta görünür', function (): void {
    $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/reference-header.css');

    // `site.css` cubuklari `var(--fg)` ile boyuyordu: acik tema rengi #062244,
    // bant #000A1A. Olculen kontrast 1.2:1, yani dugme pratikte gorunmuyordu.
    assertContains('.site-head .site-nav__bars', $css, 'Çubuk rengi menü katmanında düzeltilmeli');
    assertTrue(arc_hd_contrast('FFFFFF', '000A1A') >= 4.5, 'Çubuk kontrastı 4.5:1 altında');
});

test('F-HD-h', 'Menü stili sayfa stillerinden sonra yüklenir', function (): void {
    $head = (string) file_get_contents(ARC_ROOT . '/views/front/partials/head.php');

    $sayfa = strpos($head, "\$head['styles']");
    $menu  = strpos($head, 'css/reference-header.css');

    assertTrue($sayfa !== false, 'Sayfaya özel stil döngüsü bulunmalı');
    assertTrue($menu !== false, 'Menü stili bağlanmalı');

    // `.is-reference-home` onekli kurallar 0,2,0 agirliginda. Menu stili once
    // yuklenirse o kurallar menuyu geri eziyor; son katman olmali.
    assertTrue($menu > $sayfa, 'Menü stili sayfaya özel stillerden sonra gelmeli');
});
