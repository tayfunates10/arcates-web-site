<?php
/**
 * Security sinifi birim testleri.  DOCS.md 14.2 (U-01, U-02, U-03), 10.2, 10.6
 */

declare(strict_types=1);

use Arcates\Core\Security;

test('U-01', "Security::slug('Çanakkale Yolu') Türkçe karakteri çevirir", function (): void {
    assertSame('canakkale-yolu', Security::slug('Çanakkale Yolu'));
});

test('U-02', "Security::slug('İzmir ŞŞ Ğ') büyük Türkçe harfleri çevirir", function (): void {
    assertSame('izmir-ss-g', Security::slug('İzmir ŞŞ Ğ'));
});

test('U-03', "Security::e('<b>') HTML kaçırır", function (): void {
    assertSame('&lt;b&gt;', Security::e('<b>'));
    assertSame('&quot;', Security::e('"'));
    assertSame('&apos;', Security::e("'"), 'ENT_HTML5 kesme işaretini &apos; olarak kaçırır');
    assertSame('', Security::e(null));
});

test('U-03b', 'Slug üreticisi kenar durumları', function (): void {
    assertSame('edremit-web-tasarim', Security::slug('  Edremit   Web  Tasarım  '));
    assertSame('zeytinyagi-e-ticaret', Security::slug('Zeytinyağı & E-Ticaret'));
    assertSame('gomec-2024', Security::slug('Gömeç --- 2024!!!'));
    assertSame('', Security::slug('---'));
    assertSame('otel-pansiyon', Security::slug('Otel / Pansiyon'));
});

test('U-03c', 'Slug temizlik kontrolü Türkçe karakter ve boşluk yakalar', function (): void {
    assertTrue(Security::isCleanSlug('edremit-web-tasarim'));
    assertFalse(Security::isCleanSlug('edremit web tasarım'), 'Boşluk reddedilmeli');
    assertFalse(Security::isCleanSlug('edremit-tasarım'), 'Türkçe karakter reddedilmeli');
    assertFalse(Security::isCleanSlug('-edremit'), 'Baştaki tire reddedilmeli');
    assertFalse(Security::isCleanSlug(''), 'Boş slug reddedilmeli');
});

test('U-03d', 'JSON çıktısı HTML bağlamında güvenli', function (): void {
    $json = Security::json(['x' => '<script>alert(1)</script>', 'y' => "O'Brien & Co"]);
    assertNotContains('<script>', $json, 'Etiket ham geçmemeli');
    assertContains('\\u003Cscript', $json, 'Küçüktür işareti onaltılık kaçırılmalı');
    assertContains('\\u0026', $json, 'Ampersan onaltılık kaçırılmalı');
    assertContains('\\u0027', $json, 'Kesme işareti onaltılık kaçırılmalı');
});

test('U-03e', 'Zengin metin temizliği izin verilmeyen etiketleri düşürür', function (): void {
    $dirty = '<p>Merhaba <strong>dünya</strong></p>'
        . '<script>alert(1)</script>'
        . '<iframe src="https://kotu.example"></iframe>'
        . '<a href="javascript:alert(1)">tıkla</a>'
        . '<a href="/iletisim" onclick="alert(1)">iletişim</a>';

    $clean = Security::sanitizeHtml($dirty);

    assertContains('<strong>dünya</strong>', $clean, 'İzinli etiket korunmalı');
    assertNotContains('<script', $clean, 'script düşürülmeli');
    assertNotContains('<iframe', $clean, 'iframe düşürülmeli');
    assertNotContains('javascript:', $clean, 'javascript: şeması düşürülmeli');
    assertNotContains('onclick', $clean, 'Olay nitelikleri düşürülmeli');
    assertContains('href="/iletisim"', $clean, 'Güvenli bağlantı korunmalı');
});

test('U-03f', 'Kelime sayımı HTML etiketlerini saymaz', function (): void {
    $html = '<h2>Başlık</h2><p>Bir iki üç dört beş</p>';
    assertSame(6, Security::wordCount($html));
    assertSame(0, Security::wordCount('<p></p>'));
});

test('U-03g', 'Dosya adı kullanıcıdan gelmez', function (): void {
    $name = Security::randomFilename('jpg');
    assertTrue((bool) preg_match('/^[0-9a-f]{16}\.jpg$/', $name), 'Ad rastgele onaltılık olmalı: ' . $name);
    assertNotSame($name, Security::randomFilename('jpg'), 'Her çağrı farklı ad üretmeli');
});

test('U-03h', 'IP paketleme ve çözme döngüsü', function (): void {
    assertSame('203.0.113.10', Security::unpackIp(Security::packIp('203.0.113.10')));
    assertSame('2001:db8::1', Security::unpackIp(Security::packIp('2001:db8::1')));
    assertSame(null, Security::packIp(''));
    assertSame(null, Security::packIp('gecersiz'));
});

test('U-03i', 'Güvenli URL kontrolü', function (): void {
    assertSame('/iletisim', Security::url('/iletisim'));
    assertSame('https://arcates.com', Security::url('https://arcates.com'));
    assertSame('', Security::url('javascript:alert(1)'), 'javascript: reddedilmeli');
    assertSame('', Security::url('data:text/html,<script>'), 'data: reddedilmeli');
});
