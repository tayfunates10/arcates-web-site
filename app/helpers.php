<?php
/**
 * Kisayol fonksiyonlari.
 *
 * Sablonlarda okunabilirlik icin kullanilir; hepsi cekirdek siniflara
 * yonlendirir.  DOCS.md 3
 */

declare(strict_types=1);

use Arcates\Core\Config;
use Arcates\Core\Database;
use Arcates\Core\Lang;
use Arcates\Core\Security;
use Arcates\Core\Session;
use Arcates\Core\Settings;
use Arcates\Core\View;

if (!function_exists('e')) {
    /** HTML kacirma. DOCS.md 10.2 */
    function e(mixed $value): string
    {
        return Security::e($value);
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        return Config::get($key, $default);
    }
}

if (!function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Settings::get($key, $default);
    }
}

if (!function_exists('db')) {
    function db(): Database
    {
        return Database::instance();
    }
}

if (!function_exists('__')) {
    /** Ceviri dizesi. */
    function __(string $key, array $replace = []): string
    {
        return Lang::get($key, $replace);
    }
}

if (!function_exists('url')) {
    /**
     * Site ici mutlak adres. Dil oneki otomatik eklenir.
     * DOCS.md 4.6, 11.1 (canonical mutlak olmali)
     */
    function url(string $path = '/', ?string $lang = null): string
    {
        $base = rtrim((string) Config::get('app.base_url', ''), '/');

        if (preg_match('#^https?://#i', $path) === 1) {
            return $path;
        }

        $path   = '/' . ltrim($path, '/');
        $prefix = Lang::prefix($lang);

        if ($path === '/') {
            return $base . ($prefix !== '' ? $prefix : '/');
        }

        return $base . $prefix . rtrim($path, '/');
    }
}

if (!function_exists('path_url')) {
    /** Dil oneki eklemeden mutlak adres (sitemap, robots, panel). */
    function path_url(string $path = '/'): string
    {
        $base = rtrim((string) Config::get('app.base_url', ''), '/');
        $path = '/' . ltrim($path, '/');
        return $base . ($path === '/' ? '/' : rtrim($path, '/'));
    }
}

if (!function_exists('admin_url')) {
    /** Panel adresi. Panel yolu yapilandirmadan gelir. DOCS.md 9 */
    function admin_url(string $path = ''): string
    {
        $adminPath = trim((string) Config::get('app.admin_path', 'panel'), '/');
        $path      = trim($path, '/');
        return '/' . $adminPath . ($path !== '' ? '/' . $path : '');
    }
}

if (!function_exists('asset')) {
    /** Statik varlik adresi; degisiklikte onbellek kirilir. */
    function asset(string $path): string
    {
        $path = '/assets/' . ltrim($path, '/');
        $file = ARC_ROOT . '/public' . $path;
        $stamp = is_file($file) ? (string) filemtime($file) : (string) time();
        return $path . '?v=' . substr(md5($stamp), 0, 8);
    }
}

if (!function_exists('csrf_field')) {
    /** Her POST formunda zorunludur. DOCS.md 10.5 */
    function csrf_field(): string
    {
        return Security::csrfField();
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Security::csrfToken();
    }
}

if (!function_exists('old')) {
    /** Hatali gonderimden sonra alan degerini geri getirir. */
    function old(string $key, mixed $default = ''): mixed
    {
        $old = View::shared()['_old'] ?? [];
        return $old[$key] ?? $default;
    }
}

if (!function_exists('error_for')) {
    /** Alan icin ilk dogrulama hatasi. */
    function error_for(string $key): ?string
    {
        $errors = View::shared()['_errors'] ?? [];
        $value  = $errors[$key] ?? null;
        if (is_array($value)) {
            return $value[0] ?? null;
        }
        return is_string($value) ? $value : null;
    }
}

if (!function_exists('partial')) {
    function partial(string $template, array $data = []): string
    {
        return View::partial($template, $data);
    }
}

if (!function_exists('flash')) {
    function flash(string $type, string $message): void
    {
        Session::flash($type, $message);
    }
}

if (!function_exists('str_limit')) {
    /** Metni kelime sinirinda keser. */
    function str_limit(string $text, int $limit = 160, string $suffix = '…'): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', Security::toPlainText($text)) ?? '');
        if (mb_strlen($text) <= $limit) {
            return $text;
        }
        $cut  = mb_substr($text, 0, $limit);
        $last = mb_strrpos($cut, ' ');
        if ($last !== false && $last > $limit * 0.6) {
            $cut = mb_substr($cut, 0, $last);
        }
        return rtrim($cut) . $suffix;
    }
}

if (!function_exists('format_date')) {
    /** Turkce tarih bicimi. */
    function format_date(?string $datetime, bool $withTime = false): string
    {
        if ($datetime === null || $datetime === '' || str_starts_with($datetime, '0000')) {
            return '';
        }
        $time = strtotime($datetime);
        if ($time === false) {
            return '';
        }

        $months = [
            1 => 'Ocak', 'Subat', 'Mart', 'Nisan', 'Mayis', 'Haziran',
            'Temmuz', 'Agustos', 'Eylul', 'Ekim', 'Kasim', 'Aralik',
        ];

        $out = date('j', $time) . ' ' . $months[(int) date('n', $time)] . ' ' . date('Y', $time);
        return $withTime ? $out . ' ' . date('H:i', $time) : $out;
    }
}

if (!function_exists('format_bytes')) {
    function format_bytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i     = 0;
        $value = (float) $bytes;
        while ($value >= 1024 && $i < count($units) - 1) {
            $value /= 1024;
            $i++;
        }
        return ($i === 0 ? (string) (int) $value : number_format($value, 1, ',', '.')) . ' ' . $units[$i];
    }
}
