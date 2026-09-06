<?php
/**
 * Oturum yonetimi.
 *
 * DOCS.md 10.3 — cerez ayarlari, kimlik yenileme, 2 saat islemsizlik suresi.
 */

declare(strict_types=1);

namespace Arcates\Core;

final class Session
{
    private static bool $started = false;

    /** Oturumu guvenli cerez ayarlariyla baslatir. */
    public static function start(): void
    {
        if (self::$started || PHP_SAPI === 'cli') {
            self::$started = true;
            if (!isset($_SESSION)) {
                $_SESSION = [];
            }
            return;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            self::enforceIdleTimeout();
            return;
        }

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => (bool) Config::get('security.cookie_secure', true),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_name((string) Config::get('security.session_name', 'arcsid'));
        session_start();

        self::$started = true;
        self::enforceIdleTimeout();
    }

    /** 2 saat islemsizlikte oturum duser. DOCS.md 10.3 */
    private static function enforceIdleTimeout(): void
    {
        $idle = (int) Config::get('security.session_idle', 7200);
        $last = $_SESSION['_last_activity'] ?? null;

        if (is_int($last) && (time() - $last) > $idle) {
            self::destroy();
            self::start();
            self::flash('warning', 'Oturumunuz islemsizlik nedeniyle sonlandirildi.');
        }

        $_SESSION['_last_activity'] = time();
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /** Giriste oturum kimligi yenilenir. DOCS.md 10.3 */
    public static function regenerate(): void
    {
        if (PHP_SAPI === 'cli') {
            return;
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /** Cikista veri silinir, cerez gecmise alinir. DOCS.md 10.3 */
    public static function destroy(): void
    {
        $_SESSION = [];

        if (PHP_SAPI === 'cli') {
            self::$started = false;
            return;
        }

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                [
                    'expires'  => time() - 42000,
                    'path'     => $params['path'],
                    'domain'   => $params['domain'],
                    'secure'   => $params['secure'],
                    'httponly' => $params['httponly'],
                    'samesite' => $params['samesite'] ?? 'Lax',
                ]
            );
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        self::$started = false;
    }

    // --- Tek seferlik mesajlar ---------------------------------------------

    public static function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
    }

    /** Bekleyen mesajlari dondurur ve temizler. */
    public static function takeFlash(): array
    {
        $flash = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return is_array($flash) ? $flash : [];
    }

    /** Form hatalarini bir sonraki istege tasir. */
    public static function flashErrors(array $errors, array $old = []): void
    {
        $_SESSION['_errors'] = $errors;
        $_SESSION['_old']    = $old;
    }

    public static function takeErrors(): array
    {
        $errors = $_SESSION['_errors'] ?? [];
        unset($_SESSION['_errors']);
        return is_array($errors) ? $errors : [];
    }

    public static function takeOld(): array
    {
        $old = $_SESSION['_old'] ?? [];
        unset($_SESSION['_old']);
        return is_array($old) ? $old : [];
    }
}
