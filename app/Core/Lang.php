<?php
/**
 * Dil yonetimi.
 *
 * Varsayilan dil oneksizdir; digerleri `/en/...`, `/de/...`, `/ar/...`
 * onekiyle calisir. Arapca `rtl` yondedir.  DOCS.md 4.6, 11.3
 */

declare(strict_types=1);

namespace Arcates\Core;

final class Lang
{
    private static string $current = 'tr';
    private static string $default = 'tr';
    private static array $strings = [];
    private static ?array $languages = null;

    /** Etkin dilleri veritabanindan okur; okunamazsa yapilandirmaya duser. */
    public static function languages(): array
    {
        if (self::$languages !== null) {
            return self::$languages;
        }

        try {
            $rows = Database::instance()->all(
                'SELECT code, name, direction, is_default, is_active FROM languages WHERE is_active = 1 ORDER BY sort, code'
            );
        } catch (\Throwable) {
            $rows = [];
        }

        if (!$rows) {
            $rows = [];
            foreach ((array) Config::get('lang.available', ['tr']) as $code) {
                $rows[] = [
                    'code'       => $code,
                    'name'       => self::nativeName((string) $code),
                    'direction'  => $code === 'ar' ? 'rtl' : 'ltr',
                    'is_default' => $code === Config::get('lang.default', 'tr') ? 1 : 0,
                    'is_active'  => 1,
                ];
            }
        }

        $indexed = [];
        foreach ($rows as $row) {
            $indexed[$row['code']] = $row;
            if ((int) $row['is_default'] === 1) {
                self::$default = (string) $row['code'];
            }
        }

        return self::$languages = $indexed;
    }

    public static function codes(): array
    {
        return array_keys(self::languages());
    }

    public static function defaultCode(): string
    {
        self::languages();
        return self::$default;
    }

    public static function current(): string
    {
        return self::$current;
    }

    public static function direction(?string $code = null): string
    {
        $code = $code ?? self::$current;
        $lang = self::languages()[$code] ?? null;
        return ($lang['direction'] ?? 'ltr') === 'rtl' ? 'rtl' : 'ltr';
    }

    public static function isRtl(): bool
    {
        return self::direction() === 'rtl';
    }

    public static function name(string $code): string
    {
        return (string) (self::languages()[$code]['name'] ?? self::nativeName($code));
    }

    public static function exists(string $code): bool
    {
        return isset(self::languages()[$code]);
    }

    /** Etkin dili ayarlar ve ceviri dosyasini yukler. */
    public static function use(string $code): void
    {
        if (!self::exists($code)) {
            $code = self::defaultCode();
        }

        self::$current = $code;
        self::$strings = self::loadFile($code);
    }

    /**
     * Yoldan dil onekini ayiklar.
     *
     * @return array{0:string,1:string} [dil kodu, onek atilmis yol]
     */
    public static function detect(string $path): array
    {
        $path     = '/' . trim($path, '/');
        $segments = array_values(array_filter(explode('/', $path), static fn ($s) => $s !== ''));
        $first    = $segments[0] ?? '';

        if ($first !== '' && $first !== self::defaultCode() && self::exists($first)) {
            array_shift($segments);
            $rest = '/' . implode('/', $segments);
            return [$first, $rest === '/' ? '/' : rtrim($rest, '/')];
        }

        return [self::defaultCode(), $path === '' ? '/' : $path];
    }

    /** Dile gore yol oneki. Varsayilan dilde bostur. DOCS.md 4.6 */
    public static function prefix(?string $code = null): string
    {
        $code = $code ?? self::$current;
        return $code === self::defaultCode() ? '' : '/' . $code;
    }

    /** Ceviri dizesi. Anahtar yoksa anahtarin kendisi doner. */
    public static function get(string $key, array $replace = []): string
    {
        if (!self::$strings) {
            self::$strings = self::loadFile(self::$current);
        }

        $value = self::$strings[$key] ?? null;

        if ($value === null && self::$current !== self::defaultCode()) {
            $fallback = self::loadFile(self::defaultCode());
            $value    = $fallback[$key] ?? null;
        }

        $value = $value ?? $key;

        foreach ($replace as $search => $replacement) {
            $value = str_replace(':' . $search, (string) $replacement, $value);
        }

        return $value;
    }

    private static function loadFile(string $code): array
    {
        $file = ARC_ROOT . '/lang/' . preg_replace('/[^a-z]/', '', $code) . '.php';
        if (!is_file($file)) {
            return [];
        }
        $data = require $file;
        return is_array($data) ? $data : [];
    }

    private static function nativeName(string $code): string
    {
        return match ($code) {
            'tr'    => 'Türkçe',
            'en'    => 'English',
            'de'    => 'Deutsch',
            'ar'    => 'العربية',
            default => strtoupper($code),
        };
    }

    /** Testler icin durumu sifirlar. */
    public static function reset(): void
    {
        self::$languages = null;
        self::$strings   = [];
        self::$current   = 'tr';
        self::$default   = 'tr';
    }

    /** Testlerde dil listesini elle vermek icin. */
    public static function seed(array $languages, string $default = 'tr'): void
    {
        self::$languages = $languages;
        self::$default   = $default;
        self::$current   = $default;
        self::$strings   = self::loadFile($default);
    }
}
