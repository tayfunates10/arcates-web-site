<?php
/**
 * Bolum basliklari ve metrik bandi — referans olculeri. Referans B3.
 *
 * Olculer `public/assets/css/reference-sections.css` basindaki yorumda.
 * DOCS.md 5 (bolum 3)
 */

declare(strict_types=1);

test('F-SC-a', 'Bölüm stili sayfa stillerinin sonunda yüklenir', function (): void {
    $src = (string) file_get_contents(ARC_ROOT . '/app/Controllers/Front/HomeController.php');

    if (preg_match("~'styles'\s*=>\s*\[(.*?)\]~s", $src, $m) !== 1) {
        assertTrue(false, 'Stil listesi bulunmalı');
        return;
    }

    $parity   = strrpos($m[1], 'reference-parity');
    $sections = strpos($m[1], 'css/reference-sections.css');

    assertTrue($sections !== false, 'Bölüm stili listede olmalı');
    assertTrue($parity !== false && $sections > $parity, 'Bölüm stili parity dosyalarından sonra gelmeli');
});

test('F-SC-b', 'Başlık ve alt yazı yalnızca bağlantılı bölümlerde aynı satırda', function (): void {
    $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/reference-sections.css');

    // Referansta sag tarafinda baglanti olan bolumlerde ("Hizmetlerimiz",
    // "One Cikan Projeler") baslik ve alt yazi tek satirda; baglantisi
    // olmayanlarda ("Nasil Calisiyoruz?") alt alta. Ayrim `--local`.
    assertContains(':not(.ref-section__head--local)', $css, 'Kural yerel başlıkları dışarıda bırakmalı');
    assertContains('align-items: baseline', $css, 'Başlık ve alt yazı taban hizasında olmalı');

    // Ortalama yalnizca masaustunde; dar ekranda satir sigmaz.
    assertContains('@media (min-width: 941px)', $css, 'Kural masaüstüne sınırlı olmalı');
});

test('F-SC-c', 'Bağlantılı bölüm başlığı ile alt yazısı aynı satırda basılır', function (): void {
    arc_need_db();
    Arcates\Core\Lang::use('tr');

    $body = (new Arcates\Controllers\Front\HomeController())
        ->index(Arcates\Core\Request::make('GET', '/'), [])->body();

    // Ikisi ayni sarmalayicida olmali ki yan yana dizilebilsinler.
    if (preg_match('~<header class="ref-section__head">\s*<div>(.*?)</div>~s', $body, $m) !== 1) {
        assertTrue(false, 'Bağlantılı bölüm başlığı basılmalı');
        return;
    }

    assertContains('<h2>', $m[1], 'Başlık sarmalayıcıda olmalı');
    assertContains('<p>', $m[1], 'Alt yazı aynı sarmalayıcıda olmalı');
});

test('F-SC-d', 'Metrik hücresi parity bütçesinin içinde kalır', function (): void {
    $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/reference-sections.css');

    // Referansta hucre basligi 1440'ta ~26 px, ama hucre o zaman ~77 px
    // oluyor ve `reference-parity-check`in 56-72 px butcesini asiyor.
    // Ust sinir bilerek 26'nin altinda: butce once yukseltilmeli.
    if (preg_match('~\.ref-metric strong\s*\{\s*font-size:\s*clamp\([^,]+,\s*([\d.]+)vw~', $css, $m) !== 1) {
        assertTrue(false, 'Metrik başlığı tanımlanmalı');
        return;
    }

    // 1440 px'te vw degeri: 14.4 * vw
    $at1440 = 14.4 * (float) $m[1];
    assertTrue($at1440 > 15.0, 'Metrik başlığı eski 15 px değerinden büyük olmalı');
    assertTrue($at1440 < 24.0, 'Metrik başlığı hücre bütçesini aşacak kadar büyümemeli');
});

test('F-SC-e', 'Hizmet kartı ölçüleri bu bölümde değiştirilmedi', function (): void {
    $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/reference-sections.css');

    // Referans kart 190 px, butce 130-165 px. Ikonu buyutmek bile CI'da
    // butceye 6 px birakiyordu ve bu makinede kart yuksekligi yerelde
    // dogrulanamiyor. Kart bilerek oldugu gibi birakildi; degisiklik
    // once butcenin yukseltilmesini gerektirir.
    assertNotContains('.ref-service__icon svg {', $css, 'Kart ikonu bu katmanda boyutlandırılmamalı');
    assertContains('Hizmet kartlari', $css, 'Kararın gerekçesi dosyada yazılı olmalı');
});
