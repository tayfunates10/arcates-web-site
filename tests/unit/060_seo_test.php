<?php
/**
 * Seo birim testleri.  DOCS.md 14.2 (U-09, U-10, U-11, U-13), 9.6, 11.3
 */

declare(strict_types=1);

use Arcates\Core\Lang;
use Arcates\Core\Seo;

/** Belirtilen kelime sayisinda ornek metin uretir. */
function arc_words(int $count, string $seed = 'edremit'): string
{
    $words = [];
    for ($i = 0; $i < $count; $i++) {
        $words[] = $seed . $i;
    }
    return '<p>' . implode(' ', $words) . '</p>';
}

test('U-09', 'Seo::score 200 kelimeyle kelime uyarisi dondurur', function (): void {
    arc_test_config();

    $result = Seo::score(
        ['id' => 0, 'type' => 'page'],
        ['lang' => 'tr', 'content' => arc_words(200), 'word_count' => 200, 'slug' => 'ornek-sayfa']
    );

    $checks = array_column($result['issues'], 'check');
    assertTrue(in_array('words', $checks, true), 'Kelime uyarisi bulunmali');

    foreach ($result['issues'] as $issue) {
        if ($issue['check'] === 'words') {
            assertSame('warn', $issue['level'], 'Sayfa turunde uyari seviyesi normal olmali');
        }
    }

    // 300 kelimenin ustunde uyari cikmamali.
    $ok = Seo::score(
        ['id' => 0, 'type' => 'page'],
        ['lang' => 'tr', 'content' => arc_words(320), 'word_count' => 320, 'slug' => 'ornek-sayfa']
    );
    assertFalse(in_array('words', array_column($ok['issues'], 'check'), true), '320 kelimede uyari olmamali');
});

test('U-10', 'Seo::score location turunde 400 kelimeyle guclu uyari dondurur', function (): void {
    arc_test_config();

    $result = Seo::score(
        ['id' => 0, 'type' => 'location', 'district' => 'Edremit'],
        ['lang' => 'tr', 'content' => arc_words(400), 'word_count' => 400, 'slug' => 'edremit-web-tasarim']
    );

    $strong = null;
    foreach ($result['issues'] as $issue) {
        if ($issue['check'] === 'words') {
            $strong = $issue;
        }
    }

    assertTrue($strong !== null, 'Kelime uyarisi bulunmali');
    assertSame('strong', $strong['level'], 'Ilce sayfasinda guclu uyari olmali');
    assertContains('500', $strong['message'], 'Esik mesajda gecmeli');

    // 520 kelimede guclu uyari kalkmali.
    $ok = Seo::score(
        ['id' => 0, 'type' => 'location', 'district' => 'Edremit'],
        ['lang' => 'tr', 'content' => arc_words(520), 'word_count' => 520, 'slug' => 'edremit-web-tasarim']
    );
    $okChecks = [];
    foreach ($ok['issues'] as $issue) {
        if ($issue['check'] === 'words') {
            $okChecks[] = $issue['level'];
        }
    }
    assertCount(0, $okChecks, '520 kelimede kelime uyarisi olmamali');
});

test('U-11', 'Ilce benzerlik olcumu %70 ustu ortusmeyi yakalar', function (): void {
    $base = '<p>' . implode(' ', array_map(static fn ($i) => 'kelime' . $i, range(1, 100))) . '</p>';

    // Yalnizca ilce adi degistirilmis metin — doorway page deseni.
    $copy = str_replace('kelime5 ', 'akcay ', $base);
    assertGreaterThan(0.70, Seo::similarity($base, $copy), 'Neredeyse ayni metin yakalanmali');

    // Tamamen farkli metin.
    $other = '<p>' . implode(' ', array_map(static fn ($i) => 'baska' . $i, range(1, 100))) . '</p>';
    assertLessThan(0.10, Seo::similarity($base, $other), 'Farkli metin dusuk oran vermeli');

    // Kelime sirasi degistirilmis metin de yakalanmali.
    $shuffled = '<p>' . implode(' ', array_reverse(array_map(static fn ($i) => 'kelime' . $i, range(1, 100)))) . '</p>';
    assertGreaterThan(0.90, Seo::similarity($base, $shuffled), 'Sira degisimi ortusmeyi gizlememeli');

    assertSame(0.0, Seo::similarity('', 'herhangi bir metin'), 'Bos metin sifir dondurmeli');
});

