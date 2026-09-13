<?php
/**
 * Cagri bandi slogani ve el yazisi imza. Referans B9.
 *
 * Iki metin de sablonda sabit degil, panelden gelir (CLAUDE.md 8): slogan
 * `cta` bolumunun `slogan` alanindan, imza `footer` bolumunun `signature`
 * alanindan. Bos birakilinca ilgili oge hic cizilmez.
 * DOCS.md 5 (bolum 6), 8.6
 */

declare(strict_types=1);

use Arcates\Core\Lang;
use Arcates\Core\Request;
use Arcates\Models\HomeSection;
use Arcates\Controllers\Front\HomeController;

/** Anasayfa govdesini uretir. Onbellege alinmaz: testler icerigi degistiriyor. */
function arc_fl_body(): string
{
    Lang::use('tr');
    return (new HomeController())->index(Request::make('GET', '/'), [])->body();
}

/** Bir bolumun Turkce icerigini degistirip eski halini geri veren yardimci. */
function arc_fl_set(string $key, string $alan, string $deger): array
{
    $eski = HomeSection::content($key, 'tr');
    $yeni = $eski;
    $yeni[$alan] = $deger;
    HomeSection::saveContent($key, 'tr', $yeni);
    return $eski;
}

test('F-FL-a', 'Slogan panelden gelir ve her satırı ayrı span olur', function (): void {
    arc_need_db();
    $eski = arc_fl_set('cta', 'slogan', "BIRINCI\nIKINCI\nUCUNCU");

    try {
        $body = arc_fl_body();
        assertContains('ref-final-cta__slogan', $body, 'Slogan bloğu çizilmeli');
        assertContains('<span>BIRINCI</span><span>IKINCI</span><span>UCUNCU</span>', $body, 'Her satır ayrı span olmalı');
    } finally {
        HomeSection::saveContent('cta', 'tr', $eski);
    }
});

test('F-FL-b', 'Slogan boşken blok da üç sütun sınıfı da çizilmez', function (): void {
    arc_need_db();
    $eski = arc_fl_set('cta', 'slogan', '');

    try {
        $body = arc_fl_body();
        assertNotContains('ref-final-cta__slogan', $body, 'Boş sloganda blok çizilmemeli');
        assertNotContains('ref-final-cta__card--slogan', $body, 'Boş sloganda üç sütunlu düzen açılmamalı');
        // Bant kendisi yerinde kalmali; yalnizca slogan dusmeli.
        assertContains('ref-final-cta__card', $body, 'Çağrı bandı yerinde kalmalı');
    } finally {
        HomeSection::saveContent('cta', 'tr', $eski);
    }
});

test('F-FL-c', 'Slogandaki boş satırlar atılır, kenar boşlukları kırpılır', function (): void {
    arc_need_db();
    $eski = arc_fl_set('cta', 'slogan', "  BIR  \n\n\r\n  IKI\n   ");

    try {
        $body = arc_fl_body();
        assertContains('<span>BIR</span><span>IKI</span>', $body, 'Boş satırlar atılıp kenarlar kırpılmalı');
        assertNotContains('<span></span>', $body, 'Boş span kalmamalı');
    } finally {
        HomeSection::saveContent('cta', 'tr', $eski);
    }
});

test('F-FL-d', 'Slogan HTML olarak değil metin olarak basılır', function (): void {
    arc_need_db();
    $eski = arc_fl_set('cta', 'slogan', '<b>KOD</b>');

    try {
        $body = arc_fl_body();
        assertNotContains('<span><b>KOD</b></span>', $body, 'Panel metni HTML olarak yorumlanmamalı');
        assertContains('&lt;b&gt;KOD&lt;/b&gt;', $body, 'Panel metni kaçışlanarak basılmalı');
    } finally {
        HomeSection::saveContent('cta', 'tr', $eski);
    }
});

test('F-FL-e', 'El yazısı not panelden gelir, boşken çizilmez', function (): void {
    arc_need_db();
    $eski = HomeSection::content('footer', 'tr');

    try {
        $yeni = $eski;
        $yeni['signature'] = 'Deneme notu';
        HomeSection::saveContent('footer', 'tr', $yeni);
        $body = arc_fl_body();
        assertContains('class="ref-about__scribble">Deneme notu</p>', $body, 'Not ekip görselinin üzerine çizilmeli');

        $yeni['signature'] = '';
        HomeSection::saveContent('footer', 'tr', $yeni);
        $body = arc_fl_body();
        assertNotContains('ref-about__scribble', $body, 'Boş notta öge çizilmemeli');
    } finally {
        HomeSection::saveContent('footer', 'tr', $eski);
    }
});

test('F-FL-f', 'El yazısı için Caveat yüklenir ve son katman stili bağlanır', function (): void {
    arc_need_db();
    $body = arc_fl_body();

    // Tek izinli dis kaynak Google Fonts; Caveat ayni css2 istegine eklendi,
    // yeni bir alan adi veya script girmedi. CLAUDE.md 4
    assertContains('family=Caveat:wght@500;600', $body, 'Caveat aynı font isteğine eklenmeli');
    assertSame(1, substr_count($body, 'fonts.googleapis.com/css2'), 'Google Fonts isteği tek olmalı');
    assertContains('css/reference-flourish.css', $body, 'Son katman stili bağlanmalı');
});

test('F-FL-g', 'Stil dosyası ölçülen değerleri ve hareket kurallarını taşır', function (): void {
    $css = (string) file_get_contents(dirname(__DIR__, 2) . '/public/assets/css/reference-flourish.css');

    // Olculen renk (en parlak piksel) ve punto. B9 olcumu.
    assertContains('#3bcbff', $css, 'Slogan rengi ölçülen değer olmalı');
    assertContains('1.21vw', $css, 'Slogan puntosu ölçülen değerden gelmeli');

    // Uc sutunlu duzen yalnizca slogan varken acilir; aksi halde bandin
    // sagina bos bir sutun ve bosluk ekleniyordu.
    assertContains('.ref-final-cta__card--slogan', $css, 'Üç sütunlu düzen slogan sınıfına bağlı olmalı');

    // Imza yuzdelerle konumlanir; mutlak deger gorselin disina tasardi.
    assertContains('inset-inline-end: 55.5%', $css, 'İmza görsele göre oranla konumlanmalı');

    // DOCS 7.1: yeni hareket eklenmedi; gecis veya animasyon olmamali.
    assertNotContains('@keyframes', $css, 'Bu katman animasyon tanımlamamalı');
    assertNotContains('transition:', $css, 'Bu katman geçiş tanımlamamalı');
});
