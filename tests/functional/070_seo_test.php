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

test('U-14', 'Sitemap üretiminde taslak içerik yer almaz', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM pages');

    arc_page($db, ['status' => 'published'], ['title' => 'Yayındaki', 'slug' => 'yayindaki-sayfa']);
    arc_page($db, ['status' => 'draft'], ['title' => 'Taslak', 'slug' => 'taslak-sayfa']);

    $xml = arc_sitemap();

    assertContains('/yayindaki-sayfa', $xml, 'Yayındaki sayfa haritada olmalı');
    assertNotContains('/taslak-sayfa', $xml, 'Taslak sayfa haritada olmamalı');

    $db->run('DELETE FROM pages');
});

test('F-13', 'sitemap.xml geçerli XML üretir ve anasayfayı içerir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM pages');

    arc_page($db, [], ['title' => 'Hakkımızda', 'slug' => 'hakkimizda']);

    $xml = arc_sitemap();

    assertContains('<?xml version="1.0" encoding="UTF-8"?>', $xml, 'XML bildirimi bulunmalı');
    assertContains('<urlset', $xml, 'urlset kökü bulunmalı');
    assertContains('http://www.sitemaps.org/schemas/sitemap/0.9', $xml, 'Ad alanı doğru olmalı');
    assertContains('<lastmod>', $xml, 'lastmod bulunmalı');

    // Gecerli XML olarak ayristirilabilmeli.
    $previous = libxml_use_internal_errors(true);
    $doc      = simplexml_load_string($xml);
    libxml_use_internal_errors($previous);

    assertTrue($doc !== false, 'XML ayrıştırılabilmeli');
    assertGreaterThan(0, $doc === false ? 0 : count($doc->url), 'En az bir adres olmalı');

    $db->run('DELETE FROM pages');
});

test('O-02', 'noindex işaretli çeviri haritaya girmez', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM pages');

    arc_page($db, [], ['title' => 'KVKK', 'slug' => 'kvkk', 'robots' => 'noindex,follow']);
    arc_page($db, [], ['title' => 'Fiyatlar', 'slug' => 'fiyatlar']);

    $xml = arc_sitemap();

    assertNotContains('/kvkk', $xml, 'noindex sayfa haritada olmamalı');
    assertContains('/fiyatlar', $xml, 'Normal sayfa haritada olmalı');

    $db->run('DELETE FROM pages');
});

test('O-03', 'Canonical her sayfada doğru ve mutlak adrestir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM pages');
    arc_page($db, [], ['title' => 'İletişim', 'slug' => 'iletisim']);

    $body = arc_visit('iletisim')->body();

    assertTrue(
        preg_match('#<link rel="canonical" href="(https?://[^"]+)">#', $body, $m) === 1,
        'Canonical bulunmalı ve mutlak olmalı'
    );
    assertContains('/iletisim', $m[1], 'Canonical sayfanın kendi adresi olmalı');
    assertNotContains('?', $m[1], 'Canonical sorgu dizesi taşımamalı');

    $db->run('DELETE FROM pages');
});

test('O-04', 'Yapısal veri geçerli JSON üretir ve uydurma derecelendirme içermez', function (): void {
    $db = arc_need_db();
    Arcates\Models\HomeSection::ensureDefaults();

    $body = arc_home()->body();

    assertTrue(
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $body, $matches) > 0,
        'En az bir yapısal veri blogu bulunmalı'
    );

    foreach ($matches[1] as $json) {
        $decoded = json_decode($json, true);
        assertTrue(is_array($decoded), 'Yapısal veri geçerli JSON olmalı');
        assertTrue(isset($decoded['@context']), '@context bulunmalı');
        assertTrue(isset($decoded['@type']), '@type bulunmalı');

        // Uydurma yorum veya AggregateRating yazilmaz. DOCS.md 11.2
        assertFalse(isset($decoded['aggregateRating']), 'AggregateRating yazılmamalı');
        assertFalse(isset($decoded['review']), 'Uydurma yorum yazılmamalı');
    }

    // Anasayfada ProfessionalService bulunmali.
    assertContains('"ProfessionalService"', $body, 'Anasayfada ProfessionalService şeması olmalı');
});

