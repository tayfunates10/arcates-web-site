<?php
/**
 * Animasyon sartnamesi denetimleri.  DOCS.md 7, 14.5
 *
 * Tarayici davranisi otomatik olarak calistirilamaz; bu testler sartnamenin
 * kod tarafindaki kosullarini dogrular:
 *   - Gizli baslangic durumlari yalnizca `html.js` altinda tanimli mi?
 *   - Yalnizca transform/opacity animasyonu var mi?
 *   - prefers-reduced-motion son durumu gosteriyor mu?
 *   - Belirtecler bolum 7.2'deki degerlerle ayni mi?
 * Tarayicida elle dogrulanacak testler DOCS.md 14.5'te listelidir.
 */

declare(strict_types=1);

/** site.css icerigi. */
function arc_site_css(): string
{
    static $css = null;
    if ($css === null) {
        $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/site.css');
    }
    return $css;
}

/** site.js icerigi. */
function arc_site_js(): string
{
    static $js = null;
    if ($js === null) {
        $js = (string) file_get_contents(ARC_ROOT . '/public/assets/js/site.js');
    }
    return $js;
}

test('A-01', 'JavaScript kapaliyken hicbir icerik gizli kalmaz', function (): void {
    $css = arc_site_css();

    // Gizleyen her kural `html.js` ile baslamalidir. DOCS.md 7.1 kural 2
    $hidingSelectors = [];
    if (preg_match_all('/([^{}]+)\{([^{}]*)\}/s', $css, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $rule) {
            $selector = trim(preg_replace('/\s+/', ' ', $rule[1]) ?? '');
            $body     = $rule[2];

            // `opacity: 0` veya `transform: translate...` ile baslangicta
            // gizleyen kurallar.
            $hidesOpacity = preg_match('/(^|[;{\s])opacity\s*:\s*0\s*(;|$)/', $body) === 1;
            if (!$hidesOpacity) {
                continue;
            }
            // Keyframe bloklarinin icindeki `from`/`to` kurallari haric.
            if (preg_match('/^(from|to|\d+%)/', $selector) === 1) {
                continue;
            }
            if (str_starts_with($selector, 'html.js') || str_contains($selector, 'html.js ')) {
                continue;
            }
            $hidingSelectors[] = $selector;
        }
    }

    assertCount(
        0,
        $hidingSelectors,
        'Gizleyen kurallar yalnizca html.js altinda olmali. Ihlal: ' . implode(' | ', array_slice($hidingSelectors, 0, 5))
    );

    // Mobilde menu paneli JavaScript yoksa gorunur kalir.
    assertContains('html:not(.js) .site-head__panel', $css, 'JS yoksa menu paneli acik kalmali');
    assertContains('html:not(.js) .site-nav__toggle { display: none; }', $css, 'JS yoksa acma dugmesi gizlenmeli');
});

test('A-01b', 'Satir ici js bayragi CSP karmasiyla birebir ayni', function (): void {
    arc_test_config();

    $head = (string) file_get_contents(ARC_ROOT . '/views/front/partials/head.php');
    assertContains('Security::JS_FLAG_SCRIPT', $head, 'Bayrak sabitten basilmali');

    // Karma bu govdeden uretilir; metin degisirse CSP kirar.
    $expected = base64_encode(hash('sha256', Arcates\Core\Security::JS_FLAG_SCRIPT, true));
    assertSame($expected, Arcates\Core\Security::jsFlagHash(), 'Karma govdeden uretilmeli');
    assertContains("sha256-" . $expected, Arcates\Core\Security::csp(false), 'CSP karmayi tasimali');
    assertContains("' js'", Arcates\Core\Security::JS_FLAG_SCRIPT, 'Bayrak js sinifini eklemeli');
});

test('A-02', 'prefers-reduced-motion tum animasyonlari kapatir ve son durumu gosterir', function (): void {
    $css = arc_site_css();

    assertContains('@media (prefers-reduced-motion: reduce)', $css, 'Azaltilmis hareket bloku bulunmali');

    $start = strpos($css, '@media (prefers-reduced-motion: reduce)');
    $block = substr($css, (int) $start, 2600);

    assertContains('animation-duration: .001ms !important', $block, 'Animasyon suresi sifirlanmali');
    assertContains('transition-duration: .001ms !important', $block, 'Gecis suresi sifirlanmali');
    assertContains('opacity: 1 !important', $block, 'Ogeler gorunur duruma alinmali');
    assertContains('transform: none !important', $block, 'Son konum gosterilmeli');
    assertContains('.strip__track { animation: none !important; }', $block, 'Serit dongusu durmali');

    // Motor tarafinda da kontrol edilir.
    $js = arc_site_js();
    assertContains('prefers-reduced-motion: reduce', $js, 'Motor tercihi okumali');
    assertContains('if (prefersReducedMotion()) {', $js, 'Motor tercihe gore davranmali');
});

