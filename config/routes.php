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

// ---------------------------------------------------------------------------
// Kurulum  (DOCS.md 13)
// ---------------------------------------------------------------------------

$router->get('/install', 'Front\InstallController@index');
$router->post('/install', 'Front\InstallController@submit');

// ---------------------------------------------------------------------------
// Sistem  (DOCS.md 4.5)
// ---------------------------------------------------------------------------

$router->get('/robots.txt', 'Front\RobotsController@index');
$router->get('/sitemap.xml', 'Front\SitemapController@index');

// ---------------------------------------------------------------------------
// Anasayfa  (DOCS.md 4.1, 5)
// ---------------------------------------------------------------------------

$router->get('/', 'Front\HomeController@index');

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

// Sayfalar
$router->get($panel . '/sayfalar', 'Admin\PageController@index');
$router->get($panel . '/sayfalar/yeni', 'Admin\PageController@create');
$router->post($panel . '/sayfalar/yeni', 'Admin\PageController@store');
$router->get($panel . '/sayfalar/{id:[0-9]+}', 'Admin\PageController@edit');
$router->post($panel . '/sayfalar/{id:[0-9]+}', 'Admin\PageController@store');
$router->post($panel . '/sayfalar/{id:[0-9]+}/durum', 'Admin\PageController@toggle');
$router->post($panel . '/sayfalar/{id:[0-9]+}/sil', 'Admin\PageController@destroy');

// Anasayfa bolum yoneticisi
$router->get($panel . '/anasayfa', 'Admin\HomeController@index');
$router->get($panel . '/anasayfa/{key:[a-z]+}', 'Admin\HomeController@edit');
$router->post($panel . '/anasayfa/{key:[a-z]+}', 'Admin\HomeController@update');
$router->post($panel . '/anasayfa/{key:[a-z]+}/durum', 'Admin\HomeController@toggle');
$router->post($panel . '/anasayfa/coast/ilceler', 'Admin\HomeController@saveDistricts');

// Menuler
$router->get($panel . '/menuler', 'Admin\MenuController@index');
$router->post($panel . '/menuler', 'Admin\MenuController@save');

// Referanslar
$router->get($panel . '/referanslar', 'Admin\ProjectController@index');
$router->get($panel . '/referanslar/yeni', 'Admin\ProjectController@create');
$router->post($panel . '/referanslar/yeni', 'Admin\ProjectController@store');
$router->get($panel . '/referanslar/{id:[0-9]+}', 'Admin\ProjectController@edit');
$router->post($panel . '/referanslar/{id:[0-9]+}', 'Admin\ProjectController@store');
$router->post($panel . '/referanslar/{id:[0-9]+}/sil', 'Admin\ProjectController@destroy');

// Blog
$router->get($panel . '/blog', 'Admin\PostController@index');
$router->get($panel . '/blog/yeni', 'Admin\PostController@create');
$router->post($panel . '/blog/yeni', 'Admin\PostController@store');
$router->get($panel . '/blog/{id:[0-9]+}', 'Admin\PostController@edit');
$router->post($panel . '/blog/{id:[0-9]+}', 'Admin\PostController@store');
$router->post($panel . '/blog/{id:[0-9]+}/sil', 'Admin\PostController@destroy');

// SSS
$router->get($panel . '/sss', 'Admin\FaqController@index');
$router->get($panel . '/sss/yeni', 'Admin\FaqController@create');
$router->post($panel . '/sss/yeni', 'Admin\FaqController@store');
$router->get($panel . '/sss/{id:[0-9]+}', 'Admin\FaqController@edit');
$router->post($panel . '/sss/{id:[0-9]+}', 'Admin\FaqController@store');
$router->post($panel . '/sss/{id:[0-9]+}/sil', 'Admin\FaqController@destroy');

// Medya
$router->get($panel . '/medya', 'Admin\MediaController@index');
$router->post($panel . '/medya/yukle', 'Admin\MediaController@upload');
$router->get($panel . '/medya/{id:[0-9]+}', 'Admin\MediaController@show');
$router->post($panel . '/medya/{id:[0-9]+}', 'Admin\MediaController@update');
$router->post($panel . '/medya/{id:[0-9]+}/sil', 'Admin\MediaController@destroy');

// Form kayitlari
$router->get($panel . '/formlar', 'Admin\SubmissionController@index');
$router->get($panel . '/formlar/csv', 'Admin\SubmissionController@export');
$router->post($panel . '/formlar/temizle', 'Admin\SubmissionController@purge');
$router->get($panel . '/formlar/{id:[0-9]+}', 'Admin\SubmissionController@show');
$router->post($panel . '/formlar/{id:[0-9]+}', 'Admin\SubmissionController@update');
$router->post($panel . '/formlar/{id:[0-9]+}/sil', 'Admin\SubmissionController@destroy');

// Yonlendirmeler ve 404
$router->get($panel . '/yonlendirmeler', 'Admin\RedirectController@index');
$router->post($panel . '/yonlendirmeler', 'Admin\RedirectController@store');
$router->post($panel . '/yonlendirmeler/{id:[0-9]+}/sil', 'Admin\RedirectController@destroy');
$router->post($panel . '/yonlendirmeler/404/{id:[0-9]+}', 'Admin\RedirectController@convert');
$router->post($panel . '/yonlendirmeler/404/{id:[0-9]+}/sil', 'Admin\RedirectController@forget');

// SEO
$router->get($panel . '/seo', 'Admin\SeoController@index');
$router->post($panel . '/seo', 'Admin\SeoController@update');
$router->get($panel . '/seo/sitemap', 'Admin\SeoController@sitemap');

// Islem gunlugu (yalnizca yonetici)
$router->get($panel . '/islem-gunlugu', 'Admin\ActivityController@index');

// ---------------------------------------------------------------------------
// Referanslar, blog ve SSS  (DOCS.md 4.1)
// ---------------------------------------------------------------------------

$router->get('/referanslar', 'Front\ProjectController@index');
$router->get('/referanslar/{slug:[^/]+}', 'Front\ProjectController@show');

$router->get('/blog', 'Front\PostController@index');
$router->get('/blog/{slug:[^/]+}', 'Front\PostController@show');

$router->get('/sss', 'Front\FaqController@index');

// ---------------------------------------------------------------------------
// Teklif formu ve tesekkur sayfasi  (DOCS.md 12)
// ---------------------------------------------------------------------------

$router->get('/iletisim', 'Front\ContactController@show');
$router->post('/iletisim', 'Front\ContactController@submit');
$router->get('/tesekkurler', 'Front\ContactController@thanks');

// ---------------------------------------------------------------------------
// On yuz sayfalari  (DOCS.md 4.1 - 4.4)
//
// Hizmet, ilce ve sektor sayfalari da tek duzey slug ile calisir; tur
// veritabanindan gelir. Bu yuzden tek bir yakalayici desen kullanilir ve
// panel yollari onunde tanimlanir.
// ---------------------------------------------------------------------------

$router->get('/{slug:[^/]+}', 'Front\PageController@show');

// ---------------------------------------------------------------------------
// Son care: yonlendirme tablosuna bakilir, yoksa 404 kaydi tutulur.
// DOCS.md 9.8 — testler F-11, F-12
// ---------------------------------------------------------------------------

$router->fallback('Front\NotFoundController@index');
