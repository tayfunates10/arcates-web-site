<?php
/**
 * Anasayfadaki ok ve onay isaretleri. Referans B6.
 *
 * Metin glifi (→ ↗ ✓) fonta baglidir; font yuklenmezse kutu olarak
 * cizilir. Depoda bu donusum once bilgi kartlari ve hizmet ikonlari,
 * sonra B2'de kahraman butonlari icin yapildi. B6 kalan yerleri kapatir.
 * DOCS.md 5 (bolum 6)
 */

declare(strict_types=1);

/** Anasayfa govdesini bir kez uretir. */
function arc_mk_body(): string
{
    static $body = null;
    if ($body === null) {
        Arcates\Core\Lang::use('tr');
        $body = (new Arcates\Controllers\Front\HomeController())
            ->index(Arcates\Core\Request::make('GET', '/'), [])->body();
    }
    return $body;
}

test('F-MK-a', 'Anasayfada fonta bağlı ok veya onay glifi kalmadı', function (): void {
    arc_need_db();
    $body = arc_mk_body();

    // Yalnizca on yuz govdesi; yapisal veri ve meta disarida degil ama
    // oralarda da bu glifler bulunmamali.
    foreach (['→', '↗', '✓'] as $glif) {
        assertNotContains($glif, $body, 'Metin glifi kalmamalı: ' . $glif);
    }
});

test('F-MK-b', 'İşaretler dekoratif ve satır içi SVG', function (): void {
    arc_need_db();
    $body = arc_mk_body();

    $n = preg_match_all('~<svg class="ref-mark[^"]*"[^>]*>~', $body, $m);
    assertTrue($n >= 5, 'Sayfada birden çok ortak işaret bulunmalı');

    foreach ($m[0] as $svg) {
        assertContains('aria-hidden="true"', $svg, 'İşaret ekran okuyucudan gizli olmalı');
        assertContains('focusable="false"', $svg, 'İşaret odak sırasına girmemeli');
    }

    // Harici dosya yok. CLAUDE.md 4
    assertNotContains('<img class="ref-mark', $body, 'İşaret için resim dosyası kullanılmamalı');
});

test('F-MK-c', 'Bağlantısı olan bölüm başlığı yerel işaretlenmez', function (): void {
    arc_need_db();
    $body = arc_mk_body();

    // `--local` "sag tarafinda baglanti yok" demek; referansta o bolumlerde
    // baslik ve alt yazi alt alta durur. Baglantisi olan bir baslikta bu
    // sinif bulunursa duzen referanstan sapar.
    if (preg_match_all('~<header class="ref-section__head[^"]*">(.*?)</header>~s', $body, $m) < 1) {
        assertTrue(false, 'Bölüm başlıkları basılmalı');
        return;
    }

    foreach ($m[0] as $i => $header) {
        $yerel     = str_contains($header, 'ref-section__head--local');
        $baglanti  = preg_match('~<a\s~', $m[1][$i]) === 1;

        if ($yerel && $baglanti) {
            assertTrue(false, 'Bağlantısı olan başlık --local taşımamalı: ' . substr(strip_tags($m[1][$i]), 0, 40));
            return;
        }
    }

    assertTrue(true, 'Her başlık kendi sınıfına uygun');
});
