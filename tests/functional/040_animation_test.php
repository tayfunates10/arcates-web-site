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

test('A-01', 'JavaScript kapalıyken hiçbir içerik gizli kalmaz', function (): void {
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
        'Gizleyen kurallar yalnızca html.js altında olmalı. İhlal: ' . implode(' | ', array_slice($hidingSelectors, 0, 5))
    );

    // Mobilde menu paneli JavaScript yoksa gorunur kalir.
    assertContains('html:not(.js) .site-head__panel', $css, 'JS yoksa menü paneli açık kalmalı');
    assertContains('html:not(.js) .site-nav__toggle { display: none; }', $css, 'JS yoksa açma düğmesi gizlenmeli');
});

test('A-01b', 'Satır içi js bayrağı CSP karmasıyla birebir aynı', function (): void {
    arc_test_config();

    $head = (string) file_get_contents(ARC_ROOT . '/views/front/partials/head.php');
    assertContains('Security::JS_FLAG_SCRIPT', $head, 'Bayrak sabitten basılmalı');

    // Karma bu govdeden uretilir; metin degisirse CSP kirar.
    $expected = base64_encode(hash('sha256', Arcates\Core\Security::JS_FLAG_SCRIPT, true));
    assertSame($expected, Arcates\Core\Security::jsFlagHash(), 'Karma gövdeden üretilmeli');
    assertContains("sha256-" . $expected, Arcates\Core\Security::csp(false), 'CSP karmayı taşımalı');
    assertContains("' js'", Arcates\Core\Security::JS_FLAG_SCRIPT, 'Bayrak js sınıfını eklemeli');
});

test('A-02', 'prefers-reduced-motion tüm animasyonları kapatır ve son durumu gösterir', function (): void {
    $css = arc_site_css();

    assertContains('@media (prefers-reduced-motion: reduce)', $css, 'Azaltılmış hareket bloku bulunmalı');

    $start = strpos($css, '@media (prefers-reduced-motion: reduce)');
    $block = substr($css, (int) $start, 2600);

    assertContains('animation-duration: .001ms !important', $block, 'Animasyon süresi sıfırlanmalı');
    assertContains('transition-duration: .001ms !important', $block, 'Geçiş süresi sıfırlanmalı');
    assertContains('opacity: 1 !important', $block, 'Ögeler görünür duruma alınmalı');
    assertContains('transform: none !important', $block, 'Son konum gösterilmeli');
    assertContains('.strip__track { animation: none !important; }', $block, 'Şerit döngüsü durmalı');

    // Motor tarafinda da kontrol edilir.
    $js = arc_site_js();
    assertContains('prefers-reduced-motion: reduce', $js, 'Motor tercihi okumalı');
    assertContains('if (prefersReducedMotion()) {', $js, 'Motor tercihe göre davranmalı');
});

test('A-03', 'Açılış sırası ve gecikmeleri şartnameyle aynı', function (): void {
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
            "{$selector} için {$delay} gecikmesi tanımlı olmalı"
        );
    }

    // Grafik cizgisi 1150 ms'te cizilir.
    assertContains('animation-delay: 1150ms', $css, 'Grafik çizgisi 1150 ms\'te başlamalı');

    // Toplam acilis 1.4 saniyede biter; dongu bundan sonra baslar.
    assertContains('HERO_SETTLE = 1400', arc_site_js(), 'Açılış 1.4 saniyede tamamlanmalı');
});

test('A-04', 'Bölge haritası scroll ilerlemesine göre çizilir', function (): void {
    $js  = arc_site_js();
    $css = arc_site_css();

    assertContains('--coast-offset', $js, 'Çizgi ofseti ilerlemeye göre ayarlanmalı');
    assertContains("classList.toggle('is-lit', progress >= at)", $js, 'Noktalar çizgi geçtikçe yanmalı');
    assertContains('stroke-dashoffset: var(--coast-offset', $css, 'CSS ofseti kullanmalı');
    assertContains('data-at', (string) file_get_contents(ARC_ROOT . '/views/front/partials/coast.php'), 'Her nokta eşik değeri taşımalı');
});

test('A-05', 'Görünmüş ögeler tekrar oynatılmaz', function (): void {
    $js = arc_site_js();

    // Kural 6: bir kez gorundukten sonra unobserve edilir.
    assertContains('observer.unobserve(entries[i].target)', $js, 'Öge izlemeden çıkarılmalı');
    assertNotContains("classList.remove('is-visible')", $js, 'Görünürlük geri alınmamalı');
});

test('A-06', 'Sekme arka plana alınınca şerit duraklatılır', function (): void {
    $js = arc_site_js();

    assertContains("doc.addEventListener('visibilitychange'", $js, 'Görünürlük değişimi dinlenmeli');
    assertContains('animationPlayState', $js, 'Animasyon duraklatılmalı');
});

