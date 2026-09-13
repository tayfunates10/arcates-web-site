<?php
/**
 * Site ici arama.  DOCS.md 4.1
 *
 * Referansta ust menude bir arama dugmesi var; B1'de bilerek eklenmemisti
 * cunku arkasinda calisan bir arama yoktu. Bu modul o bosluğu kapatir.
 */

declare(strict_types=1);

use Arcates\Controllers\Front\SearchController;
use Arcates\Core\Lang;
use Arcates\Core\Request;
use Arcates\Models\Search;

/** Arama sayfasini uretir. */
function arc_search_body(string $q): string
{
    Lang::use('tr');

    return (new SearchController())
        ->index(Request::make('GET', '/ara', [], ['q' => $q]), [])->body();
}

test('F-SR-a', 'Kısa sorgu tabloyu taramaz', function (): void {
    // Tek karakterlik arama tum tabloyu tarar, sonucu da anlamsiz olur.
    assertFalse(Search::isValid('a'), 'Tek karakter geçersiz olmalı');
    assertFalse(Search::isValid(' '), 'Boşluk geçersiz olmalı');
    assertTrue(Search::isValid('ab'), 'İki karakter geçerli olmalı');

    assertSame([], Search::all('a', 'tr'), 'Kısa sorgu sonuç döndürmemeli');
});

test('F-SR-b', 'LIKE jokerleri harf olarak aranır', function (): void {
    arc_need_db();

    // Kacislanmazsa `%%%` butun tabloyu dondururdu.
    foreach (['%%%', '___', '\\\\\\'] as $joker) {
        $sonuc = Search::all($joker, 'tr');
        assertSame(0, Search::count($sonuc), 'Joker sorgusu sonuç döndürmemeli: ' . $joker);
    }
});

test('F-SR-c', 'Gerçek sorgu gruplanmış sonuç döndürür', function (): void {
    arc_need_db();

    $gruplar = Search::all('web', 'tr');
    assertTrue(Search::count($gruplar) > 0, 'Tohum içerikte "web" geçmeli');

    foreach ($gruplar as $tur => $satirlar) {
        assertTrue(in_array($tur, ['pages', 'posts', 'projects'], true), 'Bilinmeyen grup: ' . $tur);
        assertTrue($satirlar !== [], 'Boş grup döndürülmemeli');

        foreach ($satirlar as $satir) {
            assertTrue(($satir['title'] ?? '') !== '', 'Sonucun başlığı olmalı');
            assertTrue(($satir['url'] ?? '') !== '', 'Sonucun adresi olmalı');
        }
    }
});

test('F-SR-d', 'Sonuç sayfası arama motoruna kapalı', function (): void {
    arc_need_db();

    // Arama sonucu sayfalari ince icerik sayilir ve kendi sayfalarimizla
    // rekabet eder; dizine girmemeli.
    assertContains('noindex,follow', arc_search_body('web'), 'Sonuç sayfası noindex olmalı');
});

test('F-SR-e', 'Aranan metin sayfaya kaçışlanarak basılır', function (): void {
    arc_need_db();

    $body = arc_search_body('<script>alert(1)</script>');

    assertNotContains('<script>alert(1)</script>', $body, 'Ham girdi basılmamalı');
    assertContains('&lt;script&gt;', $body, 'Girdi kaçışlanmış olmalı');
});

test('F-SR-f', 'Üst menüdeki arama düğmesi arama sayfasına gider', function (): void {
    arc_need_db();
    Lang::use('tr');

    $body = (new Arcates\Controllers\Front\HomeController())
        ->index(Request::make('GET', '/'), [])->body();

    if (preg_match('~<a class="site-head__search".*?</a>~s', $body, $m) !== 1) {
        assertTrue(false, 'Arama düğmesi basılmalı');
        return;
    }

    assertContains('/ara', $m[0], 'Düğme arama sayfasına gitmeli');
    assertContains('visually-hidden', $m[0], 'Düğmenin erişilebilir adı olmalı');
    assertContains('aria-hidden="true"', $m[0], 'Büyüteç dekoratif olmalı');
});

test('F-SR-g', 'Arama rotası yakalayıcı slug deseninden önce tanımlı', function (): void {
    $routes = (string) file_get_contents(ARC_ROOT . '/config/routes.php');

    $ara   = strpos($routes, "'/ara'");
    $slug  = strpos($routes, "'/{slug:");

    assertTrue($ara !== false, 'Arama rotası tanımlı olmalı');
    assertTrue($slug !== false, 'Yakalayıcı desen tanımlı olmalı');

    // Sonra tanimlanirsa `/ara` bir sayfa slug'i sanilir ve arama hic
    // calismaz.
    assertTrue($ara < $slug, 'Arama rotası yakalayıcıdan önce gelmeli');
});
