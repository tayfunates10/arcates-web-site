<?php
/** R6 inner page redesign contract. */
declare(strict_types=1);

function arc_r6_file(string $path): string
{
    return (string) file_get_contents(ARC_ROOT . '/' . $path);
}

test('F-R6-01', 'R6 ic sayfa stili ortak redesign katmanindan sonra yuklenir', function (): void {
    $head = arc_r6_file('views/front/partials/head.php');
    $foundation = strpos($head, "asset('css/redesign.css')");
    $inner = strpos($head, "asset('css/inner-redesign.css')");
    assertTrue($foundation !== false && $inner !== false && $inner > $foundation, 'inner-redesign.css redesign.css sonrasinda yuklenmeli');
});

test('F-R6-02', 'R6 stili tum ana ic sayfa ailelerini kapsar', function (): void {
    $css = arc_r6_file('public/assets/css/inner-redesign.css');
    foreach ([
        '.page-hero {',
        '.page-hero .quick-facts',
        '.content-main,',
        '.article-main,',
        '.project-card,',
        '.post-card {',
        '.project-meta {',
        '.gallery {',
        '.page-hero--contact + .section--contact',
        '@media (max-width: 58.75rem)',
        '@media (prefers-reduced-motion: reduce)',
    ] as $needle) {
        assertContains($needle, $css, 'Eksik R6 stil sozlesmesi: ' . $needle);
    }
    assertNotContains('!important', $css, 'R6 stili !important kullanmamali');
});

test('F-R6-03', 'Genel sayfalar ortak koyu hero ve ayri icerik yuzeyi kullanir', function (): void {
    $page = arc_r6_file('views/front/page.php');
    assertContains('page-hero page-hero--simple', $page);
    assertContains('section--page section--content', $page);
    assertSame(1, substr_count($page, '<h1'));
});

test('F-R6-04', 'Iletisim sayfasi kompakt R6 hero ile form onceligini korur', function (): void {
    $contact = arc_r6_file('views/front/contact.php');
    $hero = strpos($contact, 'page-hero page-hero--contact');
    $form = strpos($contact, "partial('front/partials/form'");
    $copy = strpos($contact, '<article class="contact-copy">');
    assertTrue($hero !== false && $form !== false && $copy !== false, 'R6 iletisim iskeleti eksik');
    assertTrue($form < $copy, 'Form DOM sirasinda aciklama metninden once kalmali');
    assertSame(1, substr_count($contact, '<h1'));
});

test('F-R6-05', 'Mevcut hizmet ilce sektor blog ve proje sablonlari R6 siniflariyla uyumludur', function (): void {
    foreach ([
        'views/front/service.php',
        'views/front/location.php',
        'views/front/sector.php',
        'views/front/posts.php',
        'views/front/post.php',
        'views/front/projects.php',
        'views/front/project.php',
    ] as $path) {
        $view = arc_r6_file($path);
        assertContains('page-hero', $view, $path . ' page-hero kullanmali');
        assertSame(1, substr_count($view, '<h1'), $path . ' tek H1 tasimali');
    }
});

test('F-R6-06', 'Gercek tarayici R6 ic sayfa matrisi CI icinde calisir', function (): void {
    $ci = arc_r6_file('.github/workflows/ci.yml');
    $browser = arc_r6_file('tools/browser/inner-pages-check.mjs');
    assertContains('node tools/browser/inner-pages-check.mjs', $ci);
    foreach ([
        '/web-tasarim',
        '/edremit-web-tasarim',
        '/otel-pansiyon-web-sitesi',
        '/blog',
        '/referanslar',
        '/hakkimizda',
        '/iletisim',
        '/referanslar/akcay-pansiyon-rezervasyon-sitesi',
        '/blog/yerel-aramada-gorunurluk-isletme-profili',
    ] as $route) {
        assertContains($route, $browser, 'Eksik R6 tarayici rotasi: ' . $route);
    }
});
