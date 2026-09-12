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

test('F-SC-d', 'Metrik hücresi referansın ölçüsünde', function (): void {
    $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/reference-sections.css');

    // B3'te bu deger parity butcesi (56-72 px) yuzunden 20 px'te kalmisti.
    // Site sahibinin karariyla butce 70-88'e yukseltildi ve referansin
    // kendi olcusune (1440'ta ~26 px) cikildi.
    if (preg_match('~\.ref-metric strong\s*\{\s*font-size:\s*clamp\([^,]+,\s*([\d.]+)vw~', $css, $m) !== 1) {
        assertTrue(false, 'Metrik başlığı tanımlanmalı');
        return;
    }

    $at1440 = 14.4 * (float) $m[1];
    assertTrue($at1440 >= 25.0 && $at1440 <= 27.5, 'Metrik başlığı 1440 px\'te ~26 px olmalı');
});

test('F-SC-e', 'Hizmet kartı referansın ölçülerini taşır', function (): void {
    $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/reference-sections.css');

    // B3'te karta dokunulmamisti: butce 130-165 px idi ve referans kart
    // 190 px. Butce 165-215'e yukseltildikten sonra ikon, baslik, aciklama
    // ve ikon alti boslugu referans degerlerine getirildi.
    assertContains('.ref-home .ref-service__icon svg', $css, 'Kart ikonu boyutlandırılmalı');
    assertContains('.ref-home .ref-service h3', $css, 'Kart başlığı boyutlandırılmalı');
    assertContains('margin-block-end', $css, 'İkon altı boşluğu referansa getirilmeli');

    // Mobil dokunulmamali: referansin telefon maketinde hizmet karti yok.
    $desktop = substr($css, strpos($css, '/* --- Hizmet kartlari'));
    assertContains('@media (min-width: 941px)', $desktop, 'Kart ölçüleri masaüstüne sınırlı olmalı');
});

test('F-SC-f', 'Parity bütçeleri referans ölçüleriyle gerekçelendirilmiş', function (): void {
    $check = (string) file_get_contents(ARC_ROOT . '/tools/browser/reference-parity-check.mjs');

    // Dort butce referansin kendi olcusunden dardi ve site sahibinin
    // karariyla yukseltildi. Her birinin gerekcesi dosyada yazili olmali
    // ki ileride "neden bu aralik" sorusu olcume geri baglanabilsin.
    assertContains('inRange(state.wrap.width, 1200, 1250)', $check, 'Ray %85 oranına çekilmeli');
    assertContains('inRange(state.metric.height, 70, 88)', $check, 'Metrik hücresi bütçesi yükseltilmeli');
    assertContains('inRange(card.height, 165, 215)', $check, 'Hizmet kartı bütçesi yükseltilmeli');
    assertContains('inRange(card.height, 160, 200)', $check, 'Proje kartı bütçesi yükseltilmeli');
    assertContains('pageHeight < 2150', $check, 'Sayfa yüksekliği eşiği bölümlerin toplamına göre olmalı');
});
