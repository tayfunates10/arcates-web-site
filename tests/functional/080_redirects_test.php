<?php
/**
 * Yonlendirme ve 404 yonetimi.
 * DOCS.md 9.8 — testler F-11, F-12, S-18
 */

declare(strict_types=1);

use Arcates\Controllers\Admin\RedirectController;
use Arcates\Controllers\Front\NotFoundController;
use Arcates\Core\Request;
use Arcates\Core\Security;
use Arcates\Models\Redirect;

test('F-11', 'Olmayan adres 404 sayfası döndürür ve not_found kaydı artar', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM not_found');
    $db->run('DELETE FROM redirects');

    $controller = new NotFoundController();

    $first = $controller->handle(Request::make('GET', '/olmayan-adres'));
    assertSame(404, $first->status(), '404 döndürmeli');
    assertContains('404', $first->body(), '404 sayfası görünmeli');

    $row = $db->first('SELECT * FROM not_found WHERE path = :path', [':path' => '/olmayan-adres']);
    assertTrue($row !== null, 'Kayıt oluşmalı');
    assertSame(1, (int) $row['hits'], 'İlk istekte sayaç 1 olmalı');

    // Ayni adres tekrar istenirse sayac artar, yeni satir acilmaz.
    $controller->handle(Request::make('GET', '/olmayan-adres'));
    $controller->handle(Request::make('GET', '/olmayan-adres'));

    $row = $db->first('SELECT * FROM not_found WHERE path = :path', [':path' => '/olmayan-adres']);
    assertSame(3, (int) $row['hits'], 'Sayaç artmalı');
    assertSame(1, (int) $db->count('not_found'), 'Tek satır olmalı');

    $db->run('DELETE FROM not_found');
});

test('F-12', '404 kaydı tek tıkla yönlendirmeye çevrilir', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');

    $db->run('DELETE FROM not_found');
    $db->run('DELETE FROM redirects');
    $db->run('DELETE FROM pages');

    // Once 404 kaydi olussun.
    (new NotFoundController())->handle(Request::make('GET', '/eski-hizmet'));

    $notFound = $db->first('SELECT * FROM not_found WHERE path = :path', [':path' => '/eski-hizmet']);
    assertTrue($notFound !== null, '404 kaydı oluşmalı');

    // Hedef sayfayi olustur.
    arc_page($db, [], ['title' => 'Web Tasarım', 'slug' => 'web-tasarim']);

    $response = (new RedirectController())->convert(
        Request::make('POST', admin_url('yonlendirmeler/404/' . $notFound['id']), [
            '_token'  => Security::csrfToken(),
            'to_path' => '/web-tasarim',
        ]),
        ['id' => (int) $notFound['id']]
    );

    assertSame(302, $response->status(), 'Yönlendirme dönmeli');

    $redirect = Redirect::byFrom('/eski-hizmet');
    assertTrue($redirect !== null, 'Yönlendirme kaydı oluşmalı');
    assertSame('/web-tasarim', $redirect['to_path'], 'Hedef doğru olmalı');
    assertSame(301, (int) $redirect['code'], 'Kalıcı olmalı');

    // 404 listesinden dusmeli.
    assertSame(null, $db->first('SELECT * FROM not_found WHERE path = :path', [':path' => '/eski-hizmet']));

    // Adres artik yeni hedefe gitmeli.
    $visit = (new NotFoundController())->handle(Request::make('GET', '/eski-hizmet'));
    assertSame(301, $visit->status(), 'Adres 301 döndürmeli');
    assertContains('/web-tasarim', (string) $visit->headerLine('Location'), 'Yeni hedefe gitmeli');

    // Isabet sayaci artmali.
    $redirect = Redirect::byFrom('/eski-hizmet');
    assertSame(1, (int) $redirect['hits'], 'İsabet sayacı artmalı');

    $db->run('DELETE FROM pages');
    arc_logout_test();
});

