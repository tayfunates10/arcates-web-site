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

/** Anasayfayi isler. */
function arc_home(string $lang = 'tr'): Arcates\Core\Response
{
    Lang::use($lang);
    return (new HomeController())->index(Request::make('GET', $lang === 'tr' ? '/' : '/' . $lang), []);
}

/** Anasayfa ciktisindan yalnizca bolge haritasi bolumunu ayirir. */
function arc_extract_coast(string $body): string
{
    $start = strpos($body, '<figure class="coast__figure"');
    if ($start === false) {
        return '';
    }
    $end = strpos($body, '</figure>', $start);
    return $end === false ? substr($body, $start) : substr($body, $start, $end - $start);
}

/** Ilce noktalarini yayindaki sayfalara baglar. */
function arc_link_districts(Database $db): void
{
    $db->run('DELETE FROM pages');

    foreach (Arcates\Core\Seeder::districtSeed() as $district) {
        $pageId = $db->insert('pages', [
            'type'     => 'location',
            'template' => 'location',
            'status'   => 'published',
            'district' => $district['name'],
        ]);
        $db->insert('page_translations', [
            'page_id' => $pageId,
            'lang'    => 'tr',
            'title'   => $district['name'] . ' Web Tasarim',
            'slug'    => $district['slug'],
        ]);
        $db->update('districts', ['page_id' => $pageId], ['name' => $district['name']]);
    }
}

test('F-P5-a', 'Anasayfa tum bolumleriyle islenir', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();
    arc_link_districts($db);

    $response = arc_home();
    assertSame(200, $response->status(), 'Anasayfa 200 dondurmeli');

    $body = $response->body();

    assertContains('class="hero"', $body, 'Kahraman bolumu bulunmali');
    assertContains('class="strip"', $body, 'Sektor seridi bulunmali');
    assertContains('class="cards"', $body, 'Hizmet kartlari bulunmali');
    assertContains('coast__svg', $body, 'Bolge haritasi bulunmali');
    assertContains('class="steps"', $body, 'Surec adimlari bulunmali');
    assertContains('section--cta', $body, 'Cagri bandi bulunmali');
    assertContains('site-foot', $body, 'Alt bilgi bulunmali');
});

test('O-01', 'Anasayfada tek H1 bulunur ve baslik hiyerarsisi atlamaz', function (): void {
    arc_need_db();
    HomeSection::ensureDefaults();

    $body = arc_home()->body();

    assertSame(1, substr_count($body, '<h1'), 'Sayfada tek H1 olmali');

    // H1'den sonra H3 gelmeden once H2 gelmeli. Test E-05
    preg_match_all('/<h([1-6])\b/', $body, $matches);
    $levels = array_map('intval', $matches[1]);

    $previous = 0;
    foreach ($levels as $level) {
        if ($previous > 0) {
            assertTrue($level <= $previous + 1, "Baslik seviyesi atlanmis: h{$previous} sonrasi h{$level}");
        }
        $previous = $level;
    }
});

test('E-03', 'Dekoratif sekiller aria-hidden tasir, anlamli gorseller alt metni', function (): void {
    arc_need_db();
    HomeSection::ensureDefaults();

    $body = arc_home()->body();

    assertContains('<div class="shapes" aria-hidden="true">', $body, 'Sekil kumesi aria-hidden olmali');

    // Sekil kumesi icinde metin icerigi bulunmamali (ekran okuyucuya bilgi eklemez).
    if (preg_match('#<div class="shapes" aria-hidden="true">(.*?)</div>\s*</div>\s*</section>#s', $body, $m) === 1) {
        assertNotContains('<h', $m[1], 'Dekoratif kumede baslik olmamali');
    }

    // Ic sayfalardaki icerik gorselleri alt metni tasimali.
    $missing = Arcates\Core\Seo::countImagesWithoutAlt($body);
    assertSame(0, $missing, 'Alt metni eksik gorsel olmamali');
});

test('E-07', 'SVG harita role ve aciklayici aria-label tasir', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();
    arc_link_districts($db);

    $body = arc_home()->body();

    assertContains('role="img"', $body, 'SVG role="img" tasimali');
    assertContains('aria-label="', $body, 'SVG aria-label tasimali');

    // Tum ilce adlari aria-label icinde gecmeli. DOCS.md 5.2
    if (preg_match('/<svg class="coast__svg"[^>]*aria-label="([^"]*)"/', $body, $m) === 1) {
        foreach (['Edremit', 'Akcay', 'Altinoluk', 'Burhaniye', 'Ayvalik', 'Gomec', 'Havran', 'Balikesir'] as $name) {
            assertContains($name, $m[1], "Ilce adi aria-label icinde olmali: {$name}");
        }
    } else {
        assertTrue(false, 'coast__svg aria-label bulunamadi');
    }
});

