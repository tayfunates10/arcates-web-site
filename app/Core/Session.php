<?php

declare(strict_types=1);

namespace Arcates\Core;

final class Session
{
    private const IDLE_TIMEOUT = 7200;

    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_name('arcsid');
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
        self::enforceTimeout();
    }

    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public static function get(string $key, mixed $default = null): mixed { return $_SESSION[$key] ?? $default; }
    public static function set(string $key, mixed $value): void { $_SESSION[$key] = $value; }
    public static function remove(string $key): void { unset($_SESSION[$key]); }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    private static function enforceTimeout(): void
    {
        $now = time();
        $last = (int) ($_SESSION['_last_activity'] ?? $now);
        if ($now - $last > self::IDLE_TIMEOUT) {
            $_SESSION = [];
            self::regenerate();
        }
        $_SESSION['_last_activity'] = $now;
    }
}
