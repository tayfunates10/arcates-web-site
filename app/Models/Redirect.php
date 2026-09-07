<?php
/**
 * Yonlendirmeler.
 *
 * Slug degisince otomatik 301 kaydi olusturulur; bu davranis kapatilamaz.
 * DOCS.md 9.3, 9.8 — testler F-03, S-18
 */

declare(strict_types=1);

namespace Arcates\Models;

use Arcates\Core\Logger;
use Throwable;

final class Redirect extends Model
{
    protected static string $table = 'redirects';

    /** En fazla izlenecek zincir uzunlugu. */
    private const MAX_HOPS = 5;

    public static function listing(string $search = ''): array
    {
        if ($search === '') {
            return self::db()->all('SELECT * FROM redirects ORDER BY created_at DESC');
        }

        return self::db()->all(
            'SELECT * FROM redirects WHERE from_path LIKE :q OR to_path LIKE :q ORDER BY created_at DESC',
            [':q' => '%' . $search . '%']
        );
    }

    public static function byFrom(string $path): ?array
    {
        return self::db()->first('SELECT * FROM redirects WHERE from_path = :path', [':path' => self::normalise($path)]);
    }

    /**
     * Yolu hedefine cozer; zinciri sonuna kadar izler.
     *
     * @return array{to:string, code:int}|null
     */
    public static function resolve(string $path): ?array
    {
        $current = self::normalise($path);
        $seen    = [$current => true];
        $code    = 301;
        $target  = null;

        for ($hop = 0; $hop < self::MAX_HOPS; $hop++) {
            $row = self::byFrom($current);
            if ($row === null) {
                break;
            }

            $next = self::normalise((string) $row['to_path']);
            $code = (int) $row['code'];

            if (isset($seen[$next])) {
                // Dongu; ilk hedefte dur ve kaydet.
                Logger::warning('Yönlendirme döngüsü', ['from' => $path, 'at' => $next]);
                $target = $next;
                break;
            }

            $seen[$next] = true;
            $target      = $next;
            $current     = $next;
        }

        if ($target === null || $target === self::normalise($path)) {
            return null;
        }

        return ['to' => $target, 'code' => in_array($code, [301, 302, 307, 308], true) ? $code : 301];
    }

    /** Isabet sayacini artirir. */
    public static function hit(string $path): void
    {
        try {
            self::db()->run(
                'UPDATE redirects SET hits = hits + 1, last_hit_at = NOW() WHERE from_path = :path',
                [':path' => self::normalise($path)]
            );
        } catch (Throwable $e) {
            Logger::exception($e);
        }
    }

    /**
     * Yonlendirme ekler veya gunceller.
     *
     * @return array{ok:bool, message:string}
     */
    public static function put(string $from, string $to, int $code = 301, ?int $ignoreId = null): array
    {
        $from = self::normalise($from);
        $to   = self::normalise($to);

        if ($from === '' || $to === '') {
            return ['ok' => false, 'message' => 'Kaynak ve hedef adres boş olamaz.'];
        }

        if ($from === $to) {
            return ['ok' => false, 'message' => 'Kaynak ve hedef adres aynı olamaz.'];
        }

        if (self::wouldLoop($from, $to, $ignoreId)) {
            return ['ok' => false, 'message' => 'Bu kayıt bir yönlendirme döngüsü oluşturur.'];
        }

        $existing = self::byFrom($from);

        if ($existing !== null && (int) $existing['id'] !== ($ignoreId ?? 0)) {
            self::db()->update('redirects', ['to_path' => $to, 'code' => $code], ['id' => (int) $existing['id']]);
            return ['ok' => true, 'message' => 'Yönlendirme güncellendi.'];
        }

        if ($ignoreId !== null && $ignoreId > 0) {
            self::db()->update('redirects', ['from_path' => $from, 'to_path' => $to, 'code' => $code], ['id' => $ignoreId]);
            return ['ok' => true, 'message' => 'Yönlendirme güncellendi.'];
        }

        self::db()->insert('redirects', ['from_path' => $from, 'to_path' => $to, 'code' => $code]);
        return ['ok' => true, 'message' => 'Yönlendirme eklendi.'];
    }

    /**
     * `/a → /b` ve `/b → /a` gibi donguleri yakalar.  DOCS.md test S-18
     */
    public static function wouldLoop(string $from, string $to, ?int $ignoreId = null): bool
    {
        $from    = self::normalise($from);
        $current = self::normalise($to);

        for ($hop = 0; $hop < self::MAX_HOPS; $hop++) {
            if ($current === $from) {
                return true;
            }

            $row = self::byFrom($current);
            if ($row === null) {
                return false;
            }
            if ($ignoreId !== null && (int) $row['id'] === $ignoreId) {
                return false;
            }

            $current = self::normalise((string) $row['to_path']);
        }

        // Zincir cok uzun; dongu kabul edilir.
        return true;
    }

    /**
     * Slug degisiminde otomatik 301 kaydi. Bu davranis kapatilamaz.
     * DOCS.md 9.3, test F-03
     */
    public static function forSlugChange(string $oldSlug, string $newSlug, string $lang): void
    {
        if ($oldSlug === '' || $oldSlug === $newSlug) {
            return;
        }

        $prefix = \Arcates\Core\Lang::prefix($lang);
        $from   = $prefix . '/' . ltrim($oldSlug, '/');
        $to     = $prefix . '/' . ltrim($newSlug, '/');

        $result = self::put($from, $to, 301);

        if (!$result['ok']) {
            Logger::warning('Slug değişimi için yönlendirme yazılamadı', [
                'from'   => $from,
                'to'     => $to,
                'reason' => $result['message'],
            ]);
            return;
        }

        // Eski adrese isaret eden onceki kayitlar yeni hedefe tasinir; boylece
        // zincir uzamaz ve dongu olusmaz.
        self::db()->run(
            'UPDATE redirects SET to_path = :to WHERE to_path = :from AND from_path <> :to2',
            [':to' => $to, ':from' => $from, ':to2' => $to]
        );

        Logger::activity('redirect.auto', 'redirect', null, $from . ' → ' . $to);
    }

    /** Yolu tekil bicime getirir: bastan egik cizgi, sondan yok. */
    public static function normalise(string $path): string
    {
        $path = trim($path);
        if ($path === '') {
            return '';
        }

        // Tam adres verilmisse yalnizca yol kismi alinir.
        if (preg_match('#^https?://#i', $path) === 1) {
            $parsed = parse_url($path, PHP_URL_PATH);
            $path   = is_string($parsed) ? $parsed : '/';
        }

        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
