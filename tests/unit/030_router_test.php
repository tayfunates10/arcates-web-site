<?php
/**
 * Router birim testleri.  DOCS.md 14.2 (U-12), 4.6
 */

declare(strict_types=1);

use Arcates\Core\Lang;
use Arcates\Core\Router;

test('U-12', 'Router {slug} desenini eşleştirir ve doğru işleyiciyi çağırır', function (): void {
    $router = new Router();
    $router->get('/referanslar/{slug}', 'Front\ProjectController@show');
    $router->get('/blog/{slug}', 'Front\PostController@show');
    $router->get('/', 'Front\HomeController@index');

    $match = $router->match('GET', '/referanslar/edremit-otel');
    assertTrue($match !== null, 'Desen eşleşmeli');
    assertSame('Front\ProjectController@show', $match['handler']);
    assertSame('edremit-otel', $match['params']['slug']);

    $blog = $router->match('GET', '/blog/yerel-seo-rehberi');
    assertSame('yerel-seo-rehberi', $blog['params']['slug']);

    $home = $router->match('GET', '/');
    assertSame('Front\HomeController@index', $home['handler']);
});

test('U-12a2', 'Özel alt desenli yer tutucular çalışır', function (): void {
    $router = new Router();
    $router->get('/panel/kullanicilar/{id:[0-9]+}', 'Admin\UserController@edit');
    $router->get('/{slug:[^/]+}', 'Front\PageController@show');

    $user = $router->match('GET', '/panel/kullanicilar/42');
    assertTrue($user !== null, 'Sayısal kimlik eşleşmeli');
    assertSame('42', $user['params']['id']);
    assertSame('Admin\UserController@edit', $user['handler']);

    // Alt desen gercekten kisitlamali: harfli kimlik kullanici rotasiyla
    // eslesmez ve tek duzey sayfa desenine de uymaz.
    assertSame(null, $router->match('GET', '/panel/kullanicilar/abc'), 'Harfli kimlik eşleşmemeli');

    $page = $router->match('GET', '/edremit-web-tasarim');
    assertSame('edremit-web-tasarim', $page['params']['slug'], 'Tek düzey slug eşleşmeli');

    // Cok duzeyli yol tek duzey desene uymaz.
    $deep = new Router();
    $deep->get('/{slug:[^/]+}', 'Front\PageController@show');
    assertSame(null, $deep->match('GET', '/blog/yazi'), 'Çok düzeyli yol eşleşmemeli');
});

test('U-12b', 'Eşleşmeyen yol için son care işleyicisi döndürülür', function (): void {
    $router = new Router();
    $router->get('/hakkimizda', 'A@b');

    assertSame(null, $router->match('GET', '/olmayan-sayfa'), 'Son care yokken null dönmeli');

    $router->fallback('Front\PageController@resolve');
    $match = $router->match('GET', '/olmayan-sayfa');
    assertSame('Front\PageController@resolve', $match['handler']);
});

test('U-12c', 'Yöntem ayrımı korunur', function (): void {
    $router = new Router();
    $router->post('/iletisim', 'Front\ContactController@submit');

    assertSame(null, $router->match('GET', '/iletisim'), 'POST rotası GET ile eşleşmemeli');
    assertTrue($router->match('POST', '/iletisim') !== null, 'POST eşleşmeli');
});

test('U-12d', 'Sondaki eğik çizgi aynı rotaya düşer', function (): void {
    $router = new Router();
    $router->get('/fiyatlar', 'A@b');

    assertTrue($router->match('GET', '/fiyatlar/') !== null, 'Sondaki eğik çizgi tolere edilmeli');
    assertTrue($router->match('GET', '/fiyatlar') !== null);
});

test('U-12e', 'Dil öneki yönlendirmeden önce ayıklanır', function (): void {
    Lang::reset();
    Lang::seed([
        'tr' => ['code' => 'tr', 'name' => 'Türkçe',  'direction' => 'ltr', 'is_default' => 1, 'is_active' => 1],
        'en' => ['code' => 'en', 'name' => 'English', 'direction' => 'ltr', 'is_default' => 0, 'is_active' => 1],
        'ar' => ['code' => 'ar', 'name' => 'Arapça',  'direction' => 'rtl', 'is_default' => 0, 'is_active' => 1],
    ], 'tr');

    assertSame(['en', '/hakkimizda'], Lang::detect('/en/hakkimizda'));
    assertSame(['tr', '/hakkimizda'], Lang::detect('/hakkimizda'), 'Varsayılan dil öneksizdir');
    assertSame(['en', '/'], Lang::detect('/en'));
    assertSame(['tr', '/de-neyse'], Lang::detect('/de-neyse'), 'Dil kodu olmayan parça önek sayılmaz');

    assertSame('', Lang::prefix('tr'), 'Varsayılan dilde önek boş olmalı');
    assertSame('/en', Lang::prefix('en'));
    assertSame('rtl', Lang::direction('ar'), 'Arapça sağdan sola olmalı');

    Lang::reset();
});


test('U-12f', 'HEAD yalniz GET rotasini ve parametrelerini kullanir', function (): void {
    $router = new Router();
    $router->get('/blog/{slug}', 'Post@show');
    $router->post('/iletisim', 'Contact@store');
    assertSame($router->match('GET', '/blog/ornek'), $router->match('HEAD', '/blog/ornek'));
    assertSame(null, $router->match('HEAD', '/iletisim'), 'HEAD POST islemi baslatmamali');
    assertSame(null, $router->match('HEAD', '/bulunamayan'), 'Olmayan rota 404 akisini korumali');
});