test('A-07', 'Pencere boyutlanmasında harita çizgi uzunluğu yeniden hesaplanır', function (): void {
    $js = arc_site_js();

    assertContains("window.addEventListener('resize'", $js, 'Boyutlanma dinlenmeli');
    assertContains('{ passive: true }', $js, 'Dinleyici passive olmalı');
    assertContains('measure();', $js, 'Uzunluk yeniden ölçülmeli');
});

test('A-09', 'IntersectionObserver desteklenmiyorsa tüm ögeler görünür olur', function (): void {
    $js = arc_site_js();

    assertContains("if (!('IntersectionObserver' in window)) {", $js, 'Destek kontrolü bulunmalı');
    assertContains('revealAll();', $js, 'Destek yoksa hepsi görünür olmalı');
});

test('A-10', 'Sektör şeridi kesintisiz döner', function (): void {
    $css  = arc_site_css();
    $strip = (string) file_get_contents(ARC_ROOT . '/views/front/partials/strip.php');

    // Grup sunucudan iki kez basilir; -%50 kaydiginda sicrama olmaz.
    assertSame(2, substr_count($strip, 'strip__group'), 'Etiket grubu iki kez basılmalı');
    assertContains('to   { transform: translate3d(-50%, 0, 0); }', $css, 'Animasyon tam yarım tur kaymalı');
    assertContains('linear infinite', $css, 'Döngü doğrusal ve sonsuz olmalı');
    assertContains('--strip-loop: 34s', $css, 'Tam tur 34 saniye olmalı');
    assertContains('calc(var(--strip-loop) / 0.7)', $css, 'Mobilde hız %70\'e düşmeli');
});

test('A-P5-a', 'Yalnızca transform ve opacity animasyonu yapılır', function (): void {
    $css = arc_site_css();

    // Yasak ozelliklerin gecis veya animasyon hedefi olmadigini dogrula.
    // DOCS.md 7.1 kural 1
    if (preg_match_all('/transition\s*:\s*([^;}]+)/i', $css, $matches)) {
        foreach ($matches[1] as $value) {
            foreach (['width', 'height', 'margin', 'padding', 'top', 'left', 'right', 'bottom'] as $banned) {
                assertFalse(
                    preg_match('/(^|[\s,])' . $banned . '(\s|,|$)/i', $value) === 1,
                    "Yasak özellik geçişe girmiş: {$banned} — {$value}"
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
                        "Keyframe içinde izin verilmeyen özellik: {$property}"
                    );
                }
            }
        }
    }
});

test('A-P5-b', 'Scroll dinleyicileri passive ve rAF ile sınırlandırılmış', function (): void {
    $js = arc_site_js();

    assertContains("window.addEventListener('scroll', requestScrollFrame, { passive: true })", $js, 'Scroll passive olmalı');
    assertContains('window.requestAnimationFrame(onScrollFrame)', $js, 'rAF ile sınırlandırılmalı');
    assertContains('if (ticking) return;', $js, 'Aynı karede iki kez çalışmamalı');
});

test('A-P5-c', 'Hareket belirteçleri şartnamedeki değerlerle aynı', function (): void {
    $css = arc_site_css();
    $js  = arc_site_js();

    // DOCS.md 7.2
    assertContains('--ease: cubic-bezier(.16, 1, .3, 1)', $css, 'Yumuşatma eğrisi');
    assertContains('--dur-title: 1000ms', $css, 'Başlık maskesi 1000 ms');
    assertContains('--dur-card: 800ms', $css, 'Kart girişi 800 ms');
    assertContains('--dur-shape: 950ms', $css, 'Şekil girişi 950 ms');
    assertContains('--float-a: 7s', $css, 'Süzülme döngüsü 7 s');
    assertContains('--float-b: 9s', $css, 'Süzülme döngüsü 9 s');
    assertContains('--reveal-delay: 80ms', $css, 'Kart kademesi 80 ms');
    assertContains('--reveal-delay: 140ms', $css, 'Adım kademesi 140 ms');

    assertContains('THRESHOLD = 0.15', $js, 'Görünürlük eşiği 0.15');
    assertContains("ROOT_MARGIN = '0px 0px -8% 0px'", $js, 'rootMargin şartnamedeki gibi');
    assertContains('HEAD_STUCK_AT = 24', $js, 'Üst menü 24 px sonrası küçülür');
    assertContains('PARALLAX_LIMIT = 1.3', $js, 'Sürüklenme ilk ekranın 1.3 katı içinde');
});

test('A-P5-d', 'Metin üzerinde döngü animasyonu yoktur', function (): void {
    $css = arc_site_css();

    // Kural 7: sonsuz dongu yalnizca dekoratif sekillerde ve seritte.
    if (preg_match_all('/([^{}]+)\{([^{}]*infinite[^{}]*)\}/s', $css, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $rule) {
            $selector = trim(preg_replace('/\s+/', ' ', $rule[1]) ?? '');
            $allowed  = str_contains($selector, '.shape') || str_contains($selector, '.strip__track');
            assertTrue($allowed, "Döngü animasyonu yalnızca şekil ve şeritte olmalı; bulunan: {$selector}");
        }
    }
});
