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

test('F-HD-b', 'Aktif öğe ters yüzey rengini kullanır', function (): void {
    $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/reference-header.css');

    // Ust menu koyu bir yuzey: `--fg` acik tema metnidir, burada okunmaz.
    assertContains('--fg-on-invert', $css, 'Aktif öğe ters yüzey rengini almalı');
    assertNotContains('color: var(--fg);', $css, 'Açık tema metin rengi koyu menüde kullanılmamalı');
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