test('A-03', 'Acilis sirasi ve gecikmeleri sartnameyle ayni', function (): void {
    $css = arc_site_css();

    // DOCS.md 7.3 tablosu
    $expected = [
        '.brand__mark' => '0ms',
        '.hero__line--1 > span' => '80ms',
        '.hero__line--2 > span' => '180ms',
        '.hero__line--3 > span' => '280ms',
        '.shape--card' => '240ms',
        '.shape--circle' => '420ms',
        '.shape--ring' => '520ms',
        '.hero__text' => '560ms',
        '.hero__actions' => '660ms',
        '.shape--square' => '640ms',
        '.shape--pill' => '860ms',
    ];

    foreach ($expected as $selector => $delay) {
        assertContains(
            'animation-delay: ' . $delay,
            $css,
            "{$selector} icin {$delay} gecikmesi tanimli olmali"
        );
    }

    // Grafik cizgisi 1150 ms'te cizilir.
    assertContains('animation-delay: 1150ms', $css, 'Grafik cizgisi 1150 ms\'te baslamali');

    // Toplam acilis 1.4 saniyede biter; dongu bundan sonra baslar.
    assertContains('HERO_SETTLE = 1400', arc_site_js(), 'Acilis 1.4 saniyede tamamlanmali');
});

test('A-04', 'Bolge haritasi scroll ilerlemesine gore cizilir', function (): void {
    $js  = arc_site_js();
    $css = arc_site_css();

    assertContains('--coast-offset', $js, 'Cizgi ofseti ilerlemeye gore ayarlanmali');
    assertContains("classList.toggle('is-lit', progress >= at)", $js, 'Noktalar cizgi gectikce yanmali');
    assertContains('stroke-dashoffset: var(--coast-offset', $css, 'CSS ofseti kullanmali');
    assertContains('data-at', (string) file_get_contents(ARC_ROOT . '/views/front/partials/coast.php'), 'Her nokta esik degeri tasimali');
});

test('A-05', 'Gorunmus ogeler tekrar oynatilmaz', function (): void {
    $js = arc_site_js();

    // Kural 6: bir kez gorundukten sonra unobserve edilir.
    assertContains('observer.unobserve(entries[i].target)', $js, 'Oge izlemeden cikarilmali');
    assertNotContains("classList.remove('is-visible')", $js, 'Gorunurluk geri alinmamali');
});

test('A-06', 'Sekme arka plana alininca serit duraklatilir', function (): void {
    $js = arc_site_js();

    assertContains("doc.addEventListener('visibilitychange'", $js, 'Gorunurluk degisimi dinlenmeli');
    assertContains('animationPlayState', $js, 'Animasyon duraklatilmali');
});

test('A-07', 'Pencere boyutlanmasinda harita cizgi uzunlugu yeniden hesaplanir', function (): void {
    $js = arc_site_js();

    assertContains("window.addEventListener('resize'", $js, 'Boyutlanma dinlenmeli');
    assertContains('{ passive: true }', $js, 'Dinleyici passive olmali');
    assertContains('measure();', $js, 'Uzunluk yeniden olculmeli');
});

test('A-09', 'IntersectionObserver desteklenmiyorsa tum ogeler gorunur olur', function (): void {
    $js = arc_site_js();

    assertContains("if (!('IntersectionObserver' in window)) {", $js, 'Destek kontrolu bulunmali');
    assertContains('revealAll();', $js, 'Destek yoksa hepsi gorunur olmali');
});

test('A-10', 'Sektor seridi kesintisiz doner', function (): void {
    $css  = arc_site_css();
    $strip = (string) file_get_contents(ARC_ROOT . '/views/front/partials/strip.php');

    // Grup sunucudan iki kez basilir; -%50 kaydiginda sicrama olmaz.
    assertSame(2, substr_count($strip, 'strip__group'), 'Etiket grubu iki kez basilmali');
    assertContains('to   { transform: translate3d(-50%, 0, 0); }', $css, 'Animasyon tam yarim tur kaymali');
    assertContains('linear infinite', $css, 'Dongu dogrusal ve sonsuz olmali');
    assertContains('--strip-loop: 34s', $css, 'Tam tur 34 saniye olmali');
    assertContains('calc(var(--strip-loop) / 0.7)', $css, 'Mobilde hiz %70\'e dusmeli');
});

