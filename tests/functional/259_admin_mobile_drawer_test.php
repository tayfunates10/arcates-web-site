<?php
/**
 * Mobil panel cekmecesi dar ekran sozlesmesi.
 */
declare(strict_types=1);

test('F-R7-MOB-01', 'Mobil admin cekmecesi kesin grid ile ustten akar ve yalniz menu kayar', function (): void {
    $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/admin-r7-responsive.css');
    $js  = (string) file_get_contents(ARC_ROOT . '/public/assets/js/admin-redesign.js');

    assertContains('display: grid', $css, 'Mobil cekmece esnek kolon yerine deterministik grid olmali');
    assertContains('grid-template-rows: auto minmax(0, 1fr) auto', $css, 'Logo/menu/alt baglanti satirlari acik tanimlanmali');
    assertContains('inline-size: min(74vw, 272px)', $css, 'Mobil cekmece genisligi viewportu domine etmemeli');
    assertContains('block-size: 100dvh', $css, 'Mobil cekmece dinamik viewport yuksekligini kullanmali');
    assertContains('overflow: hidden', $css, 'Cekmecenin tamaminda rastgele dikey kayma olmamali');
    assertContains('overflow-y: auto', $css, 'Yalniz uzun panel menusu dikey kaydirilabilir olmali');
    assertContains('justify-content: flex-start', $css, 'Marka yatayda baslangica sabitlenmeli');
    assertContains('align-self: end', $css, 'Alt baglanti son grid satirinda kalmali');
    assertContains('body.admin-nav-open', $css, 'Menu acikken arka sayfa kaymasi kilitlenmeli');
    assertContains('resetDrawerScroll', $js, 'Drawer acilirken eski kaydirma konumu sifirlanmali');
    assertContains('nav.scrollTop = 0', $js, 'Menu kaydirma konumu acilista sifirlanmali');
});
