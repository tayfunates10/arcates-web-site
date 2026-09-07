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

test('U-09', 'Seo::score 200 kelimeyle kelime uyarısı döndürür', function (): void {
    arc_test_config();

    $result = Seo::score(
        ['id' => 0, 'type' => 'page'],
        ['lang' => 'tr', 'content' => arc_words(200), 'word_count' => 200, 'slug' => 'ornek-sayfa']
    );

    $checks = array_column($result['issues'], 'check');
    assertTrue(in_array('words', $checks, true), 'Kelime uyarısı bulunmalı');

    foreach ($result['issues'] as $issue) {
        if ($issue['check'] === 'words') {
            assertSame('warn', $issue['level'], 'Sayfa türünde uyarı seviyesi normal olmalı');
        }
    }

    // 300 kelimenin ustunde uyari cikmamali.
    $ok = Seo::score(
        ['id' => 0, 'type' => 'page'],
        ['lang' => 'tr', 'content' => arc_words(320), 'word_count' => 320, 'slug' => 'ornek-sayfa']
    );
    assertFalse(in_array('words', array_column($ok['issues'], 'check'), true), '320 kelimede uyarı olmamalı');
});

test('U-10', 'Seo::score location türünde 400 kelimeyle güçlü uyarı döndürür', function (): void {
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

    assertTrue($strong !== null, 'Kelime uyarısı bulunmalı');
    assertSame('strong', $strong['level'], 'İlçe sayfasında güçlü uyarı olmalı');
    assertContains('500', $strong['message'], 'Eşik mesajda geçmeli');

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
    assertCount(0, $okChecks, '520 kelimede kelime uyarısı olmamalı');
});

test('U-11', 'İlçe benzerlik ölçümü %70 üstü örtüşmeyi yakalar', function (): void {
    $base = '<p>' . implode(' ', array_map(static fn ($i) => 'kelime' . $i, range(1, 100))) . '</p>';

    // Yalnizca ilce adi degistirilmis metin — doorway page deseni.
    $copy = str_replace('kelime5 ', 'akçay ', $base);
    assertGreaterThan(0.70, Seo::similarity($base, $copy), 'Neredeyse aynı metin yakalanmalı');

    // Tamamen farkli metin.
    $other = '<p>' . implode(' ', array_map(static fn ($i) => 'baska' . $i, range(1, 100))) . '</p>';
    assertLessThan(0.10, Seo::similarity($base, $other), 'Farklı metin düşük oran vermeli');

    // Kelime sirasi degistirilmis metin de yakalanmali.
    $shuffled = '<p>' . implode(' ', array_reverse(array_map(static fn ($i) => 'kelime' . $i, range(1, 100)))) . '</p>';
    assertGreaterThan(0.90, Seo::similarity($base, $shuffled), 'Sıra değişimi örtüşmeyi gizlememeli');

    assertSame(0.0, Seo::similarity('', 'herhangi bir metin'), 'Boş metin sıfır döndürmeli');
});

test('U-13', 'hreflang üretimi eksik çeviriyi listeye almaz', function (): void {
    arc_test_config();
    Lang::reset();
    Lang::seed([
        'tr' => ['code' => 'tr', 'name' => 'Türkçe',  'direction' => 'ltr', 'is_default' => 1, 'is_active' => 1],
        'en' => ['code' => 'en', 'name' => 'English', 'direction' => 'ltr', 'is_default' => 0, 'is_active' => 1],
        'de' => ['code' => 'de', 'name' => 'Deutsch', 'direction' => 'ltr', 'is_default' => 0, 'is_active' => 1],
        'ar' => ['code' => 'ar', 'name' => 'Arapça',  'direction' => 'rtl', 'is_default' => 0, 'is_active' => 1],
    ], 'tr');

    $set = Seo::hreflang([
        'tr' => 'hakkimizda',
        'en' => 'about-us',
        'de' => '',            // ceviri yok
        'ar' => null,          // ceviri yok
    ]);

    $codes = array_column($set, 'hreflang');

    assertTrue(in_array('tr', $codes, true), 'Türkçe listede olmalı');
    assertTrue(in_array('en', $codes, true), 'İngilizce listede olmalı');
    assertFalse(in_array('de', $codes, true), 'Çevirisi olmayan Almanca listede olmamalı');
    assertFalse(in_array('ar', $codes, true), 'Çevirisi olmayan Arapça listede olmamalı');
    assertTrue(in_array('x-default', $codes, true), 'x-default bulunmalı');

    foreach ($set as $item) {
        if ($item['hreflang'] === 'en') {
            assertContains('/en/about-us', $item['href'], 'İngilizce adres önekli olmalı');
        }
        if ($item['hreflang'] === 'tr') {
            assertNotContains('/tr/', $item['href'], 'Varsayılan dil öneksiz olmalı');
        }
    }

    // Tek dil varsa hreflang seti uretilmez.
    assertCount(0, Seo::hreflang(['tr' => 'hakkimizda']), 'Tek dilde hreflang gereksiz');

    Lang::reset();
});

test('U-13b', 'Alt metni eksik görseller ve iç linkler sayılır', function (): void {
    arc_test_config();

    $html = '<p><img src="/a.webp" alt="Edremit sahili"> <img src="/b.webp"> '
        . '<img src="/c.webp" alt=""> <img src="/d.webp" alt="" aria-hidden="true"></p>';

    // a: alt dolu, b: alt yok, c: alt bos, d: alt bos ama aria-hidden (dekoratif)
    assertSame(2, Seo::countImagesWithoutAlt($html), 'Boş ve eksik alt metinler sayılmalı, dekoratif olan sayılmamalı');

    $links = '<p><a href="/iletisim">iletişim</a> <a href="https://baska.example">dışarı</a> '
        . '<a href="#bolum">aynı sayfa</a> <a href="/fiyatlar">fiyatlar</a></p>';

    assertSame(2, Seo::countInternalLinks($links), 'Yalnızca iç linkler sayılmalı');
});

test('U-13c', 'Meta başlık şablonu uygulanır ve canonical mutlak olur', function (): void {
    arc_test_config();

    $explicit = Seo::title('Edremit Web Tasarım', 'Özel başlık');
    assertSame('Özel başlık', $explicit, 'Elle girilen meta başlık olduğu gibi kullanılmalı');

    $canonical = Seo::canonical('/edremit-web-tasarim');
    assertContains('https://', $canonical, 'Canonical mutlak adres olmalı');
    assertContains('/edremit-web-tasarim', $canonical);

    $override = Seo::canonical('/a', 'https://arcates.com/b');
    assertSame('https://arcates.com/b', $override, 'Elle verilen canonical korunmalı');
});
