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
            1 => 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
            'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık',
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

if (!function_exists('opening_days')) {
    /**
     * `opening_hours` gun kodunu ekran etiketine cevirir.
     *
     * Ayar semadaki bicimi saklar (`Mo-Fr`, `Sa`, `Mo,We,Fr`) cunku
     * `Seo::professionalService()` yapisal veride ham kodu kullanmak
     * zorundadir. Ziyaretciye ise dilin kendi gun adi gosterilir:
     * Turkce sayfada "Mo-Fr" degil "Pzt–Cum".
     *
     * Tanimadigi belirtec oldugu gibi gecer; isletme panelde "Hafta ici"
     * yazdiysa o metin bozulmaz.  DOCS.md 11.2
     */
    function opening_days(string $code): string
    {
        $code = trim($code);
        if ($code === '') {
            return '';
        }

        $label = static function (string $token): string {
            $key = 'day_' . strtolower(trim($token));
            $text = __($key);
            // __() bilinmeyen anahtari kendisi dondurur; o durumda ham belirtec kalir.
            return $text === $key ? trim($token) : $text;
        };

        // Once virgullu liste, sonra her parcada tire araligi.
        $groups = array_map(
            static function (string $part) use ($label): string {
                $range = explode('-', $part);
                if (count($range) === 2) {
                    return $label($range[0]) . '–' . $label($range[1]);
                }
                return $label($part);
            },
            explode(',', $code)
        );

        return implode(', ', $groups);
    }
}

/**
 * Sosyal baglantinin ikonunu dondurur.
 *
 * Panelde yonetici yalnizca ad ve adres girer; hangi platform oldugunu
 * once adresin alan adindan, olmazsa addan cikariyoruz. Taninmayan bir
 * platform icin bos donulur ve alt bilgi metin etiketini basar — boylece
 * girilen hicbir baglanti kaybolmaz.
 *
 * Ikonlar satir ici SVG: harici dosya yok, fonta bagli glif yok.
 * CLAUDE.md 4, DOCS.md 5
 */
function social_icon(string $url, string $label): string
{
    static $paths = [
        'linkedin'  => '<path d="M4.5 3.5a2 2 0 1 1 0 4 2 2 0 0 1 0-4ZM3 9h3v12H3V9Zm6 0h2.9v1.6h.04c.4-.76 1.4-1.6 2.9-1.6 3.1 0 3.7 2 3.7 4.7V21h-3v-5.6c0-1.34-.03-3.06-1.9-3.06-1.9 0-2.2 1.46-2.2 2.96V21H9V9Z"/>',
        'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1.2"/>',
        'youtube'   => '<rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path d="m10.5 9.5 5 2.5-5 2.5v-5Z"/>',
        'github'    => '<path d="M12 2.5a9.5 9.5 0 0 0-3 18.5c.5.1.65-.2.65-.45v-1.6C7 19.4 6.4 17.9 6.4 17.9c-.45-1.15-1.1-1.45-1.1-1.45-.9-.6.07-.6.07-.6 1 .07 1.5 1.03 1.5 1.03.9 1.5 2.35 1.07 2.9.82.1-.65.35-1.08.65-1.33-2.25-.25-4.6-1.12-4.6-5 0-1.1.4-2 1.03-2.7-.1-.25-.45-1.28.1-2.66 0 0 .85-.27 2.75 1.03a9.5 9.5 0 0 1 5 0c1.9-1.3 2.75-1.03 2.75-1.03.55 1.38.2 2.41.1 2.66.64.7 1.03 1.6 1.03 2.7 0 3.89-2.36 4.74-4.61 5 .36.31.68.92.68 1.86v2.76c0 .25.15.55.66.45A9.5 9.5 0 0 0 12 2.5Z"/>',
        'facebook'  => '<path d="M13.5 21v-8h2.7l.4-3h-3.1V8.1c0-.87.24-1.46 1.5-1.46h1.7V3.96A21 21 0 0 0 14.4 3.8c-2.45 0-4.13 1.5-4.13 4.24V10H7.5v3h2.77v8h3.23Z"/>',
        'x'         => '<path d="M3 3h4.2l4.3 6 5-6H20l-6.7 8L21 21h-4.2l-4.6-6.4L6.7 21H4l7.1-8.5L3 3Z"/>',
        'whatsapp'  => '<path d="M12 3a9 9 0 0 0-7.7 13.6L3 21l4.5-1.2A9 9 0 1 0 12 3Z"/><path d="M8.8 8.4c.2-.4.4-.4.6-.4h.5c.2 0 .4 0 .6.5l.7 1.7c.1.2 0 .4-.1.6l-.4.5c-.1.2-.2.3 0 .6a6 6 0 0 0 2.8 2.4c.3.1.5.1.6-.1l.5-.6c.2-.2.3-.2.6-.1l1.6.8c.3.1.4.3.4.5 0 .5-.3 1.3-1.4 1.5-1.6.3-4-1-5.7-2.9-1.3-1.5-1.7-2.9-1.7-3.7 0-.6.2-1 .4-1.2Z"/>',
    ];

    /* Platform ACIK alan adiyla eslesir, alt dizeyle degil.
     *
     * Once `str_contains($host, $name)` kullaniyordum; `x` anahtari tek
     * karakter oldugu icin icinde "x" gecen her alan adi (example.com,
     * nextdoor.com) X ikonu aliyordu ve yoneticinin etiketi kayboluyordu.
     * Artik ya alan adinin kendisi ya da alt alan adi olarak eslesir. */
    static $domains = [
        'linkedin.com'  => 'linkedin',
        'lnkd.in'       => 'linkedin',
        'instagram.com' => 'instagram',
        'youtube.com'   => 'youtube',
        'youtu.be'      => 'youtube',
        'github.com'    => 'github',
        'facebook.com'  => 'facebook',
        'fb.com'        => 'facebook',
        'fb.me'         => 'facebook',
        'x.com'         => 'x',
        'twitter.com'   => 'x',
        't.co'          => 'x',
        'whatsapp.com'  => 'whatsapp',
        'wa.me'         => 'whatsapp',
    ];

    /* Etiketten cozerken TAM eslesme; burada da alt dize aranmaz. */
    static $labels = [
        'linkedin'  => 'linkedin',
        'instagram' => 'instagram',
        'youtube'   => 'youtube',
        'github'    => 'github',
        'facebook'  => 'facebook',
        'x'         => 'x',
        'twitter'   => 'x',
        'whatsapp'  => 'whatsapp',
    ];

    $host = strtolower((string) parse_url($url, PHP_URL_HOST));
    $host = preg_replace('~^www\.~', '', $host) ?? $host;
    $key  = '';

    foreach ($domains as $domain => $name) {
        if ($host === $domain || str_ends_with($host, '.' . $domain)) {
            $key = $name;
            break;
        }
    }

    if ($key === '') {
        $sade = strtolower(preg_replace('~[^a-z]~i', '', $label) ?? '');
        $key  = $labels[$sade] ?? '';
    }

    if ($key === '') {
        return '';
    }

    $fill   = in_array($key, ['linkedin', 'github', 'facebook', 'x'], true);
    $stroke = $fill
        ? 'fill="currentColor"'
        : 'fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"';

    return '<svg class="site-foot__social-icon" viewBox="0 0 24 24" ' . $stroke
        . ' aria-hidden="true" focusable="false">' . $paths[$key] . '</svg>';
}

