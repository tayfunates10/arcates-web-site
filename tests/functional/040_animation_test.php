<?php
/**
 * Hareket ve ilerlemeli iyilestirme denetimleri.
 * R5 ana sayfa, dekoratif sonsuz donguler yerine kisa acilis + tek seferlik
 * reveal ve bolge cizgisi hareketini kullanir.
 */
declare(strict_types=1);

function arc_site_css(): string
{
    static $css = null;
    return $css ??= (string) file_get_contents(ARC_ROOT . '/public/assets/css/site.css');
}

function arc_home_css(): string
{
    static $css = null;
    return $css ??= (string) file_get_contents(ARC_ROOT . '/public/assets/css/home-redesign.css');
}

function arc_site_js(): string
{
    static $js = null;
    return $js ??= (string) file_get_contents(ARC_ROOT . '/public/assets/js/site.js');
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
    foreach ([arc_site_css(), arc_home_css()] as $css) {
        $hidingSelectors = [];
        if (preg_match_all('/([^{}]+)\{([^{}]*)\}/s', $css, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $rule) {
                $selector = trim(preg_replace('/\s+/', ' ', $rule[1]) ?? '');
                $body = $rule[2];
                if (preg_match('/(^|[;{\s])opacity\s*:\s*0\s*(;|$)/', $body) !== 1) continue;
                if (preg_match('/^(from|to|\d+%)/', $selector) === 1) continue;
                if (str_starts_with($selector, 'html.js') || str_contains($selector, 'html.js ')) continue;
                if (str_contains($selector, '.is-suppressed')) continue;
                $hidingSelectors[] = $selector;
            }
        }
        assertCount(0, $hidingSelectors, 'JS yokken gizleyen statik kural kalmamali: ' . implode(' | ', $hidingSelectors));
    }
    assertContains('html:not(.js) .site-head__panel', arc_site_css());
    assertContains('html:not(.js) .site-nav__toggle { display: none; }', arc_site_css());
});

test('A-01b', 'Satır içi js bayrağı CSP karmasıyla birebir aynı', function (): void {
    arc_test_config();
    $head = (string) file_get_contents(ARC_ROOT . '/views/front/partials/head.php');
    assertContains('Security::JS_FLAG_SCRIPT', $head);
    $expected = base64_encode(hash('sha256', Arcates\Core\Security::JS_FLAG_SCRIPT, true));
    assertSame($expected, Arcates\Core\Security::jsFlagHash());
    assertContains('sha256-' . $expected, Arcates\Core\Security::csp(false));
});

test('A-02', 'prefers-reduced-motion R5 dahil tüm hareketi kapatır', function (): void {
    $site = arc_site_css();
    $start = strpos($site, '@media (prefers-reduced-motion: reduce)');
    assertTrue($start !== false, 'Genel azaltilmis hareket bloku bulunmali');
    $block = substr($site, (int) $start, 2600);
    assertContains('animation-duration: .001ms', $block);
    assertContains('transition-duration: .001ms', $block);
    assertContains('opacity: 1', $block);
    assertContains('transform: none', $block);
    assertNotContains('!important', $block);

    $home = arc_home_css();
    $homeStart = strpos($home, '@media (prefers-reduced-motion: reduce)');
    assertTrue($homeStart !== false, 'R5 azaltilmis hareket bloku bulunmali');
    $homeBlock = substr($home, (int) $homeStart);
    assertContains('hero-scene__art', $homeBlock);
    assertContains('opacity: 1', $homeBlock);
    assertContains('transform: none', $homeBlock);
    assertContains('animation: none', $homeBlock);
    assertContains('transition: none', $homeBlock);

    $js = arc_site_js();
    assertContains('prefers-reduced-motion: reduce', $js);
    assertContains('prefersReducedMotion()', $js);
});

