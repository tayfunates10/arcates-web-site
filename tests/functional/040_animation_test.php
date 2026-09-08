<?php
/**
 * Animasyon sartnamesi denetimleri. DOCS.md 7, 14.5
 * v1.1: testler CSS'nin metinsel bicimini degil, katmanli sistemdeki davranis
 * sozlesmesini dogrular.
 */
declare(strict_types=1);

function arc_site_css(): string
{
    static $css = null;
    return $css ??= (string) file_get_contents(ARC_ROOT . '/public/assets/css/site.css');
}

function arc_site_js(): string
{
    static $js = null;
    return $js ??= (string) file_get_contents(ARC_ROOT . '/public/assets/js/site.js');
}

/** Belirli bir CSS secicisinin ilk kural govdesini dondurur. */
function arc_css_rule(string $selector): string
{
    $css = arc_site_css();
    $quoted = preg_quote($selector, '/');
    return preg_match('/' . $quoted . '\s*\{([^{}]*)\}/s', $css, $m) === 1 ? $m[1] : '';
}

/** @keyframes bloklarini dengeli parantez okuyarak ayirir. */
function arc_keyframe_bodies(string $css): array
{
    $out = [];
    $offset = 0;
    while (preg_match('/@keyframes\s+([\w-]+)\s*\{/i', $css, $m, PREG_OFFSET_CAPTURE, $offset) === 1) {
        $name = $m[1][0];
        $start = $m[0][1] + strlen($m[0][0]);
        $depth = 1;
        $i = $start;
        $len = strlen($css);
        for (; $i < $len && $depth > 0; $i++) {
            if ($css[$i] === '{') $depth++;
            elseif ($css[$i] === '}') $depth--;
        }
        $out[$name] = substr($css, $start, max(0, $i - $start - 1));
        $offset = $i;
    }
    return $out;
}

test('A-01', 'JavaScript kapalıyken hiçbir içerik gizli kalmaz', function (): void {
    $css = arc_site_css();
    $hidingSelectors = [];
    if (preg_match_all('/([^{}]+)\{([^{}]*)\}/s', $css, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $rule) {
            $selector = trim(preg_replace('/\s+/', ' ', $rule[1]) ?? '');
            $body = $rule[2];
            if (preg_match('/(^|[;{\s])opacity\s*:\s*0\s*(;|$)/', $body) !== 1) continue;
            if (preg_match('/^(from|to|\d+%)/', $selector) === 1) continue;
            if (str_starts_with($selector, 'html.js') || str_contains($selector, 'html.js ')) continue;
            // is-suppressed baslangic durumu degildir; JS tarafindan son CTA
            // gorunurken eklenen gecici siniftir ve JS yoksa DOM'da olusmaz.
            if (str_contains($selector, '.is-suppressed')) continue;
            $hidingSelectors[] = $selector;
        }
    }
    assertCount(0, $hidingSelectors, 'JS yokken gizleyen statik kural kalmamali: ' . implode(' | ', $hidingSelectors));
    assertContains('html:not(.js) .site-head__panel', $css);
    assertContains('html:not(.js) .site-nav__toggle { display: none; }', $css);
});

test('A-01b', 'Satır içi js bayrağı CSP karmasıyla birebir aynı', function (): void {
    arc_test_config();
    $head = (string) file_get_contents(ARC_ROOT . '/views/front/partials/head.php');
    assertContains('Security::JS_FLAG_SCRIPT', $head);
    $expected = base64_encode(hash('sha256', Arcates\Core\Security::JS_FLAG_SCRIPT, true));
    assertSame($expected, Arcates\Core\Security::jsFlagHash());
    assertContains('sha256-' . $expected, Arcates\Core\Security::csp(false));
});

test('A-02', 'prefers-reduced-motion tüm animasyonları kapatır ve son durumu gösterir', function (): void {
    $css = arc_site_css();
    $start = strpos($css, '@media (prefers-reduced-motion: reduce)');
    assertTrue($start !== false, 'Azaltilmis hareket bloku bulunmali');
    $block = substr($css, (int) $start, 2600);
    assertContains('animation-duration: .001ms', $block);
    assertContains('transition-duration: .001ms', $block);
    assertContains('opacity: 1', $block);
    assertContains('transform: none', $block);
    assertContains('.strip__track { animation: none; }', $block);
    assertNotContains('!important', $block, 'v1.1 katmanli CSS reduced-motion icin important gerektirmemeli');
    $js = arc_site_js();
    assertContains('prefers-reduced-motion: reduce', $js);
    assertContains('prefersReducedMotion()', $js);
});

test('A-03', 'Açılış sırası ve gecikmeleri şartnameyle aynı', function (): void {
    $css = arc_site_css();
    $expected = [80, 180, 280, 240, 420, 520, 560, 660, 640, 860, 1150];
    foreach ($expected as $delay) assertContains('animation-delay: ' . $delay . 'ms', $css, $delay . 'ms gecikmesi korunmali');
    // Logo animasyonunda delay yazilmazsa CSS varsayilani 0ms'dir.
    $brand = arc_css_rule('html.js .brand__mark');
    assertContains('animation: hero-mark', $brand, 'Logo giris animasyonu korunmali');
    assertNotContains('animation-delay:', $brand, 'Logo gecikmesi varsayilan 0ms olmali');
    assertContains('HERO_SETTLE = 1400', arc_site_js());
});

