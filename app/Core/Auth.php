<?php
/**
 * Kimlik dogrulama ve yetkilendirme.
 *
 * DOCS.md 10.4 — password_hash/password_verify, minimum 10 karakter,
 * ayni IP'den 5 basarisiz denemede 15 dakika kilit, e-posta bazli ayri sayac,
 * kullanici var/yok ayrimi sizdirilmaz.
 */

declare(strict_types=1);

namespace Arcates\Core;

final class Auth
{
    private static ?array $user = null;
    private static bool $resolved = false;

    public static function user(): ?array
    {
        if (self::$resolved) {
            return self::$user;
        }

        self::$resolved = true;
        $id = Session::get('user_id');

        if (!is_int($id) && !ctype_digit((string) $id)) {
            return self::$user = null;
        }

        try {
            $row = Database::instance()->first(
                'SELECT id, name, email, role, status, last_login_at FROM users WHERE id = :id AND status = 1',
                [':id' => (int) $id]
            );
        } catch (\Throwable $e) {
            Logger::exception($e);
            return self::$user = null;
        }

        return self::$user = $row;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function id(): ?int
    {
        $user = self::user();
        return $user !== null ? (int) $user['id'] : null;
    }

    public static function role(): ?string
    {
        $user = self::user();
        return $user !== null ? (string) $user['role'] : null;
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    /**
     * Yetki kontrolu. DOCS.md 9.11 — admin tam yetkili, editor yalnizca icerik.
     */
    public static function can(string $ability): bool
    {
        $role = self::role();
        if ($role === null) {
            return false;
        }
        if ($role === 'admin') {
            return true;
        }

        // Editor yalnizca icerik yonetimi yetkilerini tasir. Kisisel form
        // kayitlari, yonlendirmeler, ayarlar, kullanicilar ve yedekler admin'e
        // ozeldir. DOCS.md 9.11, 10.9.
        return in_array($ability, [
            'content.view', 'content.edit',
            'pages.view', 'pages.edit',
            'posts.view', 'posts.edit',
            'projects.view', 'projects.edit',
            'faqs.view', 'faqs.edit',
            'media.view', 'media.edit',
            'home.view', 'home.edit',
            'seo.view',
            'stats.view',
        ], true);
    }

    /** @return array{ok:bool, reason:string, wait:int} */
    public static function attempt(string $email, string $password, string $ip): array
    {
        $email = mb_strtolower(trim($email));
        $db    = Database::instance();

        $lock = self::lockState($ip, $email);
        if ($lock['locked']) {
            self::record($ip, $email, false);
            Logger::activity('login.locked', 'user', null, 'E-posta: ' . $email, null, $ip);
            return ['ok' => false, 'reason' => 'locked', 'wait' => $lock['wait']];
        }

        $row = $db->first(
            'SELECT id, name, email, password_hash, role, status FROM users WHERE email = :email LIMIT 1',
            [':email' => $email]
        );

        $hash   = $row['password_hash'] ?? '$2y$12$usudopqedGWtIrjXlBbQ7uZ/eO9zRQKzTuvi3B/gnFYPzKTPBevAy';
        $verify = password_verify($password, (string) $hash);

        if ($row === null || !$verify || (int) $row['status'] !== 1) {
            self::record($ip, $email, false);
            Logger::activity('login.failed', 'user', null, 'E-posta: ' . $email, null, $ip);
            return ['ok' => false, 'reason' => 'invalid', 'wait' => 0];
        }

        if (password_needs_rehash((string) $row['password_hash'], PASSWORD_DEFAULT)) {
            $db->update('users', ['password_hash' => password_hash($password, PASSWORD_DEFAULT)], ['id' => (int) $row['id']]);
        }

        self::record($ip, $email, true);
        self::login((int) $row['id']);

        $db->update('users', ['last_login_at' => date('Y-m-d H:i:s')], ['id' => (int) $row['id']]);
        Logger::activity('login.success', 'user', (int) $row['id'], null, (int) $row['id'], $ip);

        return ['ok' => true, 'reason' => 'ok', 'wait' => 0];
    }

    public static function login(int $userId): void
    {
        Session::regenerate();
        Session::set('user_id', $userId);
        Session::set('_last_activity', time());
        self::$user     = null;
        self::$resolved = false;
    }

    public static function logout(): void
    {
        $id = self::id();
        if ($id !== null) {
            Logger::activity('logout', 'user', $id, null, $id);
        }
        Session::destroy();
        self::$user     = null;
        self::$resolved = false;
    }

    public static function forget(): void
    {
        self::$user     = null;
        self::$resolved = false;
    }

    /** @return array{locked:bool, wait:int} */
    public static function lockState(string $ip, string $email): array
    {
        $max    = (int) Config::get('security.login_max_tries', 5);
        $window = (int) Config::get('security.login_lock', 900);
        $since  = date('Y-m-d H:i:s', time() - $window);
        $db     = Database::instance();

        $byIp = (int) $db->value(
            'SELECT COUNT(*) FROM login_attempts WHERE ip = :ip AND success = 0 AND attempted_at > :since',
            [':ip' => Security::packIp($ip), ':since' => $since]
        );

        $byEmail = (int) $db->value(
            'SELECT COUNT(*) FROM login_attempts WHERE email = :email AND success = 0 AND attempted_at > :since',
            [':email' => $email, ':since' => $since]
        );

        if ($byIp < $max && $byEmail < $max) {
            return ['locked' => false, 'wait' => 0];
        }

        $last = $db->value(
            'SELECT MAX(attempted_at) FROM login_attempts WHERE (ip = :ip OR email = :email) AND success = 0 AND attempted_at > :since',
            [':ip' => Security::packIp($ip), ':email' => $email, ':since' => $since]
        );

        $wait = $window;
        if (is_string($last)) {
            $wait = max(0, $window - (time() - strtotime($last)));
        }

        return ['locked' => true, 'wait' => $wait];
    }

    private static function record(string $ip, string $email, bool $success): void
    {
        try {
            Database::instance()->insert('login_attempts', [
                'ip'      => Security::packIp($ip),
                'email'   => mb_substr($email, 0, 190),
                'success' => $success ? 1 : 0,
            ]);
        } catch (\Throwable $e) {
            Logger::exception($e);
        }
    }

    public static function clearAttempts(string $ip, string $email): void
    {
        try {
            Database::instance()->run(
                'DELETE FROM login_attempts WHERE (ip = :ip OR email = :email) AND success = 0',
                [':ip' => Security::packIp($ip), ':email' => $email]
            );
        } catch (\Throwable $e) {
            Logger::exception($e);
        }
    }

    public static function hash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