test('A-03', 'R5 hero acilisi bir saniyenin altinda oturur', function (): void {
    $css = arc_home_css();
    foreach ([
        'rd-hero-line 600ms var(--ease) both',
        'rd-hero-fade 440ms var(--ease) both',
        'rd-scene-in 700ms var(--ease) both',
        'animation-delay: 70ms',
        'animation-delay: 140ms',
        'animation-delay: 160ms',
        'animation-delay: 210ms',
        'animation-delay: 100ms',
    ] as $needle) assertContains($needle, $css);

    $js = arc_site_js();
    assertNotContains('HERO_SETTLE', $js, 'Eski 1.4 s sekil bekleme kuyrugu kalmamali');
    assertNotContains('initHero()', $js, 'Eski sekil motoru baslatilmamali');
    assertNotContains('initParallax()', $js, 'Eski hero parallax motoru baslatilmamali');
});

test('A-04', 'Bölge grafiği scroll ilerlemesine göre çizilir', function (): void {
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

test('A-06', 'Sektör grubu sekme durumuna bağlı animasyon taşımaz', function (): void {
    $strip = (string) file_get_contents(ARC_ROOT . '/views/front/partials/strip.php');
    assertNotContains('data-strip', $strip);
    assertNotContains('strip__track', $strip);
    assertNotContains('animationPlayState', arc_site_js());
});

test('A-07', 'Pencere boyutlanmasında bölge çizgisi yeniden ölçülür', function (): void {
    $js = arc_site_js();
    assertContains("window.addEventListener('resize'", $js);
    assertContains('{ passive: true }', $js);
    assertContains('measure();', $js);
});

test('A-09', 'IntersectionObserver desteklenmiyorsa tüm ögeler görünür olur', function (): void {
    $js = arc_site_js();
    assertContains("!('IntersectionObserver' in window)", $js);
    assertContains('revealAll();', $js);
});

test('A-10', 'Sektörler statik ve gerçek bağlantı grubu olarak sunulur', function (): void {
    $strip = (string) file_get_contents(ARC_ROOT . '/views/front/partials/strip.php');
    assertContains('sector-links__grid', $strip);
    assertContains('sector-link', $strip);
    assertNotContains('strip__group', $strip);
    assertNotContains('data-strip', $strip);
    assertNotContains('strip-scroll', arc_home_css());
    assertNotContains('initStrip', arc_site_js());
});

test('A-P5-a', 'Yalnızca performanslı özellikler animasyon hedefidir', function (): void {
    foreach ([arc_site_css(), arc_home_css()] as $css) {
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
    }
});

test('A-P5-b', 'Scroll dinleyicileri passive ve rAF ile sınırlandırılmış', function (): void {
    $js = arc_site_js();
    assertContains("window.addEventListener('scroll', requestScrollFrame, { passive: true })", $js);
    assertContains('window.requestAnimationFrame(onScrollFrame)', $js);
    assertContains('if (ticking) return;', $js);
});

test('A-P5-c', 'R5 hareket belirteçleri ve görünürlük eşikleri sabittir', function (): void {
    assertContains('--ease: cubic-bezier(.16, 1, .3, 1)', arc_site_css());
    $home = arc_home_css();
    assertContains('transition-duration: 460ms', $home);
    assertContains('rd-hero-line 600ms', $home);
    assertContains('rd-scene-in 700ms', $home);

    $js = arc_site_js();
    assertContains('THRESHOLD = 0.15', $js);
    assertContains("ROOT_MARGIN = '0px 0px -8% 0px'", $js);
    assertContains('HEAD_STUCK_AT = 24', $js);
    assertNotContains('PARALLAX_LIMIT', $js);
});

test('A-P5-d', 'R5 ana sayfa sonsuz animasyon tanımlamaz', function (): void {
    assertNotContains('infinite', arc_home_css());
    assertNotContains('data-strip', (string) file_get_contents(ARC_ROOT . '/views/front/partials/strip.php'));
});