test('A-P5-a', 'Yalnizca transform ve opacity animasyonu yapilir', function (): void {
    $css = arc_site_css();

    // Yasak ozelliklerin gecis veya animasyon hedefi olmadigini dogrula.
    // DOCS.md 7.1 kural 1
    if (preg_match_all('/transition\s*:\s*([^;}]+)/i', $css, $matches)) {
        foreach ($matches[1] as $value) {
            foreach (['width', 'height', 'margin', 'padding', 'top', 'left', 'right', 'bottom'] as $banned) {
                assertFalse(
                    preg_match('/(^|[\s,])' . $banned . '(\s|,|$)/i', $value) === 1,
                    "Yasak ozellik gecise girmis: {$banned} — {$value}"
                );
            }
        }
    }

    // Keyframe govdelerinde yalnizca transform, opacity ve stroke-dashoffset.
    if (preg_match_all('/@keyframes\s+[\w-]+\s*\{(.*?)\n\}/s', $css, $frames)) {
        foreach ($frames[1] as $body) {
            if (preg_match_all('/([a-z-]+)\s*:/i', $body, $props)) {
                foreach ($props[1] as $property) {
                    assertTrue(
                        in_array(strtolower($property), ['transform', 'opacity', 'stroke-dashoffset'], true),
                        "Keyframe icinde izin verilmeyen ozellik: {$property}"
                    );
                }
            }
        }
    }
});

test('A-P5-b', 'Scroll dinleyicileri passive ve rAF ile sinirlandirilmis', function (): void {
    $js = arc_site_js();

    assertContains("window.addEventListener('scroll', requestScrollFrame, { passive: true })", $js, 'Scroll passive olmali');
    assertContains('window.requestAnimationFrame(onScrollFrame)', $js, 'rAF ile sinirlandirilmali');
    assertContains('if (ticking) return;', $js, 'Ayni karede iki kez calismamali');
});

test('A-P5-c', 'Hareket belirtecleri sartnamedeki degerlerle ayni', function (): void {
    $css = arc_site_css();
    $js  = arc_site_js();

    // DOCS.md 7.2
    assertContains('--ease: cubic-bezier(.16, 1, .3, 1)', $css, 'Yumusatma egrisi');
    assertContains('--dur-title: 1000ms', $css, 'Baslik maskesi 1000 ms');
    assertContains('--dur-card: 800ms', $css, 'Kart girisi 800 ms');
    assertContains('--dur-shape: 950ms', $css, 'Sekil girisi 950 ms');
    assertContains('--float-a: 7s', $css, 'Suzulme dongusu 7 s');
    assertContains('--float-b: 9s', $css, 'Suzulme dongusu 9 s');
    assertContains('--reveal-delay: 80ms', $css, 'Kart kademesi 80 ms');
    assertContains('--reveal-delay: 140ms', $css, 'Adim kademesi 140 ms');

    assertContains('THRESHOLD = 0.15', $js, 'Gorunurluk esigi 0.15');
    assertContains("ROOT_MARGIN = '0px 0px -8% 0px'", $js, 'rootMargin sartnamedeki gibi');
    assertContains('HEAD_STUCK_AT = 24', $js, 'Ust menu 24 px sonrasi kuculur');
    assertContains('PARALLAX_LIMIT = 1.3', $js, 'Suruklenme ilk ekranin 1.3 kati icinde');
});

test('A-P5-d', 'Metin uzerinde dongu animasyonu yoktur', function (): void {
    $css = arc_site_css();

    // Kural 7: sonsuz dongu yalnizca dekoratif sekillerde ve seritte.
    if (preg_match_all('/([^{}]+)\{([^{}]*infinite[^{}]*)\}/s', $css, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $rule) {
            $selector = trim(preg_replace('/\s+/', ' ', $rule[1]) ?? '');
            $allowed  = str_contains($selector, '.shape') || str_contains($selector, '.strip__track');
            assertTrue($allowed, "Dongu animasyonu yalnizca sekil ve seritte olmali; bulunan: {$selector}");
        }
    }
});
