<?php
/**
 * Mobil panel cekmecesi dar ekran sozlesmesi.
 */
declare(strict_types=1);

test('F-R7-MOB-01', 'Mobil admin cekmecesi dar, ustten akan ve kaydirilabilir kalir', function (): void {
    $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/admin-r7-responsive.css');

    assertContains('inline-size: min(74vw, 272px)', $css, 'Mobil cekmece genisligi viewportu domine etmemeli');
    assertContains('block-size: 100dvh', $css, 'Mobil cekmece dinamik viewport yuksekligini kullanmali');
    assertContains('justify-content: flex-start', $css, 'Mobil cekmece icerigi ustten baslamali');
    assertContains('overflow-y: auto', $css, 'Uzun panel menusu dikey kaydirilabilir olmali');
    assertContains('overscroll-behavior: contain', $css, 'Cekmece kaydirmasi arka sayfaya zincirlenmemeli');
    assertContains('body.admin-nav-open', $css, 'Menu acikken arka sayfa kaymasi kilitlenmeli');
    assertContains('margin-top: 6px', $css, 'Alt baglanti menuyu dikey merkeze itmemeeli');
});
