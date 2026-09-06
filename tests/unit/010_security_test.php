<?php
/**
 * Security sinifi birim testleri.  DOCS.md 14.2 (U-01, U-02, U-03), 10.2, 10.6
 */

declare(strict_types=1);

use Arcates\Core\Security;

test('U-01', "Security::slug('Canakkale Yolu') Turkce karakteri cevirir", function (): void {
    assertSame('canakkale-yolu', Security::slug('Çanakkale Yolu'));
});

test('U-02', "Security::slug('Izmir SS G') buyuk Turkce harfleri cevirir", function (): void {
    assertSame('izmir-ss-g', Security::slug('İzmir ŞŞ Ğ'));
});

test('U-03', "Security::e('<b>') HTML kacirir", function (): void {
    assertSame('&lt;b&gt;', Security::e('<b>'));
    assertSame('&quot;', Security::e('"'));
    assertSame('&apos;', Security::e("'"), 'ENT_HTML5 kesme isaretini &apos; olarak kacirir');
    assertSame('', Security::e(null));
});

test('U-03b', 'Slug ureticisi kenar durumlari', function (): void {
    assertSame('edremit-web-tasarim', Security::slug('  Edremit   Web  Tasarım  '));
    assertSame('zeytinyagi-e-ticaret', Security::slug('Zeytinyağı & E-Ticaret'));
    assertSame('gomec-2024', Security::slug('Gömeç --- 2024!!!'));
    assertSame('', Security::slug('---'));
    assertSame('otel-pansiyon', Security::slug('Otel / Pansiyon'));
});

test('U-03c', 'Slug temizlik kontrolu Turkce karakter ve bosluk yakalar', function (): void {
    assertTrue(Security::isCleanSlug('edremit-web-tasarim'));
    assertFalse(Security::isCleanSlug('edremit web tasarim'), 'Bosluk reddedilmeli');
    assertFalse(Security::isCleanSlug('edremit-tasarım'), 'Turkce karakter reddedilmeli');
    assertFalse(Security::isCleanSlug('-edremit'), 'Bastaki tire reddedilmeli');
    assertFalse(Security::isCleanSlug(''), 'Bos slug reddedilmeli');
});

test('U-03d', 'JSON ciktisi HTML baglaminda guvenli', function (): void {
    $json = Security::json(['x' => '<script>alert(1)</script>', 'y' => "O'Brien & Co"]);
    assertNotContains('<script>', $json, 'Etiket ham gecmemeli');
    assertContains('\\u003Cscript', $json, 'Kucuktur isareti onaltilik kacirilmali');
    assertContains('\\u0026', $json, 'Ampersan onaltilik kacirilmali');
    assertContains('\\u0027', $json, 'Kesme isareti onaltilik kacirilmali');
});

test('U-03e', 'Zengin metin temizligi izin verilmeyen etiketleri dusurur', function (): void {
    $dirty = '<p>Merhaba <strong>dunya</strong></p>'
        . '<script>alert(1)</script>'
        . '<iframe src="https://kotu.example"></iframe>'
        . '<a href="javascript:alert(1)">tikla</a>'
        . '<a href="/iletisim" onclick="alert(1)">iletisim</a>';

    $clean = Security::sanitizeHtml($dirty);

    assertContains('<strong>dunya</strong>', $clean, 'Izinli etiket korunmali');
    assertNotContains('<script', $clean, 'script dusurulmeli');
    assertNotContains('<iframe', $clean, 'iframe dusurulmeli');
    assertNotContains('javascript:', $clean, 'javascript: semasi dusurulmeli');
    assertNotContains('onclick', $clean, 'Olay nitelikleri dusurulmeli');
    assertContains('href="/iletisim"', $clean, 'Guvenli baglanti korunmali');
});

test('U-03f', 'Kelime sayimi HTML etiketlerini saymaz', function (): void {
    $html = '<h2>Baslik</h2><p>Bir iki uc dort bes</p>';
    assertSame(6, Security::wordCount($html));
    assertSame(0, Security::wordCount('<p></p>'));
});

test('U-03g', 'Dosya adi kullanicidan gelmez', function (): void {
    $name = Security::randomFilename('jpg');
    assertTrue((bool) preg_match('/^[0-9a-f]{16}\.jpg$/', $name), 'Ad rastgele onaltilik olmali: ' . $name);
    assertNotSame($name, Security::randomFilename('jpg'), 'Her cagri farkli ad uretmeli');
});

test('U-03h', 'IP paketleme ve cozme dongusu', function (): void {
    assertSame('203.0.113.10', Security::unpackIp(Security::packIp('203.0.113.10')));
    assertSame('2001:db8::1', Security::unpackIp(Security::packIp('2001:db8::1')));
    assertSame(null, Security::packIp(''));
    assertSame(null, Security::packIp('gecersiz'));
});

test('U-03i', 'Guvenli URL kontrolu', function (): void {
    assertSame('/iletisim', Security::url('/iletisim'));
    assertSame('https://arcates.com', Security::url('https://arcates.com'));
    assertSame('', Security::url('javascript:alert(1)'), 'javascript: reddedilmeli');
    assertSame('', Security::url('data:text/html,<script>'), 'data: reddedilmeli');
});