test('F-16', 'Bolge haritasindaki ilce noktalari ilgili sayfaya baglanir', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();
    arc_link_districts($db);

    $figure = arc_extract_coast(arc_home()->body());

    // Her nokta ic link degeri tasir. DOCS.md 5.2
    assertContains('/edremit-web-tasarim', $figure, 'Edremit noktasi sayfaya baglanmali');
    assertContains('/akcay-web-tasarim', $figure, 'Akcay noktasi sayfaya baglanmali');

    // Noktalar map_x degerine gore yerlesir.
    assertContains('<circle cx="470"', $figure, 'Edremit noktasi map_x konumunda olmali');
    assertContains('viewBox="0 0 1000 190"', $figure, 'SVG viewBox sartnamedeki gibi olmali');

    // Taslak sayfaya bagli nokta baglanti tasimaz.
    // Denetim yalnizca harita bolumunde yapilir; alt bilgideki baglantilar
    // panelden girilen serbest icerikten gelir.
    $db->run('UPDATE pages SET status = :status WHERE district = :district', [':status' => 'draft', ':district' => 'Edremit']);

    $draftFigure = arc_extract_coast(arc_home()->body());
    assertNotContains('/edremit-web-tasarim', $draftFigure, 'Taslak sayfaya harita uzerinden baglanti verilmemeli');
    assertContains('/akcay-web-tasarim', $draftFigure, 'Yayindaki diger noktalar baglantili kalmali');
    assertContains('<circle cx="470"', $draftFigure, 'Nokta yerinde kalmali, yalnizca baglantisi kalkmali');

    $db->run('DELETE FROM pages');
});

test('F-14', 'Anasayfa bolumu kapatilinca on yuzde gorunmez', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();

    assertContains('class="strip"', arc_home()->body(), 'Serit once gorunmeli');

    HomeSection::toggle('strip');
    assertNotContains('class="strip"', arc_home()->body(), 'Kapali bolum gorunmemeli');

    HomeSection::toggle('strip');
    assertContains('class="strip"', arc_home()->body(), 'Yeniden acilinca gorunmeli');
});

test('F-15', 'Kahraman basligi degisince anasayfada aninda yansir', function (): void {
    arc_need_db();
    HomeSection::ensureDefaults();

    $content = HomeSection::content('hero', 'tr');
    $content['line3'] = 'yepyeni bir baslik satiri';

    HomeSection::saveContent('hero', 'tr', $content);

    $body = arc_home()->body();
    assertContains('yepyeni bir baslik satiri', $body, 'Yeni baslik gorunmeli');
    assertContains('hero__line--accent', $body, 'Ucuncu satir degrade sinifini tasimali');

    // Degrade metin gorsel degil, gercek metin olmali. DOCS.md 5.1
    assertNotContains('<img', substr($body, (int) strpos($body, 'hero__title'), 900), 'Degrade metin gorsel olmamali');
});

test('F-P5-b', 'Bolum sirasi sabittir', function (): void {
    arc_need_db();
    HomeSection::ensureDefaults();

    $body = arc_home()->body();

    $order = [];
    foreach ([
        'hero'  => 'class="hero"',
        'strip' => 'class="strip"',
        'cards' => 'class="cards"',
        'coast' => 'coast__svg',
        'steps' => 'class="steps"',
        'cta'   => 'section--cta',
    ] as $key => $needle) {
        $position = strpos($body, $needle);
        assertTrue($position !== false, "Bolum bulunmali: {$key}");
        $order[$key] = (int) $position;
    }

    $values = array_values($order);
    $sorted = $values;
    sort($sorted);
    assertSame($sorted, $values, 'Bolumler sartnamedeki sirada olmali');
});

test('A-08', 'Dar ekranda yatay kaydirma olusmaz', function (): void {
    $css = arc_site_css();

    assertContains('overflow-x: hidden', $css, 'Govde yatay tasmayi kesmeli');
    assertContains('width: min(100% - 40px', $css, 'Sarmalayici genisligi ekrandan kucuk olmali');
    assertContains('max-width: 100%', $css, 'Gorseller tasmamali');

    // 720 px altinda sekil olculeri kucultulur. DOCS.md 5.3
    assertContains('@media (max-width: 720px)', $css, 'Mobil kirilim bulunmali');
    assertContains('.shapes { height: 288px; }', $css, 'Sekil kumesi yuksekligi %60\'a inmeli');
});
