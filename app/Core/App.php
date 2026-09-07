<?php
/**
 * Uygulama onyukleyicisi.
 *
 * Tek giris noktasi `public/index.php` bu sinifi calistirir.
 * DOCS.md 3, 10.7
 */

declare(strict_types=1);

namespace Arcates\Core;

use Throwable;

final class App
{
    private Router $router;
    private Request $request;

    public function __construct()
    {
        $this->bootstrap();
        $this->request = Request::capture();
        $this->router  = new Router();
    }

    private function bootstrap(): void
    {
        Config::load();

        date_default_timezone_set((string) Config::get('app.timezone', 'Europe/Istanbul'));
        mb_internal_encoding('UTF-8');

        $debug = (bool) Config::get('app.debug', false);

        // DOCS.md 10.7 — canlida hata ekrana basilmaz.
        ini_set('display_errors', $debug ? '1' : '0');
        ini_set('log_errors', '1');
        error_reporting(E_ALL);

        set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
            if ((error_reporting() & $severity) === 0) {
                return false;
            }
            throw new \ErrorException($message, 0, $severity, $file, $line);
        });

        set_exception_handler(function (Throwable $e): void {
            $this->renderException($e);
        });

        Session::start();
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function request(): Request
    {
        return $this->request;
    }

    /** Yonlendirme tanimlarini yukler. */
    public function loadRoutes(): void
    {
        $routes = ARC_ROOT . '/config/routes.php';
        if (is_file($routes)) {
            (static function (Router $router, Request $request): void {
                require ARC_ROOT . '/config/routes.php';
            })($this->router, $this->request);
        }
    }

    /** Istegi isler ve yaniti gonderir. */
    public function run(): void
    {
        try {
            $response = $this->handle($this->request);
        } catch (Throwable $e) {
            $this->renderException($e);
            return;
        }

        $isAdmin = str_starts_with(
            $this->request->path(),
            '/' . trim((string) Config::get('app.admin_path', 'panel'), '/')
        );

        Security::sendHeaders($isAdmin);
        $response->send();
    }

    /** Istegi yanita cevirir. Testler bu yontemi dogrudan cagirabilir. */
    public function handle(Request $request): Response
    {
        $adminPath = trim((string) Config::get('app.admin_path', 'panel'), '/');
        $path      = $request->path();
        $isAdmin   = $path === '/' . $adminPath || str_starts_with($path, '/' . $adminPath . '/');

        // Kurulum tamamlanmadiysa her yol kurulum sihirbazina gider.
        if (!self::isInstalled() && !str_starts_with($path, '/install')) {
            return Response::redirect(path_url('/install'));
        }

        // Dil onekini ayikla; panel her zaman varsayilan dilde calisir.
        if ($isAdmin) {
            Lang::use(Lang::defaultCode());
            $routePath = $path;
        } else {
            [$lang, $routePath] = Lang::detect($path);
            Lang::use($lang);
        }

        // Bakim modu: ziyaretci bakim sayfasini gorur, oturum acmis yonetici
        // siteyi normal gorur. Panel ve kurulum bu kontrolun disindadir.
        // DOCS.md 9.11, test F-17
        if (!$isAdmin && !str_starts_with($path, '/install')) {
            $maintenance = \Arcates\Controllers\Front\Controller::maintenanceResponse();
            if ($maintenance !== null) {
                return $maintenance;
            }
        }

        $match = $this->router->match($request->method(), $routePath);

        if ($match === null) {
            return Response::html('Sayfa bulunamadı.', 404);
        }

        $result = $this->invoke($match['handler'], $match['params'], $request);

        $response = $result instanceof Response ? $result : Response::html((string) $result);

        // Ziyaret kaydi. Yalnizca on yuzdeki basarili sayfa goruntulemeleri
        // sayilir; panel, kurulum ve varlik istekleri disaridadir. Bot imzasi
        // tasiyan istekler kaydedilir ama grafiklere girmez.
        // DOCS.md 8.4, 9.10
        if (!$isAdmin && $request->method() === 'GET' && $response->status() === 200) {
            Visits::record($request, Lang::current());
        }

        return $response;
    }

    /**
     * Isleyiciyi cagirir.
     *
     * Isleyici ya bir closure ya da 'Sinif@yontem' bicimindedir.
     */
    private function invoke(mixed $handler, array $params, Request $request): mixed
    {
        if (is_callable($handler)) {
            return $handler($request, $params);
        }

        if (is_string($handler) && str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler, 2);
            $class            = 'Arcates\\Controllers\\' . $class;

            if (!class_exists($class)) {
                throw new \RuntimeException('Denetleyici bulunamadı: ' . $class);
            }

            $controller = new $class();
            if (!method_exists($controller, $method)) {
                throw new \RuntimeException('Yöntem bulunamadı: ' . $class . '::' . $method);
            }

            return $controller->{$method}($request, $params);
        }

        throw new \RuntimeException('Geçersiz yönlendirme işleyicisi.');
    }

    /** Kurulum tamamlandi mi? DOCS.md 10.7, 13 */
    public static function isInstalled(): bool
    {
        return is_file(ARC_ROOT . '/storage/installed.lock') && Config::exists();
    }

    /** Beklenmeyen hata ekrani. Canlida ayrinti gosterilmez. */
    private function renderException(Throwable $e): void
    {
        Logger::exception($e);

        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/html; charset=UTF-8');
        }

        if ((bool) Config::get('app.debug', false)) {
            echo '<h1>Uygulama hatası</h1><pre>'
                . Security::e(get_class($e) . ': ' . $e->getMessage() . "\n" . $e->getTraceAsString())
                . '</pre>';
            return;
        }

        try {
            echo View::render('errors/500');
        } catch (Throwable) {
            echo '<!doctype html><meta charset="utf-8"><title>Sunucu hatası</title>'
                . '<h1>Beklenmeyen bir hata oluştu</h1>'
                . '<p>Kısa süre sonra tekrar deneyin.</p>';
        }
    }
}
