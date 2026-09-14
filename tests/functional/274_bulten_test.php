<?php
/**
 * Bulten aboneligi — cift onay, cikis ve panel disa aktarimi.
 * DOCS.md 12 — testler F-BL-a…h
 *
 * Cift onayin sozlesmesi: adres girildiginde kayit `pending` olur ve hicbir
 * ileti listesine girmez; `active` yalnizca ziyaretci onay baglantisina
 * tikladiginda olusur. Cikis kaydi silmez.
 */

declare(strict_types=1);

use Arcates\Controllers\Front\NewsletterController;
use Arcates\Core\Lang;
use Arcates\Core\Mailer;
use Arcates\Core\Request;
use Arcates\Core\Security;
use Arcates\Core\Session;
use Arcates\Models\Newsletter;

/** Gecerli bir bulten gonderimi. */
function arc_bl_post(array $overrides = []): array
{
    return array_merge([
        '_token'  => Security::csrfToken(),
        '_return' => '/',
        'email'   => 'abone@ornek.test',
        'kvkk'    => '1',
    ], $overrides);
}

function arc_bl_subscribe(array $post, string $ip = '198.51.100.44'): Arcates\Core\Response
{
    Lang::use('tr');
    return (new NewsletterController())->subscribe(
        Request::make('POST', '/bulten', $post, [], ['REMOTE_ADDR' => $ip, 'HTTP_USER_AGENT' => 'ArcatesTest/1.0'])
    );
}

/** Tabloyu bosaltir; her test kendi durumunu kurar. */
function arc_bl_reset(): void
{
    arc_need_db();
    Arcates\Core\Database::instance()->run('DELETE FROM newsletter_subscribers');
    Session::takeFlash();
}

test('F-BL-a', 'Kayıt önce onay bekler ve adrese onay bağlantısı gider', function (): void {
    arc_bl_reset();
    Mailer::capture();

    arc_bl_subscribe(arc_bl_post());

    $satir = Arcates\Core\Database::instance()->first(
        'SELECT * FROM newsletter_subscribers WHERE email = :e',
        [':e' => 'abone@ornek.test']
    );

    assertTrue($satir !== null, 'Kayıt oluşmalı');
    assertSame('pending', (string) $satir['status'], 'Kayıt onay beklemeli, etkin olmamalı');
    assertTrue($satir['confirmed_at'] === null, 'Onay zamanı henüz dolmamalı');

    // Onayin kaniti saklanmali: KVKK/IYS icin zaman ve kaynak gerekir.
    assertTrue(!empty($satir['consent_at']), 'Onay zamanı yazılmalı');
    assertTrue(!empty($satir['consent_ip']), 'Onay IP’si yazılmalı');

    $mesajlar = Mailer::sentMessages();
    assertCount(1, $mesajlar, 'Tek onay e-postası gitmeli');
    assertContains($satir['token'], $mesajlar[0]['body'] ?? '', 'E-posta onay bağlantısını taşımalı');
    Mailer::capture(false);
});

test('F-BL-b', 'Onay bağlantısı aboneliği başlatır, ikinci tık hata değildir', function (): void {
    arc_bl_reset();
    $sonuc = Newsletter::subscribe('abone@ornek.test', 'tr', '198.51.100.44', '/');

    assertTrue(Newsletter::confirm($sonuc['token']), 'Onay başarılı olmalı');
    $satir = Newsletter::byToken($sonuc['token']);
    assertSame('active', (string) $satir['status'], 'Kayıt etkin olmalı');

    // Ziyaretci baglantiya iki kez tiklayabilir; bu bir hata degildir.
    assertTrue(Newsletter::confirm($sonuc['token']), 'İkinci tık da başarılı dönmeli');
    assertSame('active', (string) Newsletter::byToken($sonuc['token'])['status'], 'Durum bozulmamalı');
});

test('F-BL-c', 'Çıkış kaydı silmez, onayın geri alındığını yazar', function (): void {
    arc_bl_reset();
    $sonuc = Newsletter::subscribe('abone@ornek.test', 'tr', '198.51.100.44', '/');
    Newsletter::confirm($sonuc['token']);

    assertTrue(Newsletter::unsubscribe($sonuc['token']), 'Çıkış başarılı olmalı');

    $satir = Newsletter::byToken($sonuc['token']);
    assertTrue($satir !== null, 'Kayıt SİLİNMEMELİ: onayın geri alındığının kanıtıdır');
    assertSame('unsubscribed', (string) $satir['status'], 'Durum çıkış olmalı');
    assertTrue($satir['unsubscribed_at'] !== null, 'Çıkış zamanı yazılmalı');
});

