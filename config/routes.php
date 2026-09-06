<?php
/**
 * Yonlendirme tanimlari.
 *
 * Bu dosya `App::loadRoutes()` icinden yuklenir; `$router` ve `$request`
 * degiskenleri hazirdir. Dil oneki yonlendirmeden once ayiklandigi icin
 * desenler oneksiz yazilir.  DOCS.md 4, 4.6
 *
 * @var Arcates\Core\Router  $router
 * @var Arcates\Core\Request $request
 */

declare(strict_types=1);

use Arcates\Core\Response;
use Arcates\Core\View;

// ---------------------------------------------------------------------------
// Kurulum  (DOCS.md 13)
// ---------------------------------------------------------------------------

$router->get('/install', 'Front\InstallController@index');
$router->post('/install', 'Front\InstallController@submit');

// ---------------------------------------------------------------------------
// Sistem  (DOCS.md 4.5)
// ---------------------------------------------------------------------------

$router->get('/robots.txt', 'Front\RobotsController@index');

// ---------------------------------------------------------------------------
// Son care: hicbir desen eslesmezse 404
// Faz 3'te sayfa cozumleyicisi, faz 8'de yonlendirme kontrolu eklenecek.
// ---------------------------------------------------------------------------

$router->fallback(static function ($request, array $params): Response {
    return Response::html(View::render('errors/404'), 404);
});
