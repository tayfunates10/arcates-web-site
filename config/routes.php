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
// Yonetim paneli  (DOCS.md 9)
// Panel yolu config.php ile degistirilebilir.
// ---------------------------------------------------------------------------

$panel = '/' . trim((string) Arcates\Core\Config::get('app.admin_path', 'panel'), '/');

// Giris ve cikis
$router->get($panel . '/giris', 'Admin\AuthController@showLogin');
$router->post($panel . '/giris', 'Admin\AuthController@login');
$router->post($panel . '/cikis', 'Admin\AuthController@logout');

// Pano
$router->get($panel, 'Admin\DashboardController@index');

// Hesabim
$router->get($panel . '/hesabim', 'Admin\UserController@profile');
$router->post($panel . '/hesabim', 'Admin\UserController@updateProfile');

// Kullanicilar (yalnizca yonetici)
$router->get($panel . '/kullanicilar', 'Admin\UserController@index');
$router->get($panel . '/kullanicilar/yeni', 'Admin\UserController@create');
$router->post($panel . '/kullanicilar/yeni', 'Admin\UserController@store');
$router->get($panel . '/kullanicilar/{id:[0-9]+}', 'Admin\UserController@edit');
$router->post($panel . '/kullanicilar/{id:[0-9]+}', 'Admin\UserController@store');
$router->post($panel . '/kullanicilar/{id:[0-9]+}/sil', 'Admin\UserController@destroy');

// Ayarlar (yalnizca yonetici)
$router->get($panel . '/ayarlar', 'Admin\SettingController@index');
$router->post($panel . '/ayarlar', 'Admin\SettingController@update');

// Islem gunlugu (yalnizca yonetici)
$router->get($panel . '/islem-gunlugu', 'Admin\ActivityController@index');

// ---------------------------------------------------------------------------
// Son care: hicbir desen eslesmezse 404
// Faz 3'te sayfa cozumleyicisi, faz 8'de yonlendirme kontrolu eklenecek.
// ---------------------------------------------------------------------------

$router->fallback(static function ($request, array $params): Response {
    return Response::html(View::render('errors/404'), 404);
});
