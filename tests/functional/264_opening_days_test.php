<?php
/**
 * Calisma saatleri — ekranda dilin gun adi, yapisal veride ham kod.
 *
 * `opening_hours` ayari schema.org bicimini saklar (`Mo-Fr`). Yapisal veri
 * bunu oldugu gibi kullanmak zorundadir; ziyaretciye ise sayfanin dilindeki
 * gun adi gosterilir.  DOCS.md 11.2
 */

declare(strict_types=1);

use Arcates\Core\Lang;
use Arcates\Core\Seo;
use Arcates\Core\Settings;

test('F-OD-a', 'Gün kodu sayfanın diline çevrilir', function (): void {
    arc_need_db();

    Lang::use('tr');
    assertSame('Pzt–Cum', opening_days('Mo-Fr'), 'Aralık Türkçeye çevrilmeli');
    assertSame('Cmt', opening_days('Sa'), 'Tek gün çevrilmeli');
    assertSame('Pzt, Çar, Cum', opening_days('Mo,We,Fr'), 'Virgüllü liste çevrilmeli');

    // Kapali bir dile gecilemez: tohum yalnizca varsayilan dili acik birakir
    // (DOCS.md 11.3), bu yuzden once acilir.
    arc_activate_langs(['tr', 'en']);
    Lang::use('en');
    assertSame('Mon–Fri', opening_days('Mo-Fr'), 'İngilizce karşılığı verilmeli');

    arc_activate_langs(['tr']);
    Lang::use('tr');
});

test('F-OD-b', 'Tanınmayan metin bozulmadan geçer', function (): void {
    arc_test_config();
    Lang::use('tr');

    // Isletme panelde serbest metin yazabilir; o metin olduğu gibi kalmalı.
    assertSame('Hafta içi', opening_days('Hafta içi'), 'Serbest metin korunmalı');
    assertSame('', opening_days(''), 'Boş değer boş dönmeli');
    assertSame('', opening_days('   '), 'Yalnız boşluk boş dönmeli');
});

test('F-OD-c', 'Yapısal veri ham schema.org kodunu korur', function (): void {
    $db = arc_need_db();
    Lang::use('tr');

    Settings::set('opening_hours', Arcates\Core\Security::json([
        ['days' => 'Mo-Fr', 'opens' => '09:00', 'closes' => '18:00'],
    ]));
    Settings::flush();

    $schema = Seo::professionalService();
    $spec   = $schema['openingHoursSpecification'][0] ?? [];

    assertSame('Mo-Fr', (string) ($spec['dayOfWeek'] ?? ''), 'Yapısal veride ham kod kalmalı');
    assertNotContains('Pzt', Arcates\Core\Security::json($schema), 'Yapısal veri çevrilmiş etiket taşımamalı');
});

test('F-OD-d', 'Ön yüzde İngilizce gün kodu görünmez', function (): void {
    arc_need_db();
    Lang::use('tr');

    $body = arc_home()->body();

    // Alt bilgideki calisma saatleri blogu Turkce sayfada Mo/Fr basmamali.
    assertNotContains('>Mo-Fr<', $body, 'Alt bilgide ham gün kodu görünmemeli');
    assertContains('Pzt–Cum', $body, 'Türkçe gün etiketi basılmalı');
});

test('F-OD-e', 'Şablonlarda ham gün kodu basan yer kalmadı', function (): void {
    // Panel formu hariç her cikti opening_days()'ten gecmeli; aksi halde
    // yeni bir sablon sessizce "Mo-Fr" basmaya devam eder.
    $raw = [];
    foreach (glob(ARC_ROOT . '/views/front/**/*.php') ?: [] as $file) {
        $raw[] = $file;
    }
    foreach (glob(ARC_ROOT . '/views/front/*.php') ?: [] as $file) {
        $raw[] = $file;
    }

    foreach ($raw as $file) {
        $source = (string) file_get_contents($file);
        foreach (explode("\n", $source) as $line) {
            if (!str_contains($line, "['days']")) {
                continue;
            }
            assertContains(
                'opening_days(',
                $line,
                'Gün kodu doğrudan basılmamalı: ' . basename($file)
            );
        }
    }
});
