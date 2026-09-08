<?php
/**
 * Baslangic icerigi ve yayin oncesi denetimler.
 * DOCS.md 4 (URL haritasi), 4.7 (ilce kurali), 17 (teslim listesi)
 */

declare(strict_types=1);

use Arcates\Core\Security;
use Arcates\Core\Seo;

/** Tohum dosyalarini yukler. */
function arc_seed(string $name): array
{
    return require ARC_ROOT . '/db/seed/' . $name . '.php';
}

test('F-P12-a', 'Tohum verisi bölüm 4\'teki URL haritasının tamamını kapsar', function (): void {
    $expected = [
        'page' => [
            'hakkimizda', 'iletisim', 'fiyatlar', 'referanslar', 'blog', 'sss',
            'kvkk', 'gizlilik-politikasi',
        ],
        'service' => [
            'web-tasarim', 'e-ticaret-sitesi', 'rezervasyon-sistemi',
            'seo-hizmeti', 'coklu-dil-web-sitesi', 'web-sitesi-bakim',
        ],
        'location' => [
            'edremit-web-tasarim', 'akcay-web-tasarim', 'altinoluk-web-tasarim',
            'burhaniye-web-tasarim', 'havran-web-tasarim', 'ayvalik-web-tasarim',
            'gomec-web-tasarim', 'balikesir-web-tasarim',
        ],
        'sector' => [
            'otel-pansiyon-web-sitesi', 'zeytinyagi-e-ticaret-sitesi',
            'restoran-kafe-qr-menu', 'emlak-web-sitesi', 'nakliyat-web-sitesi',
            'tabela-matbaa-web-sitesi',
        ],
    ];

    $have = [];
    foreach (arc_seed('pages') as $item) {
        $have[$item['type']][] = $item['slug'];
    }
    foreach (arc_seed('locations') as $item) {
        $have['location'][] = $item['slug'];
    }
    foreach (arc_seed('sectors') as $item) {
        $have['sector'][] = $item['slug'];
    }

    foreach ($expected as $type => $slugs) {
        foreach ($slugs as $slug) {
            assertTrue(
                in_array($slug, $have[$type] ?? [], true),
                "Tohumda eksik {$type} sayfası: /{$slug}"
            );
        }
    }

    // Hizmet sayfasi sayisi sartnamedeki gibi (bolum 4.2)
    assertCount(6, array_filter(arc_seed('pages'), static fn (array $i): bool => $i['type'] === 'service'));
    assertCount(8, arc_seed('locations'), 'Sekiz ilçe sayfası olmalı (bölüm 4.3)');
    assertCount(6, arc_seed('sectors'), 'Altı sektör sayfası olmalı (bölüm 4.4)');
});

test('F-P12-b', 'Her ilçe sayfası en az 500 kelime özgün metin içerir', function (): void {
    // DOCS.md 4.7 — kritik kural
    foreach (arc_seed('locations') as $key => $item) {
        $words = Security::wordCount($item['content']);
        assertGreaterThan(
            499,
            $words,
            "{$item['slug']} yalnızca {$words} kelime; en az 500 gerekir"
        );
    }
});

test('F-P12-c', 'İlçe sayfaları birbirinin kopyası değil', function (): void {
    // DOCS.md 4.7 ve 9.6 — %70 uzeri ortusme doorway page riski
    $pages = arc_seed('locations');
    $keys  = array_keys($pages);

    $highest = 0.0;
    $pair    = '';

    foreach ($keys as $i => $a) {
        foreach (array_slice($keys, $i + 1) as $b) {
            $ratio = Seo::similarity($pages[$a]['content'], $pages[$b]['content']);
            if ($ratio > $highest) {
                $highest = $ratio;
                $pair    = $a . ' ~ ' . $b;
            }
        }
    }

    assertLessThan(
        Seo::SIMILARITY_LIMIT,
        $highest,
        sprintf('En yüksek örtüşme %.2f (%s); eşik %.2f', $highest, $pair, Seo::SIMILARITY_LIMIT)
    );

    // Ayni ilce adi degistirilmis kopya olmadigini ayrica dogrula:
    // her sayfa kendi ilcesine ozgu en az bir terim tasimali.
    $ozgun = [
        'edremit'   => 'Kaz Dağları',
        'akcay'     => 'sahil şeridi',
        'altinoluk' => 'site yönetimi',
        'burhaniye' => 'Ören',
        'havran'    => 'şeftali',
        'ayvalik'   => 'Cunda',
        'gomec'     => 'karavan',
        'balikesir' => 'organize sanayi',
    ];

    foreach ($ozgun as $key => $terim) {
        assertTrue(
            isset($pages[$key]) && stripos($pages[$key]['content'], $terim) !== false,
            "{$key} sayfası kendine özgü terimi içermeli: {$terim}"
        );
    }
});