test('O-04b', 'İlçe sayfası Service + areaServed şeması taşır', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM pages');

    arc_page(
        $db,
        ['type' => 'location', 'template' => 'location', 'district' => 'Edremit'],
        ['title' => 'Edremit Web Tasarım', 'slug' => 'edremit-web-tasarim', 'excerpt' => 'Edremit için web tasarım.']
    );

    $body = arc_visit('edremit-web-tasarim')->body();

    assertContains('"@type":"Service"', $body, 'Service şeması bulunmalı');
    assertContains('"areaServed"', $body, 'areaServed bulunmalı');
    assertContains('Edremit', $body, 'İlçe adı şemada geçmeli');
    assertContains('"BreadcrumbList"', $body, 'Kırıntı yolu şeması bulunmalı');

    $db->run('DELETE FROM pages');
});

test('O-05', 'robots.txt sitemap satırı içerir ve panel yolunu engeller', function (): void {
    arc_need_db();

    $robots = new RobotsController();
    $body   = $robots->body();

    assertContains('Sitemap:', $body, 'Sitemap satırı bulunmalı');
    assertContains('/sitemap.xml', $body, 'Sitemap adresi bulunmalı');
    assertContains('Disallow: /panel/', $body, 'Panel yolu engellenmeli');
    assertContains('Disallow: /install', $body, 'Kurulum yolu engellenmeli');

    // Panelden ozel icerik girilirse de sitemap satiri korunur.
    Settings::set('robots_txt', "User-agent: *\nDisallow: /gizli/");
    $custom = $robots->body();
    assertContains('Disallow: /gizli/', $custom, 'Özel içerik korunmalı');
    assertContains('Sitemap:', $custom, 'Sitemap satırı yine eklenmeli');

    Settings::set('robots_txt', '');
});

test('O-06', 'İlçe sayfaları arası benzerlik yayından önce yakalanır', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM pages');

    $shared = implode(' ', array_map(static fn ($i) => 'ortak' . $i, range(1, 300)));

    $first = arc_page(
        $db,
        ['type' => 'location', 'district' => 'Edremit'],
        ['title' => 'Edremit', 'slug' => 'edremit-tasarim', 'content' => '<p>' . $shared . '</p>', 'word_count' => 300]
    );

    // Ayni metnin ilce adi degistirilerek cogaltilmis hali — doorway page.
    $copy = str_replace('ortak1 ', 'akçay ', $shared);
    $second = arc_page(
        $db,
        ['type' => 'location', 'district' => 'Akçay'],
        ['title' => 'Akçay', 'slug' => 'akcay-tasarim', 'content' => '<p>' . $copy . '</p>', 'word_count' => 300]
    );

    $score = Seo::score(
        ['id' => $second, 'type' => 'location', 'district' => 'Akçay'],
        ['lang' => 'tr', 'content' => '<p>' . $copy . '</p>', 'word_count' => 300, 'slug' => 'akcay-tasarim']
    );

    $found = null;
    foreach ($score['issues'] as $issue) {
        if ($issue['check'] === 'similarity') {
            $found = $issue;
        }
    }

    assertTrue($found !== null, 'Benzerlik uyarısı bulunmalı');
    assertSame('strong', $found['level'], 'Benzerlik güçlü uyarı olmalı');
    assertContains('Edremit', $found['message'], 'Benzeyen sayfanın adı bildirilmeli');

    $db->run('DELETE FROM pages');
});

