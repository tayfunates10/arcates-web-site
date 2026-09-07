<?php
/**
 * SEO modulu — sitemap, robots, semalar, canonical, ic linkler.
 * DOCS.md 11, 14.8 — testler O-01…O-08, U-14, F-13
 */

declare(strict_types=1);

use Arcates\Controllers\Front\RobotsController;
use Arcates\Controllers\Front\SitemapController;
use Arcates\Core\Database;
use Arcates\Core\Lang;
use Arcates\Core\Request;
use Arcates\Core\Seo;
use Arcates\Core\Settings;

/** Sitemap ciktisini uretir. */
function arc_sitemap(): string
{
    return (new SitemapController())->build();
}

/** Test icin bir sayfa olusturur. */
function arc_page(Database $db, array $page, array $translation): int
{
    $id = $db->insert('pages', array_merge([
        'type' => 'page', 'template' => 'page', 'status' => 'published',
    ], $page));

    $db->insert('page_translations', array_merge([
        'page_id' => $id, 'lang' => 'tr', 'robots' => 'index,follow',
    ], $translation));

    return $id;
}

test('U-14', 'Sitemap uretiminde taslak icerik yer almaz', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM pages');

    arc_page($db, ['status' => 'published'], ['title' => 'Yayindaki', 'slug' => 'yayindaki-sayfa']);
    arc_page($db, ['status' => 'draft'], ['title' => 'Taslak', 'slug' => 'taslak-sayfa']);

    $xml = arc_sitemap();

    assertContains('/yayindaki-sayfa', $xml, 'Yayindaki sayfa haritada olmali');
    assertNotContains('/taslak-sayfa', $xml, 'Taslak sayfa haritada olmamali');

    $db->run('DELETE FROM pages');
});

test('F-13', 'sitemap.xml gecerli XML uretir ve anasayfayi icerir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM pages');

    arc_page($db, [], ['title' => 'Hakkimizda', 'slug' => 'hakkimizda']);

    $xml = arc_sitemap();

    assertContains('<?xml version="1.0" encoding="UTF-8"?>', $xml, 'XML bildirimi bulunmali');
    assertContains('<urlset', $xml, 'urlset koku bulunmali');
    assertContains('http://www.sitemaps.org/schemas/sitemap/0.9', $xml, 'Ad alani dogru olmali');
    assertContains('<lastmod>', $xml, 'lastmod bulunmali');

    // Gecerli XML olarak ayristirilabilmeli.
    $previous = libxml_use_internal_errors(true);
    $doc      = simplexml_load_string($xml);
    libxml_use_internal_errors($previous);

    assertTrue($doc !== false, 'XML ayristirilabilmeli');
    assertGreaterThan(0, $doc === false ? 0 : count($doc->url), 'En az bir adres olmali');

    $db->run('DELETE FROM pages');
});

test('O-02', 'noindex isaretli ceviri haritaya girmez', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM pages');

    arc_page($db, [], ['title' => 'KVKK', 'slug' => 'kvkk', 'robots' => 'noindex,follow']);
    arc_page($db, [], ['title' => 'Fiyatlar', 'slug' => 'fiyatlar']);

    $xml = arc_sitemap();

    assertNotContains('/kvkk', $xml, 'noindex sayfa haritada olmamali');
    assertContains('/fiyatlar', $xml, 'Normal sayfa haritada olmali');

    $db->run('DELETE FROM pages');
});

test('O-03', 'Canonical her sayfada dogru ve mutlak adrestir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM pages');
    arc_page($db, [], ['title' => 'Iletisim', 'slug' => 'iletisim']);

    $body = arc_visit('iletisim')->body();

    assertTrue(
        preg_match('#<link rel="canonical" href="(https?://[^"]+)">#', $body, $m) === 1,
        'Canonical bulunmali ve mutlak olmali'
    );
    assertContains('/iletisim', $m[1], 'Canonical sayfanin kendi adresi olmali');
    assertNotContains('?', $m[1], 'Canonical sorgu dizesi tasimamali');

    $db->run('DELETE FROM pages');
});

