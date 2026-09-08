<?php
/**
 * Anasayfa isleme ve erisilebilirlik.
 * DOCS.md 5, 14.7 — testler F-14, F-15, F-16, E-03, E-05, E-07, O-01
 */
declare(strict_types=1);

use Arcates\Controllers\Front\HomeController;
use Arcates\Core\Database;
use Arcates\Core\Lang;
use Arcates\Core\Request;
use Arcates\Models\HomeSection;

function arc_home(string $lang = 'tr'): Arcates\Core\Response
{
    Lang::use($lang);
    return (new HomeController())->index(Request::make('GET', $lang === 'tr' ? '/' : '/' . $lang), []);
}

function arc_extract_coast(string $body): string
{
    $start = strpos($body, '<figure class="coast__figure"');
    if ($start === false) return '';
    $end = strpos($body, '</figure>', $start);
    return $end === false ? substr($body, $start) : substr($body, $start, $end - $start);
}

function arc_link_districts(Database $db): void
{
    $db->run('DELETE FROM pages');
    foreach (Arcates\Core\Seeder::districtSeed() as $district) {
        $pageId = $db->insert('pages', [
            'type' => 'location', 'template' => 'location', 'status' => 'published', 'district' => $district['name'],
        ]);
        $db->insert('page_translations', [
            'page_id' => $pageId, 'lang' => 'tr', 'title' => $district['name'] . ' Web Tasarım', 'slug' => $district['slug'],
        ]);
        $db->update('districts', ['page_id' => $pageId], ['name' => $district['name']]);
    }
}

test('F-P5-a', 'Anasayfa tüm bölümleriyle işlenir', function (): void {
    $db = arc_need_db(); HomeSection::ensureDefaults(); arc_link_districts($db);
    $response = arc_home(); assertSame(200, $response->status());
    $body = $response->body();
    foreach (['class="hero"','class="strip"','class="cards"','coast__svg','class="steps"','section--cta','site-foot'] as $needle) assertContains($needle, $body);
});

test('O-01', 'Anasayfada tek H1 bulunur ve başlık hiyerarşisi atlamaz', function (): void {
    arc_need_db(); HomeSection::ensureDefaults(); $body = arc_home()->body();
    assertSame(1, substr_count($body, '<h1'));
    preg_match_all('/<h([1-6])\b/', $body, $matches);
    $levels = array_map('intval', $matches[1]); $previous = 0;
    foreach ($levels as $level) {
        if ($previous > 0) assertTrue($level <= $previous + 1, "Baslik seviyesi atlanmis: h{$previous} sonrasi h{$level}");
        $previous = $level;
    }
});

test('E-03', 'Dekoratif şekiller aria-hidden taşır, anlamlı görseller alt metni', function (): void {
    arc_need_db(); HomeSection::ensureDefaults(); $body = arc_home()->body();
    assertContains('<div class="shapes" aria-hidden="true">', $body);
    if (preg_match('#<div class="shapes" aria-hidden="true">(.*?)</div>\s*</div>\s*</section>#s', $body, $m) === 1) assertNotContains('<h', $m[1]);
    assertSame(0, Arcates\Core\Seo::countImagesWithoutAlt($body));
});

test('E-07', 'SVG harita role ve açıklayıcı aria-label taşır', function (): void {
    $db = arc_need_db(); HomeSection::ensureDefaults(); arc_link_districts($db); $body = arc_home()->body();
    assertContains('role="img"', $body); assertContains('aria-label="', $body);
    if (preg_match('/<svg class="coast__svg"[^>]*aria-label="([^"]*)"/', $body, $m) === 1) {
        foreach (['Edremit','Akçay','Altınoluk','Burhaniye','Ayvalık','Gömeç','Havran','Balıkesir'] as $name) assertContains($name, $m[1]);
    } else assertTrue(false, 'coast__svg aria-label bulunamadi');
});

test('F-16', 'Bölge haritasındaki ilçe noktaları ilgili sayfaya bağlanır', function (): void {
    $db = arc_need_db(); HomeSection::ensureDefaults(); arc_link_districts($db);
    $figure = arc_extract_coast(arc_home()->body());
    assertContains('/edremit-web-tasarim', $figure); assertContains('/akcay-web-tasarim', $figure);
    assertContains('<circle cx="470"', $figure); assertContains('viewBox="0 0 1000 190"', $figure);
    $db->run('UPDATE pages SET status = :status WHERE district = :district', [':status' => 'draft', ':district' => 'Edremit']);
    $draftFigure = arc_extract_coast(arc_home()->body());
    assertNotContains('/edremit-web-tasarim', $draftFigure); assertContains('/akcay-web-tasarim', $draftFigure); assertContains('<circle cx="470"', $draftFigure);
    $db->run('DELETE FROM pages');
});

test('F-14', 'Anasayfa bölümü kapatılınca on yüzde görünmez', function (): void {
    $db = arc_need_db(); HomeSection::ensureDefaults();
    assertContains('class="strip"', arc_home()->body());
    HomeSection::toggle('strip'); assertNotContains('class="strip"', arc_home()->body());
    HomeSection::toggle('strip'); assertContains('class="strip"', arc_home()->body());
});

test('F-15', 'Kahraman başlığı değişince anasayfada anında yansır', function (): void {
    arc_need_db(); HomeSection::ensureDefaults();
    $content = HomeSection::content('hero', 'tr'); $content['line3'] = 'yepyeni bir başlık satırı'; HomeSection::saveContent('hero', 'tr', $content);
    $body = arc_home()->body();
    assertContains('yepyeni bir başlık satırı', $body); assertContains('hero__line--accent', $body);
    assertNotContains('<img', substr($body, (int) strpos($body, 'hero__title'), 900));
});

test('F-P5-b', 'Bölüm sırası sabittir', function (): void {
    arc_need_db(); HomeSection::ensureDefaults(); $body = arc_home()->body();
    $order = [];
    foreach (['hero'=>'class="hero"','strip'=>'class="strip"','cards'=>'class="cards"','coast'=>'coast__svg','steps'=>'class="steps"','cta'=>'section--cta'] as $key=>$needle) {
        $position = strpos($body, $needle); assertTrue($position !== false, 'Bolum bulunmali: ' . $key); $order[$key] = (int) $position;
    }
    $values = array_values($order); $sorted = $values; sort($sorted); assertSame($sorted, $values);
});

test('A-08', 'Dar ekranda yatay kaydırma oluşmaz', function (): void {
    $css = arc_site_css();
    assertContains('overflow-x: hidden', $css, 'Govde yatay tasmayi kesmeli');
    assertContains('width: min(calc(100% - (var(--sp-5) * 2)), var(--wrap))', $css, 'Sarmalayici spacing tokeni kullanmali');
    assertContains('max-width: 100%', $css, 'Gorseller tasmamali');
    // v1.1 breakpoint sozlesmesi: 720px = 45rem, sekil alani 288px = 18rem.
    assertContains('@media (max-width: 45rem)', $css, '720px mobil kirilimi rem olarak bulunmali');
    assertContains('.shapes { block-size: 18rem;', $css, 'Sekil kumesi 720 altinda 18rem olmali');
});
