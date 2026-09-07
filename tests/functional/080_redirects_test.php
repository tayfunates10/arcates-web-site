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

test('F-11', 'Olmayan adres 404 sayfasi dondurur ve not_found kaydi artar', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM not_found');
    $db->run('DELETE FROM redirects');

    $controller = new NotFoundController();

    $first = $controller->handle(Request::make('GET', '/olmayan-adres'));
    assertSame(404, $first->status(), '404 dondurmeli');
    assertContains('404', $first->body(), '404 sayfasi gorunmeli');

    $row = $db->first('SELECT * FROM not_found WHERE path = :path', [':path' => '/olmayan-adres']);
    assertTrue($row !== null, 'Kayit olusmali');
    assertSame(1, (int) $row['hits'], 'Ilk istekte sayac 1 olmali');

    // Ayni adres tekrar istenirse sayac artar, yeni satir acilmaz.
    $controller->handle(Request::make('GET', '/olmayan-adres'));
    $controller->handle(Request::make('GET', '/olmayan-adres'));

    $row = $db->first('SELECT * FROM not_found WHERE path = :path', [':path' => '/olmayan-adres']);
    assertSame(3, (int) $row['hits'], 'Sayac artmali');
    assertSame(1, (int) $db->count('not_found'), 'Tek satir olmali');

    $db->run('DELETE FROM not_found');
});

test('F-12', '404 kaydi tek tikla yonlendirmeye cevrilir', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');

    $db->run('DELETE FROM not_found');
    $db->run('DELETE FROM redirects');
    $db->run('DELETE FROM pages');

    // Once 404 kaydi olussun.
    (new NotFoundController())->handle(Request::make('GET', '/eski-hizmet'));

    $notFound = $db->first('SELECT * FROM not_found WHERE path = :path', [':path' => '/eski-hizmet']);
    assertTrue($notFound !== null, '404 kaydi olusmali');

    // Hedef sayfayi olustur.
    arc_page($db, [], ['title' => 'Web Tasarim', 'slug' => 'web-tasarim']);

    $response = (new RedirectController())->convert(
        Request::make('POST', admin_url('yonlendirmeler/404/' . $notFound['id']), [
            '_token'  => Security::csrfToken(),
            'to_path' => '/web-tasarim',
        ]),
        ['id' => (int) $notFound['id']]
    );

    assertSame(302, $response->status(), 'Yonlendirme donmeli');

    $redirect = Redirect::byFrom('/eski-hizmet');
    assertTrue($redirect !== null, 'Yonlendirme kaydi olusmali');
    assertSame('/web-tasarim', $redirect['to_path'], 'Hedef dogru olmali');
    assertSame(301, (int) $redirect['code'], 'Kalici olmali');

    // 404 listesinden dusmeli.
    assertSame(null, $db->first('SELECT * FROM not_found WHERE path = :path', [':path' => '/eski-hizmet']));

    // Adres artik yeni hedefe gitmeli.
    $visit = (new NotFoundController())->handle(Request::make('GET', '/eski-hizmet'));
    assertSame(301, $visit->status(), 'Adres 301 dondurmeli');
    assertContains('/web-tasarim', (string) $visit->headerLine('Location'), 'Yeni hedefe gitmeli');

    // Isabet sayaci artmali.
    $redirect = Redirect::byFrom('/eski-hizmet');
    assertSame(1, (int) $redirect['hits'], 'Isabet sayaci artmali');

    $db->run('DELETE FROM pages');
    arc_logout_test();
});

test('S-18', 'Yonlendirme dongusu panelde engellenir', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');

    $db->run('DELETE FROM redirects');

    // /a → /b
    $first = Redirect::put('/a', '/b', 301);
    assertTrue($first['ok'], 'Ilk kayit kabul edilmeli');

    // /b → /a dongu olusturur
    $loop = Redirect::put('/b', '/a', 301);
    assertFalse($loop['ok'], 'Dongu reddedilmeli');
    assertContains('dongu', $loop['message'], 'Sebep bildirilmeli');

    // Kendine yonlendirme de reddedilir.
    $self = Redirect::put('/c', '/c', 301);
    assertFalse($self['ok'], 'Kendine yonlendirme reddedilmeli');

    // Uc adimli dongu: /b → /c, /c → /a
    Redirect::put('/b', '/c', 301);
    $threeStep = Redirect::put('/c', '/a', 301);
    assertFalse($threeStep['ok'], 'Uc adimli dongu de reddedilmeli');

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
    assertSame(302, $response->status(), 'Panel yonlendirme donmeli');
    assertSame('/c', Redirect::byFrom('/b')['to_path'], 'Dongu kaydi yazilmamis olmali');

    $db->run('DELETE FROM redirects');
    arc_logout_test();
});

test('F-P8-a', 'Yonlendirme zinciri sonuna kadar izlenir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM redirects');

    Redirect::put('/bir', '/iki', 301);
    Redirect::put('/iki', '/uc', 301);
    Redirect::put('/uc', '/dort', 301);

    $resolved = Redirect::resolve('/bir');
    assertTrue($resolved !== null, 'Zincir cozulmeli');
    assertSame('/dort', $resolved['to'], 'Zincirin sonuna gidilmeli');

    $db->run('DELETE FROM redirects');
});

test('F-P8-b', 'Slug degisimi zinciri uzatmaz', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM redirects');

    // Ilk degisim: /a → /b
    Redirect::forSlugChange('a', 'b', 'tr');
    assertSame('/b', Redirect::byFrom('/a')['to_path']);

    // Ikinci degisim: /b → /c. Eski kayit dogrudan /c'yi gostermeli.
    Redirect::forSlugChange('b', 'c', 'tr');

    assertSame('/c', Redirect::byFrom('/b')['to_path'], 'Yeni kayit dogru olmali');
    assertSame('/c', Redirect::byFrom('/a')['to_path'], 'Eski kayit yeni hedefe tasinmali');

    // Zincir tek adima inmis olmali.
    $resolved = Redirect::resolve('/a');
    assertSame('/c', $resolved['to'], 'Cozum tek adimda bitmeli');

    $db->run('DELETE FROM redirects');
});

test('F-P8-c', 'Dil onekli adresler icin de yonlendirme cozulur', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM redirects');
    $db->run('DELETE FROM not_found');

    Redirect::put('/eski-sayfa', '/yeni-sayfa', 301);

    $response = (new NotFoundController())->handle(Request::make('GET', '/en/eski-sayfa'));

    assertSame(301, $response->status(), 'Onekli adres de yonlendirilmeli');
    assertContains('/en/yeni-sayfa', (string) $response->headerLine('Location'), 'Dil oneki korunmali');

    $db->run('DELETE FROM redirects');
    $db->run('DELETE FROM not_found');
});

test('F-P8-d', 'Panel yonlendirme ekrani listeleri gosterir', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');

    $db->run('DELETE FROM redirects');
    $db->run('DELETE FROM not_found');

    Redirect::put('/eski', '/yeni', 301);
    (new NotFoundController())->handle(Request::make('GET', '/kirik-adres'));

    $response = (new RedirectController())->index(Request::make('GET', admin_url('yonlendirmeler')), []);
    assertSame(200, $response->status(), 'Ekran acilmali');

    $body = $response->body();
    assertContains('/eski', $body, 'Yonlendirme listede olmali');
    assertContains('/kirik-adres', $body, '404 kaydi listede olmali');
    assertContains('Bulunamayan adresler', $body, '404 bolumu bulunmali');

    $db->run('DELETE FROM redirects');
    $db->run('DELETE FROM not_found');
    arc_logout_test();
});