test('F-BL-d', 'Geçersiz anahtar hiçbir kaydı değiştirmez', function (): void {
    arc_bl_reset();
    $sonuc = Newsletter::subscribe('abone@ornek.test', 'tr', '198.51.100.44', '/');

    assertFalse(Newsletter::confirm(''), 'Boş anahtar reddedilmeli');
    assertFalse(Newsletter::confirm(str_repeat('z', 64)), 'Onaltılık olmayan anahtar reddedilmeli');
    assertFalse(Newsletter::confirm(str_repeat('a', 10)), 'Kısa anahtar reddedilmeli');
    assertFalse(Newsletter::unsubscribe(str_repeat('a', 64)), 'Bilinmeyen anahtar reddedilmeli');

    assertSame('pending', (string) Newsletter::byToken($sonuc['token'])['status'], 'Gerçek kayıt bozulmamalı');
});

test('F-BL-e', 'Açık rıza kutusu işaretlenmeden kayıt oluşmaz', function (): void {
    arc_bl_reset();

    // KVKK'da riza acik ve ozgur iradeyle verilmis olmali; kutu isaretsizken
    // adres toplamak riza sayilmaz.
    arc_bl_subscribe(arc_bl_post(['kvkk' => '']));

    $sayi = (int) Arcates\Core\Database::instance()->value('SELECT COUNT(*) FROM newsletter_subscribers');
    assertSame(0, $sayi, 'Onaysız gönderimde kayıt oluşmamalı');
});

test('F-BL-f', 'Etkin aboneye ikinci onay e-postası gönderilmez', function (): void {
    arc_bl_reset();
    $sonuc = Newsletter::subscribe('abone@ornek.test', 'tr', '198.51.100.44', '/');
    Newsletter::confirm($sonuc['token']);

    Mailer::capture();
    arc_bl_subscribe(arc_bl_post());
    $mesajlar = Mailer::sentMessages();
    Mailer::capture(false);

    assertCount(0, $mesajlar, 'Zaten etkin aboneye yeniden onay e-postası gitmemeli');
    assertSame('active', (string) Arcates\Core\Database::instance()->value(
        'SELECT status FROM newsletter_subscribers WHERE email = :e',
        [':e' => 'abone@ornek.test']
    ), 'Etkin abone onay beklemeye düşmemeli');
});

test('F-BL-g', 'CSV onayın kanıtını verir ama anahtarı vermez', function (): void {
    arc_bl_reset();
    $sonuc = Newsletter::subscribe('abone@ornek.test', 'tr', '198.51.100.44', '/iletisim');

    $csv = Newsletter::toCsv();

    assertContains('abone@ornek.test', $csv, 'Adres CSV’de olmalı');
    assertContains('/iletisim', $csv, 'Kaynak sayfa CSV’de olmalı');
    // CSV elden ele dolasabilir; anahtari bilen herkes o kisiyi listeden
    // cikarabilir, o yuzden disari verilmez.
    assertNotContains($sonuc['token'], $csv, 'Anahtar CSV’ye yazılmamalı');
});

test('F-BL-h', 'Alt bilgideki form CSRF ve honeypot taşır, başlıksızken çizilmez', function (): void {
    arc_need_db();
    Lang::use('tr');

    $eski = Arcates\Models\HomeSection::content('footer', 'tr');

    try {
        $yeni = $eski;
        $yeni['newsletter_title'] = 'Bülten';
        Arcates\Models\HomeSection::saveContent('footer', 'tr', $yeni);

        $body = (new Arcates\Controllers\Front\HomeController())
            ->index(Request::make('GET', '/'), [])->body();

        assertContains('id="bulten"', $body, 'Bülten sütunu çizilmeli');
        assertContains('name="_token"', $body, 'CSRF belirteci olmalı');
        assertContains('name="website"', $body, 'Honeypot alanı olmalı');
        assertContains('name="kvkk"', $body, 'Açık rıza kutusu olmalı');
        assertNotContains('name="kvkk" value="1" checked', $body, 'Rıza kutusu önceden işaretli olmamalı');

        // Baslik bos ise arkasinda liste olmayan bir forma davet etmeyiz.
        $yeni['newsletter_title'] = '';
        Arcates\Models\HomeSection::saveContent('footer', 'tr', $yeni);

        $body = (new Arcates\Controllers\Front\HomeController())
            ->index(Request::make('GET', '/'), [])->body();

        assertNotContains('id="bulten"', $body, 'Başlık boşken sütun çizilmemeli');
    } finally {
        Arcates\Models\HomeSection::saveContent('footer', 'tr', $eski);
    }
});
