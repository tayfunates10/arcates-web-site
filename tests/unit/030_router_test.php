<?php
/**
 * Router birim testleri.  DOCS.md 14.2 (U-12), 4.6
 */

declare(strict_types=1);

use Arcates\Core\Lang;
use Arcates\Core\Router;

test('U-12', 'Router {slug} desenini eslestirir ve dogru isleyiciyi cagirir', function (): void {
    $router = new Router();
    $router->get('/referanslar/{slug}', 'Front\ProjectController@show');
    $router->get('/blog/{slug}', 'Front\PostController@show');
    $router->get('/', 'Front\HomeController@index');

    $match = $router->match('GET', '/referanslar/edremit-otel');
    assertTrue($match !== null, 'Desen eslesmeli');
    assertSame('Front\ProjectController@show', $match['handler']);
    assertSame('edremit-otel', $match['params']['slug']);

    $blog = $router->match('GET', '/blog/yerel-seo-rehberi');
    assertSame('yerel-seo-rehberi', $blog['params']['slug']);

    $home = $router->match('GET', '/');
    assertSame('Front\HomeController@index', $home['handler']);
});

test('U-12b', 'Eslesmeyen yol icin son care isleyicisi dondurulur', function (): void {
    $router = new Router();
    $router->get('/hakkimizda', 'A@b');

    assertSame(null, $router->match('GET', '/olmayan-sayfa'), 'Son care yokken null donmeli');

    $router->fallback('Front\PageController@resolve');
    $match = $router->match('GET', '/olmayan-sayfa');
    assertSame('Front\PageController@resolve', $match['handler']);
});

test('U-12c', 'Yontem ayrimi korunur', function (): void {
    $router = new Router();
    $router->post('/iletisim', 'Front\ContactController@submit');

    assertSame(null, $router->match('GET', '/iletisim'), 'POST rotasi GET ile eslesmemeli');
    assertTrue($router->match('POST', '/iletisim') !== null, 'POST eslesmeli');
});

test('U-12d', 'Sondaki egik cizgi ayni rotaya duser', function (): void {
    $router = new Router();
    $router->get('/fiyatlar', 'A@b');

    assertTrue($router->match('GET', '/fiyatlar/') !== null, 'Sondaki egik cizgi tolere edilmeli');
    assertTrue($router->match('GET', '/fiyatlar') !== null);
});

test('U-12e', 'Dil oneki yonlendirmeden once ayiklanir', function (): void {
    Lang::reset();
    Lang::seed([
        'tr' => ['code' => 'tr', 'name' => 'Turkce',  'direction' => 'ltr', 'is_default' => 1, 'is_active' => 1],
        'en' => ['code' => 'en', 'name' => 'English', 'direction' => 'ltr', 'is_default' => 0, 'is_active' => 1],
        'ar' => ['code' => 'ar', 'name' => 'Arapca',  'direction' => 'rtl', 'is_default' => 0, 'is_active' => 1],
    ], 'tr');

    assertSame(['en', '/hakkimizda'], Lang::detect('/en/hakkimizda'));
    assertSame(['tr', '/hakkimizda'], Lang::detect('/hakkimizda'), 'Varsayilan dil oneksizdir');
    assertSame(['en', '/'], Lang::detect('/en'));
    assertSame(['tr', '/de-neyse'], Lang::detect('/de-neyse'), 'Dil kodu olmayan parca onek sayilmaz');

    assertSame('', Lang::prefix('tr'), 'Varsayilan dilde onek bos olmali');
    assertSame('/en', Lang::prefix('en'));
    assertSame('rtl', Lang::direction('ar'), 'Arapca sagdan sola olmali');

    Lang::reset();
});
