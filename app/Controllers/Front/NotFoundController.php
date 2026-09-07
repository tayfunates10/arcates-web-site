<?php
/**
 * 404 isleyicisi ve yonlendirme cozumu.
 *
 * Once yonlendirme tablosuna bakilir; kayit varsa 301 verilir ve isabet
 * sayaci artar. Yoksa `not_found` kaydi guncellenir ve 404 sayfasi doner.
 * DOCS.md 9.8 — testler F-11, F-12
 */

declare(strict_types=1);

namespace Arcates\Controllers\Front;

use Arcates\Core\Lang;
use Arcates\Core\Logger;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\View;
use Arcates\Models\Redirect;
use Throwable;

final class NotFoundController extends Controller
{
    public function index(Request $request, array $params): Response
    {
        return $this->handle($request);
    }

    public function handle(Request $request): Response
    {
        $path = Redirect::normalise($request->path());

        // Yonlendirme kaydi varsa once o uygulanir.
        $redirect = $this->lookupRedirect($path);
        if ($redirect !== null) {
            Redirect::hit($path);
            return Response::redirect(path_url($redirect['to']), $redirect['code']);
        }

        $this->record($path, $request->referrer());

        return Response::html(View::render('errors/404', [
            'title'   => __('error_404_title'),
            'message' => __('error_404_text'),
        ]), 404);
    }

    /**
     * Yonlendirme arar. Once tam yol, sonra dil oneki atilmis yol denenir.
     *
     * @return array{to:string, code:int}|null
     */
    private function lookupRedirect(string $path): ?array
    {
        try {
            $direct = Redirect::resolve($path);
            if ($direct !== null) {
                return $direct;
            }

            // `/en/eski-adres` gibi onekli yollar icin oneksiz kaydi da dene.
            [$lang, $bare] = Lang::detect($path);
            if ($bare !== $path) {
                $found = Redirect::resolve($bare);
                if ($found !== null) {
                    return ['to' => Lang::prefix($lang) . $found['to'], 'code' => $found['code']];
                }
            }
        } catch (Throwable $e) {
            Logger::exception($e);
        }

        return null;
    }

    /** Kirik adresi kaydeder; ayni adres tekrar gelirse sayac artar. */
    private function record(string $path, ?string $referrer): void
    {
        if ($path === '' || $path === '/') {
            return;
        }

        try {
            $this->db()->run(
                'INSERT INTO not_found (path, hits, referrer, last_seen)
                      VALUES (:path, 1, :referrer, NOW())
                 ON DUPLICATE KEY UPDATE hits = hits + 1, last_seen = NOW(),
                      referrer = COALESCE(VALUES(referrer), referrer)',
                [':path' => mb_substr($path, 0, 255), ':referrer' => $referrer]
            );
        } catch (Throwable $e) {
            Logger::exception($e);
        }
    }
}