test('F-P12-d', 'Her ilçenin referansı ve kendine özel SSS kaydı var', function (): void {
    // DOCS.md 4.7
    $districts = array_column(arc_seed('locations'), 'district');
    $projects  = array_column(arc_seed('projects'), 'district');

    foreach ($districts as $district) {
        assertTrue(
            in_array($district, $projects, true),
            "İlçeye ait referans bulunmalı: {$district}"
        );
    }

    $faqPages = [];
    foreach (arc_seed('faqs') as $faq) {
        foreach ($faq['pages'] as $slug) {
            $faqPages[$slug] = true;
        }
    }

    foreach (arc_seed('locations') as $item) {
        assertTrue(
            isset($faqPages[$item['slug']]),
            "İlçe sayfasına atanmış SSS bulunmalı: {$item['slug']}"
        );
    }

    // Hizmet sayfalarinda da SSS olmali (bolum 9.6)
    foreach (arc_seed('pages') as $item) {
        if ($item['type'] !== 'service') {
            continue;
        }
        assertTrue(
            isset($faqPages[$item['slug']]),
            "Hizmet sayfasına atanmış SSS bulunmalı: {$item['slug']}"
        );
    }
});

test('F-P12-e', 'Tohum içeriği zararlı işaretleme içermez', function (): void {
    foreach (['pages', 'locations', 'sectors', 'projects'] as $file) {
        foreach (arc_seed($file) as $item) {
            $content = (string) ($item['content'] ?? '');

            assertNotContains('<script', $content, "Tohum içeriğinde script olmamalı: {$file}");
            assertNotContains('javascript:', $content, "javascript: şeması olmamalı: {$file}");
            assertNotContains('onclick', $content, "Olay niteliği olmamalı: {$file}");

            // Temizleyiciden gecince icerik kaybolmamali.
            $clean = Security::sanitizeHtml($content);
            assertGreaterThan(
                0,
                Security::wordCount($clean),
                'Temizlik sonrası içerik boş kalmamalı: ' . ($item['slug'] ?? $file)
            );
        }
    }

    foreach (arc_seed('faqs') as $faq) {
        assertNotContains('<script', $faq['a'], 'SSS cevabında script olmamalı');
    }
});

test('F-P12-f', 'Tohum sayfaları iç link taşır', function (): void {
    // DOCS.md 11.1 — her sayfada en az 3 ic link onerilir; sablon menu ve
    // alt bilgi zaten link uretir, burada icerik govdesindeki linkler sayilir.
    foreach (['pages', 'locations', 'sectors'] as $file) {
        foreach (arc_seed($file) as $item) {
            $links = Seo::countInternalLinks((string) $item['content']);
            assertGreaterThan(
                1,
                $links,
                "{$item['slug']} içerik gövdesinde en az 2 iç link taşımalı (bulunan: {$links})"
            );
        }
    }
});

test('F-P12-g', 'Yayın öncesi denetim aracı çalışır ve maddeleri raporlar', function (): void {
    $tool = ARC_ROOT . '/tools/preflight.php';
    assertTrue(is_file($tool), 'preflight aracı bulunmalı');

    $source = (string) file_get_contents($tool);

    // DOCS.md 17'deki teslim listesi maddelerinin karsiliklari.
    foreach ([
        'SSL ve https yönlendirmesi',
        'www tercihi tek yönde sabit',
        'display_errors kapalı',
        '/install erişilemez',
        'Panel şifresi güçlü',
        'Form test edildi',
        'Search Console',
        'Analytics bağlandı',
        'Rich Results',
        '404 sayfası mevcut',
        'Favicon mevcut',
        'Örnek kayıtlar',
        'Bekleyen göç yok',
        'NAP',
        'Otomatik yedek',
    ] as $madde) {
        assertContains($madde, $source, "Teslim listesi maddesi denetlenmeli: {$madde}");
    }
});

test('F-P12-h', 'Tohum aracı var olan içeriğin üzerine yazmaz', function (): void {
    $source = (string) file_get_contents(ARC_ROOT . '/tools/seed_content.php');

    assertContains('--force', $source, 'Zorlama seçeneği bulunmalı');
    assertContains('--dry', $source, 'Kuru çalışma seçeneği bulunmalı');
    assertContains('if ($existing !== null && !$force)', $source, 'Var olan kayıt korunmalı');
});