test('O-04', 'Yapisal veri gecerli JSON uretir ve uydurma derecelendirme icermez', function (): void {
    $db = arc_need_db();
    Arcates\Models\HomeSection::ensureDefaults();

    $body = arc_home()->body();

    assertTrue(
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $body, $matches) > 0,
        'En az bir yapisal veri blogu bulunmali'
    );

    foreach ($matches[1] as $json) {
        $decoded = json_decode($json, true);
        assertTrue(is_array($decoded), 'Yapisal veri gecerli JSON olmali');
        assertTrue(isset($decoded['@context']), '@context bulunmali');
        assertTrue(isset($decoded['@type']), '@type bulunmali');

        // Uydurma yorum veya AggregateRating yazilmaz. DOCS.md 11.2
        assertFalse(isset($decoded['aggregateRating']), 'AggregateRating yazilmamali');
        assertFalse(isset($decoded['review']), 'Uydurma yorum yazilmamali');
    }

    // Anasayfada ProfessionalService bulunmali.
    assertContains('"ProfessionalService"', $body, 'Anasayfada ProfessionalService semasi olmali');
});

test('O-04b', 'Ilce sayfasi Service + areaServed semasi tasir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM pages');

    arc_page(
        $db,
        ['type' => 'location', 'template' => 'location', 'district' => 'Edremit'],
        ['title' => 'Edremit Web Tasarim', 'slug' => 'edremit-web-tasarim', 'excerpt' => 'Edremit icin web tasarim.']
    );

    $body = arc_visit('edremit-web-tasarim')->body();

    assertContains('"@type":"Service"', $body, 'Service semasi bulunmali');
    assertContains('"areaServed"', $body, 'areaServed bulunmali');
    assertContains('Edremit', $body, 'Ilce adi semada gecmeli');
    assertContains('"BreadcrumbList"', $body, 'Kirinti yolu semasi bulunmali');

    $db->run('DELETE FROM pages');
});

test('O-05', 'robots.txt sitemap satiri icerir ve panel yolunu engeller', function (): void {
    arc_need_db();

    $robots = new RobotsController();
    $body   = $robots->body();

    assertContains('Sitemap:', $body, 'Sitemap satiri bulunmali');
    assertContains('/sitemap.xml', $body, 'Sitemap adresi bulunmali');
    assertContains('Disallow: /panel/', $body, 'Panel yolu engellenmeli');
    assertContains('Disallow: /install', $body, 'Kurulum yolu engellenmeli');

    // Panelden ozel icerik girilirse de sitemap satiri korunur.
    Settings::set('robots_txt', "User-agent: *\nDisallow: /gizli/");
    $custom = $robots->body();
    assertContains('Disallow: /gizli/', $custom, 'Ozel icerik korunmali');
    assertContains('Sitemap:', $custom, 'Sitemap satiri yine eklenmeli');

    Settings::set('robots_txt', '');
});

test('O-06', 'Ilce sayfalari arasi benzerlik yayindan once yakalanir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM pages');

    $shared = implode(' ', array_map(static fn ($i) => 'ortak' . $i, range(1, 300)));

    $first = arc_page(
        $db,
        ['type' => 'location', 'district' => 'Edremit'],
        ['title' => 'Edremit', 'slug' => 'edremit-tasarim', 'content' => '<p>' . $shared . '</p>', 'word_count' => 300]
    );

    // Ayni metnin ilce adi degistirilerek cogaltilmis hali — doorway page.
    $copy = str_replace('ortak1 ', 'akcay ', $shared);
    $second = arc_page(
        $db,
        ['type' => 'location', 'district' => 'Akcay'],
        ['title' => 'Akcay', 'slug' => 'akcay-tasarim', 'content' => '<p>' . $copy . '</p>', 'word_count' => 300]
    );

    $score = Seo::score(
        ['id' => $second, 'type' => 'location', 'district' => 'Akcay'],
        ['lang' => 'tr', 'content' => '<p>' . $copy . '</p>', 'word_count' => 300, 'slug' => 'akcay-tasarim']
    );

    $found = null;
    foreach ($score['issues'] as $issue) {
        if ($issue['check'] === 'similarity') {
            $found = $issue;
        }
    }

    assertTrue($found !== null, 'Benzerlik uyarisi bulunmali');
    assertSame('strong', $found['level'], 'Benzerlik guclu uyari olmali');
    assertContains('Edremit', $found['message'], 'Benzeyen sayfanin adi bildirilmeli');

    $db->run('DELETE FROM pages');
});

