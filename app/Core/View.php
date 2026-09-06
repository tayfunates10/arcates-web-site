<?php
/**
 * Sablon motoru.
 *
 * Sade PHP sablonlari; derleme adimi yoktur. Sablona gecirilen her degisken
 * ekrana basilirken `Security::e()` ile kacirilir.  DOCS.md 1, 10.2
 */

declare(strict_types=1);

namespace Arcates\Core;

use RuntimeException;

final class View
{
    /** Tum sablonlara gecen ortak veriler. */
    private static array $shared = [];

    public static function share(string $key, mixed $value): void
    {
        self::$shared[$key] = $value;
    }

    public static function shareMany(array $data): void
    {
        self::$shared = array_merge(self::$shared, $data);
    }

    public static function shared(): array
    {
        return self::$shared;
    }

    /**
     * Sablonu isler ve ciktiyi dize olarak dondurur.
     *
     * @param string $template `front/home` gibi, `views/` altindaki yol.
     */
    public static function render(string $template, array $data = []): string
    {
        $file = self::path($template);

        $vars = array_merge(self::$shared, $data);

        ob_start();
        (static function (string $__file, array $__vars): void {
            extract($__vars, EXTR_SKIP);
            require $__file;
        })($file, $vars);

        return (string) ob_get_clean();
    }

    /**
     * Sablonu bir duzen icinde isler.
     *
     * @param string $layout   `front/layout` gibi.
     * @param string $template Icerik sablonu.
     */
    public static function renderIn(string $layout, string $template, array $data = []): string
    {
        $content = self::render($template, $data);
        return self::render($layout, array_merge($data, ['content' => $content]));
    }

    /** Parca sablonu; duzen icinden cagrilir. */
    public static function partial(string $template, array $data = []): string
    {
        return self::render($template, $data);
    }

    public static function exists(string $template): bool
    {
        return is_file(self::resolve($template));
    }

    private static function path(string $template): string
    {
        $file = self::resolve($template);
        if (!is_file($file)) {
            throw new RuntimeException('Sablon bulunamadi: ' . $template);
        }
        return $file;
    }

    private static function resolve(string $template): string
    {
        $clean = str_replace(['..', '\\'], '', $template);
        $clean = trim($clean, '/');
        return ARC_ROOT . '/views/' . $clean . '.php';
    }

    /** Testler icin ortak verileri temizler. */
    public static function reset(): void
    {
        self::$shared = [];
    }
}