test('O-07', 'Sayfa içeriğindeki iç linkler sayılır ve kırık olanlar bildirilir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM pages');

    arc_page($db, [], ['title' => 'Hedef', 'slug' => 'hedef-sayfa']);

    $content = '<p><a href="/hedef-sayfa">çalışan</a> ve <a href="/olmayan-sayfa">kırık</a></p>';
    assertSame(2, Seo::countInternalLinks($content), 'İki iç link sayılmalı');

    // Kirik linki dogrula: hedef sayfa cozulemiyorsa 404 doner.
    assertSame(200, arc_visit('hedef-sayfa')->status(), 'Çalışan link 200 döndürmeli');
    assertSame(404, arc_visit('olmayan-sayfa')->status(), 'Kırık link 404 döndürmeli');

    $db->run('DELETE FROM pages');
    $db->run('DELETE FROM not_found');
});

test('O-08', 'Sunucu yapılandırması http ve www varyantlarını tek hedefe yönlendirir', function (): void {
    $htaccess = (string) file_get_contents(ARC_ROOT . '/public/.htaccess');

    // http -> https
    assertContains('RewriteCond %{HTTPS} off', $htaccess, 'http yönlendirmesi bulunmalı');
    assertContains('https://%{HTTP_HOST}', $htaccess, 'Aynı alan adına yönlendirilmeli');

    // www -> www'suz; tercih tek yonde sabit
    assertContains('RewriteCond %{HTTP_HOST} ^www\\.(.+)$ [NC]', $htaccess, 'www varyantı yakalanmalı');
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
    assertSame(2, $activeRules, 'Etkin kalıcı yönlendirme sayısı iki olmalı');

    // Ters tercih hazir ama kapali durmali; ikisi ayni anda acik olamaz.
    assertContains('# Ters tercih', $htaccess, 'Ters tercih açıklamalı olarak bulunmalı');
    assertContains('# RewriteCond %{HTTP_HOST} !^www\\. [NC]', $htaccess, 'Ters tercih kapalı olmalı');
});

test('O-P7-a', 'Sitemap çoklu dil karşılıklarını bildirir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM pages');

    $id = $db->insert('pages', ['type' => 'page', 'template' => 'page', 'status' => 'published']);
    $db->insert('page_translations', ['page_id' => $id, 'lang' => 'tr', 'title' => 'Hakkımızda', 'slug' => 'hakkimizda']);
    $db->insert('page_translations', ['page_id' => $id, 'lang' => 'en', 'title' => 'About', 'slug' => 'about-us']);

    $xml = arc_sitemap();

    assertContains('xmlns:xhtml="http://www.w3.org/1999/xhtml"', $xml, 'xhtml ad alanı bildirilmeli');
    assertContains('<xhtml:link rel="alternate" hreflang="tr"', $xml, 'Türkçe karşılık bildirilmeli');
    assertContains('<xhtml:link rel="alternate" hreflang="en"', $xml, 'İngilizce karşılık bildirilmeli');
    assertContains('/en/about-us', $xml, 'İngilizce adres önekli olmalı');

    $db->run('DELETE FROM pages');
});

test('O-P7-b', 'Panel SEO ekranı meta durumunu listeler', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');
    $db->run('DELETE FROM pages');

    arc_page($db, [], ['title' => 'Kısa sayfa', 'slug' => 'kisa-sayfa', 'content' => '<p>Az kelime.</p>', 'word_count' => 2]);

    $response = (new Arcates\Controllers\Admin\SeoController())->index(Request::make('GET', admin_url('seo')), []);
    assertSame(200, $response->status(), 'SEO ekranı açılmalı');

    $body = $response->body();
    assertContains('Kısa sayfa', $body, 'Sayfa listede olmalı');
    assertContains('Sayfaların meta durumu', $body, 'Meta tablosu bulunmalı');
    assertContains('robots.txt', $body, 'robots.txt düzenleyicisi bulunmalı');
    assertContains('Site haritasındaki adres', $body, 'Sitemap durumu görünmeli');

    $db->run('DELETE FROM pages');
    arc_logout_test();
});
