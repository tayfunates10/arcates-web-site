<?php
/**
 * Anasayfa isleme ve erisilebilirlik.
 * R5: hero → sector links → services → steps → works → coast → faq → cta.
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

function arc_seed_sector_links(Database $db): void
{
    $db->run('DELETE FROM pages WHERE type = :type', [':type' => 'sector']);

    $publishedId = $db->insert('pages', [
        'type' => 'sector', 'template' => 'sector', 'status' => 'published', 'sort' => 1,
    ]);
    $db->insert('page_translations', [
        'page_id' => $publishedId, 'lang' => 'tr', 'title' => 'Otel ve Pansiyon Web Sitesi', 'slug' => 'otel-pansiyon-web-sitesi',
    ]);

    $draftId = $db->insert('pages', [
        'type' => 'sector', 'template' => 'sector', 'status' => 'draft', 'sort' => 2,
    ]);
    $db->insert('page_translations', [
        'page_id' => $draftId, 'lang' => 'tr', 'title' => 'Taslak Sektör', 'slug' => 'taslak-sektor',
    ]);
}

test('F-P5-a', 'R5 anasayfa temel bölümleriyle işlenir', function (): void {
    $db = arc_need_db(); HomeSection::ensureDefaults(); arc_link_districts($db);
    $response = arc_home(); assertSame(200, $response->status());
    $body = $response->body();
    foreach ([
        'hero hero--redesign',
        'sector-links__grid',
        'cards cards--services',
        'steps steps--connected',
        'home-coast',
        'home-cta',
        'site-foot',
    ] as $needle) assertContains($needle, $body);
    assertContains('css/home-redesign.css', $body, 'R5 stili yalniz anasayfaya eklenmeli');
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

test('E-03', 'Hero sahnesi dekoratif, anlamlı görseller alt metinlidir', function (): void {
    arc_need_db(); HomeSection::ensureDefaults(); $body = arc_home()->body();
    assertContains('<div class="hero-scene" aria-hidden="true">', $body);
    assertContains('alt="" aria-hidden="true"', $body, 'Dekoratif hero logo gorseli acikca gizlenmeli');
    if (preg_match('#<div class="hero-scene" aria-hidden="true">(.*?)</div>\s*</div>\s*</section>#s', $body, $m) === 1) {
        assertNotContains('<h', $m[1]);
    }
    assertSame(0, Arcates\Core\Seo::countImagesWithoutAlt($body));
});

test('E-07', 'SVG bölge grafiği role ve açıklayıcı aria-label taşır', function (): void {
    $db = arc_need_db(); HomeSection::ensureDefaults(); arc_link_districts($db); $body = arc_home()->body();
    assertContains('role="img"', $body); assertContains('aria-label="', $body);
    assertContains('coast__note', $body);
    assertContains('coğrafi sınır değildir', $body);
    if (preg_match('/<svg class="coast__svg"[^>]*aria-label="([^"]*)"/', $body, $m) === 1) {
        foreach (['Edremit','Akçay','Altınoluk','Burhaniye','Ayvalık','Gömeç','Havran','Balıkesir'] as $name) assertContains($name, $m[1]);
    } else assertTrue(false, 'coast__svg aria-label bulunamadi');
});

test('F-16', 'Bölge grafiğindeki ilçeler ilgili sayfalara bağlanır', function (): void {
    $db = arc_need_db(); HomeSection::ensureDefaults(); arc_link_districts($db);
    $figure = arc_extract_coast(arc_home()->body());
    assertContains('/edremit-web-tasarim', $figure); assertContains('/akcay-web-tasarim', $figure);
    assertContains('<circle cx="470"', $figure); assertContains('viewBox="0 0 1000 190"', $figure);
    $db->run('UPDATE pages SET status = :status WHERE district = :district', [':status' => 'draft', ':district' => 'Edremit']);
    $draftFigure = arc_extract_coast(arc_home()->body());
    assertNotContains('/edremit-web-tasarim', $draftFigure); assertContains('/akcay-web-tasarim', $draftFigure); assertContains('<circle cx="470"', $draftFigure);
    $db->run('DELETE FROM pages');
});

test('F-14', 'Anasayfa bölümü kapatılınca R5 bileşeni görünmez', function (): void {
    $db = arc_need_db(); HomeSection::ensureDefaults();
    assertContains('sector-links', arc_home()->body());
    HomeSection::toggle('strip'); assertNotContains('sector-links', arc_home()->body());
    HomeSection::toggle('strip'); assertContains('sector-links', arc_home()->body());
});

test('F-15', 'Kahraman başlığı değişince R5 hero üzerinde anında yansır', function (): void {
    arc_need_db(); HomeSection::ensureDefaults();
    $content = HomeSection::content('hero', 'tr'); $content['line3'] = 'yepyeni bir başlık satırı'; HomeSection::saveContent('hero', 'tr', $content);
    $body = arc_home()->body();
    assertContains('yepyeni bir başlık satırı', $body);
    assertContains('hero__line--accent', $body);
    assertContains('hero-scene', $body);
});

test('F-P5-b', 'R5 bölüm sırası sabittir', function (): void {
    $home = (string) file_get_contents(ARC_ROOT . '/views/front/home.php');
    $order = [];
    foreach ([
        'hero' => "partial('front/partials/hero'",
        'strip' => "partial('front/partials/strip'",
        'services' => "partial('front/partials/cards'",
        'steps' => "partial('front/partials/steps'",
        'works' => "partial('front/partials/works'",
        'coast' => "partial('front/partials/coast'",
        'faq' => "partial('front/partials/faq'",
        'cta' => "partial('front/partials/cta'",
    ] as $key => $needle) {
        $position = strpos($home, $needle);
        assertTrue($position !== false, 'Bolum bulunmali: ' . $key);
        $order[$key] = (int) $position;
    }
    $values = array_values($order); $sorted = $values; sort($sorted); assertSame($sorted, $values);
});

test('F-R5-01', 'Sektör grubu yalnız yayınlanmış gerçek sayfalara bağlantı verir', function (): void {
    $db = arc_need_db(); HomeSection::ensureDefaults(); arc_seed_sector_links($db);
    $body = arc_home()->body();
    assertContains('/otel-pansiyon-web-sitesi', $body);
    assertContains('Otel ve Pansiyon Web Sitesi', $body);
    assertNotContains('/taslak-sektor', $body);
    assertNotContains('Taslak Sektör', $body);
    assertNotContains('data-strip', $body);
});

test('F-R5-02', 'CTA WhatsApp hedefini yalnız NAP telefonundan üretir', function (): void {
    $cta = (string) file_get_contents(ARC_ROOT . '/views/front/partials/cta.php');
    assertContains("Settings::get('nap_phone'", $cta);
    assertContains('https://wa.me/', $cta);
    assertContains("preg_replace('/\\D+/'", $cta);
    assertNotContains('905359120691', $cta, 'Telefon sabitlenmemeli; NAP tek kaynak olmali');
});

test('A-08', 'Dar ekranda R5 bileşenleri responsive kurallara sahiptir', function (): void {
    $site = arc_site_css();
    $home = arc_home_css();
    assertContains('overflow-x: hidden', $site, 'Govde yatay tasmayi kesmeli');
    assertContains('width: min(calc(100% - (var(--sp-5) * 2)), var(--wrap))', $site);
    assertContains('@media (max-width: 45rem)', $home);
    assertContains('.hero-scene { min-height: 300px;', $home);
    assertContains('.sector-links__grid { grid-template-columns: repeat(2, 1fr); }', $home);
    assertContains('.cards--services { grid-template-columns: 1fr; }', $home);
    assertContains('.works--showcase { grid-template-columns: 1fr; }', $home);
});
