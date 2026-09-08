<?php
/** R7 admin + system redesign contract. */
declare(strict_types=1);

function arc_r7_file(string $path): string
{
    return (string) file_get_contents(ARC_ROOT . '/' . $path);
}

test('F-R7-01', 'R7 panel stili foundation katmanindan sonra yuklenir', function (): void {
    foreach (['views/admin/layout.php', 'views/admin/login.php'] as $path) {
        $view = arc_r7_file($path);
        $foundation = strpos($view, "asset('css/admin-redesign.css')");
        $r7 = strpos($view, "asset('css/admin-r7.css')");
        assertTrue($foundation !== false && $r7 !== false && $r7 > $foundation, $path . ' admin-r7.css sirasi hatali');
        assertContains('admin--r7', $view, $path . ' R7 body sinifini tasimali');
    }
});

test('F-R7-02', 'R7 panel stili ana yonetim ekran ailelerini kapsar', function (): void {
    $css = arc_r7_file('public/assets/css/admin-r7.css');
    foreach ([
        '.admin--r7 .admin__side',
        '.admin--r7 .card--stat',
        '.admin--r7 .filters',
        '.admin--r7 .form__actions--sticky',
        '.admin--r7 .table-scroll',
        '.admin--r7 .tabs',
        '.admin--r7 .serp',
        '.admin--r7 .menu-row',
        '.admin--r7 .media-grid',
        '.admin--r7 .map-canvas',
        '.admin--r7 .hero-preview',
        '.admin--r7 .checkbox-grid',
        '.admin--r7 .restore-row',
        '.admin--r7 .switch-list__item',
        '@media (max-width: 58.75rem)',
        '@media (prefers-reduced-motion: reduce)',
    ] as $needle) {
        assertContains($needle, $css, 'Eksik R7 panel stili: ' . $needle);
    }
    assertNotContains('!important', $css, 'R7 panel stili !important kullanmamali');
});

test('F-R7-03', 'Kurulum ve tum hata sablonlari R7 sistem katmanini yukler', function (): void {
    foreach ([
        'views/front/install.php',
        'views/errors/403.php',
        'views/errors/404.php',
        'views/errors/419.php',
        'views/errors/500.php',
        'views/errors/503.php',
    ] as $path) {
        $view = arc_r7_file($path);
        assertContains("asset('css/system-r7.css')", $view, $path . ' system-r7.css yuklemeli');
        assertContains('system-r7', $view, $path . ' R7 sistem body sinifini tasimali');
    }
});

test('F-R7-04', 'R7 sistem stili kurulum hata mobil ve azaltmis hareket durumlarini kapsar', function (): void {
    $css = arc_r7_file('public/assets/css/system-r7.css');
    foreach ([
        'body.system-r7',
        '.system-r7 .system__card',
        '.system-r7 .system__steps',
        '.system-r7--error .system__code',
        '.system-r7--install .system__card',
        '@media (max-width: 40rem)',
        '@media (prefers-reduced-motion: reduce)',
    ] as $needle) {
        assertContains($needle, $css, 'Eksik R7 sistem stili: ' . $needle);
    }
    assertNotContains('!important', $css, 'R7 sistem stili !important kullanmamali');
});

test('F-R7-05', 'R7 tasarimi panel route ve guvenlik formlarini degistirmez', function (): void {
    $routes = arc_r7_file('config/routes.php');
    foreach ([
        "$panel . '/giris'",
        "$panel . '/sayfalar'",
        "$panel . '/medya'",
        "$panel . '/seo'",
        "$panel . '/ayarlar'",
        "$panel . '/yedekleme'",
        "$panel . '/yonlendirmeler'",
    ] as $needle) {
        assertContains($needle, $routes, 'R7 route sozlesmesi eksik: ' . $needle);
    }

    $layout = arc_r7_file('views/admin/layout.php');
    assertContains("admin_url('cikis')", $layout);
    assertContains('csrf_field()', $layout);

    $login = arc_r7_file('views/admin/login.php');
    assertContains("admin_url('giris')", $login);
    assertContains('csrf_field()', $login);
});

test('F-R7-06', 'Gercek Chromium panel kabul testi CI icinde calisir', function (): void {
    $ci = arc_r7_file('.github/workflows/ci.yml');
    $browser = arc_r7_file('tools/browser/admin-check.mjs');
    assertContains('node tools/browser/admin-check.mjs', $ci);
    assertContains('r7-browser@arcates.local', $ci, 'CI gecici admin hesabi olusturmali');
    foreach ([
        '/panel/giris',
        '/panel',
        '/panel/sayfalar',
        '/panel/sayfalar/yeni',
        '/panel/medya',
        '/panel/seo',
        '/panel/ayarlar',
        '/panel/yedekleme',
        '/panel/yonlendirmeler',
        '/install',
    ] as $route) {
        assertContains($route, $browser, 'Eksik R7 browser rotasi: ' . $route);
    }
});
