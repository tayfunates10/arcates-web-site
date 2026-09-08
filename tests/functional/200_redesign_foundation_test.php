<?php
/** Full redesign R0/R1/R4 foundation contract. */
declare(strict_types=1);

function arc_redesign_file(string $path): string
{
    return (string) file_get_contents(ARC_ROOT . '/' . $path);
}

test('F-RD-01', 'R0 uygulama envanteri kaynak surumu ve fazlari kaydeder', function (): void {
    $inventory = arc_redesign_file('REDESIGN-R0-INVENTORY.md');
    assertContains('b96b112d4c0b49be1f682eace598b14bd28e01f5', $inventory);
    assertContains('R0/R1/R4 foundation', $inventory);
    assertContains('R8 kabul', $inventory);
    assertContains('public/uploads', $inventory);
});

test('F-RD-02', 'On yuz yeni anlamsal renk ve kontrol rollerini yukler', function (): void {
    $css = arc_redesign_file('public/assets/css/redesign.css');
    foreach ([
        '--rd-dark: #081426',
        '--rd-dark-raised: #10233D',
        '--rd-brand: #0B4FA8',
        '--rd-brand-bright: #1C7BF2',
        '--rd-brand-soft: #7FB6FF',
        '--rd-surface-soft: #F7FAFF',
        '--rd-text: #062244',
        '--rd-text-muted: #4A6588',
        '--rd-control-radius: 14px',
        '--rd-surface-radius: 22px',
        '--rd-control-min: 48px',
    ] as $needle) {
        assertContains($needle, $css, 'Eksik redesign rolu: ' . $needle);
    }
    assertContains('outline: 3px solid var(--rd-focus)', $css);
    assertContains("background: url('../img/logo-mark-light.png')", $css);
});

test('F-RD-03', 'Redesign stili site css sonrasinda yuklenir', function (): void {
    $head = arc_redesign_file('views/front/partials/head.php');
    $site = strpos($head, "asset('css/site.css')");
    $redesign = strpos($head, "asset('css/redesign.css')");
    assertTrue($site !== false && $redesign !== false && $redesign > $site, 'redesign.css site.css sonrasinda yuklenmeli');
});

test('F-RD-04', 'Mobil menu acik kapali durumunun erisilebilir adi vardir', function (): void {
    $header = arc_redesign_file('views/front/partials/header.php');
    $js = arc_redesign_file('public/assets/js/redesign.js');
    assertContains('data-open-label', $header);
    assertContains('data-close-label', $header);
    assertContains("toggle.setAttribute('aria-label', label)", $js);
    assertContains("event.key === 'Escape'", $js);
});

test('F-RD-05', 'Teklif formu sunucu hatalarini alanlarla programatik baglar', function (): void {
    $form = arc_redesign_file('views/front/partials/form.php');
    assertContains('data-error-summary', $form);
    assertContains('aria-invalid="true" aria-describedby="form_name_error"', $form);
    assertContains('id="form_name_error"', $form);
    assertContains('aria-invalid="true" aria-describedby="form_phone_error"', $form);
    assertContains('aria-invalid="true" aria-describedby="form_email_error"', $form);
    assertContains('aria-invalid="true" aria-describedby="form_message_error"', $form);
    assertContains('aria-invalid="true" aria-describedby="form_kvkk_error"', $form);
    assertContains('inputmode="tel"', $form);
});

test('F-RD-06', 'Sunucu hata ozeti yuklenince odaklanabilir', function (): void {
    $js = arc_redesign_file('public/assets/js/redesign.js');
    assertContains("doc.querySelector('[data-error-summary]')", $js);
    assertContains("summary.focus({ preventScroll: true })", $js);
});

test('F-RD-07', 'Panel yeni ortak yuzey ve kontrol ailesini yukler', function (): void {
    $css = arc_redesign_file('public/assets/css/admin-redesign.css');
    foreach ([
        '--adm-dark: #081426',
        '--adm-dark-raised: #10233D',
        '--adm-brand: #0B4FA8',
        '--adm-radius-control: 14px',
        '--adm-radius-surface: 22px',
        'min-block-size: 44px',
    ] as $needle) {
        assertContains($needle, $css, 'Eksik panel redesign rolu: ' . $needle);
    }
    assertContains('@media (max-width: 58.75rem)', $css);
    assertContains('.admin__side.is-open', $css);
});

test('F-RD-08', 'Panel mobil navigasyonu Escape ve odak geri donusu destekler', function (): void {
    $layout = arc_redesign_file('views/admin/layout.php');
    $js = arc_redesign_file('public/assets/js/admin-redesign.js');
    assertContains('data-admin-nav-toggle', $layout);
    assertContains('data-admin-side', $layout);
    assertContains('data-admin-nav-backdrop', $layout);
    assertContains("asset('css/admin-redesign.css')", $layout);
    assertContains("asset('js/admin-redesign.js')", $layout);
    assertContains("event.key !== 'Escape'", $js);
    assertContains("toggle.focus()", $js);
});

test('F-RD-09', 'Panel giris ekrani ayni redesign stilini kullanir', function (): void {
    $login = arc_redesign_file('views/admin/login.php');
    assertContains("asset('css/admin.css')", $login);
    assertContains("asset('css/admin-redesign.css')", $login);
});

test('F-RD-10', 'Foundation ek varliklari hafif kalir', function (): void {
    $paths = [
        'public/assets/css/redesign.css' => 30000,
        'public/assets/css/admin-redesign.css' => 30000,
        'public/assets/js/redesign.js' => 12000,
        'public/assets/js/admin-redesign.js' => 12000,
    ];
    foreach ($paths as $path => $limit) {
        $size = filesize(ARC_ROOT . '/' . $path);
        assertTrue($size !== false && $size <= $limit, $path . ' butceyi asmamali: ' . (string) $size);
    }
});