test('A-04', 'Bölge haritası scroll ilerlemesine göre çizilir', function (): void {
    assertContains('--coast-offset', arc_site_js());
    assertContains("classList.toggle('is-lit', progress >= at)", arc_site_js());
    assertContains('stroke-dashoffset: var(--coast-offset', arc_site_css());
    assertContains('data-at', (string) file_get_contents(ARC_ROOT . '/views/front/partials/coast.php'));
});

test('A-05', 'Görünmüş ögeler tekrar oynatılmaz', function (): void {
    $js = arc_site_js();
    assertContains('observer.unobserve(entries[i].target)', $js);
    assertNotContains("classList.remove('is-visible')", $js);
});

test('A-06', 'Sekme arka plana alınınca şerit duraklatılır', function (): void {
    assertContains("doc.addEventListener('visibilitychange'", arc_site_js());
    assertContains('animationPlayState', arc_site_js());
});

test('A-07', 'Pencere boyutlanmasında harita çizgi uzunluğu yeniden hesaplanır', function (): void {
    $js = arc_site_js();
    assertContains("window.addEventListener('resize'", $js);
    assertContains('{ passive: true }', $js);
    assertContains('measure();', $js);
});

test('A-09', 'IntersectionObserver desteklenmiyorsa tüm ögeler görünür olur', function (): void {
    $js = arc_site_js();
    assertContains("!('IntersectionObserver' in window)", $js, 'Destek kontrolu bulunmali');
    assertContains('revealAll();', $js);
});

test('A-10', 'Sektör şeridi kesintisiz döner', function (): void {
    $css = arc_site_css();
    $strip = (string) file_get_contents(ARC_ROOT . '/views/front/partials/strip.php');
    assertSame(2, substr_count($strip, 'strip__group'));
    $frame = arc_keyframe_bodies($css)['strip-scroll'] ?? '';
    assertContains('translate3d(-50%', $frame, 'Serit yarim grup kadar kaymali');
    assertContains('linear infinite', $css);
    assertContains('--strip-loop: 34s', $css);
    assertTrue(
        str_contains($css, 'calc(var(--strip-loop) / .7)') || str_contains($css, 'calc(var(--strip-loop) / 0.7)'),
        'Mobil serit hizi %70 olmali'
    );
});

test('A-P5-a', 'Yalnızca performanslı özellikler animasyon hedefidir', function (): void {
    $css = arc_site_css();
    if (preg_match_all('/transition\s*:\s*([^;}]+)/i', $css, $matches)) {
        foreach ($matches[1] as $value) {
            foreach (['width','height','margin','padding','top','left','right','bottom'] as $banned) {
                assertFalse(preg_match('/(^|[\s,])' . $banned . '(\s|,|$)/i', $value) === 1, 'Yasak gecis: ' . $banned);
            }
        }
    }
    foreach (arc_keyframe_bodies($css) as $name => $body) {
        if (!preg_match_all('/([a-z-]+)\s*:/i', $body, $props)) continue;
        foreach ($props[1] as $property) {
            assertTrue(
                in_array(strtolower($property), ['transform', 'opacity', 'stroke-dashoffset'], true),
                "{$name} keyframe icinde izin verilmeyen ozellik: {$property}"
            );
        }
    }
});

test('A-P5-b', 'Scroll dinleyicileri passive ve rAF ile sınırlandırılmış', function (): void {
    $js = arc_site_js();
    assertContains("window.addEventListener('scroll', requestScrollFrame, { passive: true })", $js);
    assertContains('window.requestAnimationFrame(onScrollFrame)', $js);
    assertContains('if (ticking) return;', $js);
});

test('A-P5-c', 'Hareket belirteçleri şartnamedeki değerlerle aynı', function (): void {
    $css = arc_site_css(); $js = arc_site_js();
    foreach (['--ease: cubic-bezier(.16, 1, .3, 1)','--dur-title: 1000ms','--dur-card: 800ms','--dur-shape: 950ms','--float-a: 7s','--float-b: 9s','--reveal-delay: 80ms','--reveal-delay: 140ms'] as $needle) assertContains($needle, $css);
    assertContains('THRESHOLD = 0.15', $js);
    assertContains("ROOT_MARGIN = '0px 0px -8% 0px'", $js);
    assertContains('HEAD_STUCK_AT = 24', $js);
    assertContains('PARALLAX_LIMIT = 1.3', $js);
});

test('A-P5-d', 'Metin üzerinde döngü animasyonu yoktur', function (): void {
    $css = arc_site_css();
    if (preg_match_all('/([^{}]+)\{([^{}]*infinite[^{}]*)\}/s', $css, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $rule) {
            $selector = trim(preg_replace('/\s+/', ' ', $rule[1]) ?? '');
            $allowed = str_contains($selector, '.shape') || str_contains($selector, '.strip__track');
            assertTrue($allowed, 'Dongu yalnız dekoratif sekil/seritte olmali: ' . $selector);
        }
    }
});