test('O-07', 'Sayfa icerigindeki ic linkler sayilir ve kirik olanlar bildirilir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM pages');

    arc_page($db, [], ['title' => 'Hedef', 'slug' => 'hedef-sayfa']);

    $content = '<p><a href="/hedef-sayfa">calisan</a> ve <a href="/olmayan-sayfa">kirik</a></p>';
    assertSame(2, Seo::countInternalLinks($content), 'Iki ic link sayilmali');

    // Kirik linki dogrula: hedef sayfa cozulemiyorsa 404 doner.
    assertSame(200, arc_visit('hedef-sayfa')->status(), 'Calisan link 200 dondurmeli');
    assertSame(404, arc_visit('olmayan-sayfa')->status(), 'Kirik link 404 dondurmeli');

    $db->run('DELETE FROM pages');
    $db->run('DELETE FROM not_found');
});

test('O-08', 'Sunucu yapilandirmasi http ve www varyantlarini tek hedefe yonlendirir', function (): void {
    $htaccess = (string) file_get_contents(ARC_ROOT . '/public/.htaccess');

    // http -> https
    assertContains('RewriteCond %{HTTPS} off', $htaccess, 'http yonlendirmesi bulunmali');
    assertContains('https://%{HTTP_HOST}', $htaccess, 'Ayni alan adina yonlendirilmeli');

    // www -> www'suz; tercih tek yonde sabit
    assertContains('RewriteCond %{HTTP_HOST} ^www\\.(.+)$ [NC]', $htaccess, 'www varyanti yakalanmali');
    assertContains('RewriteRule ^(.*)$ https://%1/$1 [R=301,L]', $htaccess, 'www tek hedefe 301 ile gitmeli');

    // Etkin (yorum olmayan) kalici yonlendirme tam olarak iki tanedir:
    // http->https ve www->www'suz. Ters tercih yorum satirinda durur, yoksa
    // iki kural birbirini dongi haline getirir.
    $activeRules = 0;
    foreach (explode("\n", $htaccess) as $line) {
        $line = trim($line);
        if ($line !== '' && $line[0] !== '#' && str_contains($line, '[R=301,L]')) {
            $activeRules++;
        }
    }
    assertSame(2, $activeRules, 'Etkin kalici yonlendirme sayisi iki olmali');

    // Ters tercih hazir ama kapali durmali; ikisi ayni anda acik olamaz.
    assertContains('# Ters tercih', $htaccess, 'Ters tercih aciklamali olarak bulunmali');
    assertContains('# RewriteCond %{HTTP_HOST} !^www\\. [NC]', $htaccess, 'Ters tercih kapali olmali');
});

test('O-P7-a', 'Sitemap coklu dil karsiliklarini bildirir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM pages');

    $id = $db->insert('pages', ['type' => 'page', 'template' => 'page', 'status' => 'published']);
    $db->insert('page_translations', ['page_id' => $id, 'lang' => 'tr', 'title' => 'Hakkimizda', 'slug' => 'hakkimizda']);
    $db->insert('page_translations', ['page_id' => $id, 'lang' => 'en', 'title' => 'About', 'slug' => 'about-us']);

    $xml = arc_sitemap();

    assertContains('xmlns:xhtml="http://www.w3.org/1999/xhtml"', $xml, 'xhtml ad alani bildirilmeli');
    assertContains('<xhtml:link rel="alternate" hreflang="tr"', $xml, 'Turkce karsilik bildirilmeli');
    assertContains('<xhtml:link rel="alternate" hreflang="en"', $xml, 'Ingilizce karsilik bildirilmeli');
    assertContains('/en/about-us', $xml, 'Ingilizce adres onekli olmali');

    $db->run('DELETE FROM pages');
});

test('O-P7-b', 'Panel SEO ekrani meta durumunu listeler', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');
    $db->run('DELETE FROM pages');

    arc_page($db, [], ['title' => 'Kisa sayfa', 'slug' => 'kisa-sayfa', 'content' => '<p>Az kelime.</p>', 'word_count' => 2]);

    $response = (new Arcates\Controllers\Admin\SeoController())->index(Request::make('GET', admin_url('seo')), []);
    assertSame(200, $response->status(), 'SEO ekrani acilmali');

    $body = $response->body();
    assertContains('Kisa sayfa', $body, 'Sayfa listede olmali');
    assertContains('Sayfalarin meta durumu', $body, 'Meta tablosu bulunmali');
    assertContains('robots.txt', $body, 'robots.txt duzenleyicisi bulunmali');
    assertContains('Site haritasindaki adres', $body, 'Sitemap durumu gorunmeli');

    $db->run('DELETE FROM pages');
    arc_logout_test();
});