test('U-13', 'hreflang uretimi eksik ceviriyi listeye almaz', function (): void {
    arc_test_config();
    Lang::reset();
    Lang::seed([
        'tr' => ['code' => 'tr', 'name' => 'Turkce',  'direction' => 'ltr', 'is_default' => 1, 'is_active' => 1],
        'en' => ['code' => 'en', 'name' => 'English', 'direction' => 'ltr', 'is_default' => 0, 'is_active' => 1],
        'de' => ['code' => 'de', 'name' => 'Deutsch', 'direction' => 'ltr', 'is_default' => 0, 'is_active' => 1],
        'ar' => ['code' => 'ar', 'name' => 'Arapca',  'direction' => 'rtl', 'is_default' => 0, 'is_active' => 1],
    ], 'tr');

    $set = Seo::hreflang([
        'tr' => 'hakkimizda',
        'en' => 'about-us',
        'de' => '',            // ceviri yok
        'ar' => null,          // ceviri yok
    ]);

    $codes = array_column($set, 'hreflang');

    assertTrue(in_array('tr', $codes, true), 'Turkce listede olmali');
    assertTrue(in_array('en', $codes, true), 'Ingilizce listede olmali');
    assertFalse(in_array('de', $codes, true), 'Cevirisi olmayan Almanca listede olmamali');
    assertFalse(in_array('ar', $codes, true), 'Cevirisi olmayan Arapca listede olmamali');
    assertTrue(in_array('x-default', $codes, true), 'x-default bulunmali');

    foreach ($set as $item) {
        if ($item['hreflang'] === 'en') {
            assertContains('/en/about-us', $item['href'], 'Ingilizce adres onekli olmali');
        }
        if ($item['hreflang'] === 'tr') {
            assertNotContains('/tr/', $item['href'], 'Varsayilan dil oneksiz olmali');
        }
    }

    // Tek dil varsa hreflang seti uretilmez.
    assertCount(0, Seo::hreflang(['tr' => 'hakkimizda']), 'Tek dilde hreflang gereksiz');

    Lang::reset();
});

test('U-13b', 'Alt metni eksik gorseller ve ic linkler sayilir', function (): void {
    arc_test_config();

    $html = '<p><img src="/a.webp" alt="Edremit sahili"> <img src="/b.webp"> '
        . '<img src="/c.webp" alt=""> <img src="/d.webp" alt="" aria-hidden="true"></p>';

    // a: alt dolu, b: alt yok, c: alt bos, d: alt bos ama aria-hidden (dekoratif)
    assertSame(2, Seo::countImagesWithoutAlt($html), 'Bos ve eksik alt metinler sayilmali, dekoratif olan sayilmamali');

    $links = '<p><a href="/iletisim">iletisim</a> <a href="https://baska.example">disari</a> '
        . '<a href="#bolum">ayni sayfa</a> <a href="/fiyatlar">fiyatlar</a></p>';

    assertSame(2, Seo::countInternalLinks($links), 'Yalnizca ic linkler sayilmali');
});

test('U-13c', 'Meta baslik sablonu uygulanir ve canonical mutlak olur', function (): void {
    arc_test_config();

    $explicit = Seo::title('Edremit Web Tasarim', 'Ozel baslik');
    assertSame('Ozel baslik', $explicit, 'Elle girilen meta baslik oldugu gibi kullanilmali');

    $canonical = Seo::canonical('/edremit-web-tasarim');
    assertContains('https://', $canonical, 'Canonical mutlak adres olmali');
    assertContains('/edremit-web-tasarim', $canonical);

    $override = Seo::canonical('/a', 'https://arcates.com/b');
    assertSame('https://arcates.com/b', $override, 'Elle verilen canonical korunmali');
});
