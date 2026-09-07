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
            'title'   => $district['name'] . ' Web Tasarım',
            'slug'    => $district['slug'],
        ]);
        $db->update('districts', ['page_id' => $pageId], ['name' => $district['name']]);
    }
}

test('F-P5-a', 'Anasayfa tüm bölümleriyle işlenir', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();
    arc_link_districts($db);

    $response = arc_home();
    assertSame(200, $response->status(), 'Anasayfa 200 döndürmeli');

    $body = $response->body();

    assertContains('class="hero"', $body, 'Kahraman bölümü bulunmalı');
    assertContains('class="strip"', $body, 'Sektör şeridi bulunmalı');
    assertContains('class="cards"', $body, 'Hizmet kartları bulunmalı');
    assertContains('coast__svg', $body, 'Bölge haritası bulunmalı');
    assertContains('class="steps"', $body, 'Süreç adımları bulunmalı');
    assertContains('section--cta', $body, 'Çağrı bandı bulunmalı');
    assertContains('site-foot', $body, 'Alt bilgi bulunmalı');
});

test('O-01', 'Anasayfada tek H1 bulunur ve başlık hiyerarşisi atlamaz', function (): void {
    arc_need_db();
    HomeSection::ensureDefaults();

    $body = arc_home()->body();

    assertSame(1, substr_count($body, '<h1'), 'Sayfada tek H1 olmalı');

    // H1'den sonra H3 gelmeden once H2 gelmeli. Test E-05
    preg_match_all('/<h([1-6])\b/', $body, $matches);
    $levels = array_map('intval', $matches[1]);

    $previous = 0;
    foreach ($levels as $level) {
        if ($previous > 0) {
            assertTrue($level <= $previous + 1, "Başlık seviyesi atlanmış: h{$previous} sonrası h{$level}");
        }
        $previous = $level;
    }
});

test('E-03', 'Dekoratif şekiller aria-hidden taşır, anlamlı görseller alt metni', function (): void {
    arc_need_db();
    HomeSection::ensureDefaults();

    $body = arc_home()->body();

    assertContains('<div class="shapes" aria-hidden="true">', $body, 'Şekil kümesi aria-hidden olmalı');

    // Sekil kumesi icinde metin icerigi bulunmamali (ekran okuyucuya bilgi eklemez).
    if (preg_match('#<div class="shapes" aria-hidden="true">(.*?)</div>\s*</div>\s*</section>#s', $body, $m) === 1) {
        assertNotContains('<h', $m[1], 'Dekoratif kümede başlık olmamalı');
    }

    // Ic sayfalardaki icerik gorselleri alt metni tasimali.
    $missing = Arcates\Core\Seo::countImagesWithoutAlt($body);
    assertSame(0, $missing, 'Alt metni eksik görsel olmamalı');
});

test('E-07', 'SVG harita role ve açıklayıcı aria-label taşır', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();
    arc_link_districts($db);

    $body = arc_home()->body();

    assertContains('role="img"', $body, 'SVG role="img" taşımalı');
    assertContains('aria-label="', $body, 'SVG aria-label taşımalı');

    // Tum ilce adlari aria-label icinde gecmeli. DOCS.md 5.2
    if (preg_match('/<svg class="coast__svg"[^>]*aria-label="([^"]*)"/', $body, $m) === 1) {
        foreach (['Edremit', 'Akçay', 'Altınoluk', 'Burhaniye', 'Ayvalık', 'Gömeç', 'Havran', 'Balıkesir'] as $name) {
            assertContains($name, $m[1], "İlçe adı aria-label içinde olmalı: {$name}");
        }
    } else {
        assertTrue(false, 'coast__svg aria-label bulunamadı');
    }
});

test('F-16', 'Bölge haritasındaki ilçe noktaları ilgili sayfaya bağlanır', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();
    arc_link_districts($db);

    $figure = arc_extract_coast(arc_home()->body());

    // Her nokta ic link degeri tasir. DOCS.md 5.2
    assertContains('/edremit-web-tasarim', $figure, 'Edremit noktası sayfaya bağlanmalı');
    assertContains('/akcay-web-tasarim', $figure, 'Akçay noktası sayfaya bağlanmalı');

    // Noktalar map_x degerine gore yerlesir.
    assertContains('<circle cx="470"', $figure, 'Edremit noktası map_x konumunda olmalı');
    assertContains('viewBox="0 0 1000 190"', $figure, 'SVG viewBox şartnamedeki gibi olmalı');

    // Taslak sayfaya bagli nokta baglanti tasimaz.
    // Denetim yalnizca harita bolumunde yapilir; alt bilgideki baglantilar
    // panelden girilen serbest icerikten gelir.
    $db->run('UPDATE pages SET status = :status WHERE district = :district', [':status' => 'draft', ':district' => 'Edremit']);

    $draftFigure = arc_extract_coast(arc_home()->body());
    assertNotContains('/edremit-web-tasarim', $draftFigure, 'Taslak sayfaya harita üzerinden bağlantı verilmemeli');
    assertContains('/akcay-web-tasarim', $draftFigure, 'Yayındaki diğer noktalar bağlantılı kalmalı');
    assertContains('<circle cx="470"', $draftFigure, 'Nokta yerinde kalmalı, yalnızca bağlantısı kalkmalı');

    $db->run('DELETE FROM pages');
});

test('F-14', 'Anasayfa bölümü kapatılınca on yüzde görünmez', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();

    assertContains('class="strip"', arc_home()->body(), 'Şerit önce görünmeli');

    HomeSection::toggle('strip');
    assertNotContains('class="strip"', arc_home()->body(), 'Kapalı bölüm görünmemeli');

    HomeSection::toggle('strip');
    assertContains('class="strip"', arc_home()->body(), 'Yeniden açılınca görünmeli');
});

test('F-15', 'Kahraman başlığı değişince anasayfada anında yansır', function (): void {
    arc_need_db();
    HomeSection::ensureDefaults();

    $content = HomeSection::content('hero', 'tr');
    $content['line3'] = 'yepyeni bir başlık satırı';

    HomeSection::saveContent('hero', 'tr', $content);

    $body = arc_home()->body();
    assertContains('yepyeni bir başlık satırı', $body, 'Yeni başlık görünmeli');
    assertContains('hero__line--accent', $body, 'Üçüncü satır degrade sınıfını taşımalı');

    // Degrade metin gorsel degil, gercek metin olmali. DOCS.md 5.1
    assertNotContains('<img', substr($body, (int) strpos($body, 'hero__title'), 900), 'Degrade metin görsel olmamalı');
});

test('F-P5-b', 'Bölüm sırası sabittir', function (): void {
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
        assertTrue($position !== false, "Bölüm bulunmalı: {$key}");
        $order[$key] = (int) $position;
    }

    $values = array_values($order);
    $sorted = $values;
    sort($sorted);
    assertSame($sorted, $values, 'Bölümler şartnamedeki sırada olmalı');
});

test('A-08', 'Dar ekranda yatay kaydırma oluşmaz', function (): void {
    $css = arc_site_css();

    assertContains('overflow-x: hidden', $css, 'Gövde yatay taşmayı kesmeli');
    assertContains('width: min(100% - 40px', $css, 'Sarmalayıcı genişliği ekrandan küçük olmalı');
    assertContains('max-width: 100%', $css, 'Görseller taşmamalı');

    // 720 px altinda sekil olculeri kucultulur. DOCS.md 5.3
    assertContains('@media (max-width: 720px)', $css, 'Mobil kırılım bulunmalı');
    assertContains('.shapes { height: 288px; }', $css, 'Şekil kümesi yüksekliği %60\'a inmeli');
});