test('S-18', 'Yönlendirme döngüsü panelde engellenir', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');

    $db->run('DELETE FROM redirects');

    // /a → /b
    $first = Redirect::put('/a', '/b', 301);
    assertTrue($first['ok'], 'İlk kayıt kabul edilmeli');

    // /b → /a dongu olusturur
    $loop = Redirect::put('/b', '/a', 301);
    assertFalse($loop['ok'], 'Döngü reddedilmeli');
    assertContains('döngü', $loop['message'], 'Sebep bildirilmeli');

    // Kendine yonlendirme de reddedilir.
    $self = Redirect::put('/c', '/c', 301);
    assertFalse($self['ok'], 'Kendine yönlendirme reddedilmeli');

    // Uc adimli dongu: /b → /c, /c → /a
    Redirect::put('/b', '/c', 301);
    $threeStep = Redirect::put('/c', '/a', 301);
    assertFalse($threeStep['ok'], 'Üç adımlı döngü de reddedilmeli');

    // Panel de ayni kurali uygular.
    $response = (new RedirectController())->store(
        Request::make('POST', admin_url('yonlendirmeler'), [
            '_token'    => Security::csrfToken(),
            'from_path' => '/b',
            'to_path'   => '/a',
            'code'      => '301',
        ]),
        []
    );
    assertSame(302, $response->status(), 'Panel yönlendirme dönmeli');
    assertSame('/c', Redirect::byFrom('/b')['to_path'], 'Döngü kaydı yazılmamış olmalı');

    $db->run('DELETE FROM redirects');
    arc_logout_test();
});

test('F-P8-a', 'Yönlendirme zinciri sonuna kadar izlenir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM redirects');

    Redirect::put('/bir', '/iki', 301);
    Redirect::put('/iki', '/uc', 301);
    Redirect::put('/uc', '/dort', 301);

    $resolved = Redirect::resolve('/bir');
    assertTrue($resolved !== null, 'Zincir çözülmeli');
    assertSame('/dort', $resolved['to'], 'Zincirin sonuna gidilmeli');

    $db->run('DELETE FROM redirects');
});

test('F-P8-b', 'Slug değişimi zinciri uzatmaz', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM redirects');

    // Ilk degisim: /a → /b
    Redirect::forSlugChange('a', 'b', 'tr');
    assertSame('/b', Redirect::byFrom('/a')['to_path']);

    // Ikinci degisim: /b → /c. Eski kayit dogrudan /c'yi gostermeli.
    Redirect::forSlugChange('b', 'c', 'tr');

    assertSame('/c', Redirect::byFrom('/b')['to_path'], 'Yeni kayıt doğru olmalı');
    assertSame('/c', Redirect::byFrom('/a')['to_path'], 'Eski kayıt yeni hedefe taşınmalı');

    // Zincir tek adima inmis olmali.
    $resolved = Redirect::resolve('/a');
    assertSame('/c', $resolved['to'], 'Çözüm tek adımda bitmeli');

    $db->run('DELETE FROM redirects');
});

test('F-P8-c', 'Dil önekli adresler için de yönlendirme çözülür', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM redirects');
    $db->run('DELETE FROM not_found');
    arc_activate_langs(['tr', 'en']);

    Redirect::put('/eski-sayfa', '/yeni-sayfa', 301);

    $response = (new NotFoundController())->handle(Request::make('GET', '/en/eski-sayfa'));

    assertSame(301, $response->status(), 'Önekli adres de yönlendirilmeli');
    assertContains('/en/yeni-sayfa', (string) $response->headerLine('Location'), 'Dil öneki korunmalı');

    $db->run('DELETE FROM redirects');
    $db->run('DELETE FROM not_found');
});

test('F-P8-d', 'Panel yönlendirme ekranı listeleri gösterir', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');

    $db->run('DELETE FROM redirects');
    $db->run('DELETE FROM not_found');

    Redirect::put('/eski', '/yeni', 301);
    (new NotFoundController())->handle(Request::make('GET', '/kirik-adres'));

    $response = (new RedirectController())->index(Request::make('GET', admin_url('yonlendirmeler')), []);
    assertSame(200, $response->status(), 'Ekran açılmalı');

    $body = $response->body();
    assertContains('/eski', $body, 'Yönlendirme listede olmalı');
    assertContains('/kirik-adres', $body, '404 kaydı listede olmalı');
    assertContains('Bulunamayan adresler', $body, '404 bölümü bulunmalı');

    $db->run('DELETE FROM redirects');
    $db->run('DELETE FROM not_found');
    arc_logout_test();
});
