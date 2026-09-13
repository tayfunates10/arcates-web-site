<?php
/**
 * Referans gorsellerinin cozunurlugu ve ekip gorselinin alt metni.
 *
 * Gorsellerin ilk surumu sitede basildiklari olcuden kucuktu: kahraman
 * gorseli 400x300 kaynaktan 467x350 basiliyordu, yani 1x ekranda bile
 * buyutuluyordu. Bu testler o durumun geri gelmesini engeller.
 *
 * Asagidaki "en genis basim" degerleri tarayicida olculdu: sayfa 1440, 1920
 * ve 2560 genisliklerde acilip her gorselin kutusu okundu.
 * DOCS.md 5 (bolum 9)
 */

declare(strict_types=1);

/** Referans gorselleri: [dosya, en genis basim genisligi, yuksekligi]. */
function arc_gc_gorseller(): array
{
    return [
        // Kahraman: 1440'ta 467x350 basiliyor.
        'hero-laptop.webp'  => [467, 350],
        // Ekip gorseli: 1920'de 361x212 basiliyor.
        'about-team.webp'   => [361, 212],
        // Cagri bandi arka plani: 1920'de 1586x119'luk kutuya `cover` ile
        // yayiliyor. `cover` en/boy oranini koruyarak kutuyu doldurdugu icin
        // olcut kutunun genisligi.
        'cta-mountain.webp' => [1586, 119],
    ];
}

test('F-GC-a', 'Referans görselleri 2x ekranda büyütülmüyor', function (): void {
    $kok = dirname(__DIR__, 2) . '/public/assets/img/reference/';

    foreach (arc_gc_gorseller() as $dosya => [$basimEn, $basimBoy]) {
        $yol = $kok . $dosya;
        assertTrue(is_file($yol), 'Görsel bulunmalı: ' . $dosya);

        $olcu = getimagesize($yol);
        assertTrue($olcu !== false, 'Görsel okunabilmeli: ' . $dosya);

        // Arka plan gorselinde `cover` yuksekligi kirpiyor; olcut genislik.
        $gerekli = $dosya === 'cta-mountain.webp' ? $basimEn : $basimEn * 2;

        assertTrue(
            $olcu[0] >= $gerekli,
            sprintf('%s en az %dpx geniş olmalı, %dpx', $dosya, $gerekli, $olcu[0])
        );
    }
});

test('F-GC-b', 'Görseller sayfayı şişirecek kadar büyük değil', function (): void {
    $kok = dirname(__DIR__, 2) . '/public/assets/img/reference/';

    foreach (array_keys(arc_gc_gorseller()) as $dosya) {
        $kb = (int) round(filesize($kok . $dosya) / 1024);
        assertTrue($kb <= 200, sprintf('%s 200KB altında olmalı, %dKB', $dosya, $kb));
    }
});

test('F-GC-c', 'Ekip görselindeki el yazısı alt metinde de var', function (): void {
    // Cumle fotografin icine gomulu; alt metinde tekrarlanmazsa ekran
    // okuyucu kullanicisina hic ulasmaz. WCAG 1.1.1
    $tr = require dirname(__DIR__, 2) . '/lang/tr.php';
    $alt = (string) ($tr['team_image_alt'] ?? '');

    assertContains('Daha iyi bir yarın için birlikte üretiyoruz.', $alt, 'El yazısı cümle alt metinde olmalı');
    assertContains('ekibi', $alt, 'Alt metin görselin kendisini de tarif etmeli');
});
