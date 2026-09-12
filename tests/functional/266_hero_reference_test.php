<?php
/**
 * Kahraman bolumu — referans olculeri. Referans B2.
 *
 * Olculer `public/assets/css/reference-hero.css` basindaki yorumda; hepsi
 * referans gorselden piksel olarak ornekledi.  DOCS.md 5 (bolum 2)
 */

declare(strict_types=1);

use Arcates\Controllers\Front\HomeController;
use Arcates\Core\Lang;
use Arcates\Core\Request;

/** Anasayfa govdesini bir kez uretir. */
function arc_hr_body(): string
{
    static $body = null;
    if ($body === null) {
        Lang::use('tr');
        $body = (new HomeController())->index(Request::make('GET', '/'), [])->body();
    }
    return $body;
}

test('F-HR-a', 'Kahraman butonları satır içi SVG ok taşır', function (): void {
    arc_need_db();
    $body = arc_hr_body();

    assertContains('class="ref-btn__mark"', $body, 'Butonlarda ok işareti bulunmalı');

    // Referansta ikincil buton okunu daire icinde tasiyor.
    if (preg_match_all('~<svg class="ref-btn__mark".*?</svg>~s', $body, $m) < 2) {
        assertTrue(false, 'İki buton işareti de basılmalı');
        return;
    }
    assertContains('<circle', $m[0][1], 'İkincil butonun oku daire içinde olmalı');

    foreach ($m[0] as $svg) {
        assertContains('aria-hidden="true"', $svg, 'Ok dekoratif olmalı');
        assertContains('focusable="false"', $svg, 'Ok odak sırasına girmemeli');
    }
});

test('F-HR-b', 'Onay işaretleri çember değil düz tik', function (): void {
    arc_need_db();
    $body = arc_hr_body();

    if (preg_match('~<ul class="ref-trust".*?</ul>~s', $body, $m) !== 1) {
        assertTrue(false, 'Onay satırı basılmalı');
        return;
    }

    assertSame(3, substr_count($m[0], 'class="ref-trust__mark"'), 'Üç onay işareti olmalı');

    // Eski isaret cember icinde bir metin glifiydi ve kapali bir onay
    // kutusu gibi okunuyordu; referansta duz mavi bir tik var.
    assertNotContains('<span', $m[0], 'İşaret artık span değil SVG olmalı');
    assertNotContains('✓', $m[0], 'Fonta bağlı glif kullanılmamalı');
});

test('F-HR-c', 'Kahraman metni fonta bağlı ok glifi taşımaz', function (): void {
    arc_need_db();
    $body = arc_hr_body();

    if (preg_match('~<div class="ref-hero__actions">.*?</div>~s', $body, $m) !== 1) {
        assertTrue(false, 'Buton bloğu basılmalı');
        return;
    }

    // Glif fonta bagli: font yuklenmezse kutu olarak cizilir. CLAUDE.md 4
    assertNotContains('→', $m[0], 'Metin oku glifi kalmamalı');
    assertNotContains('↗', $m[0], 'Köşe oku glifi kalmamalı');
});

test('F-HR-d', 'Başlık satırları sıkışık değil', function (): void {
    $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/reference-hero.css');

    if (preg_match('~\.ref-hero__title\s*\{\s*line-height:\s*([\d.]+)~', $css, $m) !== 1) {
        assertTrue(false, 'Başlık satır yüksekliği tanımlanmalı');
        return;
    }

    // Onceki deger 1.01 idi: satirlar neredeyse birbirine degiyordu.
    // Referansin olculen orani 1.30; ucuncu satirimiz yuzunden 1.16'dayiz.
    assertTrue((float) $m[1] >= 1.1, 'Satır yüksekliği 1.1 altına düşmemeli');
});

test('F-HR-e', 'Birincil buton düz ölçülen maviyi kullanır', function (): void {
    $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/reference-hero.css');

    // Referansta gradyan yok; zemin duz #0E77FE olctu — ust menudeki cagri
    // butonuyla ayni mavi.
    assertContains('background: #0E77FE', $css, 'Ölçülen düz mavi kullanılmalı');

    if (preg_match('~\.ref-btn--primary\s*\{[^}]*\}~s', $css, $m) === 1) {
        assertNotContains('linear-gradient', $m[0], 'Birincil butonda gradyan olmamalı');
    }
});

test('F-HR-f', 'Kahraman stili sayfa stillerinin sonunda yüklenir', function (): void {
    $src = (string) file_get_contents(ARC_ROOT . '/app/Controllers/Front/HomeController.php');

    if (preg_match("~'styles'\s*=>\s*\[(.*?)\]~s", $src, $m) !== 1) {
        assertTrue(false, 'Stil listesi bulunmalı');
        return;
    }

    $parity = strrpos($m[1], 'reference-parity');
    $hero   = strpos($m[1], 'css/reference-hero.css');

    assertTrue($hero !== false, 'Kahraman stili listede olmalı');
    assertTrue($parity !== false && $hero > $parity, 'Kahraman stili parity dosyalarından sonra gelmeli');
});
