<?php
/**
 * Alt bilgi sosyal baglantilari ve yukari cik dugmesi. Referans B7.
 *
 * Sosyal baglanti altyapisi zaten vardi (ayar, panel alanlari, yapisal
 * veride `sameAs`); eksik olan ikon olarak basilmasiydi. DOCS.md 5
 */

declare(strict_types=1);

test('F-FC-a', 'Tanınan platformun ikonu üretilir', function (): void {
    // Ikon once adresin alan adindan cikarilir.
    foreach ([
        'https://www.linkedin.com/company/x' => 'linkedin',
        'https://instagram.com/x'            => 'instagram',
        'https://www.youtube.com/@x'         => 'youtube',
        'https://github.com/x'               => 'github',
    ] as $url => $ad) {
        $svg = social_icon($url, 'Bağlantı');
        assertNotSame('', $svg, $ad . ' ikonu üretilmeli');
        assertContains('class="site-foot__social-icon"', $svg, 'İkon sınıfı taşımalı');
        assertContains('aria-hidden="true"', $svg, 'İkon dekoratif olmalı');
        assertContains('focusable="false"', $svg, 'İkon odak sırasına girmemeli');
    }
});

test('F-FC-b', 'Tanınmayan platformda ikon boş döner', function (): void {
    // Bos donunce alt bilgi etiket metnini basar; girilen baglanti kaybolmaz.
    assertSame('', social_icon('https://ornek.test/arcates', 'Kişisel site'), 'Bilinmeyen alan adı ikon üretmemeli');
    assertSame('', social_icon('', ''), 'Boş girdi ikon üretmemeli');
});

test('F-FC-g', 'Platform açık alan adıyla eşleşir, alt dizeyle değil', function (): void {
    // Once alt dize aranıyordu ve `x` anahtari tek karakter oldugu icin
    // icinde "x" gecen HER alan adi X ikonu aliyordu; yoneticinin etiketi
    // kayboluyordu. Codex incelemesinin bildirdigi hata.
    foreach (['https://example.com/user', 'https://nextdoor.com/user', 'https://xkcd.com/'] as $url) {
        assertSame('', social_icon($url, 'Kişisel site'), 'Alt dize eşleşmemeli: ' . $url);
    }

    // Alan adinin kendisi ve alt alan adi eslesmeli.
    assertNotSame('', social_icon('https://x.com/arcates', 'Bağlantı'), 'Alan adının kendisi eşleşmeli');
    assertNotSame('', social_icon('https://www.linkedin.com/company/x', 'Bağlantı'), 'www eşleşmeli');
    assertNotSame('', social_icon('https://m.youtube.com/@x', 'Bağlantı'), 'Alt alan adı eşleşmeli');
});

test('F-FC-c', 'Alan adı tanınmazsa etiket adından çözülür', function (): void {
    // Yonetici kisaltilmis bir adres girmis olabilir.
    assertNotSame('', social_icon('https://lnkd.in/abc', 'LinkedIn'), 'Etiket adı yedek olmalı');
    assertNotSame('', social_icon('https://t.co/abc', 'Twitter'), 'Twitter X ikonuna düşmeli');
});

test('F-FC-d', 'Sosyal adres girilmemişse alt bilgide liste basılmaz', function (): void {
    arc_need_db();
    Arcates\Core\Lang::use('tr');

    $onceki = Arcates\Core\Settings::get('social_links', '[]');
    Arcates\Core\Settings::set('social_links', '[]');
    Arcates\Core\Settings::flush();

    $body = (new Arcates\Controllers\Front\HomeController())
        ->index(Arcates\Core\Request::make('GET', '/'), [])->body();

    assertNotContains('site-foot__social', $body, 'Boşken sosyal liste hiç basılmamalı');

    Arcates\Core\Settings::set('social_links', (string) $onceki);
    Arcates\Core\Settings::flush();
});

test('F-FC-e', 'Yukarı çık düğmesi JS kapalıyken gizli', function (): void {
    arc_need_db();
    Arcates\Core\Lang::use('tr');

    $body = (new Arcates\Controllers\Front\HomeController())
        ->index(Arcates\Core\Request::make('GET', '/'), [])->body();

    if (preg_match('~<button class="to-top".*?</button>~s', $body, $m) !== 1) {
        assertTrue(false, 'Yukarı çık düğmesi basılmalı');
        return;
    }

    // Isaretlemede `hidden` gelir; JS onu acar. JS kapaliyken islevi yok.
    assertContains('hidden', $m[0], 'Düğme işaretlemede gizli gelmeli');
    assertContains('aria-label=', $m[0], 'Düğmenin erişilebilir adı olmalı');
    assertContains('data-to-top', $m[0], 'JS kancası bulunmalı');
});

test('F-FC-f', 'Yukarı çık yalnızca transform ve opacity ile canlanır', function (): void {
    $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/reference-chrome.css');

    if (preg_match('~\.to-top\s*\{(.*?)\}~s', $css, $m) !== 1) {
        assertTrue(false, 'Düğme kuralı bulunmalı');
        return;
    }

    // DOCS.md 7.1: yalnizca `transform` ve `opacity`.
    assertContains('transition: opacity', $m[1], 'Geçiş opacity ile olmalı');
    assertNotContains('transition: all', $m[1], 'Toptan geçiş kullanılmamalı');

    assertContains('@media (prefers-reduced-motion: reduce)', $css, 'Azaltılmış hareket kuralı olmalı');

    $js = (string) file_get_contents(ARC_ROOT . '/public/assets/js/site.js');
    assertContains("prefersReducedMotion() ? 'auto' : 'smooth'", $js, 'Kaydırma azaltılmış harekete saygı duymalı');
});
